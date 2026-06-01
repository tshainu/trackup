@extends('layouts.superadmin')
@section('title', 'Add Shop')

@push('styles')
<style>
  .form-card { background:#fff;border-radius:16px;border:1px solid #eee;padding:1.75rem; }
  .form-section-title { font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#7c3aed;margin-bottom:1rem;padding-bottom:.5rem;border-bottom:1px solid #f0f0ff; }
  .form-control, .form-select { border-radius:9px;border-color:#e5e5e5;font-size:.88rem; }
  .form-control:focus, .form-select:focus { border-color:#7c3aed;box-shadow:0 0 0 3px rgba(124,58,237,.1); }
  .form-label { font-size:.82rem;font-weight:600;color:#444;margin-bottom:.35rem; }
  .is-invalid { border-color:#dc2626!important; }
  .invalid-feedback { font-size:.75rem; }
</style>
@endpush

@section('content')

{{-- Header --}}
<div class="d-flex align-items-center gap-3 mb-4">
  <a href="{{ route('superadmin.shops.index') }}"
     class="btn btn-sm btn-outline-secondary d-flex align-items-center justify-content-center"
     style="width:36px;height:36px;border-radius:9px;padding:0;">
    <i class="bx bx-arrow-back"></i>
  </a>
  <div>
    <h4 class="fw-bold mb-0" style="color:#1e1040;">Add New Shop</h4>
    <p class="text-muted mb-0" style="font-size:.82rem;">Register a new shop on the platform</p>
  </div>
</div>

<form method="POST" action="{{ route('superadmin.shops.store') }}">
  @csrf
  <div class="row g-4">

    {{-- Left Column --}}
    <div class="col-12 col-lg-7">

      {{-- Shop Info --}}
      <div class="form-card mb-4">
        <div class="form-section-title"><i class="bx bx-store me-1"></i>Shop Information</div>
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label">Shop Name <span class="text-danger">*</span></label>
            <input type="text" name="shop_name" value="{{ old('shop_name') }}" required
                   class="form-control @error('shop_name') is-invalid @enderror"
                   placeholder="e.g. Sunrise Electronics">
            @error('shop_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-12 col-sm-6">
            <label class="form-label">Owner Name <span class="text-danger">*</span></label>
            <input type="text" name="owner_name" value="{{ old('owner_name') }}" required
                   class="form-control @error('owner_name') is-invalid @enderror"
                   placeholder="e.g. John Silva">
            @error('owner_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-12 col-sm-6">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
              <option value="active"  {{ old('status','active')==='active'    ? 'selected':'' }}>Active</option>
              <option value="pending" {{ old('status')==='pending' ? 'selected':'' }}>Pending</option>
              <option value="suspended" {{ old('status')==='suspended' ? 'selected':'' }}>Suspended</option>
            </select>
          </div>
        </div>
      </div>

      {{-- Contact --}}
      <div class="form-card mb-4">
        <div class="form-section-title"><i class="bx bx-phone me-1"></i>Contact Details</div>
        <div class="row g-3">
          <div class="col-12 col-sm-6">
            <label class="form-label">Email <span class="text-danger">*</span></label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   class="form-control @error('email') is-invalid @enderror"
                   placeholder="shop@example.com">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-12 col-sm-6">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" value="{{ old('phone') }}"
                   class="form-control @error('phone') is-invalid @enderror"
                   placeholder="+94 77 000 0000">
            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>

      {{-- Location --}}
      <div class="form-card">
        <div class="form-section-title"><i class="bx bx-map me-1"></i>Location</div>
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label">Address</label>
            <input type="text" name="address" value="{{ old('address') }}"
                   class="form-control" placeholder="Street address">
          </div>
          <div class="col-12 col-sm-6">
            <label class="form-label">City</label>
            <input type="text" name="city" value="{{ old('city') }}"
                   class="form-control" placeholder="Colombo">
          </div>
          <div class="col-12 col-sm-6">
            <label class="form-label">Country</label>
            <input type="text" name="country" value="{{ old('country','Sri Lanka') }}"
                   class="form-control" placeholder="Sri Lanka">
          </div>
        </div>
      </div>
    </div>

    {{-- Right Column --}}
    <div class="col-12 col-lg-5">

      {{-- Notes --}}
      <div class="form-card mb-4">
        <div class="form-section-title"><i class="bx bx-note me-1"></i>Notes</div>
        <textarea name="notes" rows="3" class="form-control"
                  placeholder="Internal notes about this shop (optional)...">{{ old('notes') }}</textarea>
      </div>

      {{-- Modules --}}
      <div class="form-card mb-4">
        <div class="form-section-title"><i class="bx bx-grid-alt me-1"></i>Enabled Modules</div>
        <p style="font-size:.78rem;color:#666;margin-bottom:.75rem;">Select which modules this shop can access.</p>
        <div class="d-flex flex-column gap-2">
          <label class="d-flex align-items-center gap-2" style="cursor:pointer;">
            <input type="checkbox" name="modules[]" value="job_orders" class="form-check-input m-0"
                   {{ in_array('job_orders', old('modules', ['job_orders','field_services'])) ? 'checked' : '' }}
                   style="width:18px;height:18px;accent-color:#7c3aed;">
            <span style="font-size:.88rem;">
              <strong>Job Orders</strong>
              <span class="text-muted ms-1">— Repair tracking, job cards, delivery</span>
            </span>
          </label>
          <label class="d-flex align-items-center gap-2" style="cursor:pointer;">
            <input type="checkbox" name="modules[]" value="field_services" class="form-check-input m-0"
                   {{ in_array('field_services', old('modules', ['job_orders','field_services'])) ? 'checked' : '' }}
                   style="width:18px;height:18px;accent-color:#7c3aed;">
            <span style="font-size:.88rem;">
              <strong>Field Services</strong>
              <span class="text-muted ms-1">— On-site complaints, technician dispatch</span>
            </span>
          </label>
        </div>
        @error('modules')<div class="text-danger mt-1" style="font-size:.75rem;">{{ $message }}</div>@enderror
      </div>

      {{-- Info box --}}
      <div class="form-card" style="background:#f0f0ff;border-color:#e0d7ff;">
        <div style="font-size:.8rem;color:#5b21b6;">
          <div class="fw-bold mb-2"><i class="bx bx-info-circle me-1"></i>Auto-generated credentials</div>
          <ul class="mb-0 ps-3" style="line-height:2;">
            <li>A unique <strong>Shop Code</strong> will be generated</li>
            <li>An <strong>admin username</strong> based on shop name</li>
            <li>A random <strong>secure password</strong></li>
          </ul>
          <div class="mt-2" style="font-size:.75rem;opacity:.8;">
            Credentials are shown once after creation and stored securely.
          </div>
        </div>
      </div>
    </div>

    {{-- Submit --}}
    <div class="col-12">
      <div class="d-flex gap-3">
        <button type="submit" class="btn fw-bold" style="background:linear-gradient(135deg,#7c3aed,#a855f7);color:#fff;border:none;border-radius:10px;padding:.6rem 2rem;">
          <i class="bx bx-plus me-1"></i> Create Shop
        </button>
        <a href="{{ route('superadmin.shops.index') }}" class="btn btn-outline-secondary" style="border-radius:10px;padding:.6rem 1.5rem;">
          Cancel
        </a>
      </div>
    </div>

  </div>
</form>

@endsection
