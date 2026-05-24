@extends('layouts.employee')
@section('title', 'My Jobs')

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible mb-4">
    <i class="bx bx-check-circle me-1"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Hero --}}
<div class="rounded-3 p-4 mb-4 text-white"
     style="background:linear-gradient(135deg,#696cff,#8c57ff);box-shadow:0 4px 20px rgba(105,108,255,.3);">
    <div class="d-flex align-items-center gap-3">
        <div style="width:48px;height:48px;border-radius:14px;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-size:1.5rem;">
            <i class="bx bx-clipboard"></i>
        </div>
        <div>
            <div class="fw-bold" style="font-size:1.15rem;">My Job Orders</div>
            <div style="opacity:.8;font-size:.85rem;">Accept to start work, update when done</div>
        </div>
        <span class="badge ms-auto" style="background:rgba(255,255,255,.25);font-size:.85rem;">
            {{ $jobs->total() }} total
        </span>
    </div>
</div>

<div class="card" style="border-radius:14px;border:0;box-shadow:0 2px 12px rgba(105,108,255,.1);">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="font-size:.9rem;">
                <thead>
                    <tr style="background:#f8f8fc;">
                        <th class="ps-4">Order #</th>
                        <th>Customer</th>
                        <th>Device</th>
                        <th>Fault</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th class="pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jobs as $job)
                    @php
                        $badges = ['Pending'=>'bg-label-warning','In Progress'=>'bg-label-info','Completed'=>'bg-label-success','Not Completed'=>'bg-label-danger','Cancelled'=>'bg-label-secondary'];
                        $bc = $badges[$job->status] ?? 'bg-label-secondary';
                    @endphp
                    <tr>
                        <td class="ps-4 font-monospace fw-semibold text-primary">{{ $job->order_no }}</td>
                        <td>
                            <div class="fw-semibold">{{ $job->customer_name }}</div>
                            <small class="text-muted">{{ $job->phone_no }}</small>
                        </td>
                        <td>
                            {{ $job->device_name }}
                            @if($job->device_brand)
                            <br><small class="text-muted">{{ $job->device_brand }}</small>
                            @endif
                        </td>
                        <td>
                            <span title="{{ $job->device_fault }}">{{ Str::limit($job->device_fault, 35) }}</span>
                        </td>
                        <td><span class="badge {{ $bc }}">{{ $job->status }}</span></td>
                        <td>{{ $job->date?->format('d M Y') ?? $job->created_at->format('d M Y') }}</td>
                        <td class="pe-4">
                            @if($job->status === 'Pending')
                                {{-- Accept → sets In Progress --}}
                                <form method="POST" action="{{ route('employee.jobs.accept', $job) }}" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-primary fw-semibold" style="border-radius:8px;min-width:90px;">
                                        <i class="bx bx-check me-1"></i>Accept
                                    </button>
                                </form>
                            @elseif($job->status === 'In Progress')
                                {{-- Update status form --}}
                                <a href="{{ route('employee.jobs.status', $job) }}"
                                   class="btn btn-sm btn-success fw-semibold" style="border-radius:8px;min-width:90px;">
                                    <i class="bx bx-flag me-1"></i>Complete
                                </a>
                            @elseif($job->status === 'Completed')
                                <span class="text-muted small">Done</span>
                            @else
                                {{-- Not Completed / Cancelled --}}
                                <a href="{{ route('employee.jobs.status', $job) }}"
                                   class="btn btn-sm btn-outline-secondary fw-semibold" style="border-radius:8px;">
                                    <i class="bx bx-edit me-1"></i>Update
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bx bx-clipboard fs-2 d-block mb-2"></i>
                            No jobs assigned yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($jobs->hasPages())
    <div class="card-footer">{{ $jobs->links() }}</div>
    @endif
</div>
@endsection
