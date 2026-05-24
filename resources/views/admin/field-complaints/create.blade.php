@extends('layouts.admin')
@section('title', 'New Field Complaint')

@push('styles')
<style>
  /* ══ Customer search widget ══ */
  :root {
    --c-primary: #696cff;
    --c-primary-border: #c5c7ff;
    --c-primary-soft: #f0f0ff;
    --c-text: #566a7f;
    --c-muted: #a1acbb;
    --c-border: #d9dee3;
    --c-danger: #ff3e1d;
  }
  .ps-widget {
    background: linear-gradient(135deg,#f7f7ff,#ede9ff);
    border: 1.5px dashed var(--c-primary);
    border-radius: 12px;
    padding: 16px 18px;
    margin-bottom: 20px;
    position: relative;
  }
  .ps-widget-label {
    display: flex; align-items: center; gap: 7px;
    font-size: .78rem; font-weight: 700; color: var(--c-primary);
    text-transform: uppercase; letter-spacing: .07em; margin-bottom: 10px;
  }
  .ps-widget-label .ps-tip {
    font-weight: 400; text-transform: none; letter-spacing: 0;
    color: var(--c-muted); font-size: .76rem; margin-left: 2px;
  }
  .ps-input-wrap { position: relative; }
  .ps-input-wrap .form-control {
    border-radius: 10px; border: 1.5px solid var(--c-primary-border);
    padding-left: 40px; font-size: .9rem;
    transition: border-color .15s, box-shadow .15s;
  }
  .ps-input-wrap .form-control:focus {
    border-color: var(--c-primary); box-shadow: 0 0 0 3px rgba(105,108,255,.12);
  }
  .ps-input-icon {
    position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
    color: var(--c-primary); font-size: 1.05rem; pointer-events: none; z-index: 2;
  }
  .ps-clear-btn {
    position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
    background: none; border: none; color: var(--c-muted); font-size: 1.2rem;
    line-height: 1; cursor: pointer; padding: 2px; display: none; z-index: 2;
  }
  .ps-clear-btn:hover { color: var(--c-danger); }
  .ps-dropdown {
    position: absolute; top: calc(100% + 6px); left: 0; right: 0;
    background: #fff; border: 1.5px solid var(--c-primary-border);
    border-radius: 12px; box-shadow: 0 10px 32px rgba(105,108,255,.16);
    z-index: 1000; overflow: hidden; max-height: 300px; overflow-y: auto; display: none;
  }
  .ps-item {
    display: flex; align-items: center; gap: 11px; padding: 11px 16px;
    cursor: pointer; border-bottom: 1px solid #f3f3f9; transition: background .1s;
  }
  .ps-item:last-child { border-bottom: none; }
  .ps-item:hover, .ps-item.active { background: var(--c-primary-soft); }
  .ps-item .pi-avatar {
    width: 36px; height: 36px; border-radius: 50%;
    background: linear-gradient(135deg,#dddeff,#ede8ff);
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem; color: var(--c-primary); flex-shrink: 0;
  }
  .ps-item .pi-info { flex: 1; min-width: 0; }
  .ps-item .pi-name { font-weight: 700; font-size: .875rem; color: #566a7f; }
  .ps-item .pi-phone { font-size: .78rem; color: var(--c-muted); font-family: monospace; }
  .ps-item .pi-addr { font-size: .75rem; color: #aaa; margin-top: 1px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .ps-badge { font-size: .7rem; font-weight: 700; padding: 3px 8px; border-radius: 20px; flex-shrink: 0; }
  .ps-badge.cust { background: #edfbd8; color: #3a7c11; border: 1px solid #b7eda0; }
  .ps-badge.hist { background: #fff4d4; color: #8a5500; border: 1px solid #ffd97a; }
  .ps-empty, .ps-loading { padding: 14px 16px; text-align: center; font-size: .84rem; color: var(--c-muted); }
  .ps-loading { color: var(--c-primary); }
  .ps-filled-strip {
    display: none; align-items: center; gap: 6px; margin-top: 10px;
    padding: 7px 12px; background: #edfbd8; border: 1px solid #b7eda0;
    border-radius: 8px; font-size: .8rem; font-weight: 600; color: #3a7c11;
  }
  .ps-filled-strip i { font-size: 1rem; }
  .ps-filled-strip .clear-link {
    margin-left: auto; font-size: .78rem; font-weight: 400; color: #5a9a32;
    text-decoration: underline; cursor: pointer; background: none; border: none; padding: 0;
  }
  .ps-filled-strip .clear-link:hover { color: var(--c-danger); }
  @keyframes fieldFlash {
    0%   { background: #e3f2fd; box-shadow: 0 0 0 3px rgba(105,108,255,.18); }
    100% { background: transparent; box-shadow: none; }
  }
  .field-flashed { animation: fieldFlash .55s ease forwards; }

  .fc-create-hero {
    background: linear-gradient(135deg, #696cff 0%, #8c57ff 100%);
    border-radius: 16px;
    padding: 1.25rem 1.75rem;
    color: #fff;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
  }
  .fc-create-hero .back-btn {
    width: 38px; height: 38px;
    border-radius: 10px;
    background: rgba(255,255,255,.2);
    border: 0;
    color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem;
    flex-shrink: 0;
    text-decoration: none;
    transition: background .15s;
  }
  .fc-create-hero .back-btn:hover { background: rgba(255,255,255,.32); color: #fff; }
  .fc-create-hero h4 { margin: 0; font-size: 1.25rem; font-weight: 700; }
  .fc-create-hero p  { margin: 0; opacity: .85; font-size: .85rem; }

  .section-card {
    border-radius: 14px;
    border: 0;
    box-shadow: 0 2px 12px rgba(105,108,255,.08);
    margin-bottom: 1.25rem;
  }
  .section-card .card-header {
    border-radius: 14px 14px 0 0;
    padding: .9rem 1.25rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: .6rem;
    border-bottom: 1px solid rgba(0,0,0,.06);
  }
  .section-card .card-header .header-icon {
    width: 32px; height: 32px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
  }

  .gps-preview-box {
    background: linear-gradient(135deg, #d1fae5, #ecfdf5);
    border: 1px solid #6ee7b7;
    border-radius: 10px;
    padding: .75rem 1rem;
    display: flex; align-items: center; justify-content: space-between;
    gap: 1rem;
  }

  .form-label { font-weight: 600; font-size: .82rem; color: #566a7f; margin-bottom: .35rem; }
  .form-label .required { color: #ff3e1d; }

  .customer-meta-bar {
    background: #f8f8fc;
    border-radius: 8px;
    padding: .5rem 1rem;
    font-size: .8rem;
    display: flex;
    align-items: center;
    gap: .5rem;
    margin-top: .5rem;
    min-height: 34px;
  }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="row justify-content-center">
    <div class="col-xl-9">

      {{-- Hero --}}
      <div class="fc-create-hero">
        <a href="{{ route('admin.field-complaints.index') }}" class="back-btn">
          <i class="bx bx-chevron-left"></i>
        </a>
        <div>
          <h4><i class="bx bx-plus-circle me-2"></i>New Field Complaint</h4>
          <p>Log an on-site repair / service request</p>
        </div>
      </div>

      @if($errors->any())
      <div class="alert alert-danger alert-dismissible mb-4" role="alert">
        <strong><i class="bx bx-error-circle me-1"></i>Please fix the following:</strong>
        <ul class="mb-0 mt-2 ps-3">
          @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      @endif

      <form method="POST" action="{{ route('admin.field-complaints.store') }}" id="fcForm">
        @csrf

        {{-- ═══ CUSTOMER ═══ --}}
        <div class="card section-card">
          <div class="card-header" style="background:linear-gradient(135deg,#eef2ff,#f5f0ff);">
            <div class="header-icon" style="background:#696cff20;color:#696cff;"><i class="bx bx-user"></i></div>
            <span style="color:#696cff;">Customer</span>
            <span id="customerFoundBadge" class="badge bg-success ms-auto" style="display:none;"><i class="bx bx-check me-1"></i>Existing</span>
            <span id="customerHistoryBadge" class="badge bg-warning text-dark" style="display:none;"><span id="visitCountText"></span></span>
            <span id="customerNewBadge" class="badge bg-info" style="display:none;"><i class="bx bx-plus me-1"></i>New</span>
          </div>
          <div class="card-body pt-4">

            {{-- Phone search widget --}}
            <div class="ps-widget mb-4" id="psWidget">
              <div class="ps-widget-label">
                <i class="bx bx-search-alt"></i> Phone Search
                <span class="ps-tip">— type to find existing customers</span>
              </div>
              <div class="ps-input-wrap">
                <i class="bx bx-phone-call ps-input-icon"></i>
                <input type="text" id="psInput" class="form-control"
                       placeholder="Search by phone number…" autocomplete="off" />
                <button type="button" id="psClearBtn" class="ps-clear-btn" title="Clear">
                  <i class="bx bx-x"></i>
                </button>
                <div class="ps-dropdown" id="psDropdown"></div>
              </div>
              <div class="ps-filled-strip" id="psFilledStrip">
                <i class="bx bx-check-circle"></i>
                <span id="psFilledName">Customer loaded</span>
                <button type="button" class="clear-link" onclick="clearCustomerFill()">Clear &amp; reset</button>
              </div>
            </div>

            {{-- Hidden phone field (submitted in form) --}}
            <input type="hidden" name="phone_no" id="customerPhone" value="{{ old('phone_no') }}" required>
            <input type="hidden" name="customer_db_id" id="customerDbId" value="">

            <div id="customerFields" class="{{ old('phone_no') ? '' : 'opacity-50 pe-none' }}">
              <div class="row g-3">
                <div class="col-sm-6">
                  <label class="form-label">Full Name <span class="required">*</span></label>
                  <input type="text" name="customer_name" id="customerName" value="{{ old('customer_name') }}"
                         placeholder="Customer full name" class="form-control" required>
                </div>
                <div class="col-sm-6">
                  <label class="form-label">Email</label>
                  <input type="email" name="customer_email" id="customerEmail" value="{{ old('customer_email') }}"
                         placeholder="optional" class="form-control">
                </div>
                <div class="col-12">
                  <label class="form-label">Address</label>
                  <textarea name="address" id="customerAddress" rows="2"
                            placeholder="House / apartment, road, city…"
                            class="form-control">{{ old('address') }}</textarea>
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- ═══ GPS LOCATION ═══ --}}
        <div class="card section-card">
          <div class="card-header" style="background:linear-gradient(135deg,#ecfdf5,#d1fae5);">
            <div class="header-icon" style="background:#28a74520;color:#28a745;"><i class="bx bx-map-pin"></i></div>
            <span style="color:#1e7e34;">GPS Location</span>
            <button type="button" id="btnGetLocation"
                    class="btn btn-sm ms-auto"
                    style="background:#28a745;color:#fff;border-radius:8px;">
              <i class="bx bx-current-location me-1"></i>Get My Location
            </button>
          </div>
          <div class="card-body pt-4">

            <div class="mb-3">
              <label class="form-label">Paste Location Link
                <span class="fw-normal text-muted">(Google Maps, WhatsApp, any)</span>
              </label>
              <div class="input-group">
                <input type="text" id="gpsRaw" name="gps_raw" value="{{ old('gps_raw') }}"
                       placeholder="https://maps.google.com/… or 6.9271, 79.8612"
                       class="form-control">
                <button type="button" id="btnParseLink" class="btn btn-outline-secondary px-3">Parse</button>
              </div>
              <div class="form-text">Supports Google Maps, WhatsApp location, or plain "lat, lng"</div>
            </div>

            <div class="row g-3 mb-3">
              <div class="col-sm-4">
                <label class="form-label">Latitude</label>
                <input type="number" step="0.0000001" name="gps_lat" id="gpsLat" value="{{ old('gps_lat') }}"
                       placeholder="6.9271" class="form-control font-monospace">
              </div>
              <div class="col-sm-4">
                <label class="form-label">Longitude</label>
                <input type="number" step="0.0000001" name="gps_lng" id="gpsLng" value="{{ old('gps_lng') }}"
                       placeholder="79.8612" class="form-control font-monospace">
              </div>
              <div class="col-sm-4">
                <label class="form-label">Location Label</label>
                <input type="text" name="gps_label" id="gpsLabel" value="{{ old('gps_label') }}"
                       placeholder="Home, Office, Site A" class="form-control">
              </div>
            </div>

            <div id="gpsPreview" class="d-none">
              <div class="gps-preview-box">
                <div class="d-flex align-items-center gap-2">
                  <i class="bx bxs-map-pin text-success fs-5"></i>
                  <div>
                    <div class="fw-semibold small">Location pinned</div>
                    <div class="font-monospace text-muted" style="font-size:.78rem;" id="gpsPreviewCoords"></div>
                  </div>
                </div>
                <a id="gpsPreviewLink" href="#" target="_blank" class="btn btn-sm btn-success">
                  <i class="bx bx-link-external me-1"></i>Open Maps
                </a>
              </div>
            </div>

            <div id="gpsSavedNote" class="d-none">
              <div class="alert alert-info small py-2 mt-2 mb-0">
                <i class="bx bx-info-circle me-1"></i>GPS loaded from customer record — you can update it
              </div>
            </div>
          </div>
        </div>

        {{-- ═══ SERVICE DETAILS ═══ --}}
        <div class="card section-card">
          <div class="card-header" style="background:linear-gradient(135deg,#e8f7ff,#d0efff);">
            <div class="header-icon" style="background:#03c3ec20;color:#03c3ec;"><i class="bx bx-wrench"></i></div>
            <span style="color:#0393b4;">Service Details</span>
          </div>
          <div class="card-body pt-4">
            <div class="row g-3">
              <div class="col-sm-6">
                <label class="form-label">Service Type</label>
                <select name="service_type_id" id="serviceTypeSelect" class="form-select">
                  <option value="">— Select service type —</option>
                  @foreach($serviceTypes as $st)
                  <option value="{{ $st->id }}" data-charge="{{ $st->base_charge }}"
                          {{ old('service_type_id') == $st->id ? 'selected' : '' }}>
                    {{ $st->name }} — Rs.{{ number_format($st->base_charge,2) }}
                  </option>
                  @endforeach
                </select>
              </div>
              <div class="col-sm-6">
                <label class="form-label">Priority</label>
                <select name="priority" class="form-select">
                  @foreach(['Low','Normal','High','Urgent'] as $p)
                  <option value="{{ $p }}" {{ old('priority','Normal') === $p ? 'selected' : '' }}>{{ $p }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-12">
                <label class="form-label">Fault / Issue Description</label>
                <textarea name="description" rows="3"
                          placeholder="Describe the fault or issue reported by the customer…"
                          class="form-control">{{ old('description') }}</textarea>
              </div>
              <div class="col-sm-6">
                <label class="form-label">Scheduled Date</label>
                <input type="date" name="scheduled_date" value="{{ old('scheduled_date') }}" class="form-control">
              </div>
              <div class="col-sm-6">
                <label class="form-label">
                  Service Charge (Rs.)
                  <span class="fw-normal text-muted" id="chargeAutoNote"></span>
                </label>
                <div class="input-group">
                  <span class="input-group-text fw-semibold">Rs.</span>
                  <input type="number" step="0.01" name="rupees" id="serviceCharge"
                         value="{{ old('rupees',0) }}" class="form-control font-monospace">
                </div>
              </div>
              <div class="col-12">
                <label class="form-label">Location Notes</label>
                <input type="text" name="location_notes" value="{{ old('location_notes') }}"
                       placeholder="e.g. Turn left after temple, blue gate" class="form-control">
              </div>
            </div>
          </div>
        </div>

        {{-- ═══ ADVANCE PAYMENT & NOTES ═══ --}}
        <div class="card section-card">
          <div class="card-header" style="background:linear-gradient(135deg,#fffbeb,#fef3c7);">
            <div class="header-icon" style="background:#ffab0020;color:#e6a817;"><i class="bx bx-money"></i></div>
            <span style="color:#b78105;">Advance Payment &amp; Notes</span>
          </div>
          <div class="card-body pt-4">
            <div class="row g-3">
              <div class="col-sm-6">
                <label class="form-label">Advance Amount (Rs.)</label>
                <div class="input-group">
                  <span class="input-group-text fw-semibold">Rs.</span>
                  <input type="number" step="0.01" min="0" name="advance_amount"
                         value="{{ old('advance_amount',0) }}" class="form-control font-monospace">
                </div>
                <div class="form-text">Leave 0 if no advance collected</div>
              </div>
              <div class="col-12">
                <label class="form-label">Internal Remark</label>
                <textarea name="remark" rows="2" placeholder="Any internal notes…"
                          class="form-control">{{ old('remark') }}</textarea>
              </div>
            </div>
          </div>
        </div>

        {{-- ═══ ASSIGN TO STAFF ═══ --}}
        <div class="card section-card">
          <div class="card-header" style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);">
            <div class="header-icon" style="background:#28a74520;color:#28a745;"><i class="bx bx-user-check"></i></div>
            <span style="color:#166534;">Assign to Staff</span>
            <span class="badge bg-secondary ms-auto fw-normal" style="font-size:.74rem;">Optional — can assign later</span>
          </div>
          <div class="card-body pt-4">
            <div class="row g-3">
              <div class="col-sm-6">
                <label class="form-label">Field Staff</label>
                <select name="assigned_to" id="assignedToSelect" class="form-select">
                  <option value="">— Unassigned (assign later) —</option>
                  @foreach($employees as $emp)
                  <option value="{{ $emp->id }}" {{ old('assigned_to') == $emp->id ? 'selected' : '' }}>
                    {{ $emp->employee_name }}
                    @if($emp->type) <span>({{ $emp->type }})</span> @endif
                  </option>
                  @endforeach
                </select>
                <div class="form-text">Assigning now will set status to <strong>Assigned</strong> and SMS the staff member.</div>
              </div>
              <div class="col-sm-6" id="assignScheduleWrap" style="{{ old('assigned_to') ? '' : 'display:none;' }}">
                <label class="form-label">Scheduled Date <span class="fw-normal text-muted">(for staff)</span></label>
                <input type="date" name="assign_scheduled_date" id="assignScheduledDate"
                       value="{{ old('assign_scheduled_date') }}" class="form-control">
                <div class="form-text">Overrides service scheduled date if set</div>
              </div>
            </div>
            <div id="assignPreview" class="mt-3 d-none">
              <div class="alert alert-success py-2 small mb-0">
                <i class="bx bx-check-circle me-1"></i>
                <strong id="assignPreviewName"></strong> will be notified by SMS when saved.
              </div>
            </div>
          </div>
        </div>

        {{-- Actions --}}
        <div class="d-flex justify-content-end gap-2 pb-5">
          <a href="{{ route('admin.field-complaints.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
          <button type="submit" class="btn btn-primary px-4 fw-semibold"
                  style="background:linear-gradient(135deg,#696cff,#8c57ff);border:0;box-shadow:0 4px 12px rgba(105,108,255,.4);">
            <i class="bx bx-check-circle me-1"></i>Log Complaint
          </button>
        </div>

      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
// ══════════════════════════════════════
//   CUSTOMER PHONE SEARCH WIDGET
// ══════════════════════════════════════
(function () {
  const input       = document.getElementById('psInput');
  const dropdown    = document.getElementById('psDropdown');
  const clearBtn    = document.getElementById('psClearBtn');
  const filledStrip = document.getElementById('psFilledStrip');
  const filledName  = document.getElementById('psFilledName');

  const nameEl      = document.getElementById('customerName');
  const emailEl     = document.getElementById('customerEmail');
  const addressEl   = document.getElementById('customerAddress');
  const phoneHidEl  = document.getElementById('customerPhone');
  const dbIdEl      = document.getElementById('customerDbId');
  const fieldsWrap  = document.getElementById('customerFields');
  const foundBadge  = document.getElementById('customerFoundBadge');
  const newBadge    = document.getElementById('customerNewBadge');
  const histBadge   = document.getElementById('customerHistoryBadge');
  const visitTxt    = document.getElementById('visitCountText');
  const gpsSavedNote = document.getElementById('gpsSavedNote');

  const gpsLatEl   = document.getElementById('gpsLat');
  const gpsLngEl   = document.getElementById('gpsLng');
  const gpsLabelEl = document.getElementById('gpsLabel');

  let timer = null, lastQ = '', _results = [];

  function showDrop(html) { dropdown.innerHTML = html; dropdown.style.display = 'block'; }
  function hideDrop()      { dropdown.style.display = 'none'; dropdown.innerHTML = ''; }

  function unlockFields() {
    fieldsWrap.classList.remove('opacity-50','pe-none');
  }

  function fillFields(item) {
    nameEl.value      = item.name    || '';
    emailEl.value     = item.email   || '';
    addressEl.value   = item.address || '';
    phoneHidEl.value  = item.phone   || '';
    dbIdEl.value      = item.customer_id || '';
    input.value       = item.phone   || '';

    // flash
    [nameEl, emailEl, addressEl].forEach(el => {
      if (!el.value) return;
      el.classList.remove('field-flashed');
      void el.offsetWidth;
      el.classList.add('field-flashed');
      setTimeout(() => el.classList.remove('field-flashed'), 600);
    });

    // GPS from customer record
    if (item.gps_lat && item.gps_lng) {
      gpsLatEl.value   = item.gps_lat;
      gpsLngEl.value   = item.gps_lng;
      gpsLabelEl.value = item.gps_label || '';
      updateGpsPreview();
      gpsSavedNote && gpsSavedNote.classList.remove('d-none');
    }

    // badges
    foundBadge.style.display = 'inline-block';
    newBadge.style.display   = 'none';
    if (item.visit_count > 0) {
      histBadge.style.display = 'inline-block';
      visitTxt && (visitTxt.textContent = item.visit_count + ' visit' + (item.visit_count !== 1 ? 's' : ''));
    }

    clearBtn.style.display = 'block';
    filledName.textContent = (item.name || 'Customer') + ' loaded';
    filledStrip.style.display = 'flex';
    unlockFields();
    hideDrop();
  }

  function renderResults(res) {
    _results = res;
    if (!res.length) {
      showDrop('<div class="ps-empty"><i class="bx bx-search-alt me-1"></i>No customers found — new customer will be created</div>');
      newBadge.style.display   = 'inline-block';
      foundBadge.style.display = 'none';
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
    fetch('/ajax/customer-lookup?phone=' + encodeURIComponent(q) + '&multi=1')
      .then(r => r.json())
      .then(renderResults)
      .catch(() => showDrop('<div class="ps-empty text-danger">Search failed. Try again.</div>'));
  }

  // Click result
  document.addEventListener('click', function(e) {
    const item = e.target.closest('.ps-item');
    if (item && dropdown.contains(item)) {
      fillFields(_results[+item.dataset.idx]);
    }
  });

  // Typing
  input.addEventListener('input', function() {
    const q = this.value.trim();
    phoneHidEl.value = q; // sync to hidden field while typing
    clearBtn.style.display = q ? 'block' : 'none';
    if (q.length >= 3) unlockFields();
    clearTimeout(timer);
    if (!q) { hideDrop(); lastQ = ''; return; }
    timer = setTimeout(() => doSearch(q), 280);
  });

  // Re-open on focus
  input.addEventListener('focus', () => {
    if (input.value.trim().length >= 2 && _results.length) dropdown.style.display = 'block';
  });

  // Clear btn
  clearBtn.addEventListener('click', () => clearCustomerFill());

  // Keyboard nav
  input.addEventListener('keydown', function(e) {
    const items = [...dropdown.querySelectorAll('.ps-item')];
    const cur   = dropdown.querySelector('.ps-item.active');
    if (e.key === 'ArrowDown') {
      e.preventDefault();
      const idx = cur ? items.indexOf(cur) : -1;
      if (cur) cur.classList.remove('active');
      const next = items[Math.min(idx + 1, items.length - 1)];
      if (next) { next.classList.add('active'); next.scrollIntoView({block:'nearest'}); }
    } else if (e.key === 'ArrowUp') {
      e.preventDefault();
      const idx = cur ? items.indexOf(cur) : items.length;
      if (cur) cur.classList.remove('active');
      const prev = items[Math.max(idx - 1, 0)];
      if (prev) { prev.classList.add('active'); prev.scrollIntoView({block:'nearest'}); }
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

  // If old('phone_no') exists on page load, treat as already filled
  @if(old('phone_no'))
  input.value = '{{ old("phone_no") }}';
  clearBtn.style.display = 'block';
  filledStrip.style.display = 'flex';
  filledName.textContent = '{{ old("customer_name","Customer") }} loaded';
  @endif
})();

window.clearCustomerFill = function() {
  document.getElementById('psInput').value = '';
  document.getElementById('psClearBtn').style.display = 'none';
  document.getElementById('psFilledStrip').style.display = 'none';
  document.getElementById('psDropdown').style.display = 'none';
  document.getElementById('customerPhone').value  = '';
  document.getElementById('customerDbId').value   = '';
  document.getElementById('customerName').value   = '';
  document.getElementById('customerEmail').value  = '';
  document.getElementById('customerAddress').value = '';
  document.getElementById('customerFoundBadge').style.display = 'none';
  document.getElementById('customerNewBadge').style.display   = 'none';
  document.getElementById('customerHistoryBadge').style.display = 'none';
  document.getElementById('customerFields').classList.add('opacity-50','pe-none');
  document.getElementById('psInput').focus();
};

function escH(s) {
  return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

// ══════════════════════════════════════
//   GPS
// ══════════════════════════════════════
function updateGpsPreview() {
  const lat = parseFloat(document.getElementById('gpsLat').value);
  const lng = parseFloat(document.getElementById('gpsLng').value);
  const gpsPreview = document.getElementById('gpsPreview');
  if (!isNaN(lat) && !isNaN(lng)) {
    document.getElementById('gpsPreviewCoords').textContent = lat.toFixed(6) + ', ' + lng.toFixed(6);
    document.getElementById('gpsPreviewLink').href = `https://www.google.com/maps?q=${lat},${lng}`;
    gpsPreview.classList.remove('d-none');
  } else {
    gpsPreview.classList.add('d-none');
  }
}
document.getElementById('gpsLat').addEventListener('input', updateGpsPreview);
document.getElementById('gpsLng').addEventListener('input', updateGpsPreview);
updateGpsPreview();

document.getElementById('btnParseLink').addEventListener('click', function () {
  const raw = document.getElementById('gpsRaw').value.trim();
  if (!raw) return;
  let lat = null, lng = null, m;
  m = raw.match(/^(-?\d{1,3}\.\d+)[,\s]+(-?\d{1,3}\.\d+)$/);
  if (m) { lat = parseFloat(m[1]); lng = parseFloat(m[2]); }
  if (!lat) { m = raw.match(/@(-?\d+\.\d+),(-?\d+\.\d+)/); if (m) { lat=parseFloat(m[1]); lng=parseFloat(m[2]); } }
  if (!lat) { m = raw.match(/[?&]q=(-?\d+\.\d+),(-?\d+\.\d+)/i); if (m) { lat=parseFloat(m[1]); lng=parseFloat(m[2]); } }
  if (!lat) { m = raw.match(/[?&]ll=(-?\d+\.\d+),(-?\d+\.\d+)/i); if (m) { lat=parseFloat(m[1]); lng=parseFloat(m[2]); } }
  if (!lat) { m = raw.match(/maps\/place\/[^\/]+\/@(-?\d+\.\d+),(-?\d+\.\d+)/i); if (m) { lat=parseFloat(m[1]); lng=parseFloat(m[2]); } }
  if (!lat) { m = raw.match(/geo:(-?\d+\.\d+),(-?\d+\.\d+)/i); if (m) { lat=parseFloat(m[1]); lng=parseFloat(m[2]); } }
  if (lat && lng) {
    document.getElementById('gpsLat').value = lat;
    document.getElementById('gpsLng').value = lng;
    updateGpsPreview();
    showToast('success', `Parsed: ${lat.toFixed(6)}, ${lng.toFixed(6)}`);
  } else {
    showToast('danger', 'Could not extract coordinates. Try pasting plain "lat, lng".');
  }
});

document.getElementById('btnGetLocation').addEventListener('click', function () {
  if (!navigator.geolocation) { showToast('danger', 'Geolocation not supported.'); return; }
  const btn = this;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Getting…';
  btn.disabled = true;
  navigator.geolocation.getCurrentPosition(
    function (pos) {
      document.getElementById('gpsLat').value = pos.coords.latitude.toFixed(7);
      document.getElementById('gpsLng').value = pos.coords.longitude.toFixed(7);
      updateGpsPreview();
      btn.innerHTML = '<i class="bx bx-check me-1"></i>Got it!';
      btn.disabled = false;
      const lbl = document.getElementById('gpsLabel');
      if (!lbl.value) lbl.value = 'Field Visit';
    },
    function () {
      showToast('danger', 'Location access denied or unavailable.');
      btn.innerHTML = '<i class="bx bx-current-location me-1"></i>Get My Location';
      btn.disabled = false;
    },
    { enableHighAccuracy: true, timeout: 10000 }
  );
});

// ══════════════════════════════════════
//   SERVICE TYPE → CHARGE AUTO-FILL
// ══════════════════════════════════════
document.getElementById('serviceTypeSelect').addEventListener('change', function () {
  const opt = this.options[this.selectedIndex];
  const charge = opt.dataset.charge;
  const chargeEl = document.getElementById('serviceCharge');
  const noteEl   = document.getElementById('chargeAutoNote');
  if (charge) { chargeEl.value = charge; noteEl.textContent = '(auto-filled)'; }
  else { noteEl.textContent = ''; }
});

// ══════════════════════════════════════
//   ASSIGN TO STAFF — preview
// ══════════════════════════════════════
(function() {
  const sel     = document.getElementById('assignedToSelect');
  const wrap    = document.getElementById('assignScheduleWrap');
  const preview = document.getElementById('assignPreview');
  const pname   = document.getElementById('assignPreviewName');

  sel.addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    if (this.value) {
      wrap.style.display    = '';
      preview.classList.remove('d-none');
      pname.textContent = opt.text.split('(')[0].trim();
    } else {
      wrap.style.display    = 'none';
      preview.classList.add('d-none');
    }
  });
})();

// ══════════════════════════════════════
//   TOAST
// ══════════════════════════════════════
function showToast(type, msg) {
  const div = document.createElement('div');
  div.className = `alert alert-${type} alert-dismissible position-fixed top-0 end-0 m-3 shadow`;
  div.style.zIndex = 9999;
  div.innerHTML = msg + `<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
  document.body.appendChild(div);
  setTimeout(() => div.remove(), 3500);
}
</script>
@endpush
