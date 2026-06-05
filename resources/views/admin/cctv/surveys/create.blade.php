@extends('layouts.admin')
@section('title', 'New Survey')

@push('styles')
<style>
  .hero-bar { background:linear-gradient(135deg,#00cfe8,#0090a8); border-radius:16px; padding:1.25rem 1.75rem; color:#fff; margin-bottom:1.5rem; display:flex; align-items:center; gap:1rem; }
  .hero-bar .back-btn { width:38px; height:38px; border-radius:10px; background:rgba(255,255,255,.2); border:0; color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.2rem; text-decoration:none; transition:background .15s; }
  .hero-bar .back-btn:hover { background:rgba(255,255,255,.32); color:#fff; }
  .hero-bar h4 { margin:0; font-size:1.2rem; font-weight:700; }
  .hero-bar p  { margin:0; opacity:.85; font-size:.85rem; }
  .form-card { border-radius:14px; border:0; box-shadow:0 2px 12px rgba(0,0,0,.06); margin-bottom:1.25rem; }
  .form-card .card-header { background:#f8f9fa; border-radius:14px 14px 0 0; padding:.85rem 1.25rem; font-weight:700; font-size:.875rem; display:flex; align-items:center; gap:.5rem; border-bottom:1px solid rgba(0,0,0,.06); }
  .section-icon { width:28px; height:28px; border-radius:8px; background:#e0f9fc; color:#00a4b8; display:flex; align-items:center; justify-content:center; font-size:.9rem; }

  /* Mobile lookup */
  .mobile-wrap { position:relative; }
  .mobile-spinner { position:absolute; right:12px; top:50%; transform:translateY(-50%); display:none; }
  .mobile-spinner.show { display:block; }
  .lookup-dropdown { position:absolute; top:calc(100% + 4px); left:0; right:0; background:#fff; border:1.5px solid #696cff; border-radius:10px; box-shadow:0 6px 24px rgba(105,108,255,.15); z-index:999; max-height:260px; overflow-y:auto; display:none; }
  .lookup-dropdown.show { display:block; }
  .lookup-item { padding:10px 14px; cursor:pointer; border-bottom:1px solid #f0f0f0; transition:background .1s; }
  .lookup-item:last-child { border-bottom:0; }
  .lookup-item:hover, .lookup-item.active { background:#eef0ff; }
  .lookup-item .li-name { font-weight:600; font-size:.9rem; color:#566a7f; }
  .lookup-item .li-phone { font-size:.8rem; color:#696cff; font-family:monospace; }
  .lookup-item .li-addr { font-size:.75rem; color:#8592a3; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .lookup-item .li-src { font-size:.7rem; padding:2px 7px; border-radius:10px; background:#f0f0ff; color:#8c57ff; font-weight:600; }
  .customer-found-badge { display:none; align-items:center; gap:6px; background:#e8faf0; color:#28c76f; border-radius:8px; padding:6px 12px; font-size:.82rem; font-weight:600; margin-top:6px; }
  .customer-found-badge.show { display:flex; }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="hero-bar">
    <a href="{{ route('admin.cctv.surveys.index') }}" class="back-btn"><i class="bx bx-arrow-back"></i></a>
    <div>
      <h4>New Site Survey</h4>
      <p>Schedule a CCTV site survey</p>
    </div>
  </div>

  @if($errors->any())
    <div class="alert alert-danger mb-3">{{ $errors->first() }}</div>
  @endif

  <form method="POST" action="{{ route('admin.cctv.surveys.store') }}">
    @csrf
    <div class="row g-3">
      <div class="col-lg-8">

        {{-- Customer Details --}}
        <div class="card form-card">
          <div class="card-header"><div class="section-icon"><i class="bx bx-user"></i></div> Customer Details</div>
          <div class="card-body row g-3">

            {{-- Mobile FIRST with live search --}}
            <div class="col-md-6">
              <label class="form-label fw-semibold">Mobile <span class="text-danger">*</span></label>
              <div class="mobile-wrap">
                <input type="text" id="mobileInput" name="mobile"
                       class="form-control @error('mobile') is-invalid @enderror"
                       value="{{ old('mobile') }}"
                       placeholder="Type mobile number…"
                       autocomplete="off" required>
                <span class="mobile-spinner show" id="mobileSpinner">
                  <span class="spinner-border spinner-border-sm text-primary" style="display:none" id="spinnerIcon"></span>
                </span>
                <div class="lookup-dropdown" id="lookupDropdown"></div>
              </div>
              <div class="customer-found-badge" id="foundBadge">
                <i class="bx bx-check-circle"></i> <span id="foundBadgeText">Customer found from records</span>
              </div>
              @error('mobile')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Customer Name <span class="text-danger">*</span></label>
              <input type="text" id="customerName" name="customer_name"
                     class="form-control @error('customer_name') is-invalid @enderror"
                     value="{{ old('customer_name') }}" required>
              @error('customer_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
              <label class="form-label fw-semibold">Address / Location</label>
              <textarea id="customerAddress" name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
            </div>

          </div>
        </div>

        {{-- Survey Details --}}
        <div class="card form-card">
          <div class="card-header"><div class="section-icon"><i class="bx bx-calendar"></i></div> Survey Details</div>
          <div class="card-body row g-3">
            <div class="col-md-4">
              <label class="form-label fw-semibold">Survey Date</label>
              <input type="date" name="survey_date" class="form-control" value="{{ old('survey_date', date('Y-m-d')) }}">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Technician</label>
              <input type="text" name="technician_name" class="form-control" value="{{ old('technician_name') }}">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">No. of Cameras</label>
              <input type="number" name="camera_count" class="form-control" value="{{ old('camera_count') }}" min="0">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Camera Type</label>
              <input type="text" name="camera_type" class="form-control" placeholder="e.g. Dome, Bullet" value="{{ old('camera_type') }}">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Status</label>
              <select name="status" class="form-select">
                @foreach(['Scheduled','Completed','Cancelled'] as $s)
                  <option value="{{ $s }}" {{ old('status','Scheduled')===$s?'selected':'' }}>{{ $s }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Site Observations</label>
              <textarea name="observations" class="form-control" rows="3" placeholder="Cable routing, power points, storage location…">{{ old('observations') }}</textarea>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Recommendations</label>
              <textarea name="recommendations" class="form-control" rows="3">{{ old('recommendations') }}</textarea>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Notes</label>
              <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
            </div>
          </div>
        </div>

      </div>

      <div class="col-lg-4">
        <div class="card form-card">
          <div class="card-header"><div class="section-icon"><i class="bx bx-save"></i></div> Save</div>
          <div class="card-body">
            <p class="text-muted small mb-3">Survey number auto-generated on save.</p>
            <input type="hidden" name="lead_id" value="{{ request('lead_id') }}">
            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> Save Survey</button>
              <a href="{{ route('admin.cctv.surveys.index') }}" class="btn btn-outline-secondary">Cancel</a>
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
(function () {
  const mobileInput   = document.getElementById('mobileInput');
  const nameInput     = document.getElementById('customerName');
  const addrInput     = document.getElementById('customerAddress');
  const dropdown      = document.getElementById('lookupDropdown');
  const spinner       = document.getElementById('spinnerIcon');
  const foundBadge    = document.getElementById('foundBadge');
  const foundBadgeText= document.getElementById('foundBadgeText');

  let debounceTimer = null;
  let selectedItem  = null;

  mobileInput.addEventListener('input', function () {
    const val = this.value.trim();
    clearTimeout(debounceTimer);
    hideDropdown();

    if (val.length < 3) {
      spinner.style.display = 'none';
      return;
    }

    spinner.style.display = 'inline-block';

    debounceTimer = setTimeout(() => {
      fetch(`/ajax/customer-lookup?phone=${encodeURIComponent(val)}&multi=1`)
        .then(r => r.json())
        .then(data => {
          spinner.style.display = 'none';
          if (data && data.length > 0) {
            renderDropdown(data);
          } else {
            hideDropdown();
          }
        })
        .catch(() => { spinner.style.display = 'none'; });
    }, 350);
  });

  function renderDropdown(items) {
    dropdown.innerHTML = '';
    items.forEach((item, idx) => {
      const div = document.createElement('div');
      div.className = 'lookup-item';
      div.innerHTML = `
        <div class="d-flex align-items-center justify-content-between gap-2">
          <div class="li-name">${escHtml(item.name)}</div>
          <span class="li-src">${escHtml(item.source === 'customers' ? 'Customer' : 'Job Card')}</span>
        </div>
        <div class="li-phone">${escHtml(item.phone)}</div>
        ${item.address ? `<div class="li-addr">${escHtml(item.address)}</div>` : ''}
      `;
      div.addEventListener('mousedown', (e) => {
        e.preventDefault();
        fillCustomer(item);
      });
      dropdown.appendChild(div);
    });
    dropdown.classList.add('show');
  }

  function fillCustomer(item) {
    mobileInput.value = item.phone;
    nameInput.value   = item.name;
    addrInput.value   = item.address || '';
    hideDropdown();
    foundBadge.classList.add('show');
    foundBadgeText.textContent = `Loaded from ${item.source === 'customers' ? 'Customer records' : 'Job Card records'} — update if needed`;
    selectedItem = item;
  }

  function hideDropdown() {
    dropdown.classList.remove('show');
    dropdown.innerHTML = '';
  }

  // Hide on outside click
  document.addEventListener('click', (e) => {
    if (!mobileInput.contains(e.target) && !dropdown.contains(e.target)) {
      hideDropdown();
    }
  });

  // Clear found badge if user manually changes mobile
  mobileInput.addEventListener('change', function() {
    if (selectedItem && this.value !== selectedItem.phone) {
      foundBadge.classList.remove('show');
      selectedItem = null;
    }
  });

  function escHtml(str) {
    return String(str || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }
})();
</script>
@endpush
