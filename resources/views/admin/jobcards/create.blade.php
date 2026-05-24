@extends('layouts.admin')
@section('title', 'New Job Order')
@section('page-title', 'New Job Order')
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.jobcards.index') }}">Job Orders</a></li>
  <li class="breadcrumb-item active">New</li>
@endsection

@push('styles')
<style>
/* ════════════════════════════════════════
   GLOBAL FORM TOKENS
════════════════════════════════════════ */
:root {
  --c-primary: #696cff;
  --c-primary-dark: #5a5bcc;
  --c-primary-soft: #f0f0ff;
  --c-primary-border: #dddeff;
  --c-danger: #ff3e1d;
  --c-warn: #ffab00;
  --c-success: #71dd37;
  --c-info: #03c3ec;
  --c-text: #3d3d4e;
  --c-muted: #9293a4;
  --c-border: #e6e6f0;
  --c-surface: #fafafa;
  --radius-card: 16px;
  --radius-input: 10px;
  --shadow-card: 0 4px 28px rgba(108,92,231,.10);
}

/* ── Typography ── */
.form-label {
  font-weight: 600;
  font-size: .82rem;
  color: var(--c-text);
  margin-bottom: 5px;
}
.required-star { color: var(--c-danger); }
.form-control, .form-select {
  border-radius: var(--radius-input);
  border-color: var(--c-border);
  font-size: .9rem;
  color: var(--c-text);
}
.form-control:focus, .form-select:focus {
  border-color: var(--c-primary);
  box-shadow: 0 0 0 3px rgba(108,92,231,.12);
}
.input-group-text {
  background: var(--c-primary-soft);
  border-color: var(--c-border);
  color: var(--c-primary);
}

