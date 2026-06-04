@extends('layouts.admin')
@section('title', 'Edit Survey – ' . $survey->survey_no)

@push('styles')
<style>
  .hero-bar { background:linear-gradient(135deg,#00cfe8,#0090a8); border-radius:16px; padding:1.25rem 1.75rem; color:#fff; margin-bottom:1.5rem; display:flex; align-items:center; gap:1rem; }
  .hero-bar .back-btn { width:38px; height:38px; border-radius:10px; background:rgba(255,255,255,.2); border:0; color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.2rem; text-decoration:none; transition:background .15s; }
  .hero-bar .back-btn:hover { background:rgba(255,255,255,.32); color:#fff; }
  .hero-bar h4 { margin:0; font-size:1.2rem; font-weight:700; }
  .hero-bar p { margin:0; opacity:.85; font-size:.85rem; }
  .form-card { border-radius:14px; border:0; box-shadow:0 2px 12px rgba(0,0,0,.06); margin-bottom:1.25rem; }
  .form-card .card-header { background:#f8f9fa; border-radius:14px 14px 0 0; padding:.85rem 1.25rem; font-weight:700; font-size:.875rem; display:flex; align-items:center; gap:.5rem; border-bottom:1px solid rgba(0,0,0,.06); }
  .section-icon { width:28px; height:28px; border-radius:8px; background:#e0f9fc; color:#00a4b8; display:flex; align-items:center; justify-content:center; font-size:.9rem; }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="hero-bar">
    <a href="{{ route('admin.cctv.surveys.show', $survey) }}" class="back-btn"><i class="bx bx-arrow-back"></i></a>
    <div>
      <h4>Edit Survey – {{ $survey->survey_no }}</h4>
      <p>{{ $survey->customer_name }}</p>
    </div>
  </div>

  <form method="POST" action="{{ route('admin.cctv.surveys.update', $survey) }}">
    @csrf @method('PUT')
    <div class="row g-3">
      <div class="col-lg-8">
        <div class="card form-card">
          <div class="card-header"><div class="section-icon"><i class="bx bx-user"></i></div> Customer Details</div>
          <div class="card-body row g-3">
            <div class="col-md-6">
              <label class="form-label fw-600">Customer Name <span class="text-danger">*</span></label>
              <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name', $survey->customer_name) }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">Mobile</label>
              <input type="text" name="mobile" class="form-control" value="{{ old('mobile', $survey->mobile) }}">
            </div>
            <div class="col-12">
              <label class="form-label fw-600">Address / Location</label>
              <textarea name="address" class="form-control" rows="2">{{ old('address', $survey->address) }}</textarea>
            </div>
          </div>
        </div>

        <div class="card form-card">
          <div class="card-header"><div class="section-icon"><i class="bx bx-calendar"></i></div> Survey Details</div>
          <div class="card-body row g-3">
            <div class="col-md-4">
              <label class="form-label fw-600">Survey Date</label>
              <input type="date" name="survey_date" class="form-control" value="{{ old('survey_date', $survey->survey_date ? \Carbon\Carbon::parse($survey->survey_date)->format('Y-m-d') : '') }}">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-600">Status</label>
              <select name="status" class="form-select">
                @foreach(['pending','completed','quoted'] as $s)
                  <option value="{{ $s }}" {{ old('status',$survey->status)===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-600">Technician</label>
              <input type="text" name="technician_name" class="form-control" value="{{ old('technician_name', $survey->technician_name) }}">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">No. of Cameras</label>
              <input type="number" name="camera_count" class="form-control" value="{{ old('camera_count', $survey->camera_count) }}" min="0">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">Camera Type</label>
              <input type="text" name="camera_type" class="form-control" value="{{ old('camera_type', $survey->camera_type) }}">
            </div>
            <div class="col-12">
              <label class="form-label fw-600">Site Observations</label>
              <textarea name="observations" class="form-control" rows="3">{{ old('observations', $survey->observations) }}</textarea>
            </div>
            <div class="col-12">
              <label class="form-label fw-600">Recommendations</label>
              <textarea name="recommendations" class="form-control" rows="3">{{ old('recommendations', $survey->recommendations) }}</textarea>
            </div>
            <div class="col-12">
              <label class="form-label fw-600">Notes</label>
              <textarea name="notes" class="form-control" rows="2">{{ old('notes', $survey->notes) }}</textarea>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card form-card">
          <div class="card-header"><div class="section-icon"><i class="bx bx-save"></i></div> Save</div>
          <div class="card-body">
            <div class="mb-3 p-3 rounded" style="background:#f8f9fa;">
              <div class="small text-muted fw-600">Survey No</div>
              <div class="fw-700 font-monospace text-primary">{{ $survey->survey_no }}</div>
              <div class="small text-muted mt-1">{{ $survey->created_at->format('d M Y') }}</div>
            </div>
            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> Update Survey</button>
              <a href="{{ route('admin.cctv.surveys.show', $survey) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
@endsection
