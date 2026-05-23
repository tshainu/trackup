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
  .jo-header .order-badge {
    font-size: 1.3rem;
    font-weight: 700;
    letter-spacing: 1px;
  }
  .jo-header .meta-pills {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    align-items: center;
  }
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

  /* ── Main form card ── */
  .jo-card {
    border: 0;
    border-radius: 0 0 14px 14px;
    box-shadow: 0 4px 24px rgba(108,92,231,.13);
    margin-bottom: 0;
  }

  /* ── Section headers inside card ── */
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
    font-size: 1rem;
    color: #696cff;
  }

  /* ── Priority selector ── */
  .priority-group {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
  }
  .priority-btn {
    flex: 1;
    min-width: 70px;
    text-align: center;
    padding: 8px 6px;
    border-radius: 10px;
    border: 2px solid #e0e0e0;
    cursor: pointer;
    font-size: .8rem;
    font-weight: 600;
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
    display: inline-block;
    width: 9px; height: 9px;
    border-radius: 50%;
    margin-right: 5px;
  }

  /* ── Accessories checkboxes ── */
  .acc-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
    gap: 8px;
  }
  .acc-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    border: 1.5px solid #e8e8f0;
    border-radius: 9px;
    cursor: pointer;
    font-size: .83rem;
    font-weight: 500;
    color: #555;
    transition: all .15s;
    background: #fafafa;
  }
  .acc-item:hover { border-color: #696cff; background: #f5f5ff; }
  .acc-item input[type=checkbox]:checked + .acc-label-inner { color: #696cff; }
  .acc-item:has(input:checked) {
    border-color: #696cff;
    background: #f0f0ff;
    color: #696cff;
  }
  .acc-item input[type=checkbox] { width: 16px; height: 16px; accent-color: #696cff; cursor: pointer; }

  /* ── Char count ── */
  .char-count { font-size: .74rem; color: #aaa; float: right; }

  /* ── Save bar ── */
  .jo-save-bar {
    background: #f8f8ff;
    border-radius: 12px;
    padding: 16px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 24px;
    border: 1px solid #ebebff;
  }
  .btn-save-main {
    background: linear-gradient(135deg, #696cff, #8c57ff);
    color: #fff;
    border: 0;
    padding: 10px 32px;
    border-radius: 10px;
    font-weight: 700;
    font-size: 1rem;
    transition: opacity .2s, transform .15s;
    box-shadow: 0 4px 14px rgba(108,92,231,.35);
  }
  .btn-save-main:hover { opacity: .9; transform: translateY(-1px); color: #fff; }
  .btn-save-main:active { transform: translateY(0); }

  /* ── Field enhancements ── */
  .form-control:focus, .form-select:focus {
    border-color: #696cff;
    box-shadow: 0 0 0 3px rgba(108,92,231,.12);
  }
  .form-label { font-weight: 600; font-size: .83rem; color: #444; margin-bottom: 5px; }
  .required-star { color: #ff3e1d; }

  /* ── Readonly fields ── */
  .field-readonly {
    background: linear-gradient(135deg,#f5f5ff,#fafafe) !important;
    border-color: #d5d5f5 !important;
    color: #696cff !important;
    font-weight: 700;
    letter-spacing: .5px;
  }
  .field-readonly-neutral {
    background: #f8f8f8 !important;
    color: #777 !important;
    border-color: #e0e0e0 !important;
  }

  /* ── Divider between panels ── */
  .panel-divider {
    width: 1px;
    background: linear-gradient(to bottom, transparent, #d5d5f5, transparent);
    align-self: stretch;
  }
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
            <label class="form-label">Customer Name <span class="required-star">*</span></label>
            <input type="text" name="customer_name" id="firstFocus"
              class="form-control @error('customer_name') is-invalid @enderror"
              value="{{ old('customer_name') }}"
              placeholder="Full name of customer" required autocomplete="off" />
            @error('customer_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-7">
            <label class="form-label">Phone Number <span class="required-star">*</span></label>
            <div class="input-group">
              <span class="input-group-text"><i class='bx bx-phone'></i></span>
              <input type="text" name="phone_no"
                class="form-control @error('phone_no') is-invalid @enderror"
                value="{{ old('phone_no') }}" placeholder="07X XXX XXXX" required />
            </div>
            @error('phone_no')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-5">
            <label class="form-label">NIC</label>
            <input type="text" name="customer_nic" class="form-control"
              value="{{ old('customer_nic') }}" placeholder="XXXXXXXXXX" />
          </div>

          <div class="col-12">
            <label class="form-label">Address</label>
            <input type="text" name="customer_address" class="form-control"
              value="{{ old('customer_address') }}" placeholder="Street, City" />
          </div>

          <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" name="customer_email" class="form-control"
              value="{{ old('customer_email') }}" placeholder="email@example.com" />
          </div>

          <div class="col-md-6">
            <label class="form-label">Date of Birth</label>
            <input type="text" name="customer_dob" class="form-control"
              value="{{ old('customer_dob') }}" placeholder="01/01/1990" />
          </div>

          {{-- Date Received + Estimated Delivery side by side --}}
          <div class="col-md-6">
            <label class="form-label">Date Received <span class="required-star">*</span></label>
            <input type="date" name="date"
              class="form-control @error('date') is-invalid @enderror"
              value="{{ old('date', date('Y-m-d')) }}" required />
            @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-6">
            <label class="form-label">Est. Delivery Date</label>
            <input type="date" name="estimated_delivery" class="form-control"
              value="{{ old('estimated_delivery') }}" />
          </div>

          {{-- Priority ── --}}
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
          <div class="col-12">
            <label class="form-label">Device Type <span class="required-star">*</span></label>
            <select name="device_name" id="deviceSelect"
              class="form-select @error('device_name') is-invalid @enderror" required>
              <option value="">-- Select Device --</option>
              @foreach($devices as $d)
                <option value="{{ $d->device_name }}" {{ old('device_name') == $d->device_name ? 'selected' : '' }}>{{ $d->device_name }}</option>
              @endforeach
            </select>
            @error('device_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-6">
            <label class="form-label">Brand</label>
            <select name="device_brand" id="brandSelect" class="form-select">
              <option value="">-- Select Brand --</option>
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label">Fault Type</label>
            <select name="device_fault" id="faultSelect" class="form-select">
              <option value="">-- Select Fault --</option>
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label">Serial / IMEI No</label>
            <input type="text" name="serial_no" class="form-control"
              value="{{ old('serial_no') }}" placeholder="Serial or IMEI" />
          </div>

          <div class="col-md-6">
            <label class="form-label">Device Age (yrs)</label>
            <input type="number" name="device_age" class="form-control"
              value="{{ old('device_age') }}" min="0" max="50" placeholder="e.g. 3" />
          </div>

          <div class="col-12">
            <label class="form-label d-flex justify-content-between">
              Issue Description
              <span class="char-count"><span id="issueCnt">0</span>/500</span>
            </label>
            <textarea name="issue" id="issueArea" class="form-control" rows="3"
              maxlength="500" placeholder="Customer's description of the problem...">{{ old('issue') }}</textarea>
          </div>

          <div class="col-md-6">
            <label class="form-label">Estimated Cost (Rs.)</label>
            <div class="input-group">
              <span class="input-group-text">Rs.</span>
              <input type="number" name="rupees" class="form-control"
                value="{{ old('rupees', 0) }}" min="0" step="0.01" />
            </div>
          </div>

          <div class="col-md-6">
            <label class="form-label">Assign to Employee</label>
            <select name="employee_id" class="form-select">
              <option value="">-- Unassigned --</option>
              @foreach($employees as $emp)
                <option value="{{ $emp->id }}" {{ old('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->employee_name }}</option>
              @endforeach
            </select>
          </div>

          {{-- Accessories ── --}}
          <div class="col-12">
            <label class="form-label">Accessories Received</label>
            <div class="acc-grid">
              @foreach(['Charger','Remote','Cover/Case','Battery','Power Cable','Earphones','Memory Card','Stylus','Other'] as $acc)
              <label class="acc-item">
                <input type="checkbox" name="accessories_list[]" value="{{ $acc }}"
                  {{ in_array($acc, old('accessories_list', [])) ? 'checked' : '' }} />
                <span class="acc-label-inner">{{ $acc }}</span>
              </label>
              @endforeach
            </div>
          </div>

          <div class="col-12">
            <label class="form-label d-flex justify-content-between">
              Internal Remark
              <span class="char-count"><span id="remarkCnt">0</span>/500</span>
            </label>
            <textarea name="remark" id="remarkArea" class="form-control" rows="2"
              maxlength="500" placeholder="Notes for internal use...">{{ old('remark') }}</textarea>
          </div>

          <div class="col-12">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" name="need_assistant"
                id="needAssistant" role="switch"
                {{ old('need_assistant') ? 'checked' : '' }} />
              <label class="form-check-label" for="needAssistant">
                <i class='bx bx-group me-1'></i>Needs Assistant Technician
              </label>
            </div>
          </div>
        </div>
      </div>

    </div>{{-- /row --}}
  </div>{{-- /card-body --}}
</div>{{-- /jo-card --}}

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
@endsection

@push('scripts')
<script>
const brandsUrl = '{{ route("ajax.brands") }}';
const faultsUrl = '{{ route("ajax.faults") }}';

// Auto-focus first field
document.getElementById('firstFocus')?.focus();

// Priority selector
function setPriority(p) {
  document.getElementById('priorityInput').value = p;
  document.querySelectorAll('.priority-btn').forEach(b => {
    b.className = 'priority-btn' + (b.dataset.priority === p ? ' active-' + p : '');
  });
}

// Char counters
function initCounter(textareaId, counterId) {
  const ta = document.getElementById(textareaId);
  const ct = document.getElementById(counterId);
  if (!ta || !ct) return;
  ct.textContent = ta.value.length;
  ta.addEventListener('input', () => ct.textContent = ta.value.length);
}
initCounter('issueArea',  'issueCnt');
initCounter('remarkArea', 'remarkCnt');

// Device → Brand + Fault dropdowns
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
});

// Accessories → combine into hidden input on submit
document.getElementById('joForm').addEventListener('submit', function () {
  const checked = [...document.querySelectorAll('input[name="accessories_list[]"]:checked')]
                    .map(c => c.value);
  const hidden = document.createElement('input');
  hidden.type = 'hidden';
  hidden.name = 'accessories';
  hidden.value = checked.join(', ');
  this.appendChild(hidden);
});
</script>
@endpush
