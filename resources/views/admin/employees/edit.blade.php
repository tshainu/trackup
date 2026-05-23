@extends('layouts.admin')
@section('title', 'Edit Employee')
@section('page-title', 'Edit Employee')
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.employees.index') }}">Employees</a></li>
  <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="card" style="max-width:700px">
  <div class="card-header py-3"><div class="section-title mb-0"><i class='bx bx-edit me-1'></i> Edit Employee</div></div>
  <div class="card-body">
    @if($errors->any())
      <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif
    <form action="{{ route('admin.employees.update', $employee) }}" method="POST">
      @csrf @method('PUT')
      <div class="row g-3">
        <div class="col-12"><label class="form-label">Employee ID</label><input class="form-control bg-light" value="{{ $employee->user_id }}" readonly /></div>
        <div class="col-md-6"><label class="form-label">Full Name <span class="text-danger">*</span></label><input type="text" name="employee_name" class="form-control" value="{{ old('employee_name', $employee->employee_name) }}" required /></div>
        <div class="col-md-6"><label class="form-label">Role <span class="text-danger">*</span></label>
          <select name="role" class="form-select" required>
            @foreach(['technician','helper','supervisor'] as $r)
              <option value="{{ $r }}" {{ old('role',$employee->role)==$r?'selected':'' }}>{{ ucfirst($r) }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-6"><label class="form-label">Phone 1</label><input type="text" name="phone_no_1" class="form-control" value="{{ old('phone_no_1', $employee->phone_no_1) }}" /></div>
        <div class="col-md-6"><label class="form-label">Phone 2</label><input type="text" name="phone_no_2" class="form-control" value="{{ old('phone_no_2', $employee->phone_no_2) }}" /></div>
        <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ old('email', $employee->email) }}" /></div>
        <div class="col-md-6"><label class="form-label">NIC</label><input type="text" name="nic" class="form-control" value="{{ old('nic', $employee->nic) }}" /></div>
        <div class="col-12"><label class="form-label">Address</label><input type="text" name="employee_address" class="form-control" value="{{ old('employee_address', $employee->employee_address) }}" /></div>
        <div class="col-md-6"><label class="form-label">Status</label>
          <select name="status" class="form-select">
            <option value="active" {{ old('status',$employee->status)=='active'?'selected':'' }}>Active</option>
            <option value="inactive" {{ old('status',$employee->status)=='inactive'?'selected':'' }}>Inactive</option>
          </select>
        </div>
        <div class="col-md-6"><label class="form-label">New Password <small class="text-muted">(leave blank to keep)</small></label><input type="password" name="password" class="form-control" /></div>
        <div class="col-md-6"><label class="form-label">Confirm Password</label><input type="password" name="password_confirmation" class="form-control" /></div>
      </div>
      <div class="mt-3 d-flex gap-2">
        <button type="submit" class="btn" style="background:#7c4dff;color:#fff;padding:.5rem 2rem"><i class='bx bx-save me-1'></i> Update</button>
        <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary" style="padding:.5rem 2rem">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection
