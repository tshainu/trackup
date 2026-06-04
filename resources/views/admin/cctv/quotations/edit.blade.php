@extends('layouts.admin')
@section('title', 'Edit Quotation – ' . $quotation->quotation_no)

@push('styles')
<style>
  .hero-bar { background:linear-gradient(135deg,#8c57ff,#696cff); border-radius:16px; padding:1.25rem 1.75rem; color:#fff; margin-bottom:1.5rem; display:flex; align-items:center; gap:1rem; }
  .hero-bar .back-btn { width:38px; height:38px; border-radius:10px; background:rgba(255,255,255,.2); border:0; color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.2rem; text-decoration:none; transition:background .15s; }
  .hero-bar .back-btn:hover { background:rgba(255,255,255,.32); color:#fff; }
  .hero-bar h4 { margin:0; font-size:1.2rem; font-weight:700; }
  .hero-bar p { margin:0; opacity:.85; font-size:.85rem; }
  .form-card { border-radius:14px; border:0; box-shadow:0 2px 12px rgba(0,0,0,.06); margin-bottom:1.25rem; }
  .form-card .card-header { background:#f8f9fa; border-radius:14px 14px 0 0; padding:.85rem 1.25rem; font-weight:700; font-size:.875rem; display:flex; align-items:center; gap:.5rem; border-bottom:1px solid rgba(0,0,0,.06); }
  .section-icon { width:28px; height:28px; border-radius:8px; background:#f3eeff; color:#8c57ff; display:flex; align-items:center; justify-content:center; font-size:.9rem; }
  .del-row { background:none; border:none; color:#ea5455; padding:4px 8px; border-radius:6px; }
  .del-row:hover { background:#fdeaea; }
  .total-row { background:#f8f9fa; font-weight:700; }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="hero-bar">
    <a href="{{ route('admin.cctv.quotations.show', $quotation) }}" class="back-btn"><i class="bx bx-arrow-back"></i></a>
    <div>
      <h4>Edit Quotation – {{ $quotation->quotation_no }}</h4>
      <p>{{ $quotation->customer_name }}</p>
    </div>
  </div>

  <form method="POST" action="{{ route('admin.cctv.quotations.update', $quotation) }}" id="quotationForm">
    @csrf @method('PUT')
    @php $items = is_array($quotation->items) ? $quotation->items : (json_decode($quotation->items, true) ?? [['description'=>'','qty'=>1,'unit_price'=>0]]); @endphp
    <div class="row g-3">
      <div class="col-lg-8">
        <div class="card form-card">
          <div class="card-header"><div class="section-icon"><i class="bx bx-user"></i></div> Customer Details</div>
          <div class="card-body row g-3">
            <div class="col-md-6">
              <label class="form-label fw-600">Customer Name <span class="text-danger">*</span></label>
              <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name', $quotation->customer_name) }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">Mobile</label>
              <input type="text" name="mobile" class="form-control" value="{{ old('mobile', $quotation->mobile) }}">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">Email</label>
              <input type="email" name="email" class="form-control" value="{{ old('email', $quotation->email) }}">
            </div>
            <div class="col-12">
              <label class="form-label fw-600">Address</label>
              <textarea name="address" class="form-control" rows="2">{{ old('address', $quotation->address) }}</textarea>
            </div>
          </div>
        </div>

        <div class="card form-card">
          <div class="card-header"><div class="section-icon"><i class="bx bx-list-ul"></i></div> Items</div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table align-middle">
                <thead class="table-light">
                  <tr><th style="width:40%">Description</th><th>Qty</th><th>Unit Price</th><th>Total</th><th></th></tr>
                </thead>
                <tbody id="itemsBody">
                  @foreach($items as $i => $item)
                  <tr class="item-row">
                    <td><input type="text" name="items[{{ $i }}][description]" class="form-control form-control-sm" value="{{ $item['description'] ?? '' }}"></td>
                    <td><input type="number" name="items[{{ $i }}][qty]" class="form-control form-control-sm qty-input" value="{{ $item['qty'] ?? 1 }}" min="1" style="width:70px"></td>
                    <td><input type="number" name="items[{{ $i }}][unit_price]" class="form-control form-control-sm price-input" value="{{ $item['unit_price'] ?? 0 }}" step="0.01" style="width:110px"></td>
                    <td><span class="row-total fw-600">{{ number_format(($item['qty']??1)*($item['unit_price']??0),2) }}</span></td>
                    <td><button type="button" class="del-row" onclick="removeRow(this)"><i class="bx bx-trash"></i></button></td>
                  </tr>
                  @endforeach
                </tbody>
                <tfoot>
                  <tr class="total-row"><td colspan="3" class="text-end pe-3">Sub Total</td><td><span id="subTotal">{{ number_format($quotation->sub_total ?? 0,2) }}</span></td><td></td></tr>
                  <tr><td colspan="2" class="text-end pe-3">Discount (Rs.)</td><td><input type="number" name="discount_amount" id="discountInput" class="form-control form-control-sm" value="{{ old('discount_amount', $quotation->discount_amount ?? 0) }}" step="0.01"></td><td><span id="discountDisplay">{{ number_format($quotation->discount_amount ?? 0,2) }}</span></td><td></td></tr>
                  <tr class="total-row"><td colspan="3" class="text-end pe-3 fs-6">Grand Total</td><td><span id="grandTotal" class="text-primary fs-6">{{ number_format($quotation->total_amount ?? 0,2) }}</span></td><td></td></tr>
                </tfoot>
              </table>
            </div>
            <input type="hidden" name="sub_total" id="subTotalInput" value="{{ $quotation->sub_total ?? 0 }}">
            <input type="hidden" name="total_amount" id="grandTotalInput" value="{{ $quotation->total_amount ?? 0 }}">
            <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addRow()"><i class="bx bx-plus me-1"></i> Add Row</button>
          </div>
        </div>

        <div class="card form-card">
          <div class="card-header"><div class="section-icon"><i class="bx bx-note"></i></div> Terms & Notes</div>
          <div class="card-body row g-3">
            <div class="col-12">
              <label class="form-label fw-600">Terms & Conditions</label>
              <textarea name="terms" class="form-control" rows="3">{{ old('terms', $quotation->terms) }}</textarea>
            </div>
            <div class="col-12">
              <label class="form-label fw-600">Notes</label>
              <textarea name="notes" class="form-control" rows="2">{{ old('notes', $quotation->notes) }}</textarea>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card form-card">
          <div class="card-header"><div class="section-icon"><i class="bx bx-cog"></i></div> Options</div>
          <div class="card-body row g-3">
            <div class="col-12">
              <label class="form-label fw-600">Status</label>
              <select name="status" class="form-select">
                @foreach(['draft','sent','approved','rejected','expired'] as $s)
                  <option value="{{ $s }}" {{ old('status',$quotation->status)===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-12">
              <label class="form-label fw-600">Valid Until</label>
              <input type="date" name="valid_until" class="form-control" value="{{ old('valid_until', $quotation->valid_until ? \Carbon\Carbon::parse($quotation->valid_until)->format('Y-m-d') : '') }}">
            </div>
            <div class="col-12">
              <label class="form-label fw-600">Installation Charge (Rs.)</label>
              <input type="number" name="installation_charge" class="form-control" value="{{ old('installation_charge', $quotation->installation_charge ?? 0) }}" step="0.01">
            </div>
          </div>
        </div>
        <div class="card form-card">
          <div class="card-header"><div class="section-icon"><i class="bx bx-save"></i></div> Save</div>
          <div class="card-body">
            <div class="mb-3 p-3 rounded" style="background:#f8f9fa;">
              <div class="small text-muted fw-600">Quotation No</div>
              <div class="fw-700 font-monospace text-primary">{{ $quotation->quotation_no }}</div>
            </div>
            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> Update</button>
              <a href="{{ route('admin.cctv.quotations.pdf', $quotation) }}" target="_blank" class="btn btn-outline-danger"><i class="bx bx-file-pdf me-1"></i> View PDF</a>
              <a href="{{ route('admin.cctv.quotations.show', $quotation) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
  let rowIndex = {{ count($items) }};
  function calcRow(row) {
    const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
    const price = parseFloat(row.querySelector('.price-input').value) || 0;
    row.querySelector('.row-total').textContent = (qty * price).toFixed(2);
  }
  function calcTotals() {
    let sub = 0;
    document.querySelectorAll('.item-row').forEach(r => { sub += parseFloat(r.querySelector('.row-total').textContent) || 0; });
    const disc = parseFloat(document.getElementById('discountInput').value) || 0;
    const grand = Math.max(0, sub - disc);
    document.getElementById('subTotal').textContent = sub.toFixed(2);
    document.getElementById('discountDisplay').textContent = disc.toFixed(2);
    document.getElementById('grandTotal').textContent = grand.toFixed(2);
    document.getElementById('subTotalInput').value = sub.toFixed(2);
    document.getElementById('grandTotalInput').value = grand.toFixed(2);
  }
  document.getElementById('itemsBody').addEventListener('input', function(e) {
    const row = e.target.closest('.item-row');
    if (row) calcRow(row);
    calcTotals();
  });
  document.getElementById('discountInput').addEventListener('input', calcTotals);
  function addRow() {
    const tbody = document.getElementById('itemsBody');
    const tr = document.createElement('tr');
    tr.className = 'item-row';
    tr.innerHTML = `<td><input type="text" name="items[${rowIndex}][description]" class="form-control form-control-sm"></td><td><input type="number" name="items[${rowIndex}][qty]" class="form-control form-control-sm qty-input" value="1" min="1" style="width:70px"></td><td><input type="number" name="items[${rowIndex}][unit_price]" class="form-control form-control-sm price-input" value="0" step="0.01" style="width:110px"></td><td><span class="row-total fw-600">0.00</span></td><td><button type="button" class="del-row" onclick="removeRow(this)"><i class="bx bx-trash"></i></button></td>`;
    tbody.appendChild(tr);
    rowIndex++;
  }
  function removeRow(btn) {
    if (document.querySelectorAll('.item-row').length > 1) { btn.closest('.item-row').remove(); calcTotals(); }
  }
</script>
@endpush
