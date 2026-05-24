@extends('layouts.admin')
@section('title', 'Edit – ' . $jobCard->order_no)
@section('page-title', 'Edit Job Order')
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.jobcards.index') }}">Job Orders</a></li>
  <li class="breadcrumb-item active">Edit {{ $jobCard->order_no }}</li>
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
.jo-header .meta-pills  { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }
.jo-header .meta-pill {
  background: rgba(255,255,255,0.18);
  border: 1px solid rgba(255,255,255,0.3);
  border-radius: 20px;
  padding: 4px 14px;
  font-size: .8rem; font-weight: 500;
}
@php
  $hdrStatusColors = [
    'Pending'       => 'rgba(255,171,0,.25)',
    'In Progress'   => 'rgba(3,195,236,.25)',
    'Completed'     => 'rgba(113,221,55,.25)',
    'Not Completed' => 'rgba(255,62,29,.25)',
  ];
  $hdrStatusBorders = [
    'Pending'       => 'rgba(255,171,0,.6)',
    'In Progress'   => 'rgba(3,195,236,.6)',
    'Completed'     => 'rgba(113,221,55,.6)',
    'Not Completed' => 'rgba(255,62,29,.6)',
  ];
  $hdrStatusText = [
    'Pending'       => '#ffe17a',
    'In Progress'   => '#7eecff',
    'Completed'     => '#c5ff8a',
    'Not Completed' => '#ffb3a8',
  ];
  $cs = old('status', $jobCard->status) ?: 'Pending';
@endphp

/* ── Main card ── */
.jo-card {
  border: 0;
  border-radius: 0 0 14px 14px;
  box-shadow: 0 4px 24px rgba(108,92,231,.13);
  margin-bottom: 0;
}

