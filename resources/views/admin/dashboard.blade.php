@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')

<div class="row g-4 mb-4">

  <!-- Total Job Cards -->
  <div class="col-6 col-xl-3">
    <div class="card h-100">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="avatar avatar-lg flex-shrink-0" style="background-color:#e7e4ff;">
          <i class='bx bx-file fs-3' style="color:#696cff;"></i>
        </div>
        <div>
          <p class="mb-0 text-muted small">Total Job Cards</p>
          <h4 class="mb-0 fw-bold">{{ $stats['total'] }}</h4>
        </div>
      </div>
    </div>
  </div>

  <!-- Pending -->
  <div class="col-6 col-xl-3">
    <div class="card h-100">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="avatar avatar-lg flex-shrink-0" style="background-color:#fff3cd;">
          <i class='bx bx-time fs-3' style="color:#ffab00;"></i>
        </div>
        <div>
          <p class="mb-0 text-muted small">Pending</p>
          <h4 class="mb-0 fw-bold">{{ $stats['pending'] }}</h4>
        </div>
      </div>
    </div>
  </div>

  <!-- In Progress -->
  <div class="col-6 col-xl-3">
    <div class="card h-100">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="avatar avatar-lg flex-shrink-0" style="background-color:#d1ecf1;">
          <i class='bx bx-loader-circle fs-3' style="color:#03c3ec;"></i>
        </div>
        <div>
          <p class="mb-0 text-muted small">In Progress</p>
          <h4 class="mb-0 fw-bold">{{ $stats['in_progress'] }}</h4>
        </div>
      </div>
    </div>
  </div>

  <!-- Completed -->
  <div class="col-6 col-xl-3">
    <div class="card h-100">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="avatar avatar-lg flex-shrink-0" style="background-color:#d4edda;">
          <i class='bx bx-check-circle fs-3' style="color:#71dd37;"></i>
        </div>
        <div>
          <p class="mb-0 text-muted small">Completed</p>
          <h4 class="mb-0 fw-bold">{{ $stats['completed'] }}</h4>
        </div>
      </div>
    </div>
  </div>

  <!-- Not Completed -->
  <div class="col-6 col-xl-3">
    <div class="card h-100">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="avatar avatar-lg flex-shrink-0" style="background-color:#fde8e8;">
          <i class='bx bx-x-circle fs-3' style="color:#ff3e1d;"></i>
        </div>
        <div>
          <p class="mb-0 text-muted small">Not Completed</p>
          <h4 class="mb-0 fw-bold">{{ $stats['not_completed'] }}</h4>
        </div>
      </div>
    </div>
  </div>

  <!-- Active Employees -->
  <div class="col-6 col-xl-3">
    <div class="card h-100">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="avatar avatar-lg flex-shrink-0" style="background-color:#d6f5e3;">
          <i class='bx bx-group fs-3' style="color:#00ab55;"></i>
        </div>
        <div>
          <p class="mb-0 text-muted small">Active Employees</p>
          <h4 class="mb-0 fw-bold">{{ $stats['employees'] }}</h4>
        </div>
      </div>
    </div>
  </div>

  <!-- Total Revenue -->
  <div class="col-6 col-xl-3">
    <div class="card h-100">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="avatar avatar-lg flex-shrink-0" style="background-color:#e3d9fd;">
          <i class='bx bx-money fs-3' style="color:#8c57ff;"></i>
        </div>
        <div>
          <p class="mb-0 text-muted small">Total Revenue</p>
          <h4 class="mb-0 fw-bold">Rs.{{ number_format($stats['revenue'], 0) }}</h4>
        </div>
      </div>
    </div>
  </div>

  <!-- Today's Jobs -->
  <div class="col-6 col-xl-3">
    <div class="card h-100">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="avatar avatar-lg flex-shrink-0" style="background-color:#ffe5d0;">
          <i class='bx bx-calendar-check fs-3' style="color:#ff7d00;"></i>
        </div>
        <div>
          <p class="mb-0 text-muted small">Today's Jobs</p>
          <h4 class="mb-0 fw-bold">{{ $stats['today'] }}</h4>
        </div>
      </div>
    </div>
  </div>

</div>

<!-- Recent Jobs Table -->
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0"><i class='bx bx-list-ul me-1'></i> Recent Job Cards</h5>
    <a href="{{ route('admin.jobcards.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
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
          <td>
            {{ $job->device_name }}<br>
            <small class="text-muted">{{ $job->device_brand }}</small>
          </td>
          <td><small>{{ Str::limit($job->device_fault, 25) }}</small></td>
          <td><small>{{ $job->date ? $job->date->format('d M Y') : '' }}</small></td>
          <td class="fw-semibold">Rs.{{ number_format($job->rupees, 0) }}</td>
          <td>
            @php
              $badges = [
                'Pending'       => 'bg-label-warning',
                'In Progress'   => 'bg-label-info',
                'Completed'     => 'bg-label-success',
                'Not Completed' => 'bg-label-danger',
              ];
            @endphp
            <span class="badge {{ $badges[$job->status] ?? 'bg-label-secondary' }}">{{ $job->status ?: 'Pending' }}</span>
          </td>
          <td>
            <a href="{{ route('admin.jobcards.show', $job) }}" class="btn btn-sm btn-icon btn-outline-primary me-1">
              <i class='bx bx-show'></i>
            </a>
            <a href="{{ route('admin.jobcards.edit', $job) }}" class="btn btn-sm btn-icon btn-outline-secondary">
              <i class='bx bx-edit'></i>
            </a>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

@endsection
