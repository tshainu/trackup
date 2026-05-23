@extends('layouts.admin')
@section('title', 'Store Settings')
@section('page-title', 'Store Settings')
@section('breadcrumb')<li class="breadcrumb-item active">Store Settings</li>@endsection

@push('styles')
<style>
.store-header {
  background: linear-gradient(135deg,#696cff 0%,#8c57ff 60%,#a855f7 100%);
  border-radius:14px;padding:24px 28px;color:#fff;
  display:flex;align-items:center;gap:18px;flex-wrap:wrap;
  margin-bottom:28px;
}
.store-header .store-ico {
  width:56px;height:56px;background:rgba(255,255,255,.2);border-radius:16px;
  display:flex;align-items:center;justify-content:center;font-size:1.7rem;flex-shrink:0;
}
.store-header h4 { margin:0;font-weight:700;font-size:1.25rem; }
.store-header p  { margin:0;opacity:.8;font-size:.85rem; }

.store-form-card {
  border:0;border-radius:14px;
  box-shadow:0 2px 20px rgba(108,92,231,.1);
}
.store-form-card .card-body { padding:28px; }

.ss-section {
  font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;
  color:#696cff;border-bottom:2px solid #f0f0ff;
  padding-bottom:8px;margin:28px 0 18px;
  display:flex;align-items:center;gap:8px;
}
.ss-section:first-of-type { margin-top:0; }
.ss-section .bx { font-size:1rem; }

.form-label { font-weight:600;font-size:.83rem;color:#444;margin-bottom:5px; }
.form-control:focus,.form-select:focus {
  border-color:#696cff;box-shadow:0 0 0 3px rgba(108,92,231,.12);
}
.form-control { border-radius:10px; }

/* Save bar */
.store-save-bar {
  background:#f8f8ff;border-radius:12px;
  padding:16px 20px;
  display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;
  margin-top:28px;border:1px solid #ebebff;
}
.btn-save {
  background:linear-gradient(135deg,#696cff,#8c57ff);color:#fff;border:0;
  padding:10px 32px;border-radius:10px;font-weight:700;font-size:.95rem;
  transition:.2s;box-shadow:0 4px 14px rgba(108,92,231,.3);
}
.btn-save:hover { opacity:.9;color:#fff;transform:translateY(-1px); }

/* Info card */
.store-info-card {
  background:#f8f8ff;border-radius:12px;
  border:1.5px solid #ebebff;padding:20px;
  height:100%;
}
.si-row { display:flex;align-items:flex-start;gap:10px;padding:10px 0;border-bottom:1px solid #f0f0f0; }
.si-row:last-child { border-bottom:0;padding-bottom:0; }
.si-ico { width:32px;height:32px;border-radius:8px;background:linear-gradient(135deg,#696cff11,#8c57ff22);display:flex;align-items:center;justify-content:center;font-size:.95rem;color:#696cff;flex-shrink:0;margin-top:1px; }
.si-label { font-size:.72rem;color:#aaa;font-weight:600;text-transform:uppercase;letter-spacing:.05em;line-height:1; }
.si-value { font-size:.88rem;color:#2d2d3a;font-weight:600;margin-top:2px; }
</style>
@endpush

@section('content')

{{-- Header --}}
<div class="store-header">
  <div class="store-ico"><i class='bx bx-store'></i></div>
  <div>
    <h4>Store Settings</h4>
    <p>Configure your business information used on job cards and receipts</p>
  </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mb-3">
  <i class='bx bx-check-circle me-1'></i>{{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-4">
  {{-- Form --}}
  <div class="col-lg-8">
    <div class="card store-form-card">
      <div class="card-body">
        <form action="{{ route('admin.store.update') }}" method="POST">
          @csrf @method('PUT')

          {{-- Business Info --}}
          <div class="ss-section"><i class='bx bx-building'></i> Business Information</div>
          <div class="row g-3">
            <div class="col-md-8">
              <label class="form-label">Store Name <span class="text-danger">*</span></label>
              <input type="text" name="store_name" class="form-control @error('store_name') is-invalid @enderror"
                     value="{{ old('store_name', $store->store_name ?? '') }}"
                     placeholder="Your business name" required />
              @error('store_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
              <label class="form-label">Registration No.</label>
              <input type="text" name="registration_no" class="form-control"
                     value="{{ old('registration_no', $store->registration_no ?? '') }}"
                     placeholder="BR / Company No." />
            </div>
            <div class="col-12">
              <label class="form-label">Store Address</label>
              <textarea name="store_address" class="form-control" rows="2"
                        placeholder="Full address of the store...">{{ old('store_address', $store->store_address ?? '') }}</textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label">Phone 1</label>
              <div class="input-group">
                <span class="input-group-text"><i class='bx bx-phone'></i></span>
                <input type="text" name="phone_no1" class="form-control"
                       value="{{ old('phone_no1', $store->phone_no1 ?? '') }}" placeholder="07X XXX XXXX" />
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Phone 2</label>
              <div class="input-group">
                <span class="input-group-text"><i class='bx bx-phone'></i></span>
                <input type="text" name="phone_no2" class="form-control"
                       value="{{ old('phone_no2', $store->phone_no2 ?? '') }}" placeholder="Optional" />
              </div>
            </div>
          </div>

          {{-- Owner Info --}}
          <div class="ss-section"><i class='bx bx-user-circle'></i> Owner Information</div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Owner Name</label>
              <input type="text" name="owner_name" class="form-control"
                     value="{{ old('owner_name', $store->owner_name ?? '') }}" placeholder="Full name" />
            </div>
            <div class="col-md-6">
              <label class="form-label">Owner Phone</label>
              <div class="input-group">
                <span class="input-group-text"><i class='bx bx-phone'></i></span>
                <input type="text" name="owner_phoneno" class="form-control"
                       value="{{ old('owner_phoneno', $store->owner_phoneno ?? '') }}" placeholder="07X XXX XXXX" />
              </div>
            </div>
            <div class="col-12">
              <label class="form-label">Owner Address</label>
              <input type="text" name="owner_address" class="form-control"
                     value="{{ old('owner_address', $store->owner_address ?? '') }}" placeholder="Owner's personal address" />
            </div>
          </div>

          {{-- Save --}}
          <div class="store-save-bar">
            <div style="font-size:.83rem;color:#888;">
              <i class='bx bx-info-circle me-1'></i>Used on job card printouts and receipts
            </div>
            <button type="submit" class="btn-save btn">
              <i class='bx bx-save me-1'></i>Save Settings
            </button>
          </div>

        </form>
      </div>
    </div>
  </div>

  {{-- Live Info Panel --}}
  <div class="col-lg-4">
    <div class="store-info-card">
      <div class="fw-bold mb-3" style="font-size:.85rem;color:#696cff;text-transform:uppercase;letter-spacing:.08em;">
        <i class='bx bx-info-circle me-1'></i>Current Info
      </div>
      @if($store->store_name ?? false)
      <div class="si-row">
        <div class="si-ico"><i class='bx bx-store'></i></div>
        <div>
          <div class="si-label">Store Name</div>
          <div class="si-value">{{ $store->store_name }}</div>
        </div>
      </div>
      @if($store->registration_no)
      <div class="si-row">
        <div class="si-ico"><i class='bx bx-id-card'></i></div>
        <div>
          <div class="si-label">Registration</div>
          <div class="si-value">{{ $store->registration_no }}</div>
        </div>
      </div>
      @endif
      @if($store->store_address)
      <div class="si-row">
        <div class="si-ico"><i class='bx bx-map'></i></div>
        <div>
          <div class="si-label">Address</div>
          <div class="si-value">{{ $store->store_address }}</div>
        </div>
      </div>
      @endif
      @if($store->phone_no1)
      <div class="si-row">
        <div class="si-ico"><i class='bx bx-phone'></i></div>
        <div>
          <div class="si-label">Phone</div>
          <div class="si-value">{{ $store->phone_no1 }}{{ $store->phone_no2 ? ' / '.$store->phone_no2 : '' }}</div>
        </div>
      </div>
      @endif
      @if($store->owner_name)
      <div class="si-row">
        <div class="si-ico"><i class='bx bx-user'></i></div>
        <div>
          <div class="si-label">Owner</div>
          <div class="si-value">{{ $store->owner_name }}</div>
        </div>
      </div>
      @endif
      @else
      <div class="text-center py-4">
        <i class='bx bx-store' style="font-size:2.5rem;color:#ddd;display:block;margin-bottom:8px;"></i>
        <div style="font-size:.83rem;color:#bbb;">No info saved yet.<br>Fill in the form and save.</div>
      </div>
      @endif
    </div>
  </div>

</div>
@endsection
