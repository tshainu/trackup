@extends('layouts.employee')
@section('title', 'My Field Jobs')

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible mb-4">
    <i class="bx bx-check-circle me-1"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Hero --}}
<div class="rounded-3 p-4 mb-4 text-white"
     style="background:linear-gradient(135deg,#8c57ff,#696cff);box-shadow:0 4px 20px rgba(140,87,255,.3);">
    <div class="d-flex align-items-center gap-3">
        <div style="width:48px;height:48px;border-radius:14px;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-size:1.5rem;">
            <i class="bx bx-map-pin"></i>
        </div>
        <div>
            <div class="fw-bold" style="font-size:1.15rem;">Field Service Jobs</div>
            <div style="opacity:.8;font-size:.85rem;">Jobs assigned to you — accept to start, complete when done</div>
        </div>
    </div>
</div>

<div class="card" style="border-radius:14px;border:0;box-shadow:0 2px 12px rgba(105,108,255,.1);">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="font-size:.9rem;">
                <thead>
                    <tr style="background:#f8f8fc;">
                        <th class="ps-4">Complaint #</th>
                        <th>Customer</th>
                        <th>Service Type</th>
                        <th>Address</th>
                        <th>Scheduled</th>
                        <th>Status</th>
                        <th class="pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fieldJobs as $fj)
                    @php
                        $sc = ['Assigned'=>'bg-label-info','In Progress'=>'bg-label-primary','Completed'=>'bg-label-success','Cancelled'=>'bg-label-danger'];
                        $bc = $sc[$fj->status] ?? 'bg-label-secondary';
                    @endphp
                    <tr>
                        <td class="ps-4 font-monospace fw-semibold text-primary">{{ $fj->complaint_no }}</td>
                        <td>
                            <div class="fw-semibold">{{ $fj->customer_name }}</div>
                            <small class="text-muted"><i class="bx bx-phone me-1"></i>{{ $fj->phone_no }}</small>
                        </td>
                        <td>{{ $fj->service_type_name ?: '—' }}</td>
                        <td>
                            <div style="max-width:160px;" class="text-muted small">{{ $fj->address ?: '—' }}</div>
                            @if($fj->gps_lat && $fj->gps_lng)
                            <a href="{{ $fj->googleMapsUrl() }}" target="_blank" class="small text-success">
                                <i class="bx bxs-map-pin me-1"></i>Open Maps
                            </a>
                            @endif
                        </td>
                        <td>{{ $fj->scheduled_date?->format('d M Y') ?? '—' }}</td>
                        <td><span class="badge {{ $bc }}">{{ $fj->status }}</span></td>
                        <td class="pe-4">
                            @if($fj->status === 'Assigned')
                                <form method="POST" action="{{ route('employee.field-jobs.accept', $fj) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-primary fw-semibold" style="border-radius:8px;min-width:90px;">
                                        <i class="bx bx-check me-1"></i>Accept
                                    </button>
                                </form>
                            @elseif($fj->status === 'In Progress')
                                <a href="{{ route('employee.field-jobs.complete', $fj) }}"
                                   class="btn btn-sm btn-success fw-semibold" style="border-radius:8px;min-width:90px;">
                                    <i class="bx bx-flag me-1"></i>Complete
                                </a>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bx bx-map-pin fs-2 d-block mb-2 text-muted"></i>
                            No field jobs assigned to you yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($fieldJobs->hasPages())
    <div class="card-footer">{{ $fieldJobs->links() }}</div>
    @endif
</div>
@endsection
