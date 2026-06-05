@extends('layouts.admin')
@section('title', 'Invoice – ' . $invoice->invoice_no)

@push('styles')
<style>
  .hero-bar { background:linear-gradient(135deg,#28c76f,#20a255); border-radius:16px; padding:1.25rem 1.75rem; color:#fff; margin-bottom:1rem; display:flex; align-items:center; gap:1rem; flex-wrap:wrap; }
  .hero-bar .back-btn { width:38px; height:38px; border-radius:10px; background:rgba(255,255,255,.2); border:0; color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.2rem; text-decoration:none; transition:background .15s; }
  .hero-bar .back-btn:hover { background:rgba(255,255,255,.32); color:#fff; }
  .hero-bar h4 { margin:0; font-size:1.2rem; font-weight:700; }
  .section-card { border-radius:14px; border:0; box-shadow:0 2px 12px rgba(40,199,111,.08); margin-bottom:1.25rem; }
  .section-card .card-header { background:#f8f9fa; border-radius:14px 14px 0 0; padding:.85rem 1.25rem; font-weight:700; font-size:.875rem; display:flex; align-items:center; gap:.5rem; border-bottom:1px solid rgba(0,0,0,.06); }
  .section-icon { width:28px; height:28px; border-radius:8px; background:#e8faf0; color:#28c76f; display:flex; align-items:center; justify-content:center; font-size:.9rem; }
  .info-label { font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#a1acb8; margin-bottom:.25rem; }
  .info-value { font-size:.92rem; font-weight:500; color:#32325d; }
  .billing-row { display:flex; justify-content:space-between; padding:.35rem 0; font-size:.85rem; border-bottom:1px solid #f5f5f5; }
  .billing-row.grand { font-size:1.05rem; font-weight:700; border-top:2px solid #e0e0e0; border-bottom:none; margin-top:4px; padding-top:.6rem; }
  .payment-progress { height:10px; border-radius:10px; background:#e9ecef; overflow:hidden; }
  .payment-fill { height:100%; border-radius:10px; background:linear-gradient(90deg,#28c76f,#20a255); transition:width .4s; }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  <div class="hero-bar">
    <a href="{{ route('admin.cctv.invoices.index') }}" class="back-btn"><i class="bx bx-arrow-back"></i></a>
    <div class="flex-grow-1">
      <h4>{{ $invoice->invoice_no }}</h4>
      <div style="opacity:.85;font-size:.85rem;">
        @php $sc = ['Unpaid'=>'danger','Partial'=>'warning','Paid'=>'success'][$invoice->status] ?? 'secondary' @endphp
        <span class="badge bg-label-{{ $sc }}">{{ $invoice->status }}</span>
        <span class="ms-2">{{ $invoice->customer_name }}</span>
      </div>
    </div>
    <div class="d-flex gap-2 flex-wrap">
      <a href="{{ route('admin.cctv.invoices.pdf', $invoice) }}" target="_blank" class="btn btn-light btn-sm"><i class="bx bx-file-pdf me-1"></i> PDF</a>
    </div>
  </div>

  {{-- Pipeline Banner --}}
  @php
    $pLead      = $invoice->lead      ?? ($invoice->project?->lead ?? null);
    $pProject   = $invoice->project   ?? null;
    $pQuotation = $invoice->quotation ?? ($pProject?->quotation ?? null);
    $pSurvey    = $pLead ? $pLead->surveys()->first() : null;
  @endphp
  @include('admin.cctv._pipeline_banner', [
    'lead'      => $pLead,
    'survey'    => $pSurvey,
    'quotation' => $pQuotation,
    'project'   => $pProject,
    'invoice'   => $invoice,
    'currentStep' => 'invoice',
  ])

  @if(session('success'))
  <div class="alert alert-success alert-dismissible fade show mb-3">
    <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif

  <div class="row g-3">
    <div class="col-lg-8">

      {{-- Customer --}}
      <div class="card section-card">
        <div class="card-header"><div class="section-icon"><i class="bx bx-user"></i></div> Customer</div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-sm-6"><div class="info-label">Name</div><div class="info-value">{{ $invoice->customer_name }}</div></div>
            <div class="col-sm-6"><div class="info-label">Mobile</div><div class="info-value font-monospace">{{ $invoice->mobile ?? '—' }}</div></div>
            <div class="col-12"><div class="info-label">Address</div><div class="info-value">{{ $invoice->address ?? '—' }}</div></div>
          </div>
        </div>
      </div>

      {{-- Items --}}
      <div class="card section-card">
        <div class="card-header"><div class="section-icon"><i class="bx bx-list-ul"></i></div> Items</div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table align-middle mb-0">
              <thead class="table-light">
                <tr><th>Description</th><th class="text-center">Qty</th><th class="text-end">Unit Price</th><th class="text-end">Total</th></tr>
              </thead>
              <tbody>
                @php $items = $invoice->equipment_list ?? [] @endphp
                @forelse($items as $item)
                <tr>
                  <td>{{ $item['name'] ?? '—' }}</td>
                  <td class="text-center">{{ $item['qty'] ?? 1 }}</td>
                  <td class="text-end">Rs. {{ number_format($item['unit_price'] ?? 0, 2) }}</td>
                  <td class="text-end fw-600">Rs. {{ number_format($item['total'] ?? (($item['qty']??1)*($item['unit_price']??0)), 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-muted text-center py-3">No items recorded.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
          <div class="p-3">
            @php $itemsTotal = collect($items)->sum('total') @endphp
            @if(($invoice->labour_cost ?? 0) > 0)
            <div class="billing-row"><span class="text-muted">Labour</span><span>Rs. {{ number_format($invoice->labour_cost, 2) }}</span></div>
            @endif
            @if(($invoice->installation_cost ?? 0) > 0)
            <div class="billing-row"><span class="text-muted">Installation</span><span>Rs. {{ number_format($invoice->installation_cost, 2) }}</span></div>
            @endif
            @if(($invoice->transport_cost ?? 0) > 0)
            <div class="billing-row"><span class="text-muted">Transport</span><span>Rs. {{ number_format($invoice->transport_cost, 2) }}</span></div>
            @endif
            @if(($invoice->discount ?? 0) > 0)
            <div class="billing-row"><span class="text-muted">Discount</span><span class="text-danger">- Rs. {{ number_format($invoice->discount, 2) }}</span></div>
            @endif
            @if(($invoice->tax ?? 0) > 0)
            <div class="billing-row"><span class="text-muted">Tax</span><span>Rs. {{ number_format($invoice->tax, 2) }}</span></div>
            @endif
            <div class="billing-row grand"><span>Grand Total</span><span class="text-primary">Rs. {{ number_format($invoice->grand_total ?? 0, 2) }}</span></div>
          </div>
        </div>
      </div>

      @if($invoice->notes)
      <div class="card section-card">
        <div class="card-header"><div class="section-icon"><i class="bx bx-note"></i></div> Notes</div>
        <div class="card-body"><p class="mb-0 small" style="white-space:pre-line">{{ $invoice->notes }}</p></div>
      </div>
      @endif

    </div>

    {{-- Sidebar --}}
    <div class="col-lg-4">

      {{-- Payment Status --}}
      <div class="card section-card">
        <div class="card-header"><div class="section-icon"><i class="bx bx-rupee"></i></div> Payment</div>
        <div class="card-body">
          @php
            $grand   = $invoice->grand_total ?? 0;
            $paid    = $invoice->paid_amount ?? 0;
            $balance = max(0, $grand - $paid);
            $pct     = $grand > 0 ? min(100, round(($paid / $grand) * 100)) : 0;
          @endphp
          <div class="mb-3">
            <div class="d-flex justify-content-between mb-1">
              <span class="info-label mb-0">Payment Progress</span>
              <span class="fw-600 small">{{ $pct }}%</span>
            </div>
            <div class="payment-progress">
              <div class="payment-fill" style="width:{{ $pct }}%"></div>
            </div>
          </div>
          <div class="billing-row"><span class="text-muted">Grand Total</span><span class="fw-700">Rs. {{ number_format($grand, 2) }}</span></div>
          <div class="billing-row"><span class="text-muted">Paid</span><span class="text-success fw-700">Rs. {{ number_format($paid, 2) }}</span></div>
          <div class="billing-row" style="border-bottom:none;"><span class="text-muted">Balance</span><span class="{{ $balance > 0 ? 'text-danger' : 'text-success' }} fw-700">Rs. {{ number_format($balance, 2) }}</span></div>
          <hr>
          {{-- Update payment --}}
          <form method="POST" action="{{ route('admin.cctv.invoices.updatePayment', $invoice) }}">
            @csrf @method('PATCH')
            <label class="form-label fw-600 small">Update Paid Amount</label>
            <div class="input-group input-group-sm">
              <span class="input-group-text">Rs.</span>
              <input type="number" name="paid_amount" class="form-control" value="{{ $paid }}" min="0" step="0.01">
              <button class="btn btn-success"><i class="bx bx-check"></i></button>
            </div>
          </form>
        </div>
      </div>

      {{-- Quick Info --}}
      <div class="card section-card">
        <div class="card-header"><div class="section-icon"><i class="bx bx-info-circle"></i></div> Details</div>
        <div class="card-body">
          <div class="mb-3"><div class="info-label">Invoice No</div><div class="fw-700 font-monospace text-primary">{{ $invoice->invoice_no }}</div></div>
          <div class="mb-3"><div class="info-label">Status</div><span class="badge bg-label-{{ $sc }}">{{ $invoice->status }}</span></div>
          <div class="mb-3"><div class="info-label">Invoice Date</div><div class="info-value">{{ $invoice->invoice_date ? \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') : '—' }}</div></div>
          <div class="mb-3"><div class="info-label">Due Date</div><div class="info-value">{{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') : '—' }}</div></div>
          @if($pProject ?? null)
          <div class="mb-3"><div class="info-label">Project</div><a href="{{ route('admin.cctv.projects.show', $pProject) }}" class="text-primary fw-600 font-monospace">{{ $pProject->project_no }}</a></div>
          @endif
          <div class="mb-3"><div class="info-label">Created</div><div class="info-value">{{ $invoice->created_at->format('d M Y, h:i A') }}</div></div>
          <hr>
          <div class="d-grid gap-2">
            <a href="{{ route('admin.cctv.invoices.pdf', $invoice) }}" target="_blank" class="btn btn-danger btn-sm"><i class="bx bx-file-pdf me-1"></i> Download PDF</a>
            <form method="POST" action="{{ route('admin.cctv.invoices.destroy', $invoice) }}" onsubmit="return confirm('Delete this invoice?')">
              @csrf @method('DELETE')
              <button class="btn btn-outline-danger btn-sm w-100"><i class="bx bx-trash me-1"></i> Delete</button>
            </form>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
@endsection