/* ── Section headers ── */
.jo-section-head {
  display: flex; align-items: center; gap: 10px;
  font-size: .85rem; font-weight: 700;
  text-transform: uppercase; letter-spacing: .08em;
  color: #696cff;
  border-bottom: 2px solid #f0f0ff;
  padding-bottom: 10px; margin-bottom: 18px;
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
  flex: 1; min-width: 70px; text-align: center;
  padding: 8px 6px; border-radius: 10px;
  border: 2px solid #e0e0e0; cursor: pointer;
  font-size: .8rem; font-weight: 600;
  transition: all .18s; background: #fff; user-select: none;
}
.priority-btn:hover { transform: translateY(-1px); }
.priority-btn.active-Low    { border-color:#71dd37; background:#edfbd8; color:#3a7c11; }
.priority-btn.active-Normal { border-color:#03c3ec; background:#d9f8fe; color:#0074a0; }
.priority-btn.active-High   { border-color:#ffab00; background:#fff4d4; color:#8a5500; }
.priority-btn.active-Urgent { border-color:#ff3e1d; background:#ffe0dc; color:#a00000; }
.priority-dot { display:inline-block; width:9px; height:9px; border-radius:50%; margin-right:5px; }

/* ── Age slider ── */
.age-slider-wrap { padding: 6px 0 2px; }
.age-range-row { display:flex; align-items:center; gap:10px; }
.age-range {
  -webkit-appearance:none; appearance:none;
  flex:1; height:8px; border-radius:8px;
  outline:none; cursor:pointer; border:none;
  background:#e8e8f0; transition:background .15s;
}
.age-range::-webkit-slider-thumb {
  -webkit-appearance:none; appearance:none;
  width:22px; height:22px; border-radius:50%;
  background:#fff; border:3px solid #696cff;
  box-shadow:0 2px 8px rgba(108,92,231,.35);
  cursor:grab; transition:border-color .15s, box-shadow .15s, transform .1s;
}
.age-range::-webkit-slider-thumb:active { cursor:grabbing; transform:scale(1.18); }
.age-range::-moz-range-thumb {
  width:22px; height:22px; border-radius:50%;
  background:#fff; border:3px solid #696cff;
  box-shadow:0 2px 8px rgba(108,92,231,.35); cursor:grab;
}
.age-badge {
  min-width:52px; text-align:center; font-size:.78rem; font-weight:700;
  padding:4px 8px; border-radius:20px;
  background:#f0f0ff; color:#696cff; border:1.5px solid #d5d5f5;
  white-space:nowrap; transition:background .15s, color .15s, border-color .15s;
}
.age-badge.age-mid  { background:#fff4d4; color:#8a5500; border-color:#ffd97a; }
.age-badge.age-high { background:#ffe0dc; color:#a00000; border-color:#ff9980; }
.age-ticks {
  display:flex; justify-content:space-between;
  padding:4px 11px 0; font-size:.68rem; color:#bbb; box-sizing:border-box;
}
.age-ticks span { text-align:center; flex:1; }
.age-ticks span:first-child { text-align:left; }
.age-ticks span:last-child  { text-align:right; }

/* ── Add-inline btn ── */
.btn-add-inline {
  width:28px; height:28px; border-radius:50%;
  border:2px solid #696cff; background:#fff; color:#696cff;
  display:inline-flex; align-items:center; justify-content:center;
  font-size:1rem; font-weight:700; cursor:pointer;
  transition:all .15s; flex-shrink:0; line-height:1;
}
.btn-add-inline:hover { background:#696cff; color:#fff; }

/* ── Select + add row ── */
.select-add-row { display:flex; align-items:center; gap:8px; }
.select-add-row .form-select { flex:1; }

/* ── Accessories ── */
.acc-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(130px,1fr)); gap:8px; }
.acc-item {
  display:flex; align-items:center; gap:8px;
  padding:8px 12px; border:1.5px solid #e8e8f0;
  border-radius:9px; cursor:pointer;
  font-size:.83rem; font-weight:500; color:#555;
  transition:all .15s; background:#fafafa;
}
.acc-item:hover { border-color:#696cff; background:#f5f5ff; }
.acc-item:has(input:checked) { border-color:#696cff; background:#f0f0ff; color:#696cff; }
.acc-item input[type=checkbox] { width:16px; height:16px; accent-color:#696cff; cursor:pointer; }
.acc-add-btn {
  display:flex; align-items:center; justify-content:center; gap:4px;
  padding:8px 12px; border:1.5px dashed #c0c0e0; border-radius:9px;
  cursor:pointer; font-size:.83rem; font-weight:600; color:#696cff;
  background:#fff; transition:all .15s;
}
.acc-add-btn:hover { border-color:#696cff; background:#f5f5ff; }

/* ── Char count ── */
.char-count { font-size:.74rem; color:#aaa; float:right; }

/* ── Save bar ── */
.jo-save-bar {
  background:#f8f8ff; border-radius:12px; padding:16px 20px;
  display:flex; align-items:center; justify-content:space-between;
  flex-wrap:wrap; gap:10px; margin-top:24px; border:1px solid #ebebff;
}
.btn-save-main {
  background:linear-gradient(135deg,#696cff,#8c57ff);
  color:#fff; border:0; padding:10px 32px; border-radius:10px;
  font-weight:700; font-size:1rem;
  transition:opacity .2s, transform .15s;
  box-shadow:0 4px 14px rgba(108,92,231,.35);
}
.btn-save-main:hover  { opacity:.9; transform:translateY(-1px); color:#fff; }
.btn-save-main:active { transform:translateY(0); }

/* ── Misc ── */
.form-control:focus, .form-select:focus { border-color:#696cff; box-shadow:0 0 0 3px rgba(108,92,231,.12); }
.form-label { font-weight:600; font-size:.83rem; color:#444; margin-bottom:5px; }
.required-star { color:#ff3e1d; }

/* ── Modals ── */
.quick-add-modal .modal-header {
  background:linear-gradient(135deg,#696cff,#8c57ff);
  color:#fff; border-radius:12px 12px 0 0;
}
.quick-add-modal .modal-header .btn-close { filter:invert(1); }
.quick-add-modal .modal-content { border-radius:12px; border:0; }
</style>
@endpush

@section('content')
@php
  $accList = $jobCard->accessories
    ? array_map('trim', explode(',', $jobCard->accessories))
    : [];
  $defaultAccs = ['Charger','Remote','Cover/Case','Battery','Power Cable','Earphones','Memory Card','Stylus','Other'];
  $extraAccs   = array_diff($accList, $defaultAccs);
@endphp

<form action="{{ route('admin.jobcards.update', $jobCard) }}" method="POST" id="joForm">
@csrf @method('PUT')

{{-- ── Header Strip ── --}}
<div class="jo-header">
  <div>
    <div style="font-size:.75rem;opacity:.75;margin-bottom:2px;text-transform:uppercase;letter-spacing:.08em">Job Order</div>
    <div class="order-badge"># {{ $jobCard->order_no }}</div>
  </div>
  <div class="meta-pills">
    <span class="meta-pill"><i class='bx bx-calendar me-1'></i>{{ $jobCard->date ? $jobCard->date->format('d M Y') : date('d M Y') }}</span>
    <span class="meta-pill"><i class='bx bx-user me-1'></i>{{ $jobCard->customer_id }}</span>
    <span style="background:{{ $hdrStatusColors[$cs] ?? 'rgba(255,171,0,.25)' }};border:1px solid {{ $hdrStatusBorders[$cs] ?? 'rgba(255,171,0,.6)' }};color:{{ $hdrStatusText[$cs] ?? '#ffe17a' }};border-radius:20px;padding:4px 14px;font-size:.8rem;font-weight:600">
      <i class='bx bx-time-five me-1'></i>{{ $cs }}
    </span>
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
            <label class="form-label">Order No</label>
            <input type="text" id="display_order_no" class="form-control bg-light" value="{{ $jobCard->order_no }}" readonly />
          </div>

          <div class="col-12">
            <label class="form-label">Customer Name <span class="required-star">*</span></label>
            <input type="text" name="customer_name"
              class="form-control @error('customer_name') is-invalid @enderror"
              value="{{ old('customer_name', $jobCard->customer_name) }}"
              placeholder="Full name" required />
            @error('customer_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-7">
            <label class="form-label">Phone <span class="required-star">*</span></label>
            <div class="input-group">
              <span class="input-group-text"><i class='bx bx-phone'></i></span>
              <input type="text" name="phone_no" class="form-control"
                value="{{ old('phone_no', $jobCard->phone_no) }}" placeholder="07X XXX XXXX" required />
            </div>
          </div>

          <div class="col-md-5">
            <label class="form-label">NIC</label>
            <input type="text" name="customer_nic" class="form-control"
              value="{{ old('customer_nic', $jobCard->customer_nic) }}" placeholder="XXXXXXXXXX" />
          </div>

          <div class="col-12">
            <label class="form-label">Address</label>
            <input type="text" name="customer_address" class="form-control"
              value="{{ old('customer_address', $jobCard->customer_address) }}" placeholder="Street, City" />
          </div>

          <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" name="customer_email" class="form-control"
              value="{{ old('customer_email', $jobCard->customer_email) }}" placeholder="email@example.com" />
          </div>

          <div class="col-md-6">
            <label class="form-label">Date of Birth</label>
            <input type="text" name="customer_dob" class="form-control"
              value="{{ old('customer_dob', $jobCard->customer_dob) }}" placeholder="01/01/1990" />
          </div>

          <div class="col-md-6">
            <label class="form-label">Date Received <span class="required-star">*</span></label>
            <input type="date" name="date"
              class="form-control @error('date') is-invalid @enderror"
              value="{{ old('date', $jobCard->date ? $jobCard->date->format('Y-m-d') : '') }}" required />
            @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-6">
            <label class="form-label">Est. Delivery Date</label>
            <input type="date" name="estimated_delivery" class="form-control"
              value="{{ old('estimated_delivery', $jobCard->estimated_delivery ? \Carbon\Carbon::parse($jobCard->estimated_delivery)->format('Y-m-d') : '') }}" />
          </div>

          <div class="col-12">
            <label class="form-label d-block">Priority</label>
            <div class="priority-group" id="priorityGroup">
              @foreach(['Low'=>'#71dd37','Normal'=>'#03c3ec','High'=>'#ffab00','Urgent'=>'#ff3e1d'] as $p => $color)
              @php $activePriority = old('priority', $jobCard->priority ?? 'Normal'); @endphp
              <div class="priority-btn {{ $activePriority === $p ? 'active-'.$p : '' }}"
                   data-priority="{{ $p }}" onclick="setPriority('{{ $p }}')">
                <span class="priority-dot" style="background:{{ $color }}"></span>{{ $p }}
              </div>
              @endforeach
            </div>
            <input type="hidden" name="priority" id="priorityInput" value="{{ old('priority', $jobCard->priority ?? 'Normal') }}" />
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

          <div class="col-12">
            <label class="form-label">Device Type <span class="required-star">*</span></label>
            <div class="select-add-row">
              <select name="device_name" id="deviceSelect"
                class="form-select @error('device_name') is-invalid @enderror" required>
                <option value="">-- Select Device --</option>
                @foreach($devices as $d)
                  <option value="{{ $d->device_name }}" {{ old('device_name', $jobCard->device_name) == $d->device_name ? 'selected' : '' }}>{{ $d->device_name }}</option>
                @endforeach
              </select>
              <button type="button" class="btn-add-inline" title="Add new device type"
                onclick="openQuickAdd('Device Type','deviceSelect','device_name')">+</button>
            </div>
            @error('device_name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-6">
            <label class="form-label">Brand</label>
            <div class="select-add-row">
              <select name="device_brand" id="brandSelect" class="form-select">
                <option value="">-- Select Brand --</option>
                @foreach($brands as $b)
                  <option value="{{ $b->device_brand }}" {{ old('device_brand', $jobCard->device_brand) == $b->device_brand ? 'selected' : '' }}>{{ $b->device_brand }}</option>
                @endforeach
              </select>
              <button type="button" class="btn-add-inline" title="Add new brand"
                onclick="openQuickAdd('Brand','brandSelect','device_brand')">+</button>
            </div>
          </div>

          <div class="col-md-6">
            <label class="form-label">Fault Type</label>
            <div class="select-add-row">
              <select name="device_fault" id="faultSelect" class="form-select">
                <option value="">-- Select Fault --</option>
                @foreach($faults as $f)
                  <option value="{{ $f->device_fault }}" {{ old('device_fault', $jobCard->device_fault) == $f->device_fault ? 'selected' : '' }}>{{ $f->device_fault }}</option>
                @endforeach
              </select>
              <button type="button" class="btn-add-inline" title="Add new fault type"
                onclick="openQuickAdd('Fault Type','faultSelect','device_fault')">+</button>
            </div>
          </div>

          <div class="col-md-6">
            <label class="form-label">Serial / IMEI No</label>
            <input type="text" name="serial_no" class="form-control"
              value="{{ old('serial_no', $jobCard->serial_no) }}" placeholder="Serial or IMEI" />
          </div>

          <div class="col-md-6">
            <label class="form-label d-flex justify-content-between align-items-center">
              Device Age <small class="text-muted fw-normal ms-1" style="font-size:.72rem">(0 = New · 10 = Very Old)</small>
            </label>
            <input type="hidden" name="device_age" id="deviceAgeInput" value="{{ old('device_age', $jobCard->device_age ?? 0) }}" />
            <div class="age-slider-wrap">
              <div class="age-range-row">
                <input type="range" class="age-range" id="ageRange"
                  min="0" max="10" step="1"
                  value="{{ old('device_age', $jobCard->device_age ?? 0) }}" />
                <span class="age-badge" id="ageBadge">—</span>
              </div>
              <div class="age-ticks">
                @for($t=0;$t<=10;$t++)<span>{{ $t }}</span>@endfor
              </div>
            </div>
          </div>

          <div class="col-12">
            <label class="form-label d-flex justify-content-between">
              Issue Description
              <span class="char-count"><span id="issueCnt">0</span>/500</span>
            </label>
            <textarea name="issue" id="issueArea" class="form-control" rows="3"
              maxlength="500" placeholder="Customer's description of the problem...">{{ old('issue', $jobCard->issue) }}</textarea>
          </div>

          <div class="col-md-6">
            <label class="form-label">Amount (Rs.)</label>
            <div class="input-group">
              <span class="input-group-text">Rs.</span>
              <input type="number" name="rupees" class="form-control"
                value="{{ old('rupees', $jobCard->rupees) }}" min="0" step="0.01" />
            </div>
          </div>

          <div class="col-md-6">
            <label class="form-label">Status <span class="required-star">*</span></label>
            <select name="status" class="form-select" required id="statusSelect">
              @foreach(['Pending','In Progress','Completed','Not Completed','Broken','Cancelled'] as $s)
                <option value="{{ $s }}" {{ old('status', $jobCard->status) == $s ? 'selected' : '' }}>{{ $s }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-12">
            <label class="form-label">Assign to Employee</label>
            <select name="employee_id" class="form-select">
              <option value="">-- Unassigned --</option>
              @foreach($employees as $emp)
                <option value="{{ $emp->id }}" {{ old('employee_id', $jobCard->employee_id) == $emp->id ? 'selected' : '' }}>{{ $emp->employee_name }}</option>
              @endforeach
            </select>
          </div>

          {{-- Accessories ── --}}
          <div class="col-12">
            <label class="form-label">Accessories Received</label>
            <div class="acc-grid" id="accGrid">
              @foreach($defaultAccs as $acc)
              <label class="acc-item">
                <input type="checkbox" name="accessories_list[]" value="{{ $acc }}"
                  {{ in_array($acc, old('accessories_list', $accList)) ? 'checked' : '' }} />
                <span>{{ $acc }}</span>
              </label>
              @endforeach
              @foreach($extraAccs as $acc)
              <label class="acc-item">
                <input type="checkbox" name="accessories_list[]" value="{{ $acc }}" checked />
                <span>{{ $acc }}</span>
              </label>
              @endforeach
              <div class="acc-add-btn" onclick="openAccAdd()">
                <i class='bx bx-plus'></i> Add
              </div>
            </div>
          </div>

          <div class="col-12">
            <label class="form-label d-flex justify-content-between">
              Internal Remark
              <span class="char-count"><span id="remarkCnt">0</span>/500</span>
            </label>
            <textarea name="remark" id="remarkArea" class="form-control" rows="2"
              maxlength="500" placeholder="Notes for internal use...">{{ old('remark', $jobCard->remark) }}</textarea>
          </div>

          <div class="col-12">
            <div class="d-flex gap-4 pt-1">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="need_assistant" id="needAssistant"
                  {{ old('need_assistant', $jobCard->need_assistant) ? 'checked' : '' }} />
                <label class="form-check-label" for="needAssistant" style="font-size:.85rem">Needs Assistant</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="payment_received" id="paymentReceived"
                  {{ old('payment_received', $jobCard->payment_received) ? 'checked' : '' }} />
                <label class="form-check-label" for="paymentReceived" style="font-size:.85rem">Payment Received</label>
              </div>
            </div>
          </div>

        </div>
      </div>

    </div>{{-- /row --}}
  </div>
</div>

{{-- ── Save Bar ── --}}
<div class="jo-save-bar">
  <div style="font-size:.83rem;color:#888">
    <i class='bx bx-info-circle me-1'></i>Editing order <strong style="color:#696cff">{{ $jobCard->order_no }}</strong>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('admin.jobcards.index') }}" class="btn btn-outline-secondary" style="border-radius:10px;padding:10px 24px;font-weight:600">
      <i class='bx bx-x me-1'></i>Cancel
    </a>
    @if(!in_array($jobCard->status, ['Cancelled','Completed','Not Completed']))
    <button type="button" class="btn btn-outline-danger" style="border-radius:10px;padding:10px 24px;font-weight:600" onclick="cancelOrder()">
      <i class='bx bx-block me-1'></i>Cancel Order
    </button>
    @endif
    <button type="submit" class="btn-save-main btn">
      <i class='bx bx-save me-1'></i>Update Job Order
    </button>
  </div>
</div>

</form>

{{-- ── Quick-Add Modal ── --}}
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

{{-- ── Cancel Order Modal ── --}}
<div class="modal fade quick-add-modal" id="cancelOrderModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header" style="background:linear-gradient(135deg,#ff3e1d,#ff6b4a)">
        <h6 class="modal-title fw-bold mb-0"><i class='bx bx-block me-2'></i>Cancel Order {{ $jobCard->order_no }}</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:invert(1)"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-warning py-2 mb-3" style="font-size:.83rem">
          <i class='bx bx-error-circle me-1'></i>This action cannot be undone. The order will be permanently marked as <strong>Cancelled</strong>.
        </div>
        <label class="form-label fw-semibold">Reason for Cancellation <span class="text-danger">*</span></label>
        <textarea class="form-control" id="cancelReasonInput" rows="3"
          placeholder="e.g. Customer changed mind, device not worth repairing, duplicate entry..."
          maxlength="500" style="resize:vertical"></textarea>
        <div class="text-danger mt-1" id="cancelReasonError" style="font-size:.8rem;display:none"></div>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Keep Order</button>
        <button type="button" class="btn btn-danger btn-sm fw-bold" id="cancelOrderConfirm">
          <i class='bx bx-block me-1'></i>Yes, Cancel Order
        </button>
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
const brandsUrl = '{{ route("ajax.brands") }}';
const faultsUrl = '{{ route("ajax.faults") }}';

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
initCounter('issueArea', 'issueCnt');
initCounter('remarkArea', 'remarkCnt');

// ── Age Slider ──
(function () {
  const range  = document.getElementById('ageRange');
  const hidden = document.getElementById('deviceAgeInput');
  const badge  = document.getElementById('ageBadge');
  function updateAge(val) {
    val = parseInt(val); hidden.value = val;
    badge.textContent = val + '/10' + (val === 0 ? ' · New' : val === 10 ? ' · Very Old' : '');
    badge.classList.remove('age-mid','age-high');
    if (val >= 8)      badge.classList.add('age-high');
    else if (val >= 5) badge.classList.add('age-mid');
    const thumbColor = val === 0 ? '#696cff' : val >= 8 ? '#ff3e1d' : val >= 5 ? '#ffab00' : '#696cff';
    const pct = (val / 10) * 100;
    const fillColor = val === 0 ? '#e8e8f0' : val >= 8 ? '#ff3e1d' : val >= 5 ? '#ffab00' : '#696cff';
    range.style.background = `linear-gradient(to right, ${fillColor} ${pct}%, #e8e8f0 ${pct}%)`;
    let s = document.getElementById('age-thumb-style');
    if (!s) { s = document.createElement('style'); s.id = 'age-thumb-style'; document.head.appendChild(s); }
    s.textContent = `.age-range::-webkit-slider-thumb{border-color:${thumbColor}!important}.age-range::-moz-range-thumb{border-color:${thumbColor}!important}`;
  }
  range.addEventListener('input', () => updateAge(range.value));
  updateAge(range.value);
})();

// ── Device → Brand + Fault ──
// On page load, keep existing selections; only reload on change
$('#deviceSelect').on('change', function () {
  const device = $(this).val();
  if (!device) {
    $('#brandSelect').html('<option value="">-- Select Brand --</option>');
    $('#faultSelect').html('<option value="">-- Select Fault --</option>');
    return;
  }
  const selBrand = '{{ old("device_brand", $jobCard->device_brand) }}';
  const selFault = '{{ old("device_fault", $jobCard->device_fault) }}';
  $.getJSON(brandsUrl, { device_name: device }, function (data) {
    let o = '<option value="">-- Select Brand --</option>';
    data.forEach(b => { o += `<option value="${b.device_brand}" ${b.device_brand===selBrand?'selected':''}>${b.device_brand}</option>`; });
    $('#brandSelect').html(o);
  });
  $.getJSON(faultsUrl, { device_name: device }, function (data) {
    let o = '<option value="">-- Select Fault --</option>';
    data.forEach(f => { o += `<option value="${f.device_fault}" ${f.device_fault===selFault?'selected':''}>${f.device_fault}</option>`; });
    $('#faultSelect').html(o);
  });
});

// ── Quick-Add ──
let _qaTarget = null;
function openQuickAdd(label, selectId, fieldName) {
  _qaTarget = { label, selectId, fieldName };
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
  for (let o of sel.options) {
    if (o.value.toLowerCase() === val.toLowerCase()) { err.textContent = 'Already exists.'; err.style.display = ''; return; }
  }
  const opt = new Option(val, val, true, true);
  sel.appendChild(opt); sel.value = val;
  if (_qaTarget.selectId === 'deviceSelect') $(sel).trigger('change');
  bootstrap.Modal.getInstance(document.getElementById('quickAddModal')).hide();
});
document.getElementById('quickAddInput').addEventListener('keydown', e => { if (e.key==='Enter') document.getElementById('quickAddConfirm').click(); });

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
  const grid = document.getElementById('accGrid');
  const addBtn = grid.querySelector('.acc-add-btn');
  const label = document.createElement('label');
  label.className = 'acc-item';
  label.innerHTML = `<input type="checkbox" name="accessories_list[]" value="${val}" checked /><span>${val}</span>`;
  grid.insertBefore(label, addBtn);
  bootstrap.Modal.getInstance(document.getElementById('accAddModal')).hide();
});
document.getElementById('accAddInput').addEventListener('keydown', e => { if (e.key==='Enter') document.getElementById('accAddConfirm').click(); });

// ── Submit: combine accessories ──
document.getElementById('joForm').addEventListener('submit', function () {
  const checked = [...document.querySelectorAll('input[name="accessories_list[]"]:checked')].map(c => c.value);
  const h = document.createElement('input');
  h.type = 'hidden'; h.name = 'accessories'; h.value = checked.join(', ');
  this.appendChild(h);
});

// ── Cancel Order ──
function cancelOrder() {
  document.getElementById('cancelReasonInput').value = '';
  document.getElementById('cancelReasonError').style.display = 'none';
  new bootstrap.Modal(document.getElementById('cancelOrderModal')).show();
  setTimeout(() => document.getElementById('cancelReasonInput').focus(), 350);
}
document.getElementById('cancelOrderConfirm').addEventListener('click', function () {
  const reason = document.getElementById('cancelReasonInput').value.trim();
  const err    = document.getElementById('cancelReasonError');
  if (!reason) { err.textContent = 'Please enter a reason.'; err.style.display = ''; return; }
  const form = document.getElementById('joForm');
  // Inject cancel_reason hidden input
  let ri = form.querySelector('input[name="cancel_reason"]');
  if (!ri) { ri = document.createElement('input'); ri.type = 'hidden'; ri.name = 'cancel_reason'; form.appendChild(ri); }
  ri.value = reason;
  // Override status to Cancelled
  document.querySelector('select[name="status"]').removeAttribute('name');
  let si = form.querySelector('input[name="status"]');
  if (!si) { si = document.createElement('input'); si.type = 'hidden'; si.name = 'status'; form.appendChild(si); }
  si.value = 'Cancelled';
  bootstrap.Modal.getInstance(document.getElementById('cancelOrderModal')).hide();
  form.submit();
});
</script>
@endpush
