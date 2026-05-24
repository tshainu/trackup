@extends('layouts.employee')
@section('title', 'Complete Job ' . $fieldComplaint->complaint_no)

@section('content')

<div class="rounded-3 p-4 mb-4 text-white"
     style="background:linear-gradient(135deg,#28a745,#1e7e34);box-shadow:0 4px 20px rgba(40,167,69,.3);">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('employee.field-jobs') }}"
           style="width:38px;height:38px;border-radius:10px;background:rgba(255,255,255,.2);border:0;color:#fff;display:flex;align-items:center;justify-content:center;text-decoration:none;">
            <i class="bx bx-chevron-left fs-5"></i>
        </a>
        <div>
            <div class="fw-bold" style="font-size:1.1rem;">Complete Job — {{ $fieldComplaint->complaint_no }}</div>
            <div style="opacity:.8;font-size:.85rem;">{{ $fieldComplaint->customer_name }} &bull; {{ $fieldComplaint->service_type_name }}</div>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card" style="border-radius:14px;border:0;box-shadow:0 2px 16px rgba(40,167,69,.15);">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('employee.field-jobs.complete.save', $fieldComplaint) }}">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Completion Notes</label>
                        <textarea name="completion_notes" rows="4"
                                  class="form-control"
                                  placeholder="Describe what was done, parts replaced, any observations…"
                                  style="border-radius:10px;">{{ old('completion_notes', $fieldComplaint->completion_notes) }}</textarea>
                        <div class="form-text">Optional but recommended for the admin record.</div>
                    </div>
                    <button class="btn fw-semibold w-100"
                            style="background:linear-gradient(135deg,#28a745,#1e7e34);color:#fff;border:0;border-radius:10px;padding:.75rem;box-shadow:0 4px 12px rgba(40,167,69,.3);">
                        <i class="bx bx-check-circle me-2"></i>Mark as Completed
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
