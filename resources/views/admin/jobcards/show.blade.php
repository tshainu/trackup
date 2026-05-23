@extends('layouts.admin')
@section('title', 'Job Card - ' . $jobCard->order_no)
@section('page-title', 'Job Card Details')
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.jobcards.index') }}">Job Cards</a></li>
  <li class="breadcrumb-item active">{{ $jobCard->order_no }}</li>
@endsection

@section('content')
<div class="d-flex justify-content-end gap-2 mb-3">
  <a href="{{ route('admin.jobcards.edit', $jobCard) }}" class="btn btn-sm" style="background:#7c4dff;color:#fff"><i class='bx bx-edit'></i> Edit</a>
  <a href="{{ route('admin.jobcards.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
</div>

<div class="row g-3">
  <div class="col-lg-6">
    <div class="card">
      <div class="card-header py-3"><div class="section-title mb-0"><i class='bx bx-user me-1'></i> Customer Details</div></div>
      <div class="card-body">
        <table class="table table-sm table-borderless">
          <tr><th width="40%">Order No</th><td><strong class="text-primary">{{ $jobCard->order_no }}</strong></td></tr>
          <tr><th>Customer ID</th><td>{{ $jobCard->customer_id }}</td></tr>
          <tr><th>Name</th><td>{{ $jobCard->customer_name }}</td></tr>
          <tr><th>Phone</th><td>{{ $jobCard->phone_no }}</td></tr>
          <tr><th>Address</th><td>{{ $jobCard->customer_address ?: '—' }}</td></tr>
          <tr><th>Email</th><td>{{ $jobCard->customer_email ?: '—' }}</td></tr>
          <tr><th>NIC</th><td>{{ $jobCard->customer_nic ?: '—' }}</td></tr>
          <tr><th>Date of Birth</th><td>{{ $jobCard->customer_dob ?: '—' }}</td></tr>
        </table>
      </div>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="card">
      <div class="card-header py-3"><div class="section-title mb-0"><i class='bx bx-chip me-1'></i> Device & Repair Details</div></div>
      <div class="card-body">
        <table class="table table-sm table-borderless">
          <tr><th width="40%">Device</th><td>{{ $jobCard->device_name }}</td></tr>
          <tr><th>Brand</th><td>{{ $jobCard->device_brand ?: '—' }}</td></tr>
          <tr><th>Serial No</th><td>{{ $jobCard->serial_no ?: '—' }}</td></tr>
          <tr><th>Device Age</th><td>{{ $jobCard->device_age ? $jobCard->device_age . ' years' : '—' }}</td></tr>
          <tr><th>Fault</th><td>{{ $jobCard->device_fault ?: '—' }}</td></tr>
          <tr><th>Issue</th><td>{{ $jobCard->issue ?: '—' }}</td></tr>
          <tr><th>Date</th><td>{{ $jobCard->date ? $jobCard->date->format('d M Y') : '—' }}</td></tr>
          <tr><th>Amount</th><td><strong>Rs. {{ number_format($jobCard->rupees, 2) }}</strong></td></tr>
          <tr><th>Assigned To</th><td>{{ $jobCard->employee->employee_name ?? '—' }}</td></tr>
          <tr><th>Need Assistant</th><td>{{ $jobCard->need_assistant ? '<span class="badge bg-warning text-dark">Yes</span>' : 'No' }}</td></tr>
          <tr><th>Status</th><td>
            @php $sc = ['Pending'=>'badge-pending','In Progress'=>'badge-progress','Completed'=>'badge-completed','Not Completed'=>'badge-not-completed']; @endphp
            <span class="badge {{ $sc[$jobCard->status] ?? 'bg-secondary' }}">{{ $jobCard->status ?: 'Pending' }}</span>
          </td></tr>
          <tr><th>Remark</th><td>{{ $jobCard->remark ?: '—' }}</td></tr>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
