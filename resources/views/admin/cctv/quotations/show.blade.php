@extends('layouts.admin')
@section('title', 'Quotation – ' . $quotation->quotation_no)

@push('styles')
<style>
  .hero-bar { background:linear-gradient(135deg,#8c57ff,#696cff); border-radius:16px; padding:1.25rem 1.75rem; color:#fff; margin-bottom:1.5rem; display:flex; align-items:center; gap:1rem; flex-wrap:wrap; }
  .hero-bar .back-btn { width:38px; height:38px; border-radius:10px; background:rgba(255,255,255,.2); border:0; color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.2rem; text-decoration:none; transition:background .15s; }
  .hero-bar .back-btn:hover { background:rgba(255,255,255,.32); color:#fff; }
  .hero-bar h4 { margin:0; font-size:1.2rem; font-weight:700; }
  .section-card { border-radius:14px; border:0; box-shadow:0 2px 12px rgba(105,108,255,.08); margin-bottom:1.25rem; }
  .section-card .card-header { background:#f8f9fa; border-radius:14px 14px 0 0; padding:.85rem 1.25rem; font-weight:700; font-size:.875rem; display:flex; align-items:center; gap:.5rem; border-bottom:1px solid rgba(0,0,0,.06); }
  .section-icon { width:28px; height:28px; border-radius:8px; background:#f3eeff; color:#8c57ff; display:flex; align-items:center; justify-content:center; font-size:.9rem; }
  .info-label { font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#a1acb8; margin-bottom:.25rem; }
  .info-value { font-size:.92rem; font-weight:500; color:#32325d; }
  .billing-row { display:flex; justify-content:space-between; align-items:center; font-size:.85rem; padding:.3rem 0; }
  .billing-row .label { color:#697a8d; }
  .billing-row.total { font-size:1rem; font-weight:700; border-top:2px solid #e0e0e0; margin-top:.25rem; padding-top:.5rem; }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="hero-bar">
    <a href="{{ route('admin.cctv.quotations.index') }}" class="back-btn"><i class="bx bx-arrow-back"></i></a>
    <div class="flex-grow-1">
      <h4>{{ $quotation->quotation_no }}</h4>
      <div style="opacity:.85;font-size:.85rem;">
        @php $sc = ['draft'=>'secondary','sent'=>'info','approved'=>'success','rejected'=>'danger','expired'=>'warning'][$quotation->status] ?? 'secondary' @endphp
        <span class="badge bg-label-{{ $sc }}">{{ ucfirst($quotation->status) }}</span>
        <span class="ms-2">{{ $quotation->customer_name }}</span>
      </div>
    </div>
    <div class="d-flex gap-2 flex-wrap">
      <a href="{{ route('admin.cctv.quotations.pdf', $quotation) }}" target="_blank" class="btn btn-light btn-sm"><i class="bx bx-file-pdf me-1"></i> PDF</a>
      <a href="{{ route('admin.cctv.quotations.edit', $quotation) }}" class="btn btn-light btn-sm"><i class="bx bx-edit me-1"></i> Edit</a>
    </div>
  </div>

  {{-- Pipeline Banner --}}
  @include('admin.cctv._pipeline_banner', [
    'lead'      => $lead      ?? null,
    'survey'    => $survey    ?? null,
    'quotation' => $quotation,
    'project'   => $project   ?? null,
    'invoice'   => $invoice   ?? null,
    'currentStep' => 'quotation',
  ])

  @if(session('success'))
  <div class="alert alert-success alert-dismissible fade show mb-3">
    <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif

  <div class="row g-3">
    <div class="col-lg-8">
      <div class="card section-card">
        <div class="card-header"><div class="section-icon"><i class="bx bx-user"></i></div> Customer</div>
        <div class="card-body">
          {{-- Pipeline Banner --}}
  @include('admin.cctv._pipeline_banner', [
    'lead'      => $lead      ?? null,
    'survey'    => $survey    ?? null,
    'quotation' => $quotation,
    'project'   => $project   ?? null,
    'invoice'   => $invoice   ?? null,
    'currentStep' => 'quotation',
  ])

  @if(session('success'))
  <div class="alert alert-success alert-dismissible fade show mb-3">
    <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif

  <div class="row g-3">
            <div class="col-sm-6"><div class="info-label">Name</div><div class="info-value">{{ $quotation->customer_name }}</div></div>
            <div class="col-sm-6"><div class="info-label">Mobile</div><div class="info-value font-monospace">{{ $quotation->mobile }}</div></div>
            <div class="col-sm-6"><div class="info-label">Email</div><div class="info-value">{{ $quotation->email ?? '—' }}</div></div>
            <div class="col-12"><div class="info-label">Address</div><div class="info-value">{{ $quotation->address ?? '—' }}</div></div>
          </div>
        </div>
      </div>

      <div class="card section-card">
        <div class="card-header"><div class="section-icon"><i class="bx bx-list-ul"></i></div> Items</div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table align-middle mb-0">
              <thead class="table-light">
                <tr><th>Description</th><th class="text-center">Qty</th><th class="text-end">Unit Price</th><th class="text-end">Total</th></tr>
              </thead>
              <tbody>
                @php $items = is_array($quotation->items) ? $quotation->items : (json_decode($quotation->items, true) ?? []) @endphp
                @foreach($items as $item)
                <tr>
                  <td>{{ $item['description'] ?? '—' }}</td>
                  <td class="text-center">{{ $item['qty'] ?? 1 }}</td>
                  <td class="text-end">{{ number_format($item['unit_price'] ?? 0, 2) }}</td>
                  <td class="text-end fw-600">{{ number_format(($item['qty']??1)*($item['unit_price']??0), 2) }}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <div class="p-3">
            <div class="billing-row"><span class="label">Sub Total</span><span>Rs. {{ number_format($quotation->sub_total ?? 0, 2) }}</span></div>
            @if($quotation->discount_amount > 0)
            <div class="billing-row"><span class="label">Discount</span><span class="text-danger">- Rs. {{ number_format($quotation->discount_amount, 2) }}</span></div>
            @endif
            @if($quotation->installation_charge > 0)
            <div class="billing-row"><span class="label">Installation</span><span>Rs. {{ number_format($quotation->installation_charge, 2) }}</span></div>
            @endif
            <div class="billing-row total"><span class="label">Grand Total</span><span class="text-primary">Rs. {{ number_format($quotation->total_amount ?? 0, 2) }}</span></div>
          </div>
        </div>
      </div>

      @if($quotation->terms)
      <div class="card section-card">
        <div class="card-header"><div class="section-icon"><i class="bx bx-note"></i></div> Terms & Conditions</div>
        <div class="card-body"><p class="mb-0 small" style="white-space:pre-line">{{ $quotation->terms }}</p></div>
      </div>
      @endif
    </div>

    <div class="col-lg-4">
      <div class="card section-card">
        <div class="card-header"><div class="section-icon"><i class="bx bx-info-circle"></i></div> Details</div>
        <div class="card-body">
          <div class="mb-3"><div class="info-label">Quotation No</div><div class="fw-700 font-monospace text-primary">{{ $quotation->quotation_no }}</div></div>
          <div class="mb-3"><div class="info-label">Status</div><span class="badge bg-label-{{ $sc }}">{{ ucfirst($quotation->status) }}</span></div>
          <div class="mb-3"><div class="info-label">Valid Until</div><div class="info-value">{{ $quotation->valid_until ? \Carbon\Carbon::parse($quotation->valid_until)->format('d M Y') : '—' }}</div></div>
          <div class="mb-3"><div class="info-label">Grand Total</div><div class="fw-700 fs-5 text-primary">Rs. {{ number_format($quotation->total_amount ?? 0, 2) }}</div></div>
          <div class="mb-3"><div class="info-label">Created</div><div class="info-value">{{ $quotation->created_at->format('d M Y') }}</div></div>
          <hr>
          <div class="d-grid gap-2">
            <a href="{{ route('admin.cctv.quotations.pdf', $quotation) }}" target="_blank" class="btn btn-danger btn-sm"><i class="bx bx-file-pdf me-1"></i> Download PDF</a>
            <a href="{{ route('admin.cctv.quotations.edit', $quotation) }}" class="btn btn-primary btn-sm"><i class="bx bx-edit me-1"></i> Edit</a>
            @if(!($project ?? null))
              <a href="{{ route('admin.cctv.projects.create', array_filter(['quotation_id'=>$quotation->id,'lead_id'=>$quotation->lead_id])) }}" class="btn btn-success btn-sm"><i class="bx bx-wrench me-1"></i> Create Project</a>
            @else
              <a href="{{ route('admin.cctv.projects.show', $project) }}" class="btn btn-outline-success btn-sm"><i class="bx bx-wrench me-1"></i> View Project</a>
            @endif
          </div>
          <hr>
          {{-- Quick status update --}}
          <form method="POST" action="{{ route('admin.cctv.quotations.update', $quotation) }}" class="mt-2">
            @csrf @method('PUT')
            <input type="hidden" name="customer_name" value="{{ $quotation->customer_name }}">
            <input type="hidden" name="mobile"        value="{{ $quotation->mobile }}">
            <input type="hidden" name="items"         value="{{ json_encode(is_array($quotation->items) ? $quotation->items : json_decode($quotation->items ?? '[]', true)) }}">
            <div class="info-label mb-1">Update Status</div>
            <select name="status" class="form-select form-select-sm mb-2">
              @foreach(['draft','sent','approved','rejected','expired','Postponed','Rescheduled'] as $st)
                <option value="{{ $st }}" @selected($quotation->status === $st)>{{ ucfirst($st) }}</option>
              @endforeach
            </select>
            <button class="btn btn-sm btn-outline-warning w-100"><i class="bx bx-check me-1"></i> Save Status</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
