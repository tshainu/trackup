@extends('layouts.employee')

@section('title', 'My Jobs')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">My Jobs</h2>
    <span class="badge bg-secondary fs-6">{{ $jobs->total() }} total</span>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Order No</th>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th>Device</th>
                        <th>Fault</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jobs as $job)
                    <tr>
                        <td><code>{{ $job->order_no }}</code></td>
                        <td>{{ $job->customer_name }}</td>
                        <td>{{ $job->phone_no }}</td>
                        <td>{{ $job->device_name }}<br><small class="text-muted">{{ $job->device_brand }}</small></td>
                        <td title="{{ $job->device_fault }}">{{ Str::limit($job->device_fault, 35) }}</td>
                        <td>
                            @php
                                $badges = ['Pending'=>'warning','In Progress'=>'info','Completed'=>'success','Not Completed'=>'danger'];
                                $c = $badges[$job->status] ?? 'dark';
                            @endphp
                            <span class="badge bg-{{ $c }}">{{ $job->status }}</span>
                        </td>
                        <td>{{ $job->date ? $job->date->format('d M Y') : $job->updated_at->format('d M Y') }}</td>
                        <td>
                            @if(!in_array($job->status, ['Completed']))
                                <a href="{{ route('employee.jobs.status', $job->id) }}" class="btn btn-sm btn-outline-primary">Update</a>
                            @else
                                <span class="text-muted small">Done</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No jobs found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($jobs->hasPages())
    <div class="card-footer">
        {{ $jobs->links() }}
    </div>
    @endif
</div>
@endsection
