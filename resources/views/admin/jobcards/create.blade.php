@extends('layouts.admin')
@section('title', 'New Job Order')
@section('page-title', 'New Job Order')
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.jobcards.index') }}">Job Orders</a></li>
  <li class="breadcrumb-item active">New</li>
@endsection

@push('styles')
<style>
/* ── Header strip ── */
.jo-header {
  background: linear-gradient(135deg, #696cff 0%, #8c57ff 60%, #a855f7 100%);
  border-radius: 14px 14px 0 0;
  padding: 20px 24px;
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 12px;
}
.jo-header .order-badge { font-size: 1.3rem; font-weight: 700; letter-spacing: 1px; }
.jo-header .meta-pills { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }
.jo-header .meta-pill {
  background: rgba(255,255,255,0.18);
  border: 1px solid rgba(255,255,255,0.3);
  border-radius: 20px;
  padding: 4px 14px;
  font-size: .8rem;
  font-weight: 500;
}
.status-pending-pill {
  background: rgba(255,171,0,0.25);
  border: 1px solid rgba(255,171,0,0.6);
  color: #ffe17a;
  border-radius: 20px;
  padding: 4px 14px;
  font-size: .8rem;
  font-weight: 600;
}

/* ── Main card ── */
.jo-card {
  border: 0;
  border-radius: 0 0 14px 14px;
  box-shadow: 0 4px 24px rgba(108,92,231,.13);
  margin-bottom: 0;
}

/* ── Section headers ── */
.jo-section-head {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: .85rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .08em;
  color: #696cff;
  border-bottom: 2px solid #f0f0ff;
  padding-bottom: 10px;
  margin-bottom: 18px;
}
.jo-section-head .ico-wrap {
  width: 30px; height: 30px;
  background: linear-gradient(135deg,#696cff22,#8c57ff22);
  border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  font-size: 1rem; color: #696cff;
}

/* ── Priority selector ── */
.priority-group { display: flex; gap: 8px; flex-wrap: wrap; }
.priority-btn {
  flex: 1; min-width: 70px;
  text-align: center;
  padding: 8px 6px;
  border-radius: 10px;
  border: 2px solid #e0e0e0;
  cursor: pointer;
  font-size: .8rem; font-weight: 600;
  transition: all .18s;
  background: #fff;
  user-select: none;
}
.priority-btn:hover { transform: translateY(-1px); }
.priority-btn.active-Low    { border-color: #71dd37; background: #edfbd8; color: #3a7c11; }
.priority-btn.active-Normal { border-color: #03c3ec; background: #d9f8fe; color: #0074a0; }
.priority-btn.active-High   { border-color: #ffab00; background: #fff4d4; color: #8a5500; }
.priority-btn.active-Urgent { border-color: #ff3e1d; background: #ffe0dc; color: #a00000; }
.priority-dot {
  display: inline-block; width: 9px; height: 9px;
  border-radius: 50%; margin-right: 5px;
}

/* ── Device Age Drag Slider ── */
.age-slider-wrap { padding: 6px 0 2px; }

.age-range-row {
  display: flex;
  align-items: center;
  gap: 10px;
}

/* The range input itself */
.age-range {
  -webkit-appearance: none;
  appearance: none;
  flex: 1;
  height: 8px;
  border-radius: 8px;
  outline: none;
  cursor: pointer;
  border: none;
  background: #e8e8f0; /* fallback; overridden by JS */
  transition: background .15s;
}
.age-range::-webkit-slider-thumb {
  -webkit-appearance: none;
  appearance: none;
  width: 22px;
  height: 22px;
  border-radius: 50%;
  background: #fff;
  border: 3px solid #696cff;
  box-shadow: 0 2px 8px rgba(108,92,231,.35);
  cursor: grab;
  transition: border-color .15s, box-shadow .15s, transform .1s;
}
.age-range::-webkit-slider-thumb:active {
  cursor: grabbing;
  transform: scale(1.18);
  box-shadow: 0 3px 14px rgba(108,92,231,.45);
}
.age-range::-moz-range-thumb {
  width: 22px;
  height: 22px;
  border-radius: 50%;
  background: #fff;
  border: 3px solid #696cff;
  box-shadow: 0 2px 8px rgba(108,92,231,.35);
  cursor: grab;
}
/* Badge next to slider */
.age-badge {
  min-width: 52px;
  text-align: center;
  font-size: .78rem;
  font-weight: 700;
  padding: 4px 8px;
  border-radius: 20px;
  background: #f0f0ff;
  color: #696cff;
  border: 1.5px solid #d5d5f5;
  white-space: nowrap;
  transition: background .15s, color .15s, border-color .15s;
}
.age-badge.age-mid  { background: #fff4d4; color: #8a5500; border-color: #ffd97a; }
.age-badge.age-high { background: #ffe0dc; color: #a00000; border-color: #ff9980; }

/* Tick marks */
.age-ticks {
  display: flex;
  justify-content: space-between;
  /* inset by thumb radius (11px) so labels align with thumb stops */
  padding: 4px 11px 0;
  font-size: .68rem;
  color: #bbb;
  box-sizing: border-box;
}
.age-ticks span {
  text-align: center;
  flex: 1;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: clip;
}
.age-ticks span:first-child { text-align: left; }
.age-ticks span:last-child  { text-align: right; }

/* ── Add-inline button ── */
.btn-add-inline {
  width: 28px; height: 28px;
  border-radius: 50%;
  border: 2px solid #696cff;
  background: #fff;
  color: #696cff;
  display: inline-flex; align-items: center; justify-content: center;
  font-size: 1rem; font-weight: 700;
  cursor: pointer;
  transition: all .15s;
  flex-shrink: 0;
  line-height: 1;
}
.btn-add-inline:hover { background: #696cff; color: #fff; }

/* ── Select + add row ── */
.select-add-row {
  display: flex;
  align-items: center;
  gap: 8px;
}
.select-add-row .form-select { flex: 1; }
.select-add-row .form-control { flex: 1; }

/* ── Accessories ── */
.acc-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
  gap: 8px;
}
.acc-item {
  display: flex; align-items: center; gap: 8px;
  padding: 8px 12px;
  border: 1.5px solid #e8e8f0;
  border-radius: 9px;
  cursor: pointer;
  font-size: .83rem; font-weight: 500; color: #555;
  transition: all .15s;
  background: #fafafa;
}
.acc-item:hover { border-color: #696cff; background: #f5f5ff; }
.acc-item:has(input:checked) { border-color: #696cff; background: #f0f0ff; color: #696cff; }
.acc-item input[type=checkbox] { width: 16px; height: 16px; accent-color: #696cff; cursor: pointer; }
.acc-add-btn {
  display: flex; align-items: center; justify-content: center; gap: 4px;
  padding: 8px 12px;
  border: 1.5px dashed #c0c0e0;
  border-radius: 9px;
  cursor: pointer;
  font-size: .83rem; font-weight: 600; color: #696cff;
  background: #fff;
  transition: all .15s;
}
.acc-add-btn:hover { border-color: #696cff; background: #f5f5ff; }

/* ── Char count ── */
.char-count { font-size: .74rem; color: #aaa; float: right; }

/* ── Save bar ── */
.jo-save-bar {
  background: #f8f8ff;
  border-radius: 12px;
  padding: 16px 20px;
  display: flex; align-items: center; justify-content: space-between;
  flex-wrap: wrap; gap: 10px;
  margin-top: 24px;
  border: 1px solid #ebebff;
}
.btn-save-main {
  background: linear-gradient(135deg, #696cff, #8c57ff);
  color: #fff; border: 0;
  padding: 10px 32px;
  border-radius: 10px;
  font-weight: 700; font-size: 1rem;
  transition: opacity .2s, transform .15s;
  box-shadow: 0 4px 14px rgba(108,92,231,.35);
}
.btn-save-main:hover { opacity: .9; transform: translateY(-1px); color: #fff; }
.btn-save-main:active { transform: translateY(0); }

/* ── Misc ── */
.form-control:focus, .form-select:focus { border-color: #696cff; box-shadow: 0 0 0 3px rgba(108,92,231,.12); }
.form-label { font-weight: 600; font-size: .83rem; color: #444; margin-bottom: 5px; }
.required-star { color: #ff3e1d; }

/* ── Quick-add modal ── */
.quick-add-modal .modal-header {
  background: linear-gradient(135deg,#696cff,#8c57ff);
  color: #fff;
  border-radius: 12px 12px 0 0;
}
.quick-add-modal .modal-header .btn-close { filter: invert(1); }
.quick-add-modal .modal-content { border-radius: 12px; border: 0; }
</style>
@endpush

@section('content')
<form action="{{ route('admin.jobcards.store') }}" method="POST" id="joForm">
@csrf

{{-- ── Header Strip ── --}}
<div class="jo-header">
  <div>
    <div style="font-size:.75rem;opacity:.75;margin-bottom:2px;text-transform:uppercase;letter-spacing:.08em">Job Order Number</div>
    <div class="order-badge"># {{ $orderNo }}</div>
  </div>
  <div class="meta-pills">
    <span class="meta-pill"><i class='bx bx-calendar me-1'></i>{{ date('d M Y') }}</span>
    <span class="meta-pill"><i class='bx bx-user me-1'></i>{{ $customerId }}</span>
    <span class="status-pending-pill"><i class='bx bx-time-five me-1'></i>Pending</span>
  </div>
</div>

{{-- ── Main Card ── --}}
<div class="card jo-card">
  <div class="card-body p-4">
    <div class="row g-4">

      {{-- ══ LEFT: Customer ══ --}}
      <div class="col-lg-6">
        <div class="jo-section-head">
          <span class="ico-wrap"><i class='bx bx-user'></i></span>
          Customer Information
        </div>

        <div class="row g-3">
          <div class="col-12">
            <label for="firstFocus" class="form-label">Customer Name <span class="required-star">*</span></label>
            <input type="text" name="customer_name" id="firstFocus"
              class="form-control @error('customer_name') is-invalid @enderror"
              value="{{ old('customer_name') }}"
              placeholder="Full name of customer" required autocomplete="off" />
            @error('customer_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-7">
            <label for="phone_no" class="form-label">Phone Number <span class="required-star">*</span></label>
            <div class="input-group">
              <span class="input-group-text"><i class='bx bx-phone'></i></span>
              <input type="text" name="phone_no" id="phone_no"
                class="form-control @error('phone_no') is-invalid @enderror"
                value="{{ old('phone_no') }}" placeholder="07X XXX XXXX" required />
            </div>
            @error('phone_no')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-5">
            <label for="customer_nic" class="form-label">NIC</label>
            <input type="text" name="customer_nic" id="customer_nic" class="form-control"
              value="{{ old('customer_nic') }}" placeholder="XXXXXXXXXX" />
          </div>

          <div class="col-12">
            <label for="customer_address" class="form-label">Address</label>
            <input type="text" name="customer_address" id="customer_address" class="form-control"
              value="{{ old('customer_address') }}" placeholder="Street, City" />
          </div>

          <div class="col-md-6">
            <label for="customer_email" class="form-label">Email</label>
            <input type="email" name="customer_email" id="customer_email" class="form-control"
              value="{{ old('customer_email') }}" placeholder="email@example.com" />
          </div>

          <div class="col-md-6">
            <label for="customer_dob" class="form-label">Date of Birth</label>
            <input type="text" name="customer_dob" id="customer_dob" class="form-control"
              value="{{ old('customer_dob') }}" placeholder="01/01/1990" />
          </div>

          <div class="col-md-6">
            <label for="date_received" class="form-label">Date Received <span class="required-star">*</span></label>
            <input type="date" name="date" id="date_received"
              class="form-control @error('date') is-invalid @enderror"
              value="{{ old('date', date('Y-m-d')) }}" required />
            @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-6">
            <label for="estimated_delivery" class="form-label">Est. Delivery Date</label>
            <input type="date" name="estimated_delivery" id="estimated_delivery" class="form-control"
              value="{{ old('estimated_delivery') }}" />
          </div>

          <div class="col-12">
            <label class="form-label d-block">Priority</label>
            <div class="priority-group" id="priorityGroup">
              @foreach(['Low'=>'#71dd37','Normal'=>'#03c3ec','High'=>'#ffab00','Urgent'=>'#ff3e1d'] as $p => $color)
              <div class="priority-btn {{ old('priority','Normal') === $p ? 'active-'.$p : '' }}"
                   data-priority="{{ $p }}" onclick="setPriority('{{ $p }}')">
                <span class="priority-dot" style="background:{{ $color }}"></span>{{ $p }}
              </div>
              @endforeach
            </div>
            <input type="hidden" name="priority" id="priorityInput" value="{{ old('priority','Normal') }}" />
          </div>
        </div>
      </div>

      {{-- ══ RIGHT: Device & Repair ══ --}}
      <div class="col-lg-6">
        <div class="jo-section-head">
          <span class="ico-wrap"><i class='bx bx-chip'></i></span>
          Device & Repair
        </div>

        <div class="row g-3">

          {{-- Device Type + add --}}
          <div class="col-12">
            <label for="deviceSelect" class="form-label">Device Type <span class="required-star">*</span></label>
            <div class="select-add-row">
              <select name="device_name" id="deviceSelect"
                class="form-select @error('device_name') is-invalid @enderror" required>
                <option value="">-- Select Device --</option>
                @foreach($devices as $d)
                  <option value="{{ $d->device_name }}" {{ old('device_name') == $d->device_name ? 'selected' : '' }}>{{ $d->device_name }}</option>
                @endforeach
              </select>
              <button type="button" class="btn-add-inline" title="Add new device type"
                onclick="openQuickAdd('Device Type','device_name_new','deviceSelect','device_name')">+</button>
            </div>
            @error('device_name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>

          {{-- Brand + add --}}
          <div class="col-md-6">
            <label for="brandSelect" class="form-label">Brand</label>
            <div class="select-add-row">
              <select name="device_brand" id="brandSelect" class="form-select">
                <option value="">-- Select Brand --</option>
              </select>
              <button type="button" class="btn-add-inline" title="Add new brand"
                onclick="openQuickAdd('Brand','device_brand_new','brandSelect','device_brand')">+</button>
            </div>
          </div>

          {{-- Fault Type + add --}}
          <div class="col-md-6">
            <label for="faultSelect" class="form-label">Fault Type</label>
            <div class="select-add-row">
              <select name="device_fault" id="faultSelect" class="form-select">
                <option value="">-- Select Fault --</option>
              </select>
              <button type="button" class="btn-add-inline" title="Add new fault type"
                onclick="openQuickAdd('Fault Type','device_fault_new','faultSelect','device_fault')">+</button>
            </div>
          </div>

          {{-- Item Description --}}
          <div class="col-12">
            <label for="itemDescArea" class="form-label d-flex justify-content-between">
              Item Description
              <span class="char-count"><span id="itemDescCnt">0</span>/500</span>
            </label>
            <textarea name="item_description" id="itemDescArea" class="form-control" rows="2"
              maxlength="500" placeholder="Physical description of the item (color, model, condition, markings...)">{{ old('item_description') }}</textarea>
          </div>

          <div class="col-md-6">
            <label for="serial_no" class="form-label">Serial / IMEI No</label>
            <input type="text" name="serial_no" id="serial_no" class="form-control"
              value="{{ old('serial_no') }}" placeholder="Serial or IMEI" />
          </div>

          {{-- Device Age — drag slider ── --}}
          <div class="col-md-6">
            <label class="form-label d-flex justify-content-between align-items-center">
              Device Age <small class="text-muted fw-normal ms-1" style="font-size:.72rem">(0 = New · 10 = Very Old)</small>
            </label>
            <input type="hidden" name="device_age" id="deviceAgeInput" value="{{ old('device_age', 0) }}" />
            <div class="age-slider-wrap">
              <div class="age-range-row">
                <input type="range" class="age-range" id="ageRange"
                  min="0" max="10" step="1"
                  value="{{ old('device_age', 0) }}" />
                <span class="age-badge" id="ageBadge">—</span>
              </div>
              <div class="age-ticks">
                <span>0</span>
                <span>1</span>
                <span>2</span>
                <span>3</span>
                <span>4</span>
                <span>5</span>
                <span>6</span>
                <span>7</span>
                <span>8</span>
                <span>9</span>
                <span>10</span>
              </div>
            </div>
          </div>

          <div class="col-12">
            <label for="issueArea" class="form-label d-flex justify-content-between">
              Issue Description
              <span class="char-count"><span id="issueCnt">0</span>/500</span>
            </label>
            <textarea name="issue" id="issueArea" class="form-control" rows="3"
              maxlength="500" placeholder="Customer's description of the problem...">{{ old('issue') }}</textarea>
          </div>

          <div class="col-md-6">
            <label for="rupees" class="form-label">Estimated Cost (Rs.)</label>
            <div class="input-group">
              <span class="input-group-text">Rs.</span>
              <input type="number" name="rupees" id="rupees" class="form-control clear-on-focus"
                value="{{ old('rupees', 0) }}" min="0" step="0.01" />
            </div>
          </div>

          <div class="col-md-6">
            <label for="advance_amount" class="form-label">Advance Amount (Rs.)</label>
            <div class="input-group">
              <span class="input-group-text">Rs.</span>
              <input type="number" name="advance_amount" id="advance_amount" class="form-control clear-on-focus"
                value="{{ old('advance_amount', 0) }}" min="0" step="0.01" placeholder="0.00" />
            </div>
            <small class="text-muted" style="font-size:.75rem">Amount collected upfront</small>
          </div>

          <div class="col-md-12">
            <label for="employee_id" class="form-label">Assign to Employee</label>
            <select name="employee_id" id="employee_id" class="form-select">
              <option value="">-- Unassigned --</option>
              @foreach($employees as $emp)
                <option value="{{ $emp->id }}" {{ old('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->employee_name }}</option>
              @endforeach
            </select>
          </div>

          {{-- Accessories ── --}}
          <div class="col-12">
            <label class="form-label d-block">Accessories Received</label>
            <div class="acc-grid" id="accGrid">
              @php $oldAccs = old('accessories_list', []); @endphp
              @foreach(\App\Models\DeviceAccessory::orderBy('accessory_name')->get() as $acc)
              <label class="acc-item acc-static" data-value="{{ $acc->accessory_name }}">
                <input type="checkbox" name="accessories_list[]" value="{{ $acc->accessory_name }}"
                  {{ in_array($acc->accessory_name, $oldAccs) ? 'checked' : '' }} />
                <span class="acc-label-inner">{{ $acc->accessory_name }}</span>
              </label>
              @endforeach
              {{-- Add button inside grid --}}
              <div class="acc-add-btn" id="accAddBtnGrid" onclick="openAccAdd()">
                <i class='bx bx-plus'></i> Add
              </div>
            </div>
          </div>

          <div class="col-12">
            <label for="remarkArea" class="form-label d-flex justify-content-between">
              Internal Remark
              <span class="char-count"><span id="remarkCnt">0</span>/500</span>
            </label>
            <textarea name="remark" id="remarkArea" class="form-control" rows="2"
              maxlength="500" placeholder="Notes for internal use...">{{ old('remark') }}</textarea>
          </div>

        </div>
      </div>

    </div>{{-- /row --}}
  </div>
</div>

{{-- ── Save Bar ── --}}
<div class="jo-save-bar">
  <div style="font-size:.83rem;color:#888">
    <i class='bx bx-info-circle me-1'></i>Order no <strong style="color:#696cff">{{ $orderNo }}</strong> will be auto-assigned on save.
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('admin.jobcards.index') }}" class="btn btn-outline-secondary" style="border-radius:10px;padding:10px 24px;font-weight:600">
      <i class='bx bx-x me-1'></i>Cancel
    </a>
    <button type="submit" class="btn-save-main btn">
      <i class='bx bx-save me-1'></i>Save Job Order
    </button>
  </div>
</div>

</form>

{{-- ── Quick-Add Modal (Device Type / Brand / Fault) ── --}}
<div class="modal fade quick-add-modal" id="quickAddModal" tabindex="-1">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title fw-bold mb-0" id="quickAddTitle">Add New</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <label class="form-label fw-semibold" id="quickAddLabel">Value</label>
        <input type="text" class="form-control" id="quickAddInput" placeholder="Type here..." />
        <div class="text-danger mt-1" id="quickAddError" style="font-size:.8rem;display:none"></div>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary btn-sm" id="quickAddConfirm"
          style="background:linear-gradient(135deg,#696cff,#8c57ff);border:0">Add</button>
      </div>
    </div>
  </div>
</div>

{{-- ── Accessory Add Modal ── --}}
<div class="modal fade quick-add-modal" id="accAddModal" tabindex="-1">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title fw-bold mb-0">Add Accessory</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <label class="form-label fw-semibold">Accessory Name</label>
        <input type="text" class="form-control" id="accAddInput" placeholder="e.g. Remote, Stand..." />
        <div class="text-danger mt-1" id="accAddError" style="font-size:.8rem;display:none"></div>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary btn-sm" id="accAddConfirm"
          style="background:linear-gradient(135deg,#696cff,#8c57ff);border:0">Add</button>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
const brandsUrl      = '{{ route("ajax.brands") }}';
const faultsUrl      = '{{ route("ajax.faults") }}';
const accessoriesUrl = '{{ route("ajax.accessories") }}';

// Auto-focus
document.getElementById('firstFocus')?.focus();

// ── Priority ──
function setPriority(p) {
  document.getElementById('priorityInput').value = p;
  document.querySelectorAll('.priority-btn').forEach(b => {
    b.className = 'priority-btn' + (b.dataset.priority === p ? ' active-' + p : '');
  });
}

// ── Char counters ──
function initCounter(textareaId, counterId) {
  const ta = document.getElementById(textareaId);
  const ct = document.getElementById(counterId);
  if (!ta || !ct) return;
  ct.textContent = ta.value.length;
  ta.addEventListener('input', () => ct.textContent = ta.value.length);
}
initCounter('issueArea',    'issueCnt');
initCounter('remarkArea',   'remarkCnt');
initCounter('itemDescArea', 'itemDescCnt');

// ── Device Age Drag Slider ──
(function () {
  const range  = document.getElementById('ageRange');
  const hidden = document.getElementById('deviceAgeInput');
  const badge  = document.getElementById('ageBadge');

  function updateAge(val) {
    val = parseInt(val);
    hidden.value = val;

    // Badge text
    const labels = ['New','1','2','3','4','5','6','7','8','9','Very Old'];
    badge.textContent = val + '/10' + (val === 0 ? ' · New' : val === 10 ? ' · Very Old' : '');

    // Badge colour class
    badge.classList.remove('age-mid', 'age-high');
    if (val >= 8)      badge.classList.add('age-high');
    else if (val >= 5) badge.classList.add('age-mid');

    // Thumb border colour
    const thumbColor = val === 0 ? '#696cff' : val >= 8 ? '#ff3e1d' : val >= 5 ? '#ffab00' : '#696cff';
    range.style.setProperty('--thumb-color', thumbColor);

    // Track fill gradient
    const pct = (val / 10) * 100;
    let fillColor;
    if (val === 0)      fillColor = '#e8e8f0';
    else if (val >= 8)  fillColor = '#ff3e1d';
    else if (val >= 5)  fillColor = '#ffab00';
    else                fillColor = '#696cff';

    range.style.background =
      `linear-gradient(to right, ${fillColor} ${pct}%, #e8e8f0 ${pct}%)`;

    // Thumb border via dynamic style
    const styleId = 'age-thumb-style';
    let s = document.getElementById(styleId);
    if (!s) { s = document.createElement('style'); s.id = styleId; document.head.appendChild(s); }
    s.textContent = `.age-range::-webkit-slider-thumb { border-color: ${thumbColor} !important; }
                     .age-range::-moz-range-thumb     { border-color: ${thumbColor} !important; }`;
  }

  range.addEventListener('input', () => updateAge(range.value));
  updateAge(range.value); // init
})();

// ── Device → Brand + Fault + Accessories ──
$('#deviceSelect').on('change', function () {
  const device = $(this).val();
  $('#brandSelect').html('<option value="">Loading...</option>');
  $('#faultSelect').html('<option value="">Loading...</option>');
  if (!device) {
    $('#brandSelect').html('<option value="">-- Select Brand --</option>');
    $('#faultSelect').html('<option value="">-- Select Fault --</option>');
    return;
  }
  $.getJSON(brandsUrl, { device_name: device }, function (data) {
    let opts = '<option value="">-- Select Brand --</option>';
    data.forEach(b => { opts += `<option value="${b.device_brand}">${b.device_brand}</option>`; });
    $('#brandSelect').html(opts);
  });
  $.getJSON(faultsUrl, { device_name: device }, function (data) {
    let opts = '<option value="">-- Select Fault --</option>';
    data.forEach(f => { opts += `<option value="${f.device_fault}">${f.device_fault}</option>`; });
    $('#faultSelect').html(opts);
  });
  // Accessories are global — no reload needed on device change
});

function resetAccessoriesGrid(dbAccs) {
  const grid   = document.getElementById('accGrid');
  const addBtn = document.getElementById('accAddBtnGrid');
  // remove all dynamic acc items (not static or add-btn)
  grid.querySelectorAll('.acc-item-dynamic').forEach(el => el.remove());
  // show/hide static items
  const hasDb = dbAccs.length > 0;
  grid.querySelectorAll('.acc-static').forEach(el => {
    el.style.display = hasDb ? 'none' : '';
  });
  // inject db accessories
  dbAccs.forEach(name => {
    const label = document.createElement('label');
    label.className = 'acc-item acc-item-dynamic';
    label.innerHTML = `<input type="checkbox" name="accessories_list[]" value="${escHtmlJ(name)}" /><span class="acc-label-inner">${escHtmlJ(name)}</span>`;
    grid.insertBefore(label, addBtn);
  });
}

function escHtmlJ(s) {
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ── Quick-Add (Device Type / Brand / Fault) ──
let _qaTarget = null; // { label, inputId, selectId, fieldName }

function openQuickAdd(label, inputId, selectId, fieldName) {
  _qaTarget = { label, inputId, selectId, fieldName };
  document.getElementById('quickAddTitle').textContent = 'Add ' + label;
  document.getElementById('quickAddLabel').textContent = label + ' Name';
  document.getElementById('quickAddInput').value = '';
  document.getElementById('quickAddError').style.display = 'none';
  new bootstrap.Modal(document.getElementById('quickAddModal')).show();
  setTimeout(() => document.getElementById('quickAddInput').focus(), 300);
}

document.getElementById('quickAddConfirm').addEventListener('click', function () {
  const val = document.getElementById('quickAddInput').value.trim();
  const err = document.getElementById('quickAddError');
  if (!val) { err.textContent = 'Please enter a value.'; err.style.display = ''; return; }

  const sel = document.getElementById(_qaTarget.selectId);
  // Check duplicate
  for (let o of sel.options) {
    if (o.value.toLowerCase() === val.toLowerCase()) {
      err.textContent = 'Already exists in the list.'; err.style.display = ''; return;
    }
  }
  // Add option + select it
  const opt = new Option(val, val, true, true);
  sel.appendChild(opt);
  sel.value = val;
  // Trigger change for device select to reload brand/fault
  if (_qaTarget.selectId === 'deviceSelect') $(sel).trigger('change');
  bootstrap.Modal.getInstance(document.getElementById('quickAddModal')).hide();
});

document.getElementById('quickAddInput').addEventListener('keydown', function(e) {
  if (e.key === 'Enter') document.getElementById('quickAddConfirm').click();
});

// ── Accessory Add ──
function openAccAdd() {
  document.getElementById('accAddInput').value = '';
  document.getElementById('accAddError').style.display = 'none';
  new bootstrap.Modal(document.getElementById('accAddModal')).show();
  setTimeout(() => document.getElementById('accAddInput').focus(), 300);
}

document.getElementById('accAddConfirm').addEventListener('click', function () {
  const val = document.getElementById('accAddInput').value.trim();
  const err = document.getElementById('accAddError');
  if (!val) { err.textContent = 'Please enter a value.'; err.style.display = ''; return; }

  // Inject new checkbox before the add button
  const grid = document.getElementById('accGrid');
  const addBtn = grid.querySelector('.acc-add-btn');
  const label = document.createElement('label');
  label.className = 'acc-item';
  label.innerHTML = `<input type="checkbox" name="accessories_list[]" value="${val}" checked /><span class="acc-label-inner">${val}</span>`;
  grid.insertBefore(label, addBtn);

  bootstrap.Modal.getInstance(document.getElementById('accAddModal')).hide();
});

document.getElementById('accAddInput').addEventListener('keydown', function(e) {
  if (e.key === 'Enter') document.getElementById('accAddConfirm').click();
});

// ── Submit: combine accessories ──
document.getElementById('joForm').addEventListener('submit', function () {
  const checked = [...document.querySelectorAll('input[name="accessories_list[]"]:checked')]
                    .map(c => c.value);
  const hidden = document.createElement('input');
  hidden.type = 'hidden';
  hidden.name = 'accessories';
  hidden.value = checked.join(', ');
  this.appendChild(hidden);
});

// Pre-populate brand/fault selects if old('device_name') exists (validation fail repopulation)
$(document).ready(function() {
  if ($('#deviceSelect').val()) {
    $('#deviceSelect').trigger('change');
  }
});

// ── Clear-on-focus for 0 defaults ──
document.querySelectorAll('.clear-on-focus').forEach(function(input) {
  input.addEventListener('focus', function() {
    if (parseFloat(this.value) === 0) this.value = '';
  });
  input.addEventListener('blur', function() {
    if (this.value === '' || this.value === null) this.value = '0';
  });
});
</script>
@endpush
