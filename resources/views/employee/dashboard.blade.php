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
                            @if($job->status !== 'Completed')
                                <a href="{{ route('employee.jobs.status', $job->id) }}" class="btn btn-sm btn-primary">Update</a>
                            @else
                                <span class="text-muted small">Done</span>
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
@endsection
