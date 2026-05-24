@extends('layouts.employee')

@section('title', 'My Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Welcome, {{ session('employee_name') }}</h2>
    <span class="text-muted">{{ now()->format('l, d F Y') }}</span>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="avatar avatar-lg flex-shrink-0" style="background-color:#e7e4ff;">
                    <i class='bx bx-clipboard fs-3' style="color:#696cff;"></i>
                </div>
                <div>
                    <p class="mb-0 text-muted small">Assigned Jobs</p>
                    <h4 class="mb-0">{{ $stats['assigned'] }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="avatar avatar-lg flex-shrink-0" style="background-color:#fff3cd;">
                    <i class='bx bx-time fs-3' style="color:#ffab00;"></i>
                </div>
                <div>
                    <p class="mb-0 text-muted small">Pending</p>
                    <h4 class="mb-0">{{ $stats['pending'] }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="avatar avatar-lg flex-shrink-0" style="background-color:#d1ecf1;">
                    <i class='bx bx-loader-circle fs-3' style="color:#03c3ec;"></i>
                </div>
                <div>
                    <p class="mb-0 text-muted small">In Progress</p>
                    <h4 class="mb-0">{{ $stats['in_progress'] }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="avatar avatar-lg flex-shrink-0" style="background-color:#d4edda;">
                    <i class='bx bx-check-circle fs-3' style="color:#71dd37;"></i>
                </div>
                <div>
                    <p class="mb-0 text-muted small">Completed</p>
                    <h4 class="mb-0">{{ $stats['completed'] }}</h4>
                </div>
            </div>
        </div>
    </div>
    @if($stats['field_assigned'] > 0)
    <div class="col-6 col-xl-3">
        <div class="card" style="border-left:3px solid #8c57ff;">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="avatar avatar-lg flex-shrink-0" style="background-color:#f0ebff;">
                    <i class='bx bx-map-pin fs-3' style="color:#8c57ff;"></i>
                </div>
                <div>
                    <p class="mb-0 text-muted small">Field Jobs</p>
                    <h4 class="mb-0">{{ $stats['field_assigned'] }}</h4>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

{{-- Recent Jobs --}}
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span class="card-header-title"><i class='bx bx-list-ul me-1'></i> Recent Assigned Jobs</span>
        <a href="{{ route('employee.jobs') }}" class="btn btn-sm btn-outline-primary">View All</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Order No</th>
                        <th>Customer</th>
                        <th>Device</th>
                        <th>Fault</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($myJobs as $job)
                    <tr>
                        <td><a href="#" class="fw-semibold text-primary">{{ $job->order_no }}</a></td>
                        <td>{{ $job->customer_name }}</td>
                        <td>{{ $job->device_name }}<br><small class="text-muted">{{ $job->device_brand }}</small></td>
                        <td>{{ Str::limit($job->device_fault, 35) }}</td>
                        <td>
                            @php
                                $sc = ['Pending'=>'bg-label-warning','In Progress'=>'bg-label-info','Completed'=>'bg-label-success','Not Completed'=>'bg-label-danger'];
                                $c = $sc[$job->status] ?? 'bg-label-secondary';
                            @endphp
                            <span class="badge {{ $c }}">{{ strtoupper($job->status) }}</span>
                        </td>
                        <td>{{ $job->date ? $job->date->format('d M Y') : $job->created_at->format('d M Y') }}</td>
                        <td>
                            @if($job->status === 'Pending')
                                <form method="POST" action="{{ route('employee.jobs.accept', $job) }}" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-primary fw-semibold" style="border-radius:8px;">
                                        <i class="bx bx-check me-1"></i>Accept
                                    </button>
                                </form>
                            @elseif($job->status === 'In Progress')
                                <a href="{{ route('employee.jobs.status', $job) }}"
                                   class="btn btn-sm btn-success fw-semibold" style="border-radius:8px;">
                                    <i class="bx bx-flag me-1"></i>Complete
                                </a>
                            @elseif($job->status === 'Completed')
                                <span class="text-muted small">Done</span>
                            @else
                                <a href="{{ route('employee.jobs.status', $job) }}" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;">Update</a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No jobs assigned yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Field Service Jobs --}}
@if($myFieldJobs->isNotEmpty())
<div class="card mt-4">
    <div class="card-header d-flex justify-content-between align-items-center"
         style="background:linear-gradient(135deg,#f0ebff,#e8e0ff);">
        <span class="fw-semibold" style="color:#8c57ff;">
            <i class='bx bx-map-pin me-1'></i> Field Service Jobs
        </span>
        <a href="{{ route('employee.field-jobs') }}" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;">View All</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Complaint #</th>
                        <th>Customer</th>
                        <th>Service</th>
                        <th>Scheduled</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($myFieldJobs as $fj)
                    @php
                        $sc = ['Assigned'=>'bg-label-info','In Progress'=>'bg-label-primary','Completed'=>'bg-label-success'];
                        $bc = $sc[$fj->status] ?? 'bg-label-secondary';
                    @endphp
                    <tr>
                        <td class="font-monospace fw-semibold text-primary">{{ $fj->complaint_no }}</td>
                        <td>{{ $fj->customer_name }}<br><small class="text-muted">{{ $fj->phone_no }}</small></td>
                        <td>{{ $fj->service_type_name ?: '—' }}</td>
                        <td>{{ $fj->scheduled_date?->format('d M Y') ?? '—' }}</td>
                        <td><span class="badge {{ $bc }}">{{ $fj->status }}</span></td>
                        <td>
                            @if($fj->status === 'Assigned')
                                <form method="POST" action="{{ route('employee.field-jobs.accept', $fj) }}" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-primary fw-semibold" style="border-radius:8px;">
                                        <i class="bx bx-check me-1"></i>Accept
                                    </button>
                                </form>
                            @elseif($fj->status === 'In Progress')
                                <a href="{{ route('employee.field-jobs.complete', $fj) }}"
                                   class="btn btn-sm btn-success fw-semibold" style="border-radius:8px;">
                                    <i class="bx bx-flag me-1"></i>Complete
                                </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

@endsection
