@extends('layouts.employee')

@section('title', 'My Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Welcome, {{ session('employee_name') }}</h2>
    <span class="text-muted">{{ now()->format('l, d F Y') }}</span>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card text-center shadow-sm border-primary">
            <div class="card-body py-4">
                <div class="fs-1 fw-bold text-primary">{{ $stats['assigned'] }}</div>
                <div class="text-muted">Assigned Jobs</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center shadow-sm border-warning">
            <div class="card-body py-4">
                <div class="fs-1 fw-bold text-warning">{{ $stats['pending'] }}</div>
                <div class="text-muted">Pending</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center shadow-sm border-info">
            <div class="card-body py-4">
                <div class="fs-1 fw-bold text-info">{{ $stats['in_progress'] }}</div>
                <div class="text-muted">In Progress</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center shadow-sm border-success">
            <div class="card-body py-4">
                <div class="fs-1 fw-bold text-success">{{ $stats['completed'] }}</div>
                <div class="text-muted">Completed</div>
            </div>
        </div>
    </div>
</div>

{{-- Recent Jobs --}}
<div class="card shadow-sm">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Recent Assigned Jobs</h5>
        <a href="{{ route('employee.jobs') }}" class="btn btn-sm btn-outline-light">View All</a>
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
                        <td><code>{{ $job->order_no }}</code></td>
                        <td>{{ $job->customer_name }}</td>
                        <td>{{ $job->device_name }}<br><small class="text-muted">{{ $job->device_brand }}</small></td>
                        <td>{{ Str::limit($job->device_fault, 35) }}</td>
                        <td>
                            @php
                                $badges = ['Pending'=>'warning','In Progress'=>'info','Completed'=>'success','Not Completed'=>'danger'];
                                $c = $badges[$job->status] ?? 'dark';
                            @endphp
                            <span class="badge bg-{{ $c }}">{{ $job->status }}</span>
                        </td>
                        <td>{{ $job->date ? $job->date->format('d M Y') : $job->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('employee.jobs.status', $job->id) }}" class="btn btn-sm btn-outline-primary">Update</a>
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
