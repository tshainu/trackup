@extends('layouts.admin')
@section('title', 'New Job Order')
@section('page-title', 'New Job Order')
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.jobcards.index') }}">Job Orders</a></li>
  <li class="breadcrumb-item active">New</li>
@endsection

@section('content')
<form action="{{ route('admin.jobcards.store') }}" method="POST">
@csrf
<div class="row g-3">
  <!-- Customer Info -->
  <div class="col-lg-6">
    <div class="card h-100">
      <div class="card-header py-3">
        <div class="section-title mb-0"><i class='bx bx-user me-1'></i> Customer Information</div>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label">Order No</label>
            <input type="text" class="form-control bg-light" value="{{ $orderNo }}" readonly />
          </div>
          <div class="col-12">
            <label class="form-label">Customer ID</label>
            <input type="text" class="form-control bg-light" value="{{ $customerId }}" readonly />
          </div>
          <div class="col-12">
            <label class="form-label">Customer Name <span class="text-danger">*</span></label>
            <input type="text" name="customer_name" class="form-control @error('customer_name') is-invalid @enderror" value="{{ old('customer_name') }}" required />
            @error('customer_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-12">
            <label class="form-label">Phone Number <span class="text-danger">*</span></label>
            <input type="text" name="phone_no" class="form-control @error('phone_no') is-invalid @enderror" value="{{ old('phone_no') }}" required />
            @error('phone_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-12">
            <label class="form-label">Address</label>
            <input type="text" name="customer_address" class="form-control" value="{{ old('customer_address') }}" />
          </div>
          <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" name="customer_email" class="form-control" value="{{ old('customer_email') }}" />
          </div>
          <div class="col-md-6">
            <label class="form-label">NIC</label>
            <input type="text" name="customer_nic" class="form-control" value="{{ old('customer_nic') }}" />
          </div>
          <div class="col-md-6">
            <label class="form-label">Date of Birth</label>
            <input type="text" name="customer_dob" class="form-control" value="{{ old('customer_dob') }}" placeholder="e.g. 01/01/1990" />
          </div>
          <div class="col-md-6">
            <label class="form-label">Date Received <span class="text-danger">*</span></label>
            <input type="date" name="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', date('Y-m-d')) }}" required />
            @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Device Info -->
  <div class="col-lg-6">
    <div class="card h-100">
      <div class="card-header py-3">
        <div class="section-title mb-0"><i class='bx bx-chip me-1'></i> Device Information</div>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label">Device Type <span class="text-danger">*</span></label>
            <select name="device_name" id="deviceSelect" class="form-select @error('device_name') is-invalid @enderror" required>
              <option value="">-- Select Device --</option>
              @foreach($devices as $d)
                <option value="{{ $d->device_name }}" {{ old('device_name') == $d->device_name ? 'selected' : '' }}>{{ $d->device_name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-12">
            <label class="form-label">Brand</label>
            <select name="device_brand" id="brandSelect" class="form-select">
              <option value="">-- Select Brand --</option>
            </select>
          </div>
          <div class="col-12">
            <label class="form-label">Fault</label>
            <select name="device_fault" id="faultSelect" class="form-select">
              <option value="">-- Select Fault --</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Serial No</label>
            <input type="text" name="serial_no" class="form-control" value="{{ old('serial_no') }}" />
          </div>
          <div class="col-md-6">
            <label class="form-label">Device Age (years)</label>
            <input type="number" name="device_age" class="form-control" value="{{ old('device_age') }}" min="0" max="50" />
          </div>
          <div class="col-12">
            <label class="form-label">Issue Description</label>
            <textarea name="issue" class="form-control" rows="2" placeholder="Customer's description of the problem...">{{ old('issue') }}</textarea>
          </div>
          <div class="col-md-6">
            <label class="form-label">Estimated Cost (Rs.)</label>
            <input type="number" name="rupees" class="form-control" value="{{ old('rupees', 0) }}" min="0" step="0.01" />
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
          <div class="col-12">
            <label class="form-label">Remark</label>
            <textarea name="remark" class="form-control" rows="2" placeholder="Internal notes...">{{ old('remark') }}</textarea>
          </div>
          <div class="col-12">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="need_assistant" id="needAssistant" {{ old('need_assistant') ? 'checked' : '' }} />
              <label class="form-check-label" for="needAssistant">Needs Assistant Technician</label>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="mt-3 d-flex gap-2">
  <button type="submit" class="btn" style="background:#7c4dff;color:#fff;padding:.5rem 2rem">
    <i class='bx bx-save me-1'></i> Save Job Order
  </button>
  <a href="{{ route('admin.jobcards.index') }}" class="btn btn-outline-secondary" style="padding:.5rem 2rem">Cancel</a>
</div>
</form>
@endsection

@push('scripts')
<script>
const brandsUrl = '{{ route("ajax.brands") }}';
const faultsUrl = '{{ route("ajax.faults") }}';

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
</script>
@endpush
