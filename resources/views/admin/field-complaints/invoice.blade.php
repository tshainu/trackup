@extends('layouts.admin')
@section('title', 'Invoice – ' . ($fieldComplaint->invoice_no ?? $fieldComplaint->complaint_no))
@section('page-title', 'Field Invoice')
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.field-complaints.index') }}">Field Complaints</a></li>
  <li class="breadcrumb-item"><a href="{{ route('admin.field-complaints.show', $fieldComplaint) }}">{{ $fieldComplaint->complaint_no }}</a></li>
  <li class="breadcrumb-item active">Invoice</li>
@endsection

@push('styles')
<style>
.inv-wrap { max-width:900px;margin:0 auto; }
.inv-toolbar { display:flex;gap:10px;align-items:center;margin-bottom:20px;flex-wrap:wrap; }
.inv-paper { background:#fff;border-radius:18px;box-shadow:0 4px 24px rgba(0,0,0,.1);overflow:hidden; }
.inv-header {
  background:linear-gradient(135deg,#f59e0b 0%,#d97706 55%,#b45309 100%);
  color:#fff;padding:32px 36px;
  display:flex;justify-content:space-between;align-items:flex-start;gap:24px;flex-wrap:wrap;
}
.inv-store-name { font-size:1.5rem;font-weight:800;letter-spacing:-.5px; }
.inv-store-meta  { font-size:.82rem;opacity:.8;margin-top:4px;line-height:1.6; }
.inv-no-block { text-align:right; }
.inv-no-label { font-size:.75rem;text-transform:uppercase;letter-spacing:.1em;opacity:.7; }
.inv-no       { font-size:1.6rem;font-weight:800;letter-spacing:1px; }
.inv-date     { font-size:.82rem;opacity:.75;margin-top:4px; }
.inv-body { padding:28px 36px; }
.inv-section-title { font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.12em;color:#d97706;margin-bottom:10px;padding-bottom:8px;border-bottom:2px solid #fef3c7;display:flex;align-items:center;gap:7px; }
.inv-grid { display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px; }
.inv-info-row { display:flex;gap:8px;padding:5px 0;border-bottom:1px solid #fffbeb; }
.inv-info-row:last-child { border-bottom:none; }
.inv-label { font-size:.75rem;font-weight:700;color:#aaa;text-transform:uppercase;letter-spacing:.04em;width:110px;flex-shrink:0; }
.inv-val   { font-size:.85rem;color:#333;font-weight:500;flex:1; }
.inv-table { width:100%;border-collapse:collapse;margin-bottom:24px;font-size:.85rem; }
.inv-table thead tr { background:#fffbeb; }
.inv-table thead th { padding:10px 12px;text-align:left;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#d97706;border:none; }
.inv-table tbody td { padding:10px 12px;border-bottom:1px solid #fffbeb;vertical-align:middle; }
.inv-table tbody tr:last-child td { border-bottom:none; }
.inv-table .num { text-align:right; }
.inv-totals { margin-left:auto;width:300px; }
.inv-total-row { display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #fef3c7;font-size:.86rem; }
.inv-total-row.grand { border-top:2px solid #f59e0b;border-bottom:none;padding-top:12px;font-size:1rem;font-weight:800;color:#d97706; }
.inv-total-row.balance { color:#ff3e1d;font-weight:700; }
.inv-total-row .t-label { color:#888; }
.inv-footer { background:#fffbeb;padding:18px 36px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;border-top:1px solid #fef3c7;font-size:.78rem;color:#aaa; }
.pay-status-badge { display:inline-flex;align-items:center;gap:6px;border-radius:20px;padding:6px 14px;font-size:.82rem;font-weight:700; }
.pay-status-badge.paid    { background:#d1fae5;color:#065f46; }
.pay-status-badge.unpaid  { background:#fee2e2;color:#991b1b; }
.pay-status-badge.partial { background:#fef3c7;color:#92400e; }
.field-tag { display:inline-flex;align-items:center;gap:4px;background:#fff3cd;color:#856404;padding:2px 10px;border-radius:10px;font-size:.72rem;font-weight:700;margin-bottom:4px; }

@media print {
  body * { visibility:hidden !important; }
  .inv-wrap, .inv-wrap * { visibility:visible !important; }
  .inv-wrap { position:fixed;top:0;left:0;width:100%;margin:0;padding:0; }
  .inv-toolbar { display:none !important; }
  .inv-paper { box-shadow:none;border-radius:0; }
}
</style>
@endpush

@section('content')
<div class="inv-wrap">

  <div class="inv-toolbar">
    <a href="{{ route('admin.field-complaints.show', $fieldComplaint) }}" class="btn btn-outline-secondary" style="border-radius:10px;">
      <i class='bx bx-arrow-back me-1'></i>Back
    </a>
    <button onclick="window.print()" class="btn btn-warning fw-bold" style="border-radius:10px;color:#fff;">
      <i class='bx bx-printer me-1'></i>Print / Save PDF
    </button>
    <span class="pay-status-badge {{ $fieldComplaint->payment_status }}">
      @if($fieldComplaint->payment_status === 'paid') ✓ Paid
      @elseif($fieldComplaint->payment_status === 'partial') ~ Partial
      @else ✕ Unpaid @endif
    </span>
  </div>

  <div class="inv-paper">
    {{-- Header --}}
    <div class="inv-header">
      <div>
        @if($storeInfo?->logo)
          <img src="{{ asset('storage/'.$storeInfo->logo) }}" style="height:48px;margin-bottom:8px;border-radius:8px;" alt="Logo" />
        @endif
        <div class="inv-store-name">{{ $storeInfo?->name ?? config('app.name') }}</div>
        <div class="inv-store-meta">
          {{ $storeInfo?->address }}<br>
          @if($storeInfo?->phone) Phone: {{ $storeInfo->phone }}<br>@endif
          @if($storeInfo?->email) {{ $storeInfo->email }}@endif
        </div>
      </div>
      <div class="inv-no-block">
        <div class="field-tag"><i class='bx bx-map-pin'></i> Field Service</div>
        <div class="inv-no-label">Invoice No.</div>
        <div class="inv-no">{{ $fieldComplaint->invoice_no ?? $fieldComplaint->complaint_no }}</div>
        <div class="inv-date">{{ $fieldComplaint->invoice_date?->format('d F Y') ?? now()->format('d F Y') }}</div>
        <div class="inv-date" style="margin-top:6px;opacity:.85;">Ref: {{ $fieldComplaint->complaint_no }}</div>
      </div>
    </div>

    <div class="inv-body">

      {{-- Customer & Service --}}
      <div class="inv-grid">
        <div>
          <div class="inv-section-title"><i class='bx bx-user'></i>Bill To</div>
          <div class="inv-info-row"><span class="inv-label">Name</span><span class="inv-val">{{ $fieldComplaint->customer_name }}</span></div>
          <div class="inv-info-row"><span class="inv-label">Phone</span><span class="inv-val">{{ $fieldComplaint->phone_no }}</span></div>
          <div class="inv-info-row"><span class="inv-label">Address</span><span class="inv-val">{{ $fieldComplaint->address }}</span></div>
          @if($fieldComplaint->location_notes)
          <div class="inv-info-row"><span class="inv-label">Notes</span><span class="inv-val">{{ $fieldComplaint->location_notes }}</span></div>
          @endif
        </div>
        <div>
          <div class="inv-section-title"><i class='bx bx-wrench'></i>Service Details</div>
          <div class="inv-info-row"><span class="inv-label">Service</span><span class="inv-val">{{ $fieldComplaint->service_type_name ?? '—' }}</span></div>
          @if($fieldComplaint->assignedEmployee)
          <div class="inv-info-row"><span class="inv-label">Technician</span><span class="inv-val">{{ $fieldComplaint->assignedEmployee->employee_name }}</span></div>
          @endif
          <div class="inv-info-row"><span class="inv-label">Scheduled</span><span class="inv-val">{{ $fieldComplaint->scheduled_date?->format('d M Y') ?? '—' }}</span></div>
          @if($fieldComplaint->completed_at)
          <div class="inv-info-row"><span class="inv-label">Completed</span><span class="inv-val">{{ $fieldComplaint->completed_at->format('d M Y') }}</span></div>
          @endif
          @if($fieldComplaint->description)
          <div class="inv-info-row"><span class="inv-label">Description</span><span class="inv-val">{{ $fieldComplaint->description }}</span></div>
          @endif
        </div>
      </div>

      {{-- Items Table --}}
      <div class="inv-section-title"><i class='bx bx-list-ul'></i>Services & Parts</div>
      <table class="inv-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Description</th>
            <th class="num">Qty</th>
            <th class="num">Unit Price</th>
            <th class="num">Total</th>
          </tr>
        </thead>
        <tbody>
          {{-- Service charge row --}}
          <tr>
            <td>1</td>
            <td>{{ $fieldComplaint->service_type_name ?? 'Service Charge' }}</td>
            <td class="num">1</td>
            <td class="num">Rs. {{ number_format($fieldComplaint->service_charge, 2) }}</td>
            <td class="num">Rs. {{ number_format($fieldComplaint->service_charge, 2) }}</td>
          </tr>
          {{-- Additional items --}}
          @foreach($fieldComplaint->items as $i => $item)
          <tr>
            <td>{{ $i + 2 }}</td>
            <td>{{ $item->description }}</td>
            <td class="num">{{ $item->qty }}</td>
            <td class="num">Rs. {{ number_format($item->unit_price, 2) }}</td>
            <td class="num">Rs. {{ number_format($item->total, 2) }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>

      {{-- Totals --}}
      <div class="inv-totals">
        <div class="inv-total-row">
          <span class="t-label">Subtotal</span>
          <span>Rs. {{ number_format($fieldComplaint->subtotal, 2) }}</span>
        </div>
        @if($fieldComplaint->discount > 0)
        <div class="inv-total-row">
          <span class="t-label">Discount</span>
          <span class="text-danger">− Rs. {{ number_format($fieldComplaint->discount, 2) }}</span>
        </div>
        @endif
        <div class="inv-total-row grand">
          <span>Total</span>
          <span>Rs. {{ number_format($fieldComplaint->grand_total, 2) }}</span>
        </div>
        <div class="inv-total-row">
          <span class="t-label">Paid</span>
          <span class="text-success">Rs. {{ number_format($fieldComplaint->paid_amount, 2) }}</span>
        </div>
        @if($fieldComplaint->balance > 0)
        <div class="inv-total-row balance">
          <span>Balance Due</span>
          <span>Rs. {{ number_format($fieldComplaint->balance, 2) }}</span>
        </div>
        @else
        <div class="inv-total-row" style="color:#065f46;font-weight:700;">
          <span>Balance Due</span>
          <span>Rs. 0.00 ✓</span>
        </div>
        @endif
      </div>

      {{-- Payment history --}}
      @if($fieldComplaint->paymentLogs->isNotEmpty())
      <div class="inv-section-title mt-4"><i class='bx bx-receipt'></i>Payment History</div>
      <table class="inv-table">
        <thead><tr>
          <th>Date</th><th>Note</th><th class="num">Amount</th>
        </tr></thead>
        <tbody>
          @foreach($fieldComplaint->paymentLogs as $log)
          <tr>
            <td>{{ \Carbon\Carbon::parse($log->paid_at)->format('d M Y') }}</td>
            <td>{{ $log->note }}</td>
            <td class="num">Rs. {{ number_format($log->amount, 2) }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
      @endif

    </div>

    {{-- Footer --}}
    <div class="inv-footer">
      <div>Thank you for choosing our field services!</div>
      <div>
        <span class="pay-status-badge {{ $fieldComplaint->payment_status }}">
          @if($fieldComplaint->payment_status === 'paid') ✓ Fully Paid
          @elseif($fieldComplaint->payment_status === 'partial') ~ Partially Paid
          @else ✕ Unpaid @endif
        </span>
      </div>
    </div>
  </div>
</div>
@endsection
