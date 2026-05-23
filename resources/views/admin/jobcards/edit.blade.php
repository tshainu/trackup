@extends('layouts.admin')
@section('title', 'Edit - ' . $jobCard->order_no)
@section('page-title', 'Edit Job Order')
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.jobcards.index') }}">Job Orders</a></li>
  <li class="breadcrumb-item active">Edit {{ $jobCard->order_no }}</li>
@endsection

@section('content')
<form action="{{ route('admin.jobcards.update', $jobCard) }}" method="POST">
@csrf @method('PUT')
<div class="row g-3">
  <div class="col-lg-6">
    <div class="card h-100">
      <div class="card-header py-3"><div class="section-title mb-0"><i class='bx bx-user me-1'></i> Customer Information</div></div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label">Order No</label>
            <input type="text" class="form-control bg-light" value="{{ $jobCard->order_no }}" readonly />
          </div>
          <div class="col-12">
            <label class="form-label">Customer Name <span class="text-danger">*</span></label>
            <input type="text" name="customer_name" class="form-control @error('customer_name') is-invalid @enderror" value="{{ old('customer_name', $jobCard->customer_name) }}" required />
            @error('customer_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-12">
            <label class="form-label">Phone <span class="text-danger">*</span></label>
            <input type="text" name="phone_no" class="form-control" value="{{ old('phone_no', $jobCard->phone_no) }}" required />
          </div>
          <div class="col-12">
            <label class="form-label">Address</label>
            <input type="text" name="customer_address" class="form-control" value="{{ old('customer_address', $jobCard->customer_address) }}" />
          </div>
          <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" name="customer_email" class="form-control" value="{{ old('customer_email', $jobCard->customer_email) }}" />
          </div>
          <div class="col-md-6">
            <label class="form-label">NIC</label>
            <input type="text" name="customer_nic" class="form-control" value="{{ old('customer_nic', $jobCard->customer_nic) }}" />
          </div>
          <div class="col-md-6">
            <label class="form-label">Date of Birth</label>
            <input type="text" name="customer_dob" class="form-control" value="{{ old('customer_dob', $jobCard->customer_dob) }}" />
          </div>
          <div class="col-md-6">
            <label class="form-label">Date <span class="text-danger">*</span></label>
            <input type="date" name="date" class="form-control" value="{{ old('date', $jobCard->date ? $jobCard->date->format('Y-m-d') : '') }}" required />
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card h-100">
      <div class="card-header py-3"><div class="section-title mb-0"><i class='bx bx-chip me-1'></i> Device & Status</div></div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label">Device Type <span class="text-danger">*</span></label>
            <select name="device_name" id="deviceSelect" class="form-select" required>
              <option value="">-- Select Device --</option>
              @foreach($devices as $d)
                <option value="{{ $d->device_name }}" {{ old('device_name', $jobCard->device_name) == $d->device_name ? 'selected' : '' }}>{{ $d->device_name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-12">
            <label class="form-label">Brand</label>
            <select name="device_brand" id="brandSelect" class="form-select">
              <option value="">-- Select Brand --</option>
              @foreach($brands as $b)
                <option value="{{ $b->device_brand }}" {{ old('device_brand', $jobCard->device_brand) == $b->device_brand ? 'selected' : '' }}>{{ $b->device_brand }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-12">
            <label class="form-label">Fault</label>
            <select name="device_fault" id="faultSelect" class="form-select">
              <option value="">-- Select Fault --</option>
              @foreach($faults as $f)
                <option value="{{ $f->device_fault }}" {{ old('device_fault', $jobCard->device_fault) == $f->device_fault ? 'selected' : '' }}>{{ $f->device_fault }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Serial No</label>
            <input type="text" name="serial_no" class="form-control" value="{{ old('serial_no', $jobCard->serial_no) }}" />
          </div>
          <div class="col-md-6">
            <label class="form-label">Device Age (years)</label>
            <input type="number" name="device_age" class="form-control" value="{{ old('device_age', $jobCard->device_age) }}" />
          </div>
          <div class="col-12">
            <label class="form-label">Issue</label>
            <textarea name="issue" class="form-control" rows="2">{{ old('issue', $jobCard->issue) }}</textarea>
          </div>
          <div class="col-md-6">
            <label class="form-label">Amount (Rs.)</label>
            <input type="number" name="rupees" class="form-control" value="{{ old('rupees', $jobCard->rupees) }}" step="0.01" />
          </div>
          <div class="col-md-6">
            <label class="form-label">Status <span class="text-danger">*</span></label>
            <select name="status" class="form-select" required>
              @foreach(['Pending','In Progress','Completed','Not Completed'] as $s)
                <option value="{{ $s }}" {{ old('status', $jobCard->status) == $s ? 'selected' : '' }}>{{ $s }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-12">
            <label class="form-label">Assign to Employee</label>
            <select name="employee_id" class="form-select">
              <option value="">-- Unassigned --</option>
              @foreach($employees as $emp)
                <option value="{{ $emp->id }}" {{ old('employee_id', $jobCard->employee_id) == $emp->id ? 'selected' : '' }}>{{ $emp->employee_name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-12">
            <label class="form-label">Remark</label>
            <textarea name="remark" class="form-control" rows="2">{{ old('remark', $jobCard->remark) }}</textarea>
          </div>
          <div class="col-md-6">
            <div class="form-check mt-2">
              <input class="form-check-input" type="checkbox" name="need_assistant" id="needAssistant" {{ old('need_assistant', $jobCard->need_assistant) ? 'checked' : '' }} />
              <label class="form-check-label" for="needAssistant">Needs Assistant</label>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-check mt-2">
              <input class="form-check-input" type="checkbox" name="payment_received" id="paymentReceived" {{ old('payment_received', $jobCard->payment_received) ? 'checked' : '' }} />
              <label class="form-check-label" for="paymentReceived">Payment Received</label>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="mt-3 d-flex gap-2">
  <button type="submit" class="btn" style="background:#7c4dff;color:#fff;padding:.5rem 2rem"><i class='bx bx-save me-1'></i> Update</button>
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
  if (!device) { $('#brandSelect,#faultSelect').html('<option value="">-- Select --</option>'); return; }
  $.getJSON(brandsUrl, { device_name: device }, function (data) {
    let o = '<option value="">-- Select Brand --</option>';
    data.forEach(b => { o += `<option value="${b.device_brand}">${b.device_brand}</option>`; });
    $('#brandSelect').html(o);
  });
  $.getJSON(faultsUrl, { device_name: device }, function (data) {
    let o = '<option value="">-- Select Fault --</option>';
    data.forEach(f => { o += `<option value="${f.device_fault}">${f.device_fault}</option>`; });
    $('#faultSelect').html(o);
  });
});
</script>
@endpush
