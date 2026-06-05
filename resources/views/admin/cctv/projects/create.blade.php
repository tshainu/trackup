@extends('layouts.admin')
@section('title', 'New Project')

@push('styles')
<style>
  .hero-bar { background:linear-gradient(135deg,#fd7e14,#e55a00); border-radius:16px; padding:1.25rem 1.75rem; color:#fff; margin-bottom:1.5rem; display:flex; align-items:center; gap:1rem; }
  .hero-bar .back-btn { width:38px; height:38px; border-radius:10px; background:rgba(255,255,255,.2); border:0; color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.2rem; text-decoration:none; transition:background .15s; }
  .hero-bar .back-btn:hover { background:rgba(255,255,255,.32); color:#fff; }
  .hero-bar h4 { margin:0; font-size:1.2rem; font-weight:700; }
  .hero-bar p { margin:0; opacity:.85; font-size:.85rem; }
  .form-card { border-radius:14px; border:0; box-shadow:0 2px 12px rgba(0,0,0,.06); margin-bottom:1.25rem; }
  .form-card .card-header { background:#f8f9fa; border-radius:14px 14px 0 0; padding:.85rem 1.25rem; font-weight:700; font-size:.875rem; display:flex; align-items:center; gap:.5rem; border-bottom:1px solid rgba(0,0,0,.06); }
  .section-icon { width:28px; height:28px; border-radius:8px; background:#fff3e8; color:#fd7e14; display:flex; align-items:center; justify-content:center; font-size:.9rem; }

  /* ── Multi-select technician picker ── */
  .tech-picker { position:relative; }
  .tech-tags-box {
    min-height:40px; border:1px solid #d9dee3; border-radius:6px; padding:4px 8px;
    display:flex; flex-wrap:wrap; gap:4px; align-items:center; cursor:text;
    background:#fff; transition:border-color .15s;
  }
  .tech-tags-box:focus-within { border-color:#fd7e14; box-shadow:0 0 0 3px rgba(253,126,20,.15); }
  .tech-tag {
    display:inline-flex; align-items:center; gap:4px;
    background:#fff3e8; color:#e55a00; border:1px solid #fdc98a;
    border-radius:20px; padding:2px 10px 2px 10px; font-size:.8rem; white-space:nowrap;
  }
  .tech-tag .remove-tag { cursor:pointer; font-size:.9rem; line-height:1; margin-left:2px; color:#e55a00; }
  .tech-tag .remove-tag:hover { color:#b04000; }
  .tech-search-input {
    border:0; outline:none; flex:1; min-width:120px; font-size:.875rem; padding:2px 4px; background:transparent;
  }
  .tech-dropdown {
    display:none; position:absolute; top:calc(100% + 4px); left:0; right:0; z-index:200;
    background:#fff; border:1px solid #d9dee3; border-radius:8px;
    box-shadow:0 4px 18px rgba(0,0,0,.1); max-height:220px; overflow-y:auto;
  }
  .tech-dropdown.open { display:block; }
  .tech-option {
    padding:.5rem .875rem; cursor:pointer; font-size:.875rem; display:flex; align-items:center; gap:.5rem;
  }
  .tech-option:hover, .tech-option.highlighted { background:#fff3e8; }
  .tech-option.selected { color:#e55a00; font-weight:600; }
  .tech-option .check { color:#fd7e14; font-size:.75rem; }

  /* ── Items table ── */
  .items-table th { font-size:.78rem; text-transform:uppercase; letter-spacing:.04em; color:#6c757d; background:#f8f9fa; }
  .items-table td { vertical-align:middle; padding:.45rem .5rem; }
  .items-table input { border:1px solid #e0e3e7; border-radius:5px; padding:.3rem .5rem; font-size:.875rem; width:100%; background:#fff; }
  .items-table input:focus { outline:none; border-color:#fd7e14; box-shadow:0 0 0 2px rgba(253,126,20,.12); }
  .items-table .row-total { font-weight:600; color:#333; text-align:right; font-size:.875rem; min-width:80px; }
  .btn-add-row { font-size:.8rem; }
  .grand-total-row td { font-weight:700; font-size:.9rem; background:#fff8f0; }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="hero-bar">
    <a href="{{ route('admin.cctv.projects.index') }}" class="back-btn"><i class="bx bx-arrow-back"></i></a>
    <div><h4>New Installation Project</h4><p>Schedule a CCTV installation</p></div>
  </div>

  @if($quotation)
  <div class="alert alert-success d-flex align-items-center gap-2 mb-3" style="border-radius:12px;">
    <i class="bx bx-link-alt fs-5"></i>
    <div>
      <strong>Pre-filled from Quotation {{ $quotation->quotation_no }}</strong> —
      customer details, contract amount, and equipment list have been auto-filled. Review and confirm before saving.
    </div>
  </div>
  @endif

  <form method="POST" action="{{ route('admin.cctv.projects.store') }}">
    @csrf
    <div class="row g-3">
      <div class="col-lg-8">

        {{-- Customer Details --}}
        <div class="card form-card">
          <div class="card-header"><div class="section-icon"><i class="bx bx-user"></i></div> Customer Details</div>
          <div class="card-body row g-3">
            <div class="col-md-6">
              <label class="form-label fw-600">Customer Name <span class="text-danger">*</span></label>
              <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name', $quotation?->customer_name ?? $lead?->customer_name ?? '') }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">Mobile <span class="text-danger">*</span></label>
              <input type="text" name="mobile" class="form-control" value="{{ old('mobile', $quotation?->mobile ?? $lead?->mobile ?? '') }}" required>
            </div>
            <div class="col-12">
              <label class="form-label fw-600">Address</label>
              <textarea name="address" class="form-control" rows="2">{{ old('address', $quotation?->address ?? $lead?->address ?? '') }}</textarea>
            </div>
          </div>
        </div>

        {{-- Project Details --}}
        <div class="card form-card">
          <div class="card-header"><div class="section-icon"><i class="bx bx-wrench"></i></div> Project Details</div>
          <div class="card-body row g-3">
            <div class="col-md-4">
              <label class="form-label fw-600">Status</label>
              <select name="status" class="form-select">
                @foreach(['scheduled','in_progress','completed','on_hold','cancelled'] as $s)
                  <option value="{{ $s }}" {{ old('status','scheduled')===$s?'selected':'' }}>{{ ucwords(str_replace('_',' ',$s)) }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-600">Start Date</label>
              <input type="date" name="start_date" class="form-control" value="{{ old('start_date', date('Y-m-d')) }}">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-600">End Date</label>
              <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}">
            </div>

            {{-- Technician multi-select --}}
            <div class="col-md-6">
              <label class="form-label fw-600">Technician(s)</label>
              <div class="tech-picker" id="techPicker">
                <div class="tech-tags-box" id="techTagsBox">
                  <input type="text" class="tech-search-input" id="techSearch" placeholder="Search technician…" autocomplete="off">
                </div>
                <div class="tech-dropdown" id="techDropdown">
                  @foreach($employees as $emp)
                    <div class="tech-option" data-id="{{ $emp->id }}" data-name="{{ $emp->employee_name }}">
                      <span class="check" style="visibility:hidden;">✓</span>
                      <span>{{ $emp->employee_name }}</span>
                    </div>
                  @endforeach
                  <div class="tech-option text-muted" id="techNoResult" style="display:none;">No match found</div>
                </div>
              </div>
              {{-- Hidden inputs populated by JS --}}
              <div id="techHiddenInputs"></div>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-600">No. of Cameras</label>
              <input type="number" name="camera_count" class="form-control" value="{{ old('camera_count') }}" min="0">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">Contract Amount (Rs.)</label>
              <input type="number" name="contract_amount" id="contractAmount" step="0.01" class="form-control" value="{{ old('contract_amount', $quotation?->grand_total ?? '') }}">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">Advance Paid (Rs.)</label>
              <input type="number" name="advance_paid" step="0.01" class="form-control" value="{{ old('advance_paid', 0) }}">
            </div>
            <div class="col-12">
              <label class="form-label fw-600">Scope of Work</label>
              <textarea name="scope" class="form-control" rows="3">{{ old('scope') }}</textarea>
            </div>
            <div class="col-12">
              <label class="form-label fw-600">Notes</label>
              <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
            </div>
          </div>
        </div>

        {{-- Equipment / Items table --}}
        <div class="card form-card">
          <div class="card-header">
            <div class="section-icon"><i class="bx bx-list-ul"></i></div> Equipment / Scope Items
            <button type="button" class="btn btn-sm btn-outline-primary ms-auto btn-add-row" id="addItemRow">
              <i class="bx bx-plus"></i> Add Row
            </button>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-bordered items-table mb-0" id="itemsTable">
                <thead>
                  <tr>
                    <th style="width:45%">Description</th>
                    <th style="width:12%">Qty</th>
                    <th style="width:18%">Unit Price (Rs.)</th>
                    <th style="width:18%" class="text-end">Total (Rs.)</th>
                    <th style="width:7%"></th>
                  </tr>
                </thead>
                <tbody id="itemsBody">
                  {{-- Pre-filled from quotation --}}
                  @if($quotation && !empty($quotation->equipment_list))
                    @foreach($quotation->equipment_list as $i => $item)
                    <tr class="item-row">
                      <td><input type="text" name="items[{{ $i }}][description]" value="{{ $item['name'] ?? '' }}" placeholder="Item description"></td>
                      <td><input type="number" name="items[{{ $i }}][qty]" value="{{ $item['qty'] ?? 1 }}" min="1" class="item-qty" style="text-align:center;"></td>
                      <td><input type="number" name="items[{{ $i }}][unit_price]" value="{{ $item['unit_price'] ?? 0 }}" step="0.01" class="item-price"></td>
                      <td class="row-total">{{ number_format(($item['qty'] ?? 1) * ($item['unit_price'] ?? 0), 2) }}</td>
                      <td class="text-center"><button type="button" class="btn btn-sm btn-link text-danger p-0 remove-row" title="Remove"><i class="bx bx-trash"></i></button></td>
                    </tr>
                    @endforeach
                  @else
                    <tr class="item-row">
                      <td><input type="text" name="items[0][description]" placeholder="Item description"></td>
                      <td><input type="number" name="items[0][qty]" value="1" min="1" class="item-qty" style="text-align:center;"></td>
                      <td><input type="number" name="items[0][unit_price]" value="0" step="0.01" class="item-price"></td>
                      <td class="row-total">0.00</td>
                      <td class="text-center"><button type="button" class="btn btn-sm btn-link text-danger p-0 remove-row" title="Remove"><i class="bx bx-trash"></i></button></td>
                    </tr>
                  @endif
                </tbody>
                <tfoot>
                  <tr class="grand-total-row">
                    <td colspan="3" class="text-end pe-3">Grand Total</td>
                    <td class="text-end pe-2" id="grandTotal">{{ $quotation && !empty($quotation->equipment_list) ? number_format(collect($quotation->equipment_list)->sum(fn($i) => ($i['qty']??1)*($i['unit_price']??0)), 2) : '0.00' }}</td>
                    <td></td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>

      </div>{{-- /col-lg-8 --}}

      <div class="col-lg-4">
        <div class="card form-card">
          <div class="card-header"><div class="section-icon"><i class="bx bx-save"></i></div> Save</div>
          <div class="card-body">
            <input type="hidden" name="quotation_id" value="{{ $quotation?->id ?? request('quotation_id') }}">
            <input type="hidden" name="lead_id" value="{{ $quotation?->lead_id ?? $lead?->id ?? '' }}">
            <input type="hidden" name="customer_id" value="{{ $quotation?->customer_id ?? '' }}">
            <p class="text-muted small mb-3">Project number auto-generated.</p>
            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> Save Project</button>
              <a href="{{ route('admin.cctv.projects.index') }}" class="btn btn-outline-secondary">Cancel</a>
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
/* ── Technician multi-select ── */
(function(){
  const picker       = document.getElementById('techPicker');
  const tagsBox      = document.getElementById('techTagsBox');
  const searchInput  = document.getElementById('techSearch');
  const dropdown     = document.getElementById('techDropdown');
  const hiddenInputs = document.getElementById('techHiddenInputs');
  const noResult     = document.getElementById('techNoResult');
  const allOptions   = Array.from(document.querySelectorAll('#techDropdown .tech-option[data-id]'));

  let selected = {}; // id -> name

  function renderTags() {
    // Remove existing tags
    tagsBox.querySelectorAll('.tech-tag').forEach(t => t.remove());
    // Re-insert tags before the search input
    Object.entries(selected).forEach(([id, name]) => {
      const tag = document.createElement('span');
      tag.className = 'tech-tag';
      tag.innerHTML = `${name} <span class="remove-tag" data-id="${id}">&times;</span>`;
      tagsBox.insertBefore(tag, searchInput);
    });
    // Rebuild hidden inputs
    hiddenInputs.innerHTML = '';
    Object.keys(selected).forEach(id => {
      const inp = document.createElement('input');
      inp.type  = 'hidden';
      inp.name  = 'technician_ids[]';
      inp.value = id;
      hiddenInputs.appendChild(inp);
    });
    // Update option checkmarks
    allOptions.forEach(opt => {
      const chk = opt.querySelector('.check');
      if (selected[opt.dataset.id]) {
        chk.style.visibility = 'visible';
        opt.classList.add('selected');
      } else {
        chk.style.visibility = 'hidden';
        opt.classList.remove('selected');
      }
    });
  }

  function filterOptions(q) {
    let anyVisible = false;
    allOptions.forEach(opt => {
      const match = opt.dataset.name.toLowerCase().includes(q.toLowerCase());
      opt.style.display = match ? '' : 'none';
      if (match) anyVisible = true;
    });
    noResult.style.display = anyVisible ? 'none' : '';
  }

  // Open/close dropdown
  tagsBox.addEventListener('click', () => {
    dropdown.classList.add('open');
    searchInput.focus();
    filterOptions(searchInput.value);
  });

  document.addEventListener('click', e => {
    if (!picker.contains(e.target)) dropdown.classList.remove('open');
  });

  searchInput.addEventListener('input', () => filterOptions(searchInput.value));

  // Select option
  dropdown.addEventListener('click', e => {
    const opt = e.target.closest('.tech-option[data-id]');
    if (!opt) return;
    const id   = opt.dataset.id;
    const name = opt.dataset.name;
    if (selected[id]) {
      delete selected[id];
    } else {
      selected[id] = name;
    }
    renderTags();
    searchInput.value = '';
    filterOptions('');
    searchInput.focus();
  });

  // Remove tag
  tagsBox.addEventListener('click', e => {
    const rm = e.target.closest('.remove-tag');
    if (!rm) return;
    delete selected[rm.dataset.id];
    renderTags();
  });
})();

/* ── Items table ── */
(function(){
  const tbody     = document.getElementById('itemsBody');
  const addBtn    = document.getElementById('addItemRow');
  const grandTotalCell = document.getElementById('grandTotal');

  function getRowIndex() {
    return tbody.querySelectorAll('.item-row').length;
  }

  function calcRow(row) {
    const qty   = parseFloat(row.querySelector('.item-qty')?.value) || 0;
    const price = parseFloat(row.querySelector('.item-price')?.value) || 0;
    const total = qty * price;
    row.querySelector('.row-total').textContent = total.toFixed(2);
    return total;
  }

  function calcAll() {
    let grand = 0;
    tbody.querySelectorAll('.item-row').forEach(r => grand += calcRow(r));
    grandTotalCell.textContent = grand.toFixed(2);
  }

  function addRow() {
    const idx = getRowIndex();
    const tr  = document.createElement('tr');
    tr.className = 'item-row';
    tr.innerHTML = `
      <td><input type="text" name="items[${idx}][description]" placeholder="Item description"></td>
      <td><input type="number" name="items[${idx}][qty]" value="1" min="1" class="item-qty" style="text-align:center;"></td>
      <td><input type="number" name="items[${idx}][unit_price]" value="0" step="0.01" class="item-price"></td>
      <td class="row-total">0.00</td>
      <td class="text-center"><button type="button" class="btn btn-sm btn-link text-danger p-0 remove-row" title="Remove"><i class="bx bx-trash"></i></button></td>`;
    tbody.appendChild(tr);
    tr.querySelector('input[type=text]').focus();
    reindexRows();
  }

  function reindexRows() {
    tbody.querySelectorAll('.item-row').forEach((row, i) => {
      row.querySelectorAll('input[name]').forEach(inp => {
        inp.name = inp.name.replace(/items\[\d+\]/, `items[${i}]`);
      });
    });
    calcAll();
  }

  addBtn.addEventListener('click', addRow);

  tbody.addEventListener('input', e => {
    if (e.target.matches('.item-qty,.item-price')) calcAll();
  });

  tbody.addEventListener('click', e => {
    const rmBtn = e.target.closest('.remove-row');
    if (!rmBtn) return;
    const rows = tbody.querySelectorAll('.item-row');
    if (rows.length <= 1) {
      // Clear last row instead of removing
      rmBtn.closest('tr').querySelectorAll('input').forEach(i => { i.value = i.type === 'number' ? (i.classList.contains('item-qty') ? 1 : 0) : ''; });
      calcAll();
      return;
    }
    rmBtn.closest('tr').remove();
    reindexRows();
  });

  // Initial calc (for pre-filled rows)
  calcAll();
})();
</script>
@endpush
