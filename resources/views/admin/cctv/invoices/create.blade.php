@extends('layouts.admin')
@section('title', 'Create Invoice')

@push('styles')
<style>
  .hero-bar { background:linear-gradient(135deg,#28c76f,#20a255); border-radius:16px; padding:1.25rem 1.75rem; color:#fff; margin-bottom:1.5rem; display:flex; align-items:center; gap:1rem; }
  .hero-bar .back-btn { width:38px; height:38px; border-radius:10px; background:rgba(255,255,255,.2); border:0; color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.2rem; text-decoration:none; }
  .hero-bar .back-btn:hover { background:rgba(255,255,255,.32); color:#fff; }
  .hero-bar h4 { margin:0; font-size:1.2rem; font-weight:700; }
  .section-card { border-radius:14px; border:0; box-shadow:0 2px 12px rgba(40,199,111,.08); margin-bottom:1.25rem; }
  .section-card .card-header { background:#f8f9fa; border-radius:14px 14px 0 0; padding:.85rem 1.25rem; font-weight:700; font-size:.875rem; display:flex; align-items:center; gap:.5rem; }
  .section-icon { width:28px; height:28px; border-radius:8px; background:#e8faf0; color:#28c76f; display:flex; align-items:center; justify-content:center; font-size:.9rem; }
  .items-table th { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; color:#697a8d; }
  .item-row td { vertical-align:middle; }
  .remove-item { width:28px; height:28px; border-radius:6px; background:#fdeaea; color:#ea5455; border:0; display:flex; align-items:center; justify-content:center; font-size:.9rem; cursor:pointer; }
  .remove-item:hover { background:#ea5455; color:#fff; }
  .total-row { font-size:.9rem; display:flex; justify-content:space-between; padding:.3rem 0; }
  .total-row.grand { font-size:1.05rem; font-weight:700; border-top:2px solid #ddd; padding-top:.6rem; margin-top:.3rem; }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  <div class="hero-bar">
    <a href="{{ route('admin.cctv.invoices.index') }}" class="back-btn"><i class="bx bx-arrow-back"></i></a>
    <div>
      <h4><i class="bx bx-receipt me-2"></i> Create Invoice</h4>
      @if($project ?? null)
        <div style="opacity:.85;font-size:.85rem;">Linked to Project: {{ $project->project_no }}</div>
      @endif
    </div>
  </div>

  <form method="POST" action="{{ route('admin.cctv.invoices.store') }}" id="invoiceForm">
    @csrf

    {{-- Hidden link IDs --}}
    @if($project ?? null)
      <input type="hidden" name="project_id"   value="{{ $project->id }}">
      <input type="hidden" name="quotation_id" value="{{ $project->quotation_id }}">
      <input type="hidden" name="lead_id"      value="{{ $project->lead_id }}">
    @endif

    <div class="row g-3">
      {{-- LEFT --}}
      <div class="col-lg-8">

        {{-- Customer --}}
        <div class="card section-card">
          <div class="card-header"><div class="section-icon"><i class="bx bx-user"></i></div> Customer</div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-sm-6">
                <label class="form-label fw-600">Customer Name <span class="text-danger">*</span></label>
                <input type="text" name="customer_name" class="form-control"
                  value="{{ old('customer_name', $project?->customer_name ?? '') }}" required>
              </div>
              <div class="col-sm-6">
                <label class="form-label fw-600">Mobile</label>
                <input type="text" name="mobile" class="form-control"
                  value="{{ old('mobile', $project?->mobile ?? '') }}">
              </div>
              <div class="col-12">
                <label class="form-label fw-600">Address</label>
                <textarea name="address" class="form-control" rows="2">{{ old('address', $project?->address ?? '') }}</textarea>
              </div>
            </div>
          </div>
        </div>

        {{-- Items --}}
        <div class="card section-card">
          <div class="card-header"><div class="section-icon"><i class="bx bx-list-ul"></i></div> Equipment / Items</div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table items-table mb-0" id="itemsTable">
                <thead class="table-light">
                  <tr>
                    <th style="width:50%">Description</th>
                    <th style="width:10%">Qty</th>
                    <th style="width:20%">Unit Price</th>
                    <th style="width:15%">Total</th>
                    <th style="width:5%"></th>
                  </tr>
                </thead>
                <tbody id="itemsBody">
                  @php
                    $prefillItems = [];
                    if ($project ?? null) {
                      // Pull from quotation items if linked
                      if ($project->quotation) {
                        $raw = is_array($project->quotation->items) ? $project->quotation->items : json_decode($project->quotation->items ?? '[]', true);
                        foreach ($raw as $it) {
                          $prefillItems[] = ['name'=>$it['description']??'', 'qty'=>$it['qty']??1, 'unit_price'=>$it['unit_price']??0];
                        }
                      }
                      // Or from equipment_list on project
                      if (empty($prefillItems) && $project->equipment_list) {
                        foreach ($project->equipment_list as $it) {
                          $prefillItems[] = ['name'=>$it['name']??'', 'qty'=>$it['qty']??1, 'unit_price'=>$it['unit_price']??0];
                        }
                      }
                    }
                    if (empty($prefillItems)) $prefillItems = [['name'=>'','qty'=>1,'unit_price'=>0]];
                  @endphp
                  @foreach($prefillItems as $idx => $item)
                  <tr class="item-row">
                    <td><input type="text" name="items[{{ $idx }}][description]" class="form-control form-control-sm item-desc" value="{{ $item['name'] }}" placeholder="Description"></td>
                    <td><input type="number" name="items[{{ $idx }}][qty]" class="form-control form-control-sm item-qty" value="{{ $item['qty'] }}" min="1" step="1"></td>
                    <td><input type="number" name="items[{{ $idx }}][unit_price]" class="form-control form-control-sm item-price" value="{{ $item['unit_price'] }}" min="0" step="0.01"></td>
                    <td class="item-total fw-600 text-end pe-3">{{ number_format($item['qty'] * $item['unit_price'], 2) }}</td>
                    <td><button type="button" class="remove-item"><i class="bx bx-x"></i></button></td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            <div class="p-3">
              <button type="button" class="btn btn-sm btn-outline-primary" id="addItem"><i class="bx bx-plus me-1"></i> Add Item</button>
            </div>
          </div>
        </div>

        {{-- Notes --}}
        <div class="card section-card">
          <div class="card-header"><div class="section-icon"><i class="bx bx-note"></i></div> Notes</div>
          <div class="card-body">
            <textarea name="notes" class="form-control" rows="3" placeholder="Payment terms, warranty, notes…">{{ old('notes') }}</textarea>
          </div>
        </div>

      </div>

      {{-- RIGHT --}}
      <div class="col-lg-4">

        {{-- Invoice Details --}}
        <div class="card section-card">
          <div class="card-header"><div class="section-icon"><i class="bx bx-calendar"></i></div> Invoice Details</div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label fw-600">Invoice Date</label>
              <input type="date" name="invoice_date" class="form-control" value="{{ old('invoice_date', now()->format('Y-m-d')) }}">
            </div>
            <div class="mb-3">
              <label class="form-label fw-600">Due Date</label>
              <input type="date" name="due_date" class="form-control" value="{{ old('due_date') }}">
            </div>
          </div>
        </div>

        {{-- Charges --}}
        <div class="card section-card">
          <div class="card-header"><div class="section-icon"><i class="bx bx-rupee"></i></div> Charges & Totals</div>
          <div class="card-body">
            <div class="mb-2">
              <label class="form-label fw-600 small">Labour Cost</label>
              <input type="number" name="labour_cost" id="labourCost" class="form-control form-control-sm charge-field"
                value="{{ old('labour_cost', $project?->contract_amount ?? 0) }}" min="0" step="0.01">
            </div>
            <div class="mb-2">
              <label class="form-label fw-600 small">Installation Cost</label>
              <input type="number" name="installation_cost" id="installCost" class="form-control form-control-sm charge-field"
                value="{{ old('installation_cost', 0) }}" min="0" step="0.01">
            </div>
            <div class="mb-2">
              <label class="form-label fw-600 small">Transport Cost</label>
              <input type="number" name="transport_cost" id="transportCost" class="form-control form-control-sm charge-field"
                value="{{ old('transport_cost', 0) }}" min="0" step="0.01">
            </div>
            <div class="mb-2">
              <label class="form-label fw-600 small">Discount</label>
              <input type="number" name="discount" id="discountField" class="form-control form-control-sm charge-field"
                value="{{ old('discount', 0) }}" min="0" step="0.01">
            </div>
            <div class="mb-3">
              <label class="form-label fw-600 small">Tax</label>
              <input type="number" name="tax" id="taxField" class="form-control form-control-sm charge-field"
                value="{{ old('tax', 0) }}" min="0" step="0.01">
            </div>
            <hr>
            <div class="total-row"><span class="text-muted">Items Sub-Total</span><span id="displaySubtotal">0.00</span></div>
            <div class="total-row"><span class="text-muted">Discount</span><span id="displayDiscount" class="text-danger">- 0.00</span></div>
            <div class="total-row grand"><span>Grand Total</span><span id="displayTotal" class="text-success">0.00</span></div>
            <hr>
            <label class="form-label fw-600 small">Advance / Paid Amount</label>
            <input type="number" name="paid_amount" id="paidAmount" class="form-control form-control-sm"
              value="{{ old('paid_amount', $project?->advance_paid ?? 0) }}" min="0" step="0.01">
          </div>
        </div>

        <div class="d-grid gap-2">
          <button type="submit" class="btn btn-success"><i class="bx bx-check me-1"></i> Create Invoice</button>
          <a href="{{ route('admin.cctv.invoices.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>

      </div>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
let itemIdx = {{ count($prefillItems) }};

function recalc() {
  let sub = 0;
  document.querySelectorAll('#itemsBody .item-row').forEach((row, i) => {
    const qty   = parseFloat(row.querySelector('.item-qty').value)   || 0;
    const price = parseFloat(row.querySelector('.item-price').value) || 0;
    const total = qty * price;
    row.querySelector('.item-total').textContent = total.toFixed(2);
    sub += total;
  });
  const labour   = parseFloat(document.getElementById('labourCost').value)   || 0;
  const install  = parseFloat(document.getElementById('installCost').value)  || 0;
  const transport= parseFloat(document.getElementById('transportCost').value)|| 0;
  const discount = parseFloat(document.getElementById('discountField').value) || 0;
  const tax      = parseFloat(document.getElementById('taxField').value)      || 0;
  const grand    = Math.max(0, sub + labour + install + transport - discount + tax);
  document.getElementById('displaySubtotal').textContent = sub.toFixed(2);
  document.getElementById('displayDiscount').textContent = '- ' + discount.toFixed(2);
  document.getElementById('displayTotal').textContent    = 'Rs. ' + grand.toFixed(2);
}

document.getElementById('addItem').addEventListener('click', () => {
  const tbody = document.getElementById('itemsBody');
  const tr = document.createElement('tr');
  tr.className = 'item-row';
  tr.innerHTML = `
    <td><input type="text" name="items[${itemIdx}][description]" class="form-control form-control-sm item-desc" placeholder="Description"></td>
    <td><input type="number" name="items[${itemIdx}][qty]" class="form-control form-control-sm item-qty" value="1" min="1" step="1"></td>
    <td><input type="number" name="items[${itemIdx}][unit_price]" class="form-control form-control-sm item-price" value="0" min="0" step="0.01"></td>
    <td class="item-total fw-600 text-end pe-3">0.00</td>
    <td><button type="button" class="remove-item"><i class="bx bx-x"></i></button></td>
  `;
  tbody.appendChild(tr);
  itemIdx++;
  bindRow(tr);
});

function bindRow(row) {
  row.querySelector('.remove-item').addEventListener('click', () => { row.remove(); recalc(); });
  row.querySelectorAll('input').forEach(inp => inp.addEventListener('input', recalc));
}

document.querySelectorAll('#itemsBody .item-row').forEach(bindRow);
document.querySelectorAll('.charge-field').forEach(f => f.addEventListener('input', recalc));
recalc();
</script>
@endpush
