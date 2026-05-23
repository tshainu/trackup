@extends('layouts.employee')

@section('title', 'Update Job Status')

@section('content')
<div class="mb-4">
    <a href="{{ route('employee.jobs') }}" class="btn btn-outline-secondary btn-sm">&larr; Back to Jobs</a>
</div>

<div class="row justify-content-center">
    <div class="col-md-7">
        {{-- Job Summary --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0">Job Order: <code class="text-white">{{ $jobCard->order_no }}</code></h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="text-muted small">Customer</div>
                        <div class="fw-semibold">{{ $jobCard->customer_name }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Phone</div>
                        <div class="fw-semibold">{{ $jobCard->phone_no }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Device</div>
                        <div class="fw-semibold">{{ $jobCard->device_name }} – {{ $jobCard->device_brand }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Current Status</div>
                        <div>
                            @php
                                $badges = ['Pending'=>'warning','In Progress'=>'info','Completed'=>'success','Not Completed'=>'danger'];
                                $c = $badges[$jobCard->status] ?? 'dark';
                            @endphp
                            <span class="badge bg-{{ $c }} fs-6">{{ $jobCard->status }}</span>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="text-muted small">Reported Fault</div>
                        <div>{{ $jobCard->device_fault }}</div>
                    </div>
                    @if($jobCard->issue)
                    <div class="col-12">
                        <div class="text-muted small">Additional Issue</div>
                        <div>{{ $jobCard->issue }}</div>
                    </div>
                    @endif
                    @if($jobCard->remark)
                    <div class="col-12">
                        <div class="text-muted small">Previous Remark</div>
                        <div class="fst-italic text-secondary">{{ $jobCard->remark }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Update Form --}}
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Update Status</h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif

                <form action="{{ route('employee.jobs.status.save', $jobCard->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold">New Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="">— Select Status —</option>
                            @foreach(['Pending','In Progress','Completed','Not Completed'] as $s)
                                <option value="{{ $s }}" {{ (old('status', $jobCard->status) == $s) ? 'selected' : '' }}>{{ $s }}</option>
                            @endforeach
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Remark / Notes</label>
                        <textarea name="remark" class="form-control @error('remark') is-invalid @enderror"
                                  rows="4" placeholder="Add any notes about the repair progress...">{{ old('remark', $jobCard->remark) }}</textarea>
                        @error('remark')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">Save Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