/* ════════════════════════════════════════
   PAGE HEADER CARD
════════════════════════════════════════ */
.jo-page-header {
  background: linear-gradient(135deg, #696cff 0%, #8c57ff 55%, #a855f7 100%);
  border-radius: var(--radius-card);
  padding: 22px 28px;
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 12px;
  margin-bottom: 20px;
  box-shadow: 0 8px 24px rgba(108,92,231,.28);
}
.jo-page-header .order-no {
  font-size: 1.5rem;
  font-weight: 800;
  letter-spacing: 1.5px;
}
.jo-page-header .order-label {
  font-size: .7rem;
  opacity: .75;
  text-transform: uppercase;
  letter-spacing: .1em;
  margin-bottom: 2px;
}
.header-pills {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  align-items: center;
}
.h-pill {
  background: rgba(255,255,255,.18);
  border: 1px solid rgba(255,255,255,.28);
  border-radius: 20px;
  padding: 5px 14px;
  font-size: .78rem;
  font-weight: 500;
}
.h-pill-warn {
  background: rgba(255,171,0,.28);
  border-color: rgba(255,171,0,.5);
  color: #ffe17a;
  font-weight: 600;
}

/* ════════════════════════════════════════
   SECTION PANELS
════════════════════════════════════════ */
.jo-panel {
  background: #fff;
  border: 1px solid var(--c-border);
  border-radius: var(--radius-card);
  box-shadow: var(--shadow-card);
  overflow: hidden;
  margin-bottom: 20px;
}
.jo-panel-head {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 14px 22px;
  border-bottom: 1px solid var(--c-border);
  background: var(--c-primary-soft);
}
.jo-panel-head .ph-icon {
  width: 32px; height: 32px;
  border-radius: 9px;
  background: linear-gradient(135deg, rgba(108,92,231,.2), rgba(140,87,255,.2));
  display: flex; align-items: center; justify-content: center;
  font-size: 1rem; color: var(--c-primary);
  flex-shrink: 0;
}
.jo-panel-head .ph-title {
  font-weight: 700;
  font-size: .88rem;
  text-transform: uppercase;
  letter-spacing: .07em;
  color: var(--c-primary);
}
.jo-panel-head .ph-sub {
  font-size: .75rem;
  color: var(--c-muted);
  margin-left: auto;
}
.jo-panel-body {
  padding: 22px;
}

/* ════════════════════════════════════════
   PHONE SEARCH WIDGET
════════════════════════════════════════ */
.ps-widget {
  background: linear-gradient(135deg, #f7f7ff 0%, #ede9ff 100%);
  border: 1.5px dashed var(--c-primary);
  border-radius: 12px;
  padding: 16px 18px;
  margin-bottom: 20px;
  position: relative;
}
.ps-widget-label {
  display: flex;
  align-items: center;
  gap: 7px;
  font-size: .78rem;
  font-weight: 700;
  color: var(--c-primary);
  text-transform: uppercase;
  letter-spacing: .07em;
  margin-bottom: 10px;
}
.ps-widget-label .ps-tip {
  font-weight: 400;
  text-transform: none;
  letter-spacing: 0;
  color: var(--c-muted);
  font-size: .76rem;
  margin-left: 2px;
}
.ps-input-wrap { position: relative; }
.ps-input-wrap .form-control {
  border-radius: 10px;
  border: 1.5px solid var(--c-primary-border);
  padding-left: 40px;
  font-size: .9rem;
  transition: border-color .15s, box-shadow .15s;
}
.ps-input-wrap .form-control:focus {
  border-color: var(--c-primary);
  box-shadow: 0 0 0 3px rgba(108,92,231,.12);
}
.ps-input-icon {
  position: absolute;
  left: 12px; top: 50%;
  transform: translateY(-50%);
  color: var(--c-primary);
  font-size: 1.05rem;
  pointer-events: none;
  z-index: 2;
}
.ps-clear-btn {
  position: absolute;
  right: 10px; top: 50%;
  transform: translateY(-50%);
  background: none; border: none;
  color: var(--c-muted);
  font-size: 1.2rem;
  line-height: 1;
  cursor: pointer;
  padding: 2px;
  display: none;
  z-index: 2;
}
.ps-clear-btn:hover { color: var(--c-danger); }

/* Dropdown */
.ps-dropdown {
  position: absolute;
  top: calc(100% + 6px);
  left: 0; right: 0;
  background: #fff;
  border: 1.5px solid var(--c-primary-border);
  border-radius: 12px;
  box-shadow: 0 10px 32px rgba(108,92,231,.16);
  z-index: 1000;
  overflow: hidden;
  max-height: 300px;
  overflow-y: auto;
  display: none;
}
.ps-item {
  display: flex;
  align-items: center;
  gap: 11px;
  padding: 11px 16px;
  cursor: pointer;
  border-bottom: 1px solid #f3f3f9;
  transition: background .1s;
}
.ps-item:last-child { border-bottom: none; }
.ps-item:hover, .ps-item.active { background: var(--c-primary-soft); }
.ps-item .pi-avatar {
  width: 36px; height: 36px;
  border-radius: 50%;
  background: linear-gradient(135deg, #dddeff, #ede8ff);
  display: flex; align-items: center; justify-content: center;
  font-size: 1rem; color: var(--c-primary);
  flex-shrink: 0;
}
.ps-item .pi-info { flex: 1; min-width: 0; }
.ps-item .pi-name { font-weight: 700; font-size: .875rem; color: var(--c-text); }
.ps-item .pi-phone { font-size: .78rem; color: var(--c-muted); font-family: monospace; }
.ps-item .pi-addr { font-size: .75rem; color: #aaa; margin-top: 1px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.ps-badge {
  font-size: .7rem;
  font-weight: 700;
  padding: 3px 8px;
  border-radius: 20px;
  flex-shrink: 0;
}
.ps-badge.cust  { background: #edfbd8; color: #3a7c11; border: 1px solid #b7eda0; }
.ps-badge.hist  { background: #fff4d4; color: #8a5500; border: 1px solid #ffd97a; }
.ps-empty, .ps-loading {
  padding: 14px 16px;
  text-align: center;
  font-size: .84rem;
  color: var(--c-muted);
}
.ps-loading { color: var(--c-primary); }

/* Filled badge */
.ps-filled-strip {
  display: flex;
  align-items: center;
  gap: 6px;
  margin-top: 10px;
  padding: 7px 12px;
  background: #edfbd8;
  border: 1px solid #b7eda0;
  border-radius: 8px;
  font-size: .8rem;
  font-weight: 600;
  color: #3a7c11;
  display: none;
}
.ps-filled-strip i { font-size: 1rem; }
.ps-filled-strip .clear-link {
  margin-left: auto;
  font-size: .78rem;
  font-weight: 400;
  color: #5a9a32;
  text-decoration: underline;
  cursor: pointer;
  background: none; border: none; padding: 0;
}
.ps-filled-strip .clear-link:hover { color: var(--c-danger); }

/* Flash animation when fields are filled */
@keyframes fieldFlash {
  0%   { background: #e3f2fd; box-shadow: 0 0 0 3px rgba(108,92,231,.18); }
  100% { background: transparent; box-shadow: none; }
}
.field-flashed { animation: fieldFlash .55s ease forwards; }

/* ════════════════════════════════════════
   PRIORITY BUTTONS
════════════════════════════════════════ */
.priority-row { display: flex; gap: 8px; }
.prio-btn {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 4px;
  padding: 10px 6px;
  border-radius: 10px;
  border: 2px solid var(--c-border);
  cursor: pointer;
  font-size: .78rem;
  font-weight: 700;
  transition: all .16s;
  background: #fff;
  color: var(--c-muted);
  user-select: none;
}
.prio-btn:hover { transform: translateY(-2px); border-color: #aaa; }
.prio-dot { width: 10px; height: 10px; border-radius: 50%; }
.prio-btn.active-Low    { border-color: #71dd37; background: #edfbd8; color: #3a7c11; }
.prio-btn.active-Normal { border-color: #03c3ec; background: #d9f8fe; color: #0074a0; }
.prio-btn.active-High   { border-color: #ffab00; background: #fff4d4; color: #8a5500; }
.prio-btn.active-Urgent { border-color: #ff3e1d; background: #ffe0dc; color: #a00000; }

/* ════════════════════════════════════════
   AGE SLIDER
════════════════════════════════════════ */
.age-slider { -webkit-appearance: none; appearance: none; width: 100%; height: 7px; border-radius: 10px; background: #e8e8f0; outline: none; cursor: pointer; }
.age-slider::-webkit-slider-thumb { -webkit-appearance: none; width: 22px; height: 22px; border-radius: 50%; background: #fff; border: 3px solid var(--c-primary); box-shadow: 0 2px 8px rgba(108,92,231,.3); cursor: grab; }
.age-slider::-webkit-slider-thumb:active { cursor: grabbing; transform: scale(1.15); }
.age-slider::-moz-range-thumb { width: 22px; height: 22px; border-radius: 50%; background: #fff; border: 3px solid var(--c-primary); box-shadow: 0 2px 8px rgba(108,92,231,.3); }
.age-badge {
  min-width: 60px; text-align: center;
  font-size: .78rem; font-weight: 700;
  padding: 4px 10px; border-radius: 20px;
  background: var(--c-primary-soft); color: var(--c-primary);
  border: 1.5px solid var(--c-primary-border);
}
.age-badge.mid  { background: #fff4d4; color: #8a5500; border-color: #ffd97a; }
.age-badge.high { background: #ffe0dc; color: #a00000; border-color: #ff9980; }
.age-ticks { display: flex; justify-content: space-between; padding: 3px 11px 0; font-size: .66rem; color: #ccc; }

/* ════════════════════════════════════════
   ACCESSORIES GRID
════════════════════════════════════════ */
.acc-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 8px; }
.acc-chip {
  display: flex; align-items: center; gap: 7px;
  padding: 8px 11px;
  border: 1.5px solid var(--c-border);
  border-radius: 9px;
  cursor: pointer;
  font-size: .82rem; font-weight: 500; color: #555;
  background: var(--c-surface);
  transition: all .14s;
  user-select: none;
}
.acc-chip:hover { border-color: var(--c-primary); background: var(--c-primary-soft); }
.acc-chip:has(input:checked) { border-color: var(--c-primary); background: var(--c-primary-soft); color: var(--c-primary); }
.acc-chip input[type=checkbox] { width: 15px; height: 15px; accent-color: var(--c-primary); }
.acc-add-chip {
  display: flex; align-items: center; justify-content: center; gap: 5px;
  padding: 8px 11px;
  border: 1.5px dashed #c0c0e0;
  border-radius: 9px;
  cursor: pointer;
  font-size: .82rem; font-weight: 600; color: var(--c-primary);
  background: #fff;
  transition: all .14s;
}
.acc-add-chip:hover { border-color: var(--c-primary); background: var(--c-primary-soft); }

/* ════════════════════════════════════════
   SELECT + ADD ROW
════════════════════════════════════════ */
.sel-add-row { display: flex; align-items: center; gap: 8px; }
.sel-add-row .form-select { flex: 1; }
.btn-circle-add {
  width: 32px; height: 32px; flex-shrink: 0;
  border-radius: 50%;
  border: 2px solid var(--c-primary);
  background: #fff;
  color: var(--c-primary);
  display: flex; align-items: center; justify-content: center;
  font-size: 1.1rem; font-weight: 700;
  cursor: pointer;
  transition: all .14s;
  line-height: 1;
}
.btn-circle-add:hover { background: var(--c-primary); color: #fff; }

/* ════════════════════════════════════════
   CHAR COUNTER
════════════════════════════════════════ */
.char-count { font-size: .72rem; color: #bbb; float: right; font-weight: 400; }

/* ════════════════════════════════════════
   SAVE BAR
════════════════════════════════════════ */
.jo-save-bar {
  background: #fff;
  border: 1px solid var(--c-border);
  border-radius: var(--radius-card);
  padding: 16px 24px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 12px;
  box-shadow: var(--shadow-card);
}
.btn-save {
  background: linear-gradient(135deg, #696cff, #8c57ff);
  color: #fff; border: 0;
  padding: 11px 36px;
  border-radius: 10px;
  font-weight: 700; font-size: .95rem;
  box-shadow: 0 4px 14px rgba(108,92,231,.32);
  transition: opacity .2s, transform .15s;
}
.btn-save:hover { opacity: .9; transform: translateY(-1px); color: #fff; }
.btn-cancel {
  padding: 11px 24px;
  border-radius: 10px;
  font-weight: 600;
  font-size: .9rem;
}

/* ════════════════════════════════════════
   QUICK-ADD MODALS
════════════════════════════════════════ */
.qa-modal .modal-content { border-radius: 14px; border: 0; box-shadow: 0 8px 40px rgba(0,0,0,.15); }
.qa-modal .modal-header { background: linear-gradient(135deg, #696cff, #8c57ff); color: #fff; border-radius: 14px 14px 0 0; padding: 14px 18px; }
.qa-modal .modal-header .btn-close { filter: invert(1) opacity(.8); }
.qa-modal .modal-footer { border-top: 0; padding-top: 0; }

/* ════════════════════════════════════════
   DIVIDER LABEL (between field groups)
════════════════════════════════════════ */
.field-divider {
  display: flex;
  align-items: center;
  gap: 10px;
  margin: 6px 0 14px;
  font-size: .72rem;
  font-weight: 700;
  color: var(--c-muted);
  text-transform: uppercase;
  letter-spacing: .07em;
}
.field-divider::before, .field-divider::after {
  content: '';
  flex: 1;
  height: 1px;
  background: var(--c-border);
}
</style>
@endpush

@section('content')
<form action="{{ route('admin.jobcards.store') }}" method="POST" id="joForm">
@csrf

{{-- ═══ PAGE HEADER ═══ --}}
<div class="jo-page-header">
  <div>
    <div class="order-label">Job Order Number</div>
    <div class="order-no"># {{ $orderNo }}</div>
  </div>
  <div class="header-pills">
    <span class="h-pill"><i class='bx bx-calendar me-1'></i>{{ date('d M Y') }}</span>
    <span class="h-pill"><i class='bx bx-user-circle me-1'></i>{{ $customerId }}</span>
    <span class="h-pill h-pill-warn"><i class='bx bx-time-five me-1'></i>Pending</span>
  </div>
</div>

<div class="row g-4">

  {{-- ══════════════════════════════════════
       LEFT COLUMN — Customer Information
  ══════════════════════════════════════ --}}
  <div class="col-xl-5 col-lg-6">

    {{-- CUSTOMER PANEL --}}
    <div class="jo-panel">
      <div class="jo-panel-head">
        <div class="ph-icon"><i class='bx bx-user'></i></div>
        <div class="ph-title">Customer Information</div>
        <div class="ph-sub">Fill or search below</div>
      </div>
      <div class="jo-panel-body">

        {{-- ─── Phone Search Widget ─── --}}
        <div class="ps-widget" id="psWidget">
          <div class="ps-widget-label">
            <i class='bx bx-search-alt-2'></i>
            Quick Search
            <span class="ps-tip">— type phone to load an existing customer</span>
          </div>
          <div class="ps-input-wrap" id="psInputWrap">
            <i class='bx bx-phone-call ps-input-icon'></i>
            <input type="text" id="psInput" class="form-control"
              placeholder="Search by phone number…" autocomplete="off" />
            <button type="button" id="psClearBtn" class="ps-clear-btn" title="Clear">
              <i class='bx bx-x'></i>
            </button>
            <div class="ps-dropdown" id="psDropdown"></div>
          </div>
          <div class="ps-filled-strip" id="psFilledStrip">
            <i class='bx bx-check-circle'></i>
            <span id="psFilledName">Existing customer loaded</span>
            <button type="button" class="clear-link" onclick="clearCustomerFill()">Clear &amp; reset</button>
          </div>
        </div>

        {{-- ─── Core fields ─── --}}
        <div class="row g-3">

          <div class="col-12">
            <label for="firstFocus" class="form-label">Full Name <span class="required-star">*</span></label>
            <div class="input-group">
              <span class="input-group-text"><i class='bx bx-user'></i></span>
              <input type="text" name="customer_name" id="firstFocus"
                class="form-control @error('customer_name') is-invalid @enderror"
                value="{{ old('customer_name') }}"
                placeholder="Customer's full name" required autocomplete="off" />
            </div>
            @error('customer_name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
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
            <label for="customer_nic" class="form-label">NIC / ID</label>
            <input type="text" name="customer_nic" id="customer_nic" class="form-control"
              value="{{ old('customer_nic') }}" placeholder="XXXXXXXXXX" />
          </div>

          <div class="col-12">
            <label for="customer_address" class="form-label">Address</label>
            <div class="input-group">
              <span class="input-group-text"><i class='bx bx-map-pin'></i></span>
              <input type="text" name="customer_address" id="customer_address" class="form-control"
                value="{{ old('customer_address') }}" placeholder="Street, City" />
            </div>
          </div>

          <div class="col-md-6">
            <label for="customer_email" class="form-label">Email</label>
            <div class="input-group">
              <span class="input-group-text"><i class='bx bx-envelope'></i></span>
              <input type="email" name="customer_email" id="customer_email" class="form-control"
                value="{{ old('customer_email') }}" placeholder="email@example.com" />
            </div>
          </div>

          <div class="col-md-6">
            <label for="customer_dob" class="form-label">Date of Birth</label>
            <input type="text" name="customer_dob" id="customer_dob" class="form-control"
              value="{{ old('customer_dob') }}" placeholder="YYYY-MM-DD" />
          </div>

        </div>
      </div>
    </div>

    {{-- JOB SCHEDULE PANEL --}}
    <div class="jo-panel">
      <div class="jo-panel-head">
        <div class="ph-icon"><i class='bx bx-calendar-check'></i></div>
        <div class="ph-title">Schedule &amp; Priority</div>
      </div>
      <div class="jo-panel-body">
        <div class="row g-3">

          <div class="col-md-6">
            <label for="date_received" class="form-label">Date Received <span class="required-star">*</span></label>
            <input type="date" name="date" id="date_received"
              class="form-control @error('date') is-invalid @enderror"
              value="{{ old('date', date('Y-m-d')) }}" required />
            @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-6">
            <label for="estimated_delivery" class="form-label">Est. Delivery</label>
            <input type="date" name="estimated_delivery" id="estimated_delivery" class="form-control"
              value="{{ old('estimated_delivery') }}" />
          </div>

          <div class="col-12">
            <label class="form-label d-block">Priority</label>
            <div class="priority-row" id="priorityRow">
              @foreach(['Low'=>['#71dd37','bx-down-arrow-circle'],'Normal'=>['#03c3ec','bx-minus-circle'],'High'=>['#ffab00','bx-up-arrow-circle'],'Urgent'=>['#ff3e1d','bx-error-circle']] as $p => $meta)
              <div class="prio-btn {{ old('priority','Normal') === $p ? 'active-'.$p : '' }}"
                   data-priority="{{ $p }}" onclick="setPriority('{{ $p }}')">
                <span class="prio-dot" style="background:{{ $meta[0] }}"></span>
                {{ $p }}
              </div>
              @endforeach
            </div>
            <input type="hidden" name="priority" id="priorityInput" value="{{ old('priority','Normal') }}" />
          </div>

          <div class="col-md-6">
            <label for="employee_id" class="form-label">Assign To</label>
            <div class="input-group">
              <span class="input-group-text"><i class='bx bx-wrench'></i></span>
              <select name="employee_id" id="employee_id" class="form-select">
                <option value="">— Unassigned —</option>
                @foreach($employees as $emp)
                  <option value="{{ $emp->id }}" {{ old('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->employee_name }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="col-md-6">
            <label class="form-label">Advance Paid (Rs.)</label>
            <div class="input-group">
              <span class="input-group-text">Rs.</span>
              <input type="number" name="advance_amount" id="advance_amount" class="form-control clear-on-zero"
                value="{{ old('advance_amount', 0) }}" min="0" step="0.01" />
            </div>
          </div>

        </div>
      </div>
    </div>

  </div>

  {{-- ══════════════════════════════════════
       RIGHT COLUMN — Device & Repair
  ══════════════════════════════════════ --}}
  <div class="col-xl-7 col-lg-6">

    {{-- DEVICE PANEL --}}
    <div class="jo-panel">
      <div class="jo-panel-head">
        <div class="ph-icon"><i class='bx bx-chip'></i></div>
        <div class="ph-title">Device Details</div>
        <div class="ph-sub">Select or add new types</div>
      </div>
      <div class="jo-panel-body">
        <div class="row g-3">

          <div class="col-12">
            <label for="deviceSelect" class="form-label">Device Type <span class="required-star">*</span></label>
            <div class="sel-add-row">
              <select name="device_name" id="deviceSelect"
                class="form-select @error('device_name') is-invalid @enderror" required>
                <option value="">— Select Device —</option>
                @foreach($devices as $d)
                  <option value="{{ $d->device_name }}" {{ old('device_name') == $d->device_name ? 'selected' : '' }}>{{ $d->device_name }}</option>
                @endforeach
              </select>
              <button type="button" class="btn-circle-add" title="Add device type"
                onclick="openQuickAdd('Device Type','deviceSelect','device_name')">+</button>
            </div>
            @error('device_name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-6">
            <label for="brandSelect" class="form-label">Brand</label>
            <div class="sel-add-row">
              <select name="device_brand" id="brandSelect" class="form-select">
                <option value="">— Select Brand —</option>
              </select>
              <button type="button" class="btn-circle-add" title="Add brand"
                onclick="openQuickAdd('Brand','brandSelect','device_brand')">+</button>
            </div>
          </div>

          <div class="col-md-6">
            <label for="faultSelect" class="form-label">Fault Type</label>
            <div class="sel-add-row">
              <select name="device_fault" id="faultSelect" class="form-select">
                <option value="">— Select Fault —</option>
              </select>
              <button type="button" class="btn-circle-add" title="Add fault"
                onclick="openQuickAdd('Fault Type','faultSelect','device_fault')">+</button>
            </div>
          </div>

          <div class="col-md-6">
            <label for="serial_no" class="form-label">Serial / IMEI</label>
            <div class="input-group">
              <span class="input-group-text"><i class='bx bx-barcode'></i></span>
              <input type="text" name="serial_no" id="serial_no" class="form-control"
                value="{{ old('serial_no') }}" placeholder="Serial or IMEI number" />
            </div>
          </div>

          <div class="col-md-6">
            <label class="form-label d-flex justify-content-between align-items-center">
              Device Age
              <small class="text-muted fw-normal" style="font-size:.7rem">0 = New &nbsp;·&nbsp; 10 = Very Old</small>
            </label>
            <input type="hidden" name="device_age" id="deviceAgeInput" value="{{ old('device_age', 0) }}" />
            <div class="d-flex align-items-center gap-2 mt-1">
              <input type="range" class="age-slider" id="ageRange" min="0" max="10" step="1" value="{{ old('device_age', 0) }}" />
              <span class="age-badge" id="ageBadge">—</span>
            </div>
            <div class="age-ticks">
              @for($i = 0; $i <= 10; $i++) <span>{{ $i }}</span> @endfor
            </div>
          </div>

          <div class="col-12">
            <label for="itemDescArea" class="form-label d-flex justify-content-between">
              Item Description
              <span class="char-count"><span id="itemDescCnt">0</span>/500</span>
            </label>
            <textarea name="item_description" id="itemDescArea" class="form-control" rows="2"
              maxlength="500"
              placeholder="Physical condition, colour, model, markings…">{{ old('item_description') }}</textarea>
          </div>

        </div>
      </div>
    </div>

    {{-- REPAIR PANEL --}}
    <div class="jo-panel">
      <div class="jo-panel-head">
        <div class="ph-icon"><i class='bx bx-wrench'></i></div>
        <div class="ph-title">Repair Details</div>
      </div>
      <div class="jo-panel-body">
        <div class="row g-3">

          <div class="col-12">
            <label for="issueArea" class="form-label d-flex justify-content-between">
              Issue Description
              <span class="char-count"><span id="issueCnt">0</span>/500</span>
            </label>
            <textarea name="issue" id="issueArea" class="form-control" rows="3"
              maxlength="500" placeholder="Customer's description of the problem…">{{ old('issue') }}</textarea>
          </div>

          <div class="col-md-6">
            <label for="rupees" class="form-label">Estimated Cost</label>
            <div class="input-group">
              <span class="input-group-text">Rs.</span>
              <input type="number" name="rupees" id="rupees" class="form-control clear-on-zero"
                value="{{ old('rupees', 0) }}" min="0" step="0.01" />
            </div>
          </div>

          <div class="col-md-6">
            <label class="form-label">Accessories Received</label>
            <div class="acc-grid" id="accGrid">
              @php $oldAccs = old('accessories_list', []); @endphp
              @foreach(\App\Models\DeviceAccessory::orderBy('accessory_name')->get() as $acc)
              <label class="acc-chip acc-static" data-value="{{ $acc->accessory_name }}">
                <input type="checkbox" name="accessories_list[]" value="{{ $acc->accessory_name }}"
                  {{ in_array($acc->accessory_name, $oldAccs) ? 'checked' : '' }} />
                {{ $acc->accessory_name }}
              </label>
              @endforeach
              <div class="acc-add-chip" onclick="openAccAdd()"><i class='bx bx-plus'></i> Add</div>
            </div>
          </div>

          <div class="col-12">
            <label for="remarkArea" class="form-label d-flex justify-content-between">
              Internal Remark
              <span class="char-count"><span id="remarkCnt">0</span>/500</span>
            </label>
            <textarea name="remark" id="remarkArea" class="form-control" rows="2"
              maxlength="500" placeholder="Internal notes, not shown to customer…">{{ old('remark') }}</textarea>
          </div>

        </div>
      </div>
    </div>

  </div>
</div>{{-- /row --}}

{{-- ═══ SAVE BAR ═══ --}}
<div class="jo-save-bar">
  <div style="font-size:.83rem;color:var(--c-muted)">
    <i class='bx bx-info-circle me-1' style="color:var(--c-primary)"></i>
    Order <strong style="color:var(--c-primary)">{{ $orderNo }}</strong> will be auto-assigned on save.
  </div>
  <div class="d-flex gap-2 align-items-center">
    <a href="{{ route('admin.jobcards.index') }}" class="btn btn-outline-secondary btn-cancel">
      <i class='bx bx-x me-1'></i>Cancel
    </a>
    <button type="submit" class="btn btn-save">
      <i class='bx bx-save me-1'></i>Save Job Order
    </button>
  </div>
</div>

</form>

{{-- ═══ QUICK-ADD MODAL ═══ --}}
<div class="modal fade qa-modal" id="quickAddModal" tabindex="-1">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title fw-bold mb-0" id="qaTitle">Add New</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <label class="form-label" id="qaLabel">Value</label>
        <input type="text" class="form-control" id="qaInput" placeholder="Type here…" />
        <div class="text-danger mt-1" id="qaError" style="font-size:.8rem;display:none"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary btn-sm" id="qaConfirm"
          style="background:linear-gradient(135deg,#696cff,#8c57ff);border:0">Add</button>
      </div>
    </div>
  </div>
</div>

{{-- ═══ ACCESSORY ADD MODAL ═══ --}}
<div class="modal fade qa-modal" id="accModal" tabindex="-1">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title fw-bold mb-0">Add Accessory</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <label class="form-label">Accessory Name</label>
        <input type="text" class="form-control" id="accInput" placeholder="e.g. Remote, Stand…" />
        <div class="text-danger mt-1" id="accError" style="font-size:.8rem;display:none"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary btn-sm" id="accConfirm"
          style="background:linear-gradient(135deg,#696cff,#8c57ff);border:0">Add</button>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
const BRANDS_URL    = '{{ route("ajax.brands") }}';
const FAULTS_URL    = '{{ route("ajax.faults") }}';
const CUST_URL      = '{{ route("ajax.customer-lookup") }}';

// ── Auto focus ──
document.getElementById('firstFocus')?.focus();

// ── Priority ──
function setPriority(p) {
  document.getElementById('priorityInput').value = p;
  document.querySelectorAll('.prio-btn').forEach(b => {
    b.className = 'prio-btn' + (b.dataset.priority === p ? ' active-' + p : '');
  });
}

// ── Char counters ──
['issueArea:issueCnt','remarkArea:remarkCnt','itemDescArea:itemDescCnt'].forEach(pair => {
  const [taId, ctId] = pair.split(':');
  const ta = document.getElementById(taId), ct = document.getElementById(ctId);
  if (!ta || !ct) return;
  ct.textContent = ta.value.length;
  ta.addEventListener('input', () => ct.textContent = ta.value.length);
});

// ── Clear-on-zero ──
document.querySelectorAll('.clear-on-zero').forEach(el => {
  el.addEventListener('focus', () => { if (parseFloat(el.value) === 0) el.value = ''; });
  el.addEventListener('blur',  () => { if (el.value === '') el.value = '0'; });
});

// ── Age Slider ──
(function() {
  const range  = document.getElementById('ageRange');
  const hidden = document.getElementById('deviceAgeInput');
  const badge  = document.getElementById('ageBadge');
  function update(v) {
    v = +v;
    hidden.value = v;
    badge.textContent = v === 0 ? 'New' : v === 10 ? 'Very Old' : v + '/10';
    badge.className = 'age-badge' + (v >= 8 ? ' high' : v >= 5 ? ' mid' : '');
    const pct = (v / 10) * 100;
    const col = v >= 8 ? '#ff3e1d' : v >= 5 ? '#ffab00' : '#696cff';
    range.style.background = `linear-gradient(to right, ${col} ${pct}%, #e8e8f0 ${pct}%)`;
    // update thumb colour
    let s = document.getElementById('_age_thumb_style');
    if (!s) { s = document.createElement('style'); s.id = '_age_thumb_style'; document.head.appendChild(s); }
    s.textContent = `.age-slider::-webkit-slider-thumb{border-color:${col}!important}.age-slider::-moz-range-thumb{border-color:${col}!important}`;
  }
  range.addEventListener('input', () => update(range.value));
  update(range.value);
})();

// ── Device → Brand & Fault ──
$('#deviceSelect').on('change', function() {
  const d = $(this).val();
  if (!d) {
    $('#brandSelect').html('<option value="">— Select Brand —</option>');
    $('#faultSelect').html('<option value="">— Select Fault —</option>');
    return;
  }
  $('#brandSelect').html('<option>Loading…</option>');
  $('#faultSelect').html('<option>Loading…</option>');
  $.getJSON(BRANDS_URL, { device_name: d }, function(data) {
    let o = '<option value="">— Select Brand —</option>';
    data.forEach(b => o += `<option value="${b.device_brand}">${b.device_brand}</option>`);
    $('#brandSelect').html(o);
  });
  $.getJSON(FAULTS_URL, { device_name: d }, function(data) {
    let o = '<option value="">— Select Fault —</option>';
    data.forEach(f => o += `<option value="${f.device_fault}">${f.device_fault}</option>`);
    $('#faultSelect').html(o);
  });
});
$(document).ready(() => { if ($('#deviceSelect').val()) $('#deviceSelect').trigger('change'); });

// ── Quick-Add Modal ──
let _qa = null;
function openQuickAdd(label, selectId, fieldName) {
  _qa = { label, selectId, fieldName };
  document.getElementById('qaTitle').textContent  = 'Add ' + label;
  document.getElementById('qaLabel').textContent  = label + ' Name';
  document.getElementById('qaInput').value        = '';
  document.getElementById('qaError').style.display = 'none';
  new bootstrap.Modal(document.getElementById('quickAddModal')).show();
  setTimeout(() => document.getElementById('qaInput').focus(), 300);
}
document.getElementById('qaConfirm').addEventListener('click', () => {
  const val = document.getElementById('qaInput').value.trim();
  const err = document.getElementById('qaError');
  if (!val) { err.textContent = 'Enter a value.'; err.style.display = ''; return; }
  const sel = document.getElementById(_qa.selectId);
  for (const o of sel.options) {
    if (o.value.toLowerCase() === val.toLowerCase()) {
      err.textContent = 'Already exists.'; err.style.display = ''; return;
    }
  }
  sel.appendChild(new Option(val, val, true, true));
  if (_qa.selectId === 'deviceSelect') $(sel).trigger('change');
  bootstrap.Modal.getInstance(document.getElementById('quickAddModal')).hide();
});
document.getElementById('qaInput').addEventListener('keydown', e => { if (e.key === 'Enter') document.getElementById('qaConfirm').click(); });

// ── Accessory Add Modal ──
function openAccAdd() {
  document.getElementById('accInput').value = '';
  document.getElementById('accError').style.display = 'none';
  new bootstrap.Modal(document.getElementById('accModal')).show();
  setTimeout(() => document.getElementById('accInput').focus(), 300);
}
document.getElementById('accConfirm').addEventListener('click', () => {
  const val = document.getElementById('accInput').value.trim();
  const err = document.getElementById('accError');
  if (!val) { err.textContent = 'Enter a name.'; err.style.display = ''; return; }
  const grid = document.getElementById('accGrid');
  const addBtn = grid.querySelector('.acc-add-chip');
  const label = document.createElement('label');
  label.className = 'acc-chip';
  label.innerHTML = `<input type="checkbox" name="accessories_list[]" value="${escH(val)}" checked />${escH(val)}`;
  grid.insertBefore(label, addBtn);
  bootstrap.Modal.getInstance(document.getElementById('accModal')).hide();
});
document.getElementById('accInput').addEventListener('keydown', e => { if (e.key === 'Enter') document.getElementById('accConfirm').click(); });

// ── Form submit: flatten accessories ──
document.getElementById('joForm').addEventListener('submit', function() {
  const vals = [...document.querySelectorAll('input[name="accessories_list[]"]:checked')].map(c => c.value);
  const h = document.createElement('input');
  h.type = 'hidden'; h.name = 'accessories'; h.value = vals.join(', ');
  this.appendChild(h);
});

// ══════════════════════════════════════
//   PHONE SEARCH WIDGET
// ══════════════════════════════════════
(function() {
  const input       = document.getElementById('psInput');
  const dropdown    = document.getElementById('psDropdown');
  const clearBtn    = document.getElementById('psClearBtn');
  const filledStrip = document.getElementById('psFilledStrip');
  const filledName  = document.getElementById('psFilledName');

  let timer = null, lastQ = '', _results = [];

  function showDrop(html) { dropdown.innerHTML = html; dropdown.style.display = 'block'; }
  function hideDrop()      { dropdown.style.display = 'none'; dropdown.innerHTML = ''; }

  function fillFields(item) {
    const map = {
      firstFocus       : item.name    || '',
      phone_no         : item.phone   || '',
      customer_nic     : item.nic     || '',
      customer_address : item.address || '',
      customer_email   : item.email   || '',
      customer_dob     : item.dob     || '',
    };
    Object.entries(map).forEach(([id, val]) => {
      const el = document.getElementById(id);
      if (!el) return;
      el.value = val;
      el.classList.remove('field-flashed');
      void el.offsetWidth;
      el.classList.add('field-flashed');
      setTimeout(() => el.classList.remove('field-flashed'), 600);
    });
    input.value = item.phone;
    clearBtn.style.display = 'block';
    filledName.textContent = (item.name || 'Customer') + ' loaded';
    filledStrip.style.display = 'flex';
    hideDrop();
  }

  function renderResults(res) {
    _results = res;
    if (!res.length) {
      showDrop('<div class="ps-empty"><i class="bx bx-search-alt me-1"></i>No customers found</div>');
      return;
    }
    let html = '';
    res.forEach((r, i) => {
      const badge = r.source === 'customers'
        ? '<span class="ps-badge cust">Customer</span>'
        : '<span class="ps-badge hist">History</span>';
      html += `<div class="ps-item" data-idx="${i}">
        <div class="pi-avatar"><i class="bx bx-user"></i></div>
        <div class="pi-info">
          <div class="pi-name">${escH(r.name)}</div>
          <div class="pi-phone">${escH(r.phone)}</div>
          ${r.address ? `<div class="pi-addr">${escH(r.address)}</div>` : ''}
        </div>
        ${badge}
      </div>`;
    });
    showDrop(html);
  }

  function doSearch(q) {
    if (q === lastQ) return;
    lastQ = q;
    if (q.length < 2) { hideDrop(); return; }
    showDrop('<div class="ps-loading"><i class="bx bx-loader-alt bx-spin me-1"></i>Searching…</div>');
    $.getJSON(CUST_URL, { phone: q, multi: 1 }, renderResults)
     .fail(() => showDrop('<div class="ps-empty text-danger">Search failed. Try again.</div>'));
  }

  // Click result
  $(document).on('click', '.ps-item', function() {
    fillFields(_results[+this.dataset.idx]);
  });

  // Hover highlight
  $(document).on('mouseenter', '.ps-item', function() {
    document.querySelectorAll('.ps-item').forEach(el => el.classList.remove('active'));
    this.classList.add('active');
  });

  // Typing
  input.addEventListener('input', function() {
    const q = this.value.trim();
    clearBtn.style.display = q ? 'block' : 'none';
    clearTimeout(timer);
    if (!q) { hideDrop(); lastQ = ''; return; }
    timer = setTimeout(() => doSearch(q), 280);
  });

  // Re-open dropdown on focus if already typed
  input.addEventListener('focus', () => {
    if (input.value.trim().length >= 2 && _results.length) dropdown.style.display = 'block';
  });

  // Clear btn
  clearBtn.addEventListener('click', () => window.clearCustomerFill());

  // Keyboard nav
  input.addEventListener('keydown', function(e) {
    const items = [...dropdown.querySelectorAll('.ps-item')];
    const cur   = dropdown.querySelector('.ps-item.active');
    if (e.key === 'ArrowDown') {
      e.preventDefault();
      const idx = cur ? items.indexOf(cur) : -1;
      if (cur) cur.classList.remove('active');
      const next = items[Math.min(idx + 1, items.length - 1)];
      if (next) { next.classList.add('active'); next.scrollIntoView({ block: 'nearest' }); }
    } else if (e.key === 'ArrowUp') {
      e.preventDefault();
      const idx = cur ? items.indexOf(cur) : items.length;
      if (cur) cur.classList.remove('active');
      const prev = items[Math.max(idx - 1, 0)];
      if (prev) { prev.classList.add('active'); prev.scrollIntoView({ block: 'nearest' }); }
    } else if (e.key === 'Enter' && cur) {
      e.preventDefault();
      fillFields(_results[items.indexOf(cur)]);
    } else if (e.key === 'Escape') {
      hideDrop();
    }
  });

  // Outside click
  document.addEventListener('click', e => {
    if (!document.getElementById('psWidget').contains(e.target)) hideDrop();
  });
})();

window.clearCustomerFill = function() {
  ['firstFocus','phone_no','customer_nic','customer_address','customer_email','customer_dob']
    .forEach(id => { const el = document.getElementById(id); if (el) el.value = ''; });
  document.getElementById('psInput').value = '';
  document.getElementById('psClearBtn').style.display = 'none';
  document.getElementById('psFilledStrip').style.display = 'none';
  document.getElementById('psDropdown').style.display = 'none';
  document.getElementById('firstFocus').focus();
};

function escH(s) {
  return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}
</script>
@endpush
