@extends('layouts.admin')
@section('title', 'Invoice – ' . $jobCard->invoice_no)
@section('page-title', 'Invoice')
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.invoices.index') }}">Invoices</a></li>
  <li class="breadcrumb-item active">{{ $jobCard->invoice_no ?? $jobCard->order_no }}</li>
@endsection

@push('styles')
<style>
/* ── Screen layout ── */
.inv-wrap { max-width: 900px; margin: 0 auto; }
.inv-toolbar { display:flex; gap:10px; align-items:center; margin-bottom:20px; flex-wrap:wrap; }

/* ── Screen invoice paper ── */
.inv-paper {
  background:#fff; border-radius:18px;
  box-shadow:0 4px 24px rgba(0,0,0,.1); overflow:hidden;
}
.inv-header {
  background:linear-gradient(135deg,#696cff 0%,#8c57ff 55%,#a855f7 100%);
  color:#fff; padding:32px 36px;
  display:flex; justify-content:space-between; align-items:flex-start; gap:24px; flex-wrap:wrap;
}
.inv-store-name { font-size:1.5rem; font-weight:800; letter-spacing:-.5px; }
.inv-store-meta  { font-size:.82rem; opacity:.8; margin-top:4px; line-height:1.6; }
.inv-no-block { text-align:right; }
.inv-no-label { font-size:.75rem; text-transform:uppercase; letter-spacing:.1em; opacity:.7; }
.inv-no       { font-size:1.6rem; font-weight:800; letter-spacing:1px; }
.inv-date     { font-size:.82rem; opacity:.75; margin-top:4px; }
.inv-body { padding:28px 36px; }
.inv-section-title {
  font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.12em;
  color:#696cff; margin-bottom:10px; padding-bottom:8px;
  border-bottom:2px solid #ebebff; display:flex; align-items:center; gap:7px;
}
.inv-grid { display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:24px; }
.inv-info-row { display:flex; gap:8px; padding:5px 0; border-bottom:1px solid #f5f5ff; }
.inv-info-row:last-child { border-bottom:none; }
.inv-label { font-size:.75rem; font-weight:700; color:#aaa; text-transform:uppercase; letter-spacing:.04em; width:105px; flex-shrink:0; }
.inv-val   { font-size:.85rem; color:#333; font-weight:500; flex:1; }
.inv-table { width:100%; border-collapse:collapse; margin-bottom:24px; font-size:.85rem; }
.inv-table thead tr { background:#f5f5ff; }
.inv-table thead th { padding:10px 12px; text-align:left; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:#696cff; border:none; }
.inv-table tbody td { padding:10px 12px; border-bottom:1px solid #f5f5ff; vertical-align:middle; }
.inv-table tbody tr:last-child td { border-bottom:none; }
.inv-table .num { text-align:right; }
.inv-totals { margin-left:auto; width:300px; }
.inv-total-row { display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid #f5f5ff; font-size:.86rem; }
.inv-total-row.grand { border-top:2px solid #696cff; border-bottom:none; padding-top:12px; font-size:1rem; font-weight:800; color:#696cff; }
.inv-total-row.balance { color:#ff3e1d; font-weight:700; }
.inv-total-row .t-label { color:#888; }
.inv-footer {
  background:#f8f8fc; padding:18px 36px;
  display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;
  border-top:1px solid #f0f0ff; font-size:.78rem; color:#aaa;
}
.pay-status-badge { display:inline-flex; align-items:center; gap:6px; border-radius:20px; padding:6px 14px; font-size:.82rem; font-weight:700; }
.pay-status-badge.paid    { background:#d1fae5; color:#065f46; }
.pay-status-badge.unpaid  { background:#fee2e2; color:#991b1b; }
.pay-status-badge.partial { background:#fef3c7; color:#92400e; }

/* ── Edit Panel ── */
.edit-panel { background:#f8f8fc; border-radius:14px; padding:20px; margin-bottom:24px; border:1.5px solid #e8e8ff; }
.edit-panel-title { font-size:.78rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:#696cff; margin-bottom:14px; }
.item-delete-btn { background:none; border:none; color:#ff3e1d; cursor:pointer; padding:4px 8px; border-radius:6px; opacity:.6; }
.item-delete-btn:hover { opacity:1; background:#fee2e2; }
.add-item-btn { background:#ebebff; color:#696cff; border:1.5px dashed #696cff; border-radius:10px; padding:8px 18px; font-size:.82rem; font-weight:600; cursor:pointer; width:100%; margin-top:6px; }
.add-item-btn:hover { background:#696cff; color:#fff; }

/* ══════════════════════════════════════
   80mm THERMAL PRINT STYLES
   Paper width: 80mm — printable ~72mm
   ══════════════════════════════════════ */
@media print {
  /* Hide everything except the receipt */
  body * { visibility:hidden !important; }
  .receipt-print, .receipt-print * { visibility:visible !important; }
  .receipt-print {
    position:fixed; top:0; left:0;
    width:80mm;
    z-index:9999;
  }
  /* Hide screen invoice, toolbars, edit panel */
  .inv-toolbar, .edit-panel, .no-print,
  .inv-printable { display:none !important; visibility:hidden !important; }

  /* Receipt paper */
  .receipt-paper {
    width:72mm;
    margin:0 auto;
    font-family:'Courier New', Courier, monospace;
    font-size:10pt;
    color:#000;
    background:#fff;
    padding:4mm 4mm 8mm 2mm;
  }
  .rp-center { text-align:center; }
  .rp-store-name { font-size:13pt; font-weight:bold; text-transform:uppercase; letter-spacing:1px; }
  .rp-divider { border:none; border-top:1px dashed #000; margin:3mm 0; }
  .rp-divider-solid { border:none; border-top:2px solid #000; margin:3mm 0; }
  .rp-row { display:flex; justify-content:space-between; font-size:9.5pt; margin:1mm 0; }
  .rp-label { color:#000; }
  .rp-table { width:100%; font-size:9pt; border-collapse:collapse; margin:2mm 0; }
  .rp-table th { text-align:left; font-size:8.5pt; border-bottom:1px solid #000; padding:1mm 0; }
  .rp-table th.r, .rp-table td.r { text-align:right; }
  .rp-table td { padding:1mm 0; vertical-align:top; }
  .rp-total-row { display:flex; justify-content:space-between; font-size:10pt; padding:1mm 0; border-top:1px dashed #000; }
  .rp-grand { font-weight:bold; font-size:12pt; border-top:2px solid #000; padding-top:2mm; margin-top:1mm; }
  .rp-footer { text-align:center; font-size:8.5pt; margin-top:4mm; }
  .rp-thank { font-size:11pt; font-weight:bold; text-align:center; margin:3mm 0 1mm; }
  .rp-pay-status { text-align:center; font-weight:bold; font-size:10pt; border:1px solid #000; padding:1mm 3mm; display:inline-block; margin:2mm auto; }
}

/* Receipt preview on screen (hidden) */
.receipt-print { display:none; }
@media print { .receipt-print { display:block !important; } }
</style>
@endpush

@section('content')
@php
  $grand        = $jobCard->grand_total;
  $paid         = (float)$jobCard->paid_amount;
  $balance      = $jobCard->balance;
  $subtotal     = $jobCard->subtotal;
  $itemsSum     = (float)$jobCard->invoiceItems->sum('total');
  $payStatus    = $paid >= $grand && $grand > 0 ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid');
  $payLabels    = ['paid'=>'✓ Fully Paid','partial'=>'⚡ Partially Paid','unpaid'=>'● Payment Pending'];
  $statusColors = ['Pending'=>'warning','In Progress'=>'info','Completed'=>'success','Not Completed'=>'danger','Cancelled'=>'secondary'];
  $paymentLogs  = $jobCard->paymentLogs ?? collect();
@endphp

<div class="inv-wrap">

  {{-- Toolbar --}}
  <div class="inv-toolbar no-print">
    <a href="{{ route('admin.invoices.index') }}" class="btn btn-outline-secondary" style="border-radius:10px">
      <i class='bx bx-arrow-back me-1'></i> Back
    </a>
    <a href="{{ route('admin.jobcards.show', $jobCard) }}" class="btn btn-outline-primary" style="border-radius:10px">
      <i class='bx bx-file me-1'></i> View Job Card
    </a>
    <button onclick="window.print()" class="btn btn-outline-secondary" style="border-radius:10px">
      <i class='bx bx-printer me-1'></i> Print Receipt
    </button>
    @if($payStatus !== 'paid')
    <button type="button" class="btn btn-success" style="border-radius:10px" id="invoicePayBtn"
      data-url="{{ route('admin.jobcards.payment', $jobCard) }}"
      data-post="{{ route('admin.jobcards.completePayment', $jobCard) }}">
      <i class='bx bx-dollar-circle me-1'></i> Payment
    </button>
    @endif
    <button onclick="toggleEdit()" class="btn btn-primary ms-auto" id="editToggleBtn" style="border-radius:10px">
      <i class='bx bx-edit me-1'></i> Edit Invoice
    </button>
  </div>

  @if(session('success'))
  <div class="alert alert-success d-flex align-items-center gap-2 no-print" style="border-radius:12px;margin-bottom:16px">
    <i class='bx bx-check-circle'></i> {{ session('success') }}
  </div>
  @endif

  {{-- ─────────────── EDIT PANEL ─────────────── --}}
  <div class="edit-panel no-print" id="editPanel" style="display:none">
    <div class="edit-panel-title"><i class='bx bx-edit me-1'></i> Edit Invoice Details</div>
    <form method="POST" action="{{ route('admin.invoices.update', $jobCard) }}" id="invoiceForm">
      @csrf @method('PUT')

      <div class="row g-3 mb-3">
        <div class="col-md-6">
          <label class="form-label fw-semibold" style="font-size:.8rem">Service Charge (Rs.) <span style="color:#aaa;font-weight:400">— base repair fee</span></label>
          <input type="number" name="rupees" step="0.01" min="0" value="{{ $jobCard->rupees }}"
                 class="form-control" style="border-radius:8px" id="rupeesInput" oninput="recalc()">
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold" style="font-size:.8rem">Discount (Rs.)</label>
          <input type="number" name="discount" step="0.01" min="0" value="{{ $jobCard->discount }}"
                 class="form-control" style="border-radius:8px" id="discountInput" oninput="recalc()">
        </div>
      </div>

      {{-- Live totals preview --}}
      <div class="row g-2 mb-3">
        <div class="col-12">
          <div style="background:#ebebff;border-radius:10px;padding:12px 16px;display:flex;gap:24px;flex-wrap:wrap;font-size:.82rem">
            <div><span style="color:#888">Subtotal:</span> <strong id="previewSubtotal">Rs. {{ number_format($subtotal,2) }}</strong></div>
            <div><span style="color:#888">Grand Total:</span> <strong id="previewGrand" style="color:#696cff">Rs. {{ number_format($grand,2) }}</strong></div>
            <div><span style="color:#888">Balance:</span> <strong id="previewBalance" style="color:#ff3e1d">Rs. {{ number_format($balance,2) }}</strong></div>
          </div>
        </div>
      </div>

      {{-- Payment History (locked / read-only) --}}
      @if($paymentLogs->count() > 0 || $paid > 0)
      <div style="margin-bottom:18px">
        <div class="edit-panel-title mb-2"><i class='bx bx-lock me-1'></i> Payment History <span style="color:#aaa;font-weight:400;font-size:.72rem;text-transform:none;letter-spacing:0">(locked — cannot be edited)</span></div>

        {{-- If no logs yet but paid_amount exists (legacy data before logging) --}}
        @if($paymentLogs->count() === 0 && $paid > 0)
        <div class="row g-2 align-items-center mb-2">
          <div class="col-md-5">
            <div class="form-control form-control-sm" style="border-radius:7px;background:#f4f4f8;color:#888;border-color:#e0e0f0;cursor:not-allowed">
              Payment (Legacy)
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-control form-control-sm" style="border-radius:7px;background:#f4f4f8;color:#888;border-color:#e0e0f0;cursor:not-allowed;text-align:right">
              Rs. {{ number_format($paid, 2) }}
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-control form-control-sm" style="border-radius:7px;background:#f4f4f8;color:#aaa;border-color:#e0e0f0;cursor:not-allowed">
              —
            </div>
          </div>
          <div class="col-md-1 text-center">
            <i class='bx bx-lock' style="color:#ccc;font-size:1rem"></i>
          </div>
        </div>
        @else
        @foreach($paymentLogs as $log)
        <div class="row g-2 align-items-center mb-2">
          <div class="col-md-5">
            <div class="form-control form-control-sm" style="border-radius:7px;background:#f4f4f8;color:#555;border-color:#e0e0f0;cursor:not-allowed">
              {{ $log->note ?? 'Payment' }}
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-control form-control-sm" style="border-radius:7px;background:#f4f4f8;color:#333;font-weight:700;border-color:#e0e0f0;cursor:not-allowed;text-align:right">
              Rs. {{ number_format($log->amount, 2) }}
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-control form-control-sm" style="border-radius:7px;background:#f4f4f8;color:#aaa;border-color:#e0e0f0;cursor:not-allowed;font-size:.78rem">
              {{ $log->paid_at->format('d M Y, h:i A') }}
            </div>
          </div>
          <div class="col-md-1 text-center">
            <i class='bx bx-lock' style="color:#ccc;font-size:1rem"></i>
          </div>
        </div>
        @endforeach
        @endif

        {{-- + Add New Payment row (only if balance > 0) --}}
        @if($balance > 0)
        <div id="newPaymentRow" style="border:1.5px dashed #696cff;border-radius:10px;padding:12px 14px;margin-top:8px;background:#fafaff">
          <div style="font-size:.76rem;font-weight:700;color:#696cff;text-transform:uppercase;letter-spacing:.07em;margin-bottom:10px">
            <i class='bx bx-plus-circle me-1'></i> New Payment Entry
          </div>
          <div class="row g-2 align-items-end">
            <div class="col-md-6">
              <label style="font-size:.73rem;color:#888;font-weight:600">Note <span style="font-weight:400">(optional)</span></label>
              <input type="text" id="newPayNote" placeholder="e.g. Advance, Final payment…"
                     class="form-control form-control-sm" style="border-radius:7px">
            </div>
            <div class="col-md-3">
              <label style="font-size:.73rem;color:#888;font-weight:600">Amount (Rs.) <span style="color:#ff3e1d">*</span></label>
              <input type="number" id="newPayAmount" step="0.01" min="0.01"
                     max="{{ $balance }}" value="{{ number_format($balance, 2, '.', '') }}"
                     placeholder="0.00" class="form-control form-control-sm" style="border-radius:7px">
              <div style="font-size:.71rem;color:#aaa;margin-top:3px">Balance: Rs. {{ number_format($balance, 2) }}</div>
            </div>
            <div class="col-md-3">
              <button type="button" id="saveNewPayBtn" onclick="saveNewPayment()"
                      class="btn btn-success btn-sm w-100" style="border-radius:7px;font-weight:600">
                <i class='bx bx-check me-1'></i> Record Payment
              </button>
            </div>
          </div>
        </div>
        @else
        <div style="background:#d1fae5;border-radius:10px;padding:10px 14px;font-size:.82rem;color:#065f46;font-weight:600;margin-top:6px">
          <i class='bx bx-check-circle me-1'></i> Fully paid — no balance remaining.
        </div>
        @endif
      </div>
      @else
      {{-- No payments yet — show the + new payment row --}}
      <div style="margin-bottom:18px">
        <div class="edit-panel-title mb-2"><i class='bx bx-dollar-circle me-1'></i> Payments</div>
        <div id="newPaymentRow" style="border:1.5px dashed #696cff;border-radius:10px;padding:12px 14px;background:#fafaff">
          <div style="font-size:.76rem;font-weight:700;color:#696cff;text-transform:uppercase;letter-spacing:.07em;margin-bottom:10px">
            <i class='bx bx-plus-circle me-1'></i> Record First Payment
          </div>
          <div class="row g-2 align-items-end">
            <div class="col-md-6">
              <label style="font-size:.73rem;color:#888;font-weight:600">Note <span style="font-weight:400">(optional)</span></label>
              <input type="text" id="newPayNote" placeholder="e.g. Advance, Full payment…"
                     class="form-control form-control-sm" style="border-radius:7px">
            </div>
            <div class="col-md-3">
              <label style="font-size:.73rem;color:#888;font-weight:600">Amount (Rs.) <span style="color:#ff3e1d">*</span></label>
              <input type="number" id="newPayAmount" step="0.01" min="0.01"
                     max="{{ $grand }}" value="{{ number_format($grand, 2, '.', '') }}"
                     placeholder="0.00" class="form-control form-control-sm" style="border-radius:7px">
              <div style="font-size:.71rem;color:#aaa;margin-top:3px">Total: Rs. {{ number_format($grand, 2) }}</div>
            </div>
            <div class="col-md-3">
              <button type="button" id="saveNewPayBtn" onclick="saveNewPayment()"
                      class="btn btn-success btn-sm w-100" style="border-radius:7px;font-weight:600">
                <i class='bx bx-check me-1'></i> Record Payment
              </button>
            </div>
          </div>
        </div>
      </div>
      @endif

      {{-- Line Items --}}
      <div class="d-flex align-items-center justify-content-between mb-2">
        <div class="edit-panel-title mb-0"><i class='bx bx-list-ul me-1'></i> Additional Line Items <span style="color:#aaa;font-weight:400;font-size:.72rem">(spare parts, labour, etc.)</span></div>
      </div>
      {{-- Header row --}}
      <div class="row g-2 mb-1" style="font-size:.72rem;font-weight:700;color:#aaa;text-transform:uppercase;letter-spacing:.06em">
        <div class="col-md-6">Description</div>
        <div class="col-md-2">Qty</div>
        <div class="col-md-3">Unit Price (Rs.)</div>
        <div class="col-md-1"></div>
      </div>
      <div id="itemsContainer">
        @foreach($jobCard->invoiceItems as $i => $item)
        <div class="row g-2 align-items-center mb-2 item-row">
          <div class="col-md-6">
            <input type="text" name="items[{{ $i }}][description]" value="{{ $item->description }}"
                   placeholder="e.g. Replacement screen, Labour" class="form-control form-control-sm" style="border-radius:7px" required>
          </div>
          <div class="col-md-2">
            <input type="number" name="items[{{ $i }}][qty]" value="{{ $item->qty }}"
                   min="1" class="form-control form-control-sm" style="border-radius:7px" oninput="recalc()" required>
          </div>
          <div class="col-md-3">
            <input type="number" name="items[{{ $i }}][unit_price]" value="{{ $item->unit_price }}"
                   step="0.01" min="0" class="form-control form-control-sm" style="border-radius:7px" oninput="recalc()" required>
          </div>
          <div class="col-md-1 text-center">
            <button type="button" class="item-delete-btn" onclick="removeItem(this)" title="Remove"><i class='bx bx-trash'></i></button>
          </div>
        </div>
        @endforeach
      </div>
      <button type="button" class="add-item-btn" onclick="addItem()">
        <i class='bx bx-plus me-1'></i> Add Line Item
      </button>

      <div class="d-flex gap-2 mt-3 justify-content-end">
        <button type="button" onclick="toggleEdit()" class="btn btn-outline-secondary" style="border-radius:8px">Cancel</button>
        <button type="submit" class="btn btn-primary" style="border-radius:8px">
          <i class='bx bx-save me-1'></i> Save Invoice
        </button>
      </div>
    </form>
  </div>

  {{-- ═══════════════════ SCREEN INVOICE ═══════════════════ --}}
  <div class="inv-printable">
    <div class="inv-paper">
      <div class="inv-header">
        <div>
          @if($store && $store->logo)
            <img src="{{ asset('storage/' . $store->logo) }}" alt="Logo" style="height:44px;margin-bottom:8px;filter:brightness(0) invert(1)">
          @endif
          <div class="inv-store-name">{{ $store->store_name ?? config('app.name', 'TrackUp') }}</div>
          <div class="inv-store-meta">
            @if($store)
              {{ $store->store_address }}<br>
              {{ $store->phone_no1 }}{{ $store->phone_no2 ? ' · '.$store->phone_no2 : '' }}<br>
              @if($store->registration_no) Reg: {{ $store->registration_no }} @endif
            @endif
          </div>
        </div>
        <div class="inv-no-block">
          <div class="inv-no-label">Invoice</div>
          <div class="inv-no">{{ $jobCard->invoice_no }}</div>
          <div class="inv-date">Date: {{ $jobCard->invoice_date ? $jobCard->invoice_date->format('d M Y') : now()->format('d M Y') }}</div>
          <div style="margin-top:8px"><span class="pay-status-badge {{ $payStatus }}">{{ $payLabels[$payStatus] }}</span></div>
        </div>
      </div>

      <div class="inv-body">
        <div class="inv-grid">
          <div>
            <div class="inv-section-title"><i class='bx bx-user'></i> Bill To</div>
            <div class="inv-info-row"><div class="inv-label">Name</div><div class="inv-val">{{ $jobCard->customer_name }}</div></div>
            <div class="inv-info-row"><div class="inv-label">Phone</div><div class="inv-val">{{ $jobCard->phone_no }}</div></div>
            @if($jobCard->customer_nic)
            <div class="inv-info-row"><div class="inv-label">NIC</div><div class="inv-val">{{ $jobCard->customer_nic }}</div></div>
            @endif
            @if($jobCard->customer_address)
            <div class="inv-info-row"><div class="inv-label">Address</div><div class="inv-val">{{ $jobCard->customer_address }}</div></div>
            @endif
            @if($jobCard->customer_email)
            <div class="inv-info-row"><div class="inv-label">Email</div><div class="inv-val">{{ $jobCard->customer_email }}</div></div>
            @endif
          </div>
          <div>
            <div class="inv-section-title"><i class='bx bx-chip'></i> Device Info</div>
            <div class="inv-info-row"><div class="inv-label">Order No.</div><div class="inv-val">{{ $jobCard->order_no }}</div></div>
            <div class="inv-info-row"><div class="inv-label">Device</div><div class="inv-val">{{ $jobCard->device_name }}{{ $jobCard->device_brand ? ' – '.$jobCard->device_brand : '' }}</div></div>
            @if($jobCard->serial_no)
            <div class="inv-info-row"><div class="inv-label">Serial/IMEI</div><div class="inv-val">{{ $jobCard->serial_no }}</div></div>
            @endif
            @if($jobCard->device_fault)
            <div class="inv-info-row"><div class="inv-label">Fault</div><div class="inv-val">{{ $jobCard->device_fault }}</div></div>
            @endif
            <div class="inv-info-row"><div class="inv-label">Status</div>
              <div class="inv-val"><span class="badge bg-label-{{ $statusColors[$jobCard->status] ?? 'secondary' }}">{{ $jobCard->status }}</span></div>
            </div>
            @if($jobCard->estimated_delivery)
            <div class="inv-info-row"><div class="inv-label">Est. Delivery</div><div class="inv-val">{{ $jobCard->estimated_delivery->format('d M Y') }}</div></div>
            @endif
          </div>
        </div>

        <div class="inv-section-title" style="margin-top:8px"><i class='bx bx-list-ul'></i> Services & Parts</div>
        <table class="inv-table">
          <thead>
            <tr>
              <th>#</th><th>Description</th>
              <th class="num">Qty</th><th class="num">Unit Price</th><th class="num">Total</th>
            </tr>
          </thead>
          <tbody>
            {{-- Always show base service charge row --}}
            @if((float)$jobCard->rupees > 0)
            <tr>
              <td style="color:#aaa;font-size:.8rem">1</td>
              <td>{{ $jobCard->device_fault ?: 'Repair Service' }}{{ $jobCard->issue ? ' – '.$jobCard->issue : '' }}</td>
              <td class="num">1</td>
              <td class="num">Rs. {{ number_format($jobCard->rupees, 2) }}</td>
              <td class="num" style="font-weight:600">Rs. {{ number_format($jobCard->rupees, 2) }}</td>
            </tr>
            @endif
            {{-- Additional line items --}}
            @foreach($jobCard->invoiceItems as $idx => $item)
            <tr>
              <td style="color:#aaa;font-size:.8rem">{{ (float)$jobCard->rupees > 0 ? $idx+2 : $idx+1 }}</td>
              <td>{{ $item->description }}</td>
              <td class="num">{{ $item->qty }}</td>
              <td class="num">Rs. {{ number_format($item->unit_price, 2) }}</td>
              <td class="num" style="font-weight:600">Rs. {{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
            @if((float)$jobCard->rupees == 0 && $jobCard->invoiceItems->count() == 0)
            <tr>
              <td colspan="5" style="text-align:center;color:#aaa;font-style:italic;padding:20px">No items — edit invoice to add charges</td>
            </tr>
            @endif
          </tbody>
        </table>

        <div class="d-flex justify-content-end">
          <div class="inv-totals">
            @if($jobCard->invoiceItems->count() > 0)
            <div class="inv-total-row">
              <span class="t-label">Service Charge</span>
              <span>Rs. {{ number_format($jobCard->rupees, 2) }}</span>
            </div>
            <div class="inv-total-row">
              <span class="t-label">Parts & Labour</span>
              <span>Rs. {{ number_format($itemsSum, 2) }}</span>
            </div>
            @endif
            <div class="inv-total-row">
              <span class="t-label">Subtotal</span>
              <span>Rs. {{ number_format($subtotal, 2) }}</span>
            </div>
            @if($jobCard->discount > 0)
            <div class="inv-total-row" style="color:#71dd37">
              <span class="t-label">Discount</span>
              <span>− Rs. {{ number_format($jobCard->discount, 2) }}</span>
            </div>
            @endif
            <div class="inv-total-row grand">
              <span>Grand Total</span>
              <span>Rs. {{ number_format($grand, 2) }}</span>
            </div>
            {{-- Payment breakdown with dates --}}
            @if($paymentLogs->count() > 0)
              @foreach($paymentLogs as $log)
              <div class="inv-total-row" style="color:#059669;font-size:.82rem">
                <span class="t-label" style="color:#059669">
                  {{ $log->note ?? 'Payment' }}
                  <span style="font-size:.72rem;color:#aaa;font-weight:400;margin-left:4px">{{ $log->paid_at->format('d M Y') }}</span>
                </span>
                <span>Rs. {{ number_format($log->amount, 2) }}</span>
              </div>
              @endforeach
            @elseif($paid > 0)
            <div class="inv-total-row" style="color:#71dd37">
              <span class="t-label">Amount Paid</span>
              <span>Rs. {{ number_format($paid, 2) }}</span>
            </div>
            @endif
            @if($balance > 0)
            <div class="inv-total-row balance">
              <span>Balance Due</span>
              <span>Rs. {{ number_format($balance, 2) }}</span>
            </div>
            @endif
          </div>
        </div>

        @if($jobCard->remark)
        <div style="margin-top:20px;padding:14px;background:#f8f8fc;border-radius:10px;border-left:3px solid #696cff">
          <div style="font-size:.72rem;font-weight:700;color:#696cff;text-transform:uppercase;letter-spacing:.08em;margin-bottom:4px">Remarks</div>
          <div style="font-size:.85rem;color:#555">{{ $jobCard->remark }}</div>
        </div>
        @endif
      </div>

      <div class="inv-footer">
        <div><strong>{{ $store->store_name ?? 'TrackUp' }}</strong><br>Thank you for your business!</div>
        <div style="text-align:right">
          <div>Generated: {{ now()->format('d M Y, h:i A') }}</div>
          <div>{{ $jobCard->invoice_no }} · {{ $jobCard->order_no }}</div>
        </div>
      </div>
    </div>
  </div>{{-- .inv-printable --}}

</div>{{-- .inv-wrap --}}

{{-- ════════════════════════════════════════
     80mm THERMAL RECEIPT (print-only)
     ════════════════════════════════════════ --}}
<div class="receipt-print">
  <div class="receipt-paper">

    {{-- Store Header --}}
    <div class="rp-center">
      <div class="rp-store-name">{{ $store->store_name ?? 'TrackUp' }}</div>
      @if($store && $store->store_address)
      <div style="font-size:8.5pt;margin-top:1mm">{{ $store->store_address }}</div>
      @endif
      @if($store && $store->phone_no1)
      <div style="font-size:8.5pt">Tel: {{ $store->phone_no1 }}{{ $store->phone_no2 ? ' / '.$store->phone_no2 : '' }}</div>
      @endif

    </div>

    <hr class="rp-divider-solid">

    {{-- Invoice Meta --}}
    <div style="font-size:8.5pt;line-height:1.2">
      <div class="rp-row" style="margin:0.4mm 0"><span class="rp-label">Invoice:</span><span><strong>{{ $jobCard->invoice_no }}</strong></span></div>
      <div class="rp-row" style="margin:0.4mm 0"><span class="rp-label">Order:</span><span>{{ $jobCard->order_no }}</span></div>
      <div class="rp-row" style="margin:0.4mm 0"><span class="rp-label">Date:</span><span>{{ $jobCard->invoice_date ? $jobCard->invoice_date->format('d/m/Y') : now()->format('d/m/Y') }}</span></div>
    </div>

    <hr class="rp-divider">

    {{-- Customer --}}
    <div style="font-size:8.5pt;font-weight:bold;text-transform:uppercase;margin-bottom:1mm">Customer</div>
    <div style="font-size:9pt">{{ $jobCard->customer_name }} - {{ $jobCard->phone_no }}</div>
    @if($jobCard->customer_nic)
    <div style="font-size:8.5pt;color:#000">NIC: {{ $jobCard->customer_nic }}</div>
    @endif

    <hr class="rp-divider">

    {{-- Device --}}
    <div style="font-size:8.5pt;font-weight:bold;text-transform:uppercase;margin-bottom:1mm">Device</div>
    <div style="font-size:9pt">{{ $jobCard->device_name }}{{ $jobCard->device_brand ? '/'.$jobCard->device_brand : '' }}{{ $jobCard->device_fault ? '/'.$jobCard->device_fault : '' }}</div>
    @if($jobCard->serial_no)
    <div style="font-size:8.5pt">S/N: {{ $jobCard->serial_no }}</div>
    @endif

    <hr class="rp-divider">

    {{-- Items --}}
    <table class="rp-table">
      <thead>
        <tr>
          <th style="width:50%">Item Description</th>
          <th class="r" style="width:15%">Qty</th>
          <th class="r" style="width:35%">Amount</th>
        </tr>
      </thead>
      <tbody>
        {{-- Always show service charge as first row --}}
        <tr>
          <td>Service Charges</td>
          <td class="r">1</td>
          <td class="r">{{ number_format((float)$jobCard->rupees, 2) }}</td>
        </tr>
        @foreach($jobCard->invoiceItems as $item)
        <tr>
          <td>{{ $item->description }}</td>
          <td class="r">{{ $item->qty }}</td>
          <td class="r">{{ number_format($item->total, 2) }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>

    {{-- Totals --}}
    <div class="rp-total-row"><span>Subtotal</span><span>{{ number_format($subtotal, 2) }}</span></div>
    @if($jobCard->discount > 0)
    <div class="rp-total-row"><span>Discount</span><span>-{{ number_format($jobCard->discount, 2) }}</span></div>
    @endif
    <div class="rp-total-row rp-grand"><span>TOTAL (Rs.)</span><span>{{ number_format($grand, 2) }}</span></div>

    {{-- Payment breakdown with dates --}}
    @if($paymentLogs->count() > 0)
      @foreach($paymentLogs as $log)
      <div class="rp-total-row" style="font-size:8.5pt">
        <span>{{ $log->note ?? 'Payment' }} ({{ $log->paid_at->format('d/m/Y') }})</span>
        <span>{{ number_format($log->amount, 2) }}</span>
      </div>
      @endforeach
      @if($paid > 0)
      <div class="rp-total-row" style="border-top:1px solid #000;font-weight:bold;font-size:9.5pt">
        <span>Total Paid</span><span>{{ number_format($paid, 2) }}</span>
      </div>
      @endif
    @elseif($paid > 0)
    <div class="rp-total-row"><span>Amount Paid</span><span>{{ number_format($paid, 2) }}</span></div>
    @endif

    @if($balance > 0)
    <div class="rp-total-row" style="font-weight:bold"><span>Balance Due</span><span>{{ number_format($balance, 2) }}</span></div>
    @endif

    {{-- Payment Status --}}
    <div class="rp-center" style="margin:2mm 0">
      <span class="rp-pay-status">
        @if($payStatus === 'paid') *** PAID IN FULL ***
        @elseif($payStatus === 'partial') PARTIALLY PAID
        @else PAYMENT PENDING @endif
      </span>
    </div>

    @if($jobCard->remark)
    <hr class="rp-divider">
    <div style="font-size:8.5pt"><strong>Note:</strong> {{ $jobCard->remark }}</div>
    @endif

    <hr class="rp-divider">

    {{-- Footer --}}
    <div class="rp-thank">Thank You!</div>
    <div class="rp-footer">
      <div>Powered by Trackup product of AxisXNOR</div>
    </div>

    {{-- Blank feed space for tear --}}
    <div style="height:10mm"></div>
  </div>
</div>

{{-- ── Payment Modal ── --}}
<div class="modal fade" id="invPaymentModal" tabindex="-1" aria-labelledby="invPaymentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:18px;border:0;box-shadow:0 8px 40px rgba(0,0,0,.18);">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold" id="invPaymentModalLabel"><i class='bx bx-dollar-circle me-2'></i>Take Payment</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body pt-2" id="invPayModalBody">
        <div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
let itemCount = {{ $jobCard->invoiceItems->count() }};

function toggleEdit() {
  const panel = document.getElementById('editPanel');
  const btn   = document.getElementById('editToggleBtn');
  const showing = panel.style.display !== 'none';
  panel.style.display = showing ? 'none' : 'block';
  btn.innerHTML = showing
    ? "<i class='bx bx-edit me-1'></i> Edit Invoice"
    : "<i class='bx bx-x me-1'></i> Close Editor";
  if (!showing) panel.scrollIntoView({behavior:'smooth', block:'nearest'});
}

function addItem() {
  const c   = document.getElementById('itemsContainer');
  const idx = itemCount++;
  const row = document.createElement('div');
  row.className = 'row g-2 align-items-center mb-2 item-row';
  row.innerHTML = `
    <div class="col-md-6">
      <input type="text" name="items[${idx}][description]" placeholder="e.g. Replacement screen, Labour"
             class="form-control form-control-sm" style="border-radius:7px" required>
    </div>
    <div class="col-md-2">
      <input type="number" name="items[${idx}][qty]" placeholder="Qty" min="1" value="1"
             class="form-control form-control-sm" style="border-radius:7px" oninput="recalc()" required>
    </div>
    <div class="col-md-3">
      <input type="number" name="items[${idx}][unit_price]" placeholder="0.00" step="0.01" min="0"
             class="form-control form-control-sm" style="border-radius:7px" oninput="recalc()" required>
    </div>
    <div class="col-md-1 text-center">
      <button type="button" class="item-delete-btn" onclick="removeItem(this)" title="Remove">
        <i class='bx bx-trash'></i>
      </button>
    </div>`;
  c.appendChild(row);
  row.querySelector('input').focus();
  recalc();
}

function removeItem(btn) {
  btn.closest('.item-row').remove();
  recalc();
}

function fmt(v) {
  return 'Rs. ' + v.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

function recalc() {
  // sum all line item rows
  let itemsTotal = 0;
  document.querySelectorAll('.item-row').forEach(row => {
    const qty   = parseFloat(row.querySelector('[name*="[qty]"]')?.value)        || 0;
    const price = parseFloat(row.querySelector('[name*="[unit_price]"]')?.value) || 0;
    itemsTotal += qty * price;
  });

  const rupees   = parseFloat(document.getElementById('rupeesInput')?.value)   || 0;
  const discount = parseFloat(document.getElementById('discountInput')?.value) || 0;
  const paid     = parseFloat(document.getElementById('paidInput')?.value)     || 0;

  const subtotal = rupees + itemsTotal;
  const grand    = Math.max(0, subtotal - discount);
  const balance  = Math.max(0, grand - paid);

  const el = id => document.getElementById(id);
  if (el('previewSubtotal')) el('previewSubtotal').textContent = fmt(subtotal);
  if (el('previewGrand'))    el('previewGrand').textContent    = fmt(grand);
  if (el('previewBalance'))  el('previewBalance').textContent  = fmt(balance);
}

// ── Inline payment recording (from edit panel) ──────────────
async function saveNewPayment() {
  const amountEl = document.getElementById('newPayAmount');
  const noteEl   = document.getElementById('newPayNote');
  const btn      = document.getElementById('saveNewPayBtn');
  if (!amountEl) return;

  const amount = parseFloat(amountEl.value);
  if (!amount || amount <= 0) {
    amountEl.focus();
    amountEl.style.borderColor = '#ff3e1d';
    return;
  }
  amountEl.style.borderColor = '';

  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving…';

  try {
    const res = await fetch('{{ route("admin.jobcards.completePayment", $jobCard) }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
        'X-Requested-With': 'XMLHttpRequest',
      },
      body: JSON.stringify({ amount_paid: amount, note: noteEl?.value || '' })
    });
    const data = await res.json();
    if (data.ok) {
      triggerPrintThenRedirect();
    } else {
      alert(data.message || 'Payment failed.');
      btn.disabled = false;
      btn.innerHTML = '<i class="bx bx-check me-1"></i> Record Payment';
    }
  } catch(e) {
    alert('Network error. Please try again.');
    btn.disabled = false;
    btn.innerHTML = '<i class="bx bx-check me-1"></i> Record Payment';
  }
}

// Open editor on validation errors
@if($errors->any()) toggleEdit(); @endif

// Re-index items array before submit so PHP receives correct indices
document.getElementById('invoiceForm')?.addEventListener('submit', function() {
  document.querySelectorAll('.item-row').forEach((row, i) => {
    row.querySelectorAll('[name]').forEach(el => {
      el.name = el.name.replace(/items\[\d+\]/, `items[${i}]`);
    });
  });
});

// ── Invoice Payment Modal ──────────────────────────────────────
(function () {
  const payBtn = document.getElementById('invoicePayBtn');
  if (!payBtn) return;

  const payModal = new bootstrap.Modal(document.getElementById('invPaymentModal'));

  // Auto-open if redirected from notification bell (?pay=1)
  if (new URLSearchParams(window.location.search).get('pay') === '1') {
    setTimeout(() => payBtn.click(), 400);
  }

  payBtn.addEventListener('click', function () {
    const fetchUrl = this.dataset.url;
    const postUrl  = this.dataset.post;
    const body     = document.getElementById('invPayModalBody');

    body.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>';
    payModal.show();

    fetch(fetchUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(r => r.json())
      .then(d => {
        body.innerHTML = `
          <div class="mb-3">
            <div class="d-flex justify-content-between mb-1"><span class="text-muted small">Customer</span><span class="fw-semibold">${d.customer_name}</span></div>
            <div class="d-flex justify-content-between mb-1"><span class="text-muted small">Order No</span><span class="fw-semibold">${d.order_no}</span></div>
            <div class="d-flex justify-content-between mb-1"><span class="text-muted small">Device</span><span class="fw-semibold">${d.device_name}</span></div>
            <div class="d-flex justify-content-between mb-1"><span class="text-muted small">Total</span><span class="fw-bold text-primary">Rs.${parseFloat(d.grand_total).toLocaleString()}</span></div>
            <div class="d-flex justify-content-between mb-1"><span class="text-muted small">Paid So Far</span><span class="fw-semibold text-success">Rs.${parseFloat(d.paid_amount).toLocaleString()}</span></div>
            <div class="d-flex justify-content-between mb-2"><span class="text-muted small">Balance Due</span><span class="fw-bold text-danger" id="inv-pay-balance">${parseFloat(d.balance).toLocaleString()}</span></div>
          </div>
          <hr class="my-2">
          <form id="invPayForm">
            <label class="form-label fw-semibold small">Amount Receiving (Rs.)</label>
            <input type="number" id="inv-pay-amount" class="form-control mb-2" min="0.01" step="0.01"
                   value="${parseFloat(d.balance).toFixed(2)}" required>
            <div id="inv-pay-partial-note" class="alert alert-warning py-2 px-3 mt-1 small mb-0" style="border-radius:8px;display:none">
              Paying less than the balance records a <strong>partial payment</strong>.
            </div>
            <div class="d-flex gap-2 mt-3">
              <button type="button" class="btn btn-outline-secondary flex-fill" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" id="inv-pay-submit" class="btn btn-success flex-fill fw-semibold">
                <i class='bx bx-printer me-1'></i> Pay Now
              </button>
            </div>
          </form>`;

        const balanceVal = parseFloat(d.balance);

        document.getElementById('inv-pay-amount').addEventListener('input', function () {
          const entered = parseFloat(this.value) || 0;
          const note = document.getElementById('inv-pay-partial-note');
          note.style.display = (entered < balanceVal && entered > 0) ? '' : 'none';
        });

        document.getElementById('invPayForm').addEventListener('submit', function (e) {
          e.preventDefault();
          const amount  = parseFloat(document.getElementById('inv-pay-amount').value);
          const submitBtn = document.getElementById('inv-pay-submit');
          submitBtn.disabled = true;
          submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Processing…';

          fetch(postUrl, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
              'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ amount_paid: amount })
          })
          .then(r => r.json())
          .then(res => {
            payModal.hide();
            if (res.ok) {
              triggerPrintThenRedirect();
            } else {
              submitBtn.disabled = false;
              submitBtn.innerHTML = '<i class="bx bx-check-circle me-1"></i> Confirm Payment';
              alert(res.message || 'Payment failed.');
            }
          })
          .catch(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bx bx-check-circle me-1"></i> Confirm Payment';
            alert('Network error. Please try again.');
          });
        });
      })
      .catch(() => {
        body.innerHTML = '<div class="alert alert-danger">Failed to load payment details.</div>';
      });
  });


})();

function triggerPrintThenRedirect() {
  const invoicesUrl = '{{ route("admin.invoices.index") }}';
  // Use onafterprint to redirect after the print dialog closes
  window.onafterprint = function () {
    window.onafterprint = null;
    window.location.href = invoicesUrl;
  };
  window.print();
}
</script>
@endpush
