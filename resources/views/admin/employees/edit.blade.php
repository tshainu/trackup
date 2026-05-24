@extends('layouts.admin')
@section('title', 'Edit Employee')
@section('page-title', 'Edit Employee')
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.employees.index') }}">Employees</a></li>
  <li class="breadcrumb-item active">Edit</li>
@endsection

@push('styles')
<style>
.emp-form-header {
  background: linear-gradient(135deg,#696cff,#8c57ff);
  border-radius: 14px 14px 0 0;
  padding: 20px 24px; color: #fff;
  display: flex; align-items: center; gap: 14px;
}
.emp-form-header .ico { width:44px;height:44px;background:rgba(255,255,255,.2);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.4rem; }
.emp-form-header h5 { margin:0;font-weight:700; }
.emp-form-header p  { margin:0;opacity:.8;font-size:.82rem; }
.emp-form-card { border:0;border-radius:0 0 14px 14px;box-shadow:0 4px 24px rgba(108,92,231,.13); }
.section-divider { font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#696cff;border-bottom:2px solid #f0f0ff;padding-bottom:8px;margin:24px 0 16px;display:flex;align-items:center;gap:8px; }
.photo-upload-area {
  width:100px;height:100px;border-radius:50%;
  border:3px dashed #c0c0e0;
  display:flex;flex-direction:column;align-items:center;justify-content:center;
  cursor:pointer;transition:.2s;position:relative;overflow:hidden;background:#f8f8ff;
}
.photo-upload-area:hover { border-color:#696cff;background:#f0f0ff; }
.photo-upload-area img { width:100%;height:100%;object-fit:cover;position:absolute;top:0;left:0;border-radius:50%; }
.photo-upload-area .overlay { position:absolute;inset:0;background:rgba(108,92,231,.45);display:none;align-items:center;justify-content:center;color:#fff;font-size:1.4rem;border-radius:50%; }
.photo-upload-area:hover .overlay { display:flex; }
.photo-label { font-size:.75rem;color:#888;margin-top:8px;text-align:center; }
</style>
@endpush

@section('content')
<div style="max-width:720px">

<div class="emp-form-header">
  <div class="ico"><i class='bx bx-edit'></i></div>
  <div>
    <h5>{{ $employee->employee_name }}</h5>
    <p>{{ $employee->user_id }} &middot; {{ ucfirst($employee->role) }}</p>
  </div>
</div>

<div class="card emp-form-card">
  <div class="card-body p-4">

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
      {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <form action="{{ route('admin.employees.update', $employee) }}" method="POST" enctype="multipart/form-data">
      @csrf @method('PUT')

      {{-- Photo --}}
      <div class="text-center mb-2">
        <div class="d-inline-block">
          <label for="photoInput" class="photo-upload-area">
            @if($employee->photo)
              <img id="photoPreview" src="{{ asset('storage/'.$employee->photo) }}" />
            @else
              <img id="photoPreview" src="" style="display:none" />
              <i class='bx bx-camera' style="font-size:1.8rem;color:#c0c0e0;" id="photoIcon"></i>
              <small style="font-size:.6rem;color:#bbb;margin-top:2px">Upload</small>
            @endif
            <div class="overlay"><i class='bx bx-camera'></i></div>
          </label>
          <input type="file" name="photo" id="photoInput" accept="image/*" class="d-none" />
          <div class="photo-label">Click to change photo</div>
        </div>
      </div>

      {{-- Personal --}}
      <div class="section-divider"><i class='bx bx-user'></i> Personal Info</div>
      <div class="row g-3">
        <div class="col-md-8">
          <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
          <input type="text" name="employee_name" class="form-control @error('employee_name') is-invalid @enderror"
            value="{{ old('employee_name', $employee->employee_name) }}" required />
          @error('employee_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
          <select name="role" class="form-select" required>
            <option value="technician" {{ old('role',$employee->role)=='technician'?'selected':'' }}>Technician</option>
            <option value="helper"     {{ old('role',$employee->role)=='helper'?'selected':'' }}>Helper</option>
            <option value="supervisor" {{ old('role',$employee->role)=='supervisor'?'selected':'' }}>Supervisor</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">Phone 1</label>
          <div class="input-group">
            <span class="input-group-text"><i class='bx bx-phone'></i></span>
            <input type="text" name="phone_no_1" class="form-control" value="{{ old('phone_no_1',$employee->phone_no_1) }}" />
          </div>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">Phone 2</label>
          <div class="input-group">
            <span class="input-group-text"><i class='bx bx-phone'></i></span>
            <input type="text" name="phone_no_2" class="form-control" value="{{ old('phone_no_2',$employee->phone_no_2) }}" />
          </div>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">Email</label>
          <input type="email" name="email" class="form-control" value="{{ old('email',$employee->email) }}" />
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">NIC</label>
          <input type="text" name="nic" class="form-control" value="{{ old('nic',$employee->nic) }}" />
        </div>
        <div class="col-md-8">
          <label class="form-label fw-semibold">Address</label>
          <input type="text" name="employee_address" class="form-control" value="{{ old('employee_address',$employee->employee_address) }}" />
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold">Status</label>
          <select name="status" class="form-select">
            <option value="active"   {{ old('status',$employee->status)=='active'?'selected':'' }}>Active</option>
            <option value="inactive" {{ old('status',$employee->status)=='inactive'?'selected':'' }}>Inactive</option>
          </select>
        </div>
      </div>

      {{-- Staff Type --}}
      <div class="section-divider"><i class='bx bx-map'></i> Staff Type</div>
      <div class="row g-3">
        <div class="col-12">
          <label class="form-label fw-semibold">Work Type <span class="text-danger">*</span></label>
          <div class="d-flex gap-3 mt-1">
            <div class="form-check">
              <input class="form-check-input" type="radio" name="type" id="typeInbound" value="inbound"
                {{ old('type', $employee->type ?? 'inbound') === 'inbound' ? 'checked' : '' }} />
              <label class="form-check-label" for="typeInbound">
                <span class="fw-semibold">Inbound</span> <small class="text-muted">— Office / Workshop</small>
              </label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="type" id="typeOutbound" value="outbound"
                {{ old('type', $employee->type) === 'outbound' ? 'checked' : '' }} />
              <label class="form-check-label" for="typeOutbound">
                <span class="fw-semibold">Outbound</span> <small class="text-muted">— Field Staff</small>
              </label>
            </div>
          </div>
        </div>
      </div>

      {{-- Password --}}
      <div class="section-divider"><i class='bx bx-lock-alt'></i> Change Password <small class="text-muted fw-normal ms-1">(leave blank to keep current)</small></div>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label fw-semibold">New Password</label>
          <input type="password" name="password" class="form-control" placeholder="Min 6 characters" autocomplete="new-password" />
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">Confirm Password</label>
          <input type="password" name="password_confirmation" class="form-control" />
        </div>
      </div>

      <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn btn-primary px-4 fw-bold" style="background:linear-gradient(135deg,#696cff,#8c57ff);border:0;border-radius:10px;">
          <i class='bx bx-save me-1'></i>Save Changes
        </button>
        <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary px-4 fw-bold" style="border-radius:10px;">Cancel</a>
      </div>
    </form>

  </div>
</div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('photoInput').addEventListener('change', function() {
  const file = this.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = e => {
    const img = document.getElementById('photoPreview');
    img.src = e.target.result;
    img.style.display = 'block';
    const icon = document.getElementById('photoIcon');
    if (icon) icon.style.display = 'none';
  };
  reader.readAsDataURL(file);
});
</script>
@endpush
