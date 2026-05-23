@extends('layouts.admin')

@section('title', 'Reports')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Reports</h2>
    <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
</div>

{{-- Date Filter --}}
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('admin.reports.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">From Date</label>
                <input type="date" name="from" class="form-control" value="{{ $from }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">To Date</label>
                <input type="date" name="to" class="form-control" value="{{ $to }}">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">Generate Report</button>
            </div>
        </form>
    </div>
</div>

{{-- Summary Stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card text-center shadow-sm border-primary">
            <div class="card-body">
                <div class="fs-1 fw-bold text-primary">{{ $summary['total'] }}</div>
                <div class="text-muted">Total Jobs</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center shadow-sm border-warning">
            <div class="card-body">
                <div class="fs-1 fw-bold text-warning">{{ $summary['pending'] }}</div>
                <div class="text-muted">Pending</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center shadow-sm border-success">
            <div class="card-body">
                <div class="fs-1 fw-bold text-success">{{ $summary['completed'] }}</div>
                <div class="text-muted">Completed</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center shadow-sm border-info">
            <div class="card-body">
                <div class="fs-1 fw-bold text-info">Rs. {{ number_format($summary['revenue'], 2) }}</div>
                <div class="text-muted">Revenue</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    {{-- By Device --}}
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Jobs by Device Type</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th>Device</th><th class="text-end">Jobs</th></tr>
                    </thead>
                    <tbody>
                        @forelse($byDevice as $deviceName => $count)
                        <tr>
                            <td>{{ $deviceName ?: 'Unknown' }}</td>
                            <td class="text-end"><span class="badge bg-primary">{{ $count }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="text-center text-muted py-3">No data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- By Status --}}
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Jobs by Status</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th>Status</th><th class="text-end">Jobs</th></tr>
                    </thead>
                    <tbody>
                        @forelse($byStatus as $statusName => $count)
                        <tr>
                            <td>
                                @php
                                    $badges = ['Pending'=>'warning','In Progress'=>'info','Completed'=>'success','Not Completed'=>'danger'];
                                    $color = $badges[$statusName] ?? 'dark';
                                @endphp
                                <span class="badge bg-{{ $color }}">{{ $statusName }}</span>
                            </td>
                            <td class="text-end"><span class="badge bg-primary">{{ $count }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="text-center text-muted py-3">No data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Jobs Table --}}
<div class="card shadow-sm">
    <div class="card-header bg-dark text-white d-flex justify-content-between">
        <h5 class="mb-0">Job Cards
            <small class="fw-normal">({{ $from }} → {{ $to }})</small>
        </h5>
        <span class="badge bg-light text-dark">{{ $jobs->count() }} records</span>
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
                        <th>Assigned To</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jobs as $job)
                    <tr>
                        <td><code>{{ $job->order_no }}</code></td>
                        <td>{{ $job->customer_name }}</td>
                        <td>{{ $job->device_name }}<br><small class="text-muted">{{ $job->device_brand }}</small></td>
                        <td>{{ Str::limit($job->device_fault, 30) }}</td>
                        <td>{{ $job->employee ? $job->employee->employee_name : '—' }}</td>
                        <td>{{ $job->rupees > 0 ? 'Rs. '.number_format($job->rupees,2) : '—' }}</td>
                        <td>
                            @php
                                $badges = ['Pending'=>'warning','In Progress'=>'info','Completed'=>'success','Not Completed'=>'danger'];
                                $c = $badges[$job->status] ?? 'dark';
                            @endphp
                            <span class="badge bg-{{ $c }}">{{ $job->status }}</span>
                        </td>
                        <td>{{ $job->date ? $job->date->format('d M Y') : '—' }}</td>
                        <td><a href="{{ route('admin.jobcards.show', $job->id) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center text-muted py-4">No job cards found for this period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
