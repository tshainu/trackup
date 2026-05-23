@extends('layouts.admin')
@section('title', 'Invoice – ' . $jobCard->invoice_no)
@section('page-title', 'Invoice')
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.invoices.index') }}">Invoices</a></li>
  <li class="breadcrumb-item active">{{ $jobCard->invoice_no ?? $jobCard->order_no }}</li>
@endsection

@push('styles')
<style>
/* ── Layout ── */
.inv-wrap { max-width: 900px; margin: 0 auto; }
.inv-toolbar {
  display: flex; gap: 10px; align-items: center; margin-bottom: 20px; flex-wrap: wrap;
}
/* ── Invoice Card ── */
.inv-paper {
  background: #fff;
  border-radius: 18px;
  box-shadow: 0 4px 24px rgba(0,0,0,.1);
  overflow: hidden;
}
.inv-header {
  background: linear-gradient(135deg, #696cff 0%, #8c57ff 55%, #a855f7 100%);
  color: #fff;
  padding: 32px 36px;
  display: flex; justify-content: space-between; align-items: flex-start; gap: 24px; flex-wrap: wrap;
}
.inv-store-name { font-size: 1.5rem; font-weight: 800; letter-spacing: -.5px; }
.inv-store-meta  { font-size: .82rem; opacity: .8; margin-top: 4px; line-height: 1.6; }
.inv-no-block { text-align: right; }
.inv-no-label { font-size: .75rem; text-transform: uppercase; letter-spacing: .1em; opacity: .7; }
.inv-no       { font-size: 1.6rem; font-weight: 800; letter-spacing: 1px; }
.inv-date      { font-size: .82rem; opacity: .75; margin-top: 4px; }
/* ── Body ── */
.inv-body { padding: 28px 36px; }
.inv-section-title {
  font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .12em;
  color: #696cff; margin-bottom: 10px; padding-bottom: 8px;
  border-bottom: 2px solid #ebebff;
  display: flex; align-items: center; gap: 7px;
}
.inv-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px; }
.inv-info-row { display: flex; gap: 8px; padding: 5px 0; border-bottom: 1px solid #f5f5ff; }
.inv-info-row:last-child { border-bottom: none; }
.inv-label { font-size: .75rem; font-weight: 700; color: #aaa; text-transform: uppercase; letter-spacing: .04em; width: 105px; flex-shrink: 0; }
.inv-val   { font-size: .85rem; color: #333; font-weight: 500; flex: 1; }
/* ── Items Table ── */
.inv-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; font-size: .85rem; }
.inv-table thead tr { background: #f5f5ff; }
.inv-table thead th { padding: 10px 12px; text-align: left; font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; color: #696cff; border: none; }
.inv-table tbody td { padding: 10px 12px; border-bottom: 1px solid #f5f5ff; vertical-align: middle; }
.inv-table tbody tr:last-child td { border-bottom: none; }
.inv-table .num { text-align: right; }
/* Edit mode inputs */
.inv-table .edit-inp { border: 1.5px solid #e0e0ff; border-radius: 7px; padding: 5px 8px; font-size: .84rem; width: 100%; background: #fafaff; }
.inv-table .edit-inp:focus { outline: none; border-color: #696cff; }
.item-delete-btn { background: none; border: none; color: #ff3e1d; cursor: pointer; padding: 4px 8px; border-radius: 6px; opacity: .6; }
.item-delete-btn:hover { opacity: 1; background: #fee2e2; }
.add-item-btn { background: #ebebff; color: #696cff; border: 1.5px dashed #696cff; border-radius: 10px; padding: 8px 18px; font-size: .82rem; font-weight: 600; cursor: pointer; width: 100%; margin-top: 6px; }
.add-item-btn:hover { background: #696cff; color: #fff; }
/* ── Totals ── */
.inv-totals { margin-left: auto; width: 300px; }
.inv-total-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f5f5ff; font-size: .86rem; }
.inv-total-row.grand { border-top: 2px solid #696cff; border-bottom: none; padding-top: 12px; font-size: 1rem; font-weight: 800; color: #696cff; }
.inv-total-row.balance { color: #ff3e1d; font-weight: 700; }
.inv-total-row .t-label { color: #888; }
/* ── Footer ── */
.inv-footer {
  background: #f8f8fc;
  padding: 18px 36px;
  display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;
  border-top: 1px solid #f0f0ff;
  font-size: .78rem; color: #aaa;
}
.pay-status-badge {
  display: inline-flex; align-items: center; gap: 6px;
  border-radius: 20px; padding: 6px 14px; font-size: .82rem; font-weight: 700;
}
.pay-status-badge.paid    { background: #d1fae5; color: #065f46; }
.pay-status-badge.unpaid  { background: #fee2e2; color: #991b1b; }
.pay-status-badge.partial { background: #fef3c7; color: #92400e; }
/* ── Edit Panel ── */
.edit-panel { background: #f8f8fc; border-radius: 14px; padding: 20px; margin-bottom: 24px; border: 1.5px solid #e8e8ff; }
.edit-panel-title { font-size: .78rem; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: #696cff; margin-bottom: 14px; }
/* ── Print ── */
@media print {
  body * { visibility: hidden !important; }
  .inv-printable, .inv-printable * { visibility: visible !important; }
  .inv-printable { position: fixed; top: 0; left: 0; width: 100%; z-index: 9999; }
  .inv-paper { box-shadow: none !important; border-radius: 0 !important; }
  .inv-toolbar, .edit-panel, .no-print { display: none !important; }
}
</style>
@endpush

@section('content')
@php
  $grand   = $jobCard->grand_total;
  $paid    = (float)$jobCard->paid_amount;
  $balance = $jobCard->balance;
  $payStatus = $paid >= $grand && $grand > 0 ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid');
  $payLabels = ['paid'=>'✓ Fully Paid','partial'=>'⚡ Partially Paid','unpaid'=>'● Payment Pending'];
  $statusColors = ['Pending'=>'warning','In Progress'=>'info','Completed'=>'success','Not Completed'=>'danger'];
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
      <i class='bx bx-printer me-1'></i> Print
    </button>
    @if($payStatus !== 'paid')
    <form method="POST" action="{{ route('admin.invoices.markPaid', $jobCard) }}" style="display:inline">
      @csrf @method('PATCH')
      <button type="submit" class="btn btn-success" style="border-radius:10px"
              onclick="return confirm('Mark as fully paid?')">
        <i class='bx bx-check-circle me-1'></i> Mark Paid
      </button>
    </form>
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

  {{-- Edit Panel (hidden by default) --}}
  <div class="edit-panel no-print" id="editPanel" style="display:none">
    <div class="edit-panel-title"><i class='bx bx-edit me-1'></i> Edit Invoice Details</div>
    <form method="POST" action="{{ route('admin.invoices.update', $jobCard) }}" id="invoiceForm">
      @csrf @method('PUT')

      <div class="row g-3 mb-3">
        <div class="col-md-4">
          <label class="form-label fw-semibold" style="font-size:.8rem">Service Charge (Rs.)</label>
          <input type="number" name="rupees" step="0.01" min="0" value="{{ $jobCard->rupees }}"
                 class="form-control" style="border-radius:8px" id="rupeesInput">
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold" style="font-size:.8rem">Discount (Rs.)</label>
          <input type="number" name="discount" step="0.01" min="0" value="{{ $jobCard->discount }}"
                 class="form-control" style="border-radius:8px" id="discountInput" oninput="recalc()">
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold" style="font-size:.8rem">Amount Paid (Rs.)</label>
          <input type="number" name="paid_amount" step="0.01" min="0" value="{{ $jobCard->paid_amount }}"
                 class="form-control" style="border-radius:8px" id="paidInput" oninput="recalc()">
        </div>
      </div>

      {{-- Line Items --}}
      <div class="edit-panel-title mt-2"><i class='bx bx-list-ul me-1'></i> Line Items (Spare Parts / Labour)</div>
      <div id="itemsContainer">
        @foreach($jobCard->invoiceItems as $i => $item)
        <div class="row g-2 align-items-center mb-2 item-row">
          <div class="col-md-6">
            <input type="text" name="items[{{ $i }}][description]" value="{{ $item->description }}"
                   placeholder="Description" class="form-control form-control-sm" style="border-radius:7px" required>
          </div>
          <div class="col-md-2">
            <input type="number" name="items[{{ $i }}][qty]" value="{{ $item->qty }}"
                   placeholder="Qty" min="1" class="form-control form-control-sm" style="border-radius:7px" oninput="recalc()" required>
          </div>
          <div class="col-md-3">
            <input type="number" name="items[{{ $i }}][unit_price]" value="{{ $item->unit_price }}"
                   placeholder="Unit Price" step="0.01" min="0" class="form-control form-control-sm" style="border-radius:7px" oninput="recalc()" required>
          </div>
          <div class="col-md-1 text-center">
            <button type="button" class="item-delete-btn" onclick="removeItem(this)"><i class='bx bx-trash'></i></button>
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

  {{-- ═══════════════════ PRINTABLE INVOICE ═══════════════════ --}}
  <div class="inv-printable">
    <div class="inv-paper">

      {{-- Header --}}
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
          <div class="inv-date">
            Date: {{ $jobCard->invoice_date ? $jobCard->invoice_date->format('d M Y') : now()->format('d M Y') }}
          </div>
          <div style="margin-top:8px">
            <span class="pay-status-badge {{ $payStatus }}">{{ $payLabels[$payStatus] }}</span>
          </div>
        </div>
      </div>

      {{-- Body --}}
      <div class="inv-body">

        {{-- Customer + Device Grid --}}
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
            <div class="inv-info-row"><div class="inv-label">Job Status</div>
              <div class="inv-val">
                <span class="badge bg-label-{{ $statusColors[$jobCard->status] ?? 'secondary' }}">{{ $jobCard->status }}</span>
              </div>
            </div>
            @if($jobCard->estimated_delivery)
            <div class="inv-info-row"><div class="inv-label">Est. Delivery</div><div class="inv-val">{{ $jobCard->estimated_delivery->format('d M Y') }}</div></div>
            @endif
          </div>
        </div>

        {{-- Line Items --}}
        <div class="inv-section-title" style="margin-top:8px"><i class='bx bx-list-ul'></i> Services & Parts</div>
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
            @if($jobCard->invoiceItems->count())
              @foreach($jobCard->invoiceItems as $idx => $item)
              <tr>
                <td style="color:#aaa;font-size:.8rem">{{ $idx+1 }}</td>
                <td>{{ $item->description }}</td>
                <td class="num">{{ $item->qty }}</td>
                <td class="num">Rs. {{ number_format($item->unit_price, 2) }}</td>
                <td class="num" style="font-weight:600">Rs. {{ number_format($item->total, 2) }}</td>
              </tr>
              @endforeach
            @else
              <tr>
                <td>1</td>
                <td>{{ $jobCard->device_fault ?: 'Repair Service' }}{{ $jobCard->issue ? ' – '.$jobCard->issue : '' }}</td>
                <td class="num">1</td>
                <td class="num">Rs. {{ number_format($jobCard->rupees, 2) }}</td>
                <td class="num" style="font-weight:600">Rs. {{ number_format($jobCard->rupees, 2) }}</td>
              </tr>
            @endif
          </tbody>
        </table>

        {{-- Totals --}}
        <div class="d-flex justify-content-end">
          <div class="inv-totals">
            <div class="inv-total-row">
              <span class="t-label">Subtotal</span>
              <span id="displaySubtotal">Rs. {{ number_format($jobCard->subtotal, 2) }}</span>
            </div>
            @if($jobCard->discount > 0)
            <div class="inv-total-row" style="color:#71dd37">
              <span class="t-label">Discount</span>
              <span>− Rs. {{ number_format($jobCard->discount, 2) }}</span>
            </div>
            @endif
            <div class="inv-total-row grand">
              <span>Grand Total</span>
              <span id="displayGrand">Rs. {{ number_format($grand, 2) }}</span>
            </div>
            @if($paid > 0)
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

        {{-- Remarks --}}
        @if($jobCard->remark)
        <div style="margin-top:20px;padding:14px;background:#f8f8fc;border-radius:10px;border-left:3px solid #696cff">
          <div style="font-size:.72rem;font-weight:700;color:#696cff;text-transform:uppercase;letter-spacing:.08em;margin-bottom:4px">Remarks</div>
          <div style="font-size:.85rem;color:#555">{{ $jobCard->remark }}</div>
        </div>
        @endif
      </div>

      {{-- Footer --}}
      <div class="inv-footer">
        <div>
          <strong>{{ $store->store_name ?? 'TrackUp' }}</strong><br>
          Thank you for your business!
        </div>
        <div style="text-align:right">
          <div>Generated: {{ now()->format('d M Y, h:i A') }}</div>
          <div>{{ $jobCard->invoice_no }} · {{ $jobCard->order_no }}</div>
        </div>
      </div>

    </div>{{-- .inv-paper --}}
  </div>{{-- .inv-printable --}}

</div>{{-- .inv-wrap --}}
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
  const c = document.getElementById('itemsContainer');
  const idx = itemCount++;
  const row = document.createElement('div');
  row.className = 'row g-2 align-items-center mb-2 item-row';
  row.innerHTML = `
    <div class="col-md-6">
      <input type="text" name="items[${idx}][description]" placeholder="Description"
             class="form-control form-control-sm" style="border-radius:7px" required>
    </div>
    <div class="col-md-2">
      <input type="number" name="items[${idx}][qty]" placeholder="Qty" min="1" value="1"
             class="form-control form-control-sm" style="border-radius:7px" oninput="recalc()" required>
    </div>
    <div class="col-md-3">
      <input type="number" name="items[${idx}][unit_price]" placeholder="Unit Price" step="0.01" min="0"
             class="form-control form-control-sm" style="border-radius:7px" oninput="recalc()" required>
    </div>
    <div class="col-md-1 text-center">
      <button type="button" class="item-delete-btn" onclick="removeItem(this)"><i class='bx bx-trash'></i></button>
    </div>`;
  c.appendChild(row);
  row.querySelector('input').focus();
}

function removeItem(btn) {
  btn.closest('.item-row').remove();
  recalc();
}

function recalc() {
  let subtotal = 0;
  document.querySelectorAll('.item-row').forEach(row => {
    const qty   = parseFloat(row.querySelector('[name*="[qty]"]')?.value) || 0;
    const price = parseFloat(row.querySelector('[name*="[unit_price]"]')?.value) || 0;
    subtotal += qty * price;
  });

  // If no items, use the rupees field
  if (subtotal === 0) {
    subtotal = parseFloat(document.getElementById('rupeesInput')?.value) || 0;
  }

  const discount  = parseFloat(document.getElementById('discountInput')?.value) || 0;
  const paid      = parseFloat(document.getElementById('paidInput')?.value) || 0;
  const grand     = Math.max(0, subtotal - discount);
  const balance   = Math.max(0, grand - paid);

  const fmt = v => 'Rs. ' + v.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
  const el = id => document.getElementById(id);
  if (el('displaySubtotal')) el('displaySubtotal').textContent = fmt(subtotal);
  if (el('displayGrand'))    el('displayGrand').textContent    = fmt(grand);
}

// Open editor if there are validation errors
@if($errors->any()) toggleEdit(); @endif

// Also re-index items before submit
document.getElementById('invoiceForm')?.addEventListener('submit', function() {
  const rows = document.querySelectorAll('.item-row');
  rows.forEach((row, i) => {
    row.querySelectorAll('[name]').forEach(el => {
      el.name = el.name.replace(/\[\d+\]/, `[${i}]`);
    });
  });
});
</script>
@endpush
