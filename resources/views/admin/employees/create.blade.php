@extends('layouts.admin')
@section('title', 'Add Employee')
@section('page-title', 'Add Employee')
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.employees.index') }}">Employees</a></li>
  <li class="breadcrumb-item active">Add</li>
@endsection

@section('content')
<div class="card" style="max-width:700px">
  <div class="card-header py-3"><div class="section-title mb-0"><i class='bx bx-user-plus me-1'></i> New Employee</div></div>
  <div class="card-body">
    @if($errors->any())
      <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif
    <form action="{{ route('admin.employees.store') }}" method="POST">
      @csrf
      <div class="row g-3">
        <div class="col-12"><label class="form-label">Employee ID (auto)</label><input class="form-control bg-light" value="{{ $nextId }}" readonly /></div>
        <div class="col-md-6"><label class="form-label">Full Name <span class="text-danger">*</span></label><input type="text" name="employee_name" class="form-control" value="{{ old('employee_name') }}" required /></div>
        <div class="col-md-6"><label class="form-label">Role <span class="text-danger">*</span></label>
          <select name="role" class="form-select" required>
            <option value="technician" {{ old('role')=='technician'?'selected':'' }}>Technician</option>
            <option value="helper" {{ old('role')=='helper'?'selected':'' }}>Helper</option>
            <option value="supervisor" {{ old('role')=='supervisor'?'selected':'' }}>Supervisor</option>
          </select>
        </div>
        <div class="col-md-6"><label class="form-label">Phone 1</label><input type="text" name="phone_no_1" class="form-control" value="{{ old('phone_no_1') }}" /></div>
        <div class="col-md-6"><label class="form-label">Phone 2</label><input type="text" name="phone_no_2" class="form-control" value="{{ old('phone_no_2') }}" /></div>
        <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ old('email') }}" /></div>
        <div class="col-md-6"><label class="form-label">NIC</label><input type="text" name="nic" class="form-control" value="{{ old('nic') }}" /></div>
        <div class="col-12"><label class="form-label">Address</label><input type="text" name="employee_address" class="form-control" value="{{ old('employee_address') }}" /></div>
        <div class="col-md-4"><label class="form-label">Username <span class="text-danger">*</span></label><input type="text" name="user_name" class="form-control" value="{{ old('user_name') }}" required /></div>
        <div class="col-md-4"><label class="form-label">Password <span class="text-danger">*</span></label><input type="password" name="password" class="form-control" required /></div>
        <div class="col-md-4"><label class="form-label">Confirm Password <span class="text-danger">*</span></label><input type="password" name="password_confirmation" class="form-control" required /></div>
      </div>
      <div class="mt-3 d-flex gap-2">
        <button type="submit" class="btn" style="background:#7c4dff;color:#fff;padding:.5rem 2rem"><i class='bx bx-save me-1'></i> Save</button>
        <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary" style="padding:.5rem 2rem">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection
