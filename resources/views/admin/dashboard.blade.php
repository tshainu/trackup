@extends('layouts.admin')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<!-- Stats Row -->
<div class="row g-3 mb-4">
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card" style="background:linear-gradient(135deg,#7c4dff,#5f27cd)">
      <div class="stat-value">{{ $stats['total'] }}</div>
      <div class="stat-label">Total Job Cards</div>
      <i class='bx bx-file'></i>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card" style="background:linear-gradient(135deg,#f7971e,#ffd200)">
      <div class="stat-value">{{ $stats['pending'] }}</div>
      <div class="stat-label">Pending</div>
      <i class='bx bx-time'></i>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card" style="background:linear-gradient(135deg,#11998e,#38ef7d)">
      <div class="stat-value">{{ $stats['completed'] }}</div>
      <div class="stat-label">Completed</div>
      <i class='bx bx-check-circle'></i>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card" style="background:linear-gradient(135deg,#1e3c72,#2a5298)">
      <div class="stat-value">{{ $stats['in_progress'] }}</div>
      <div class="stat-label">In Progress</div>
      <i class='bx bx-loader-circle'></i>
    </div>
  </div>
</div>

<div class="row g-3 mb-4">
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card" style="background:linear-gradient(135deg,#e94560,#c0392b)">
      <div class="stat-value">{{ $stats['not_completed'] }}</div>
      <div class="stat-label">Not Completed</div>
      <i class='bx bx-x-circle'></i>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card" style="background:linear-gradient(135deg,#00b09b,#96c93d)">
      <div class="stat-value">{{ $stats['employees'] }}</div>
      <div class="stat-label">Active Employees</div>
      <i class='bx bx-group'></i>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card" style="background:linear-gradient(135deg,#8360c3,#2ebf91)">
      <div class="stat-value">Rs.{{ number_format($stats['revenue'], 0) }}</div>
      <div class="stat-label">Total Revenue</div>
      <i class='bx bx-money'></i>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card" style="background:linear-gradient(135deg,#fc4a1a,#f7b733)">
      <div class="stat-value">{{ $stats['today'] }}</div>
      <div class="stat-label">Today's Jobs</div>
      <i class='bx bx-calendar-check'></i>
    </div>
  </div>
</div>

<!-- Recent Jobs Table -->
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center py-3">
    <span><i class='bx bx-list-ul me-1'></i> Recent Job Cards</span>
    <a href="{{ route('admin.jobcards.index') }}" class="btn btn-sm btn-outline-secondary">View All</a>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th>Order No</th>
            <th>Customer</th>
            <th>Device</th>
            <th>Fault</th>
            <th>Date</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($recentJobs as $job)
          <tr>
            <td><span class="fw-semibold text-primary">{{ $job->order_no }}</span></td>
            <td>
              <div class="fw-semibold">{{ $job->customer_name }}</div>
              <small class="text-muted">{{ $job->phone_no }}</small>
            </td>
            <td>{{ $job->device_name }}<br><small class="text-muted">{{ $job->device_brand }}</small></td>
            <td><small>{{ Str::limit($job->device_fault, 25) }}</small></td>
            <td><small>{{ $job->date ? $job->date->format('d M Y') : '' }}</small></td>
            <td>Rs.{{ number_format($job->rupees, 0) }}</td>
            <td>
              @php
                $sc = ['Pending'=>'badge-pending','In Progress'=>'badge-progress','Completed'=>'badge-completed','Not Completed'=>'badge-not-completed'];
              @endphp
              <span class="badge {{ $sc[$job->status] ?? 'bg-secondary' }}">{{ $job->status ?: 'Pending' }}</span>
            </td>
            <td>
              <a href="{{ route('admin.jobcards.show', $job) }}" class="btn btn-xs btn-outline-primary btn-sm py-0 px-1">
                <i class='bx bx-eye'></i>
              </a>
              <a href="{{ route('admin.jobcards.edit', $job) }}" class="btn btn-xs btn-outline-secondary btn-sm py-0 px-1">
                <i class='bx bx-edit'></i>
              </a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
// Quick chart using inline canvas (no lib needed)
</script>
@endpush
