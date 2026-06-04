@extends('layouts.admin')
@section('title', 'Edit Project – ' . $project->project_no)

@push('styles')
<style>
  .hero-bar { background:linear-gradient(135deg,#fd7e14,#e55a00); border-radius:16px; padding:1.25rem 1.75rem; color:#fff; margin-bottom:1.5rem; display:flex; align-items:center; gap:1rem; }
  .hero-bar .back-btn { width:38px; height:38px; border-radius:10px; background:rgba(255,255,255,.2); border:0; color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.2rem; text-decoration:none; transition:background .15s; }
  .hero-bar .back-btn:hover { background:rgba(255,255,255,.32); color:#fff; }
  .hero-bar h4 { margin:0; font-size:1.2rem; font-weight:700; }
  .hero-bar p { margin:0; opacity:.85; font-size:.85rem; }
  .form-card { border-radius:14px; border:0; box-shadow:0 2px 12px rgba(0,0,0,.06); margin-bottom:1.25rem; }
  .form-card .card-header { background:#f8f9fa; border-radius:14px 14px 0 0; padding:.85rem 1.25rem; font-weight:700; font-size:.875rem; display:flex; align-items:center; gap:.5rem; border-bottom:1px solid rgba(0,0,0,.06); }
  .section-icon { width:28px; height:28px; border-radius:8px; background:#fff3e8; color:#fd7e14; display:flex; align-items:center; justify-content:center; font-size:.9rem; }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="hero-bar">
    <a href="{{ route('admin.cctv.projects.show', $project) }}" class="back-btn"><i class="bx bx-arrow-back"></i></a>
    <div><h4>Edit Project – {{ $project->project_no }}</h4><p>{{ $project->customer_name }}</p></div>
  </div>

  <form method="POST" action="{{ route('admin.cctv.projects.update', $project) }}">
    @csrf @method('PUT')
    <div class="row g-3">
      <div class="col-lg-8">
        <div class="card form-card">
          <div class="card-header"><div class="section-icon"><i class="bx bx-user"></i></div> Customer</div>
          <div class="card-body row g-3">
            <div class="col-md-6">
              <label class="form-label fw-600">Customer Name <span class="text-danger">*</span></label>
              <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name', $project->customer_name) }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">Mobile</label>
              <input type="text" name="mobile" class="form-control" value="{{ old('mobile', $project->mobile) }}">
            </div>
            <div class="col-12">
              <label class="form-label fw-600">Address</label>
              <textarea name="address" class="form-control" rows="2">{{ old('address', $project->address) }}</textarea>
            </div>
          </div>
        </div>

        <div class="card form-card">
          <div class="card-header"><div class="section-icon"><i class="bx bx-wrench"></i></div> Project Details</div>
          <div class="card-body row g-3">
            <div class="col-md-4">
              <label class="form-label fw-600">Status</label>
              <select name="status" class="form-select">
                @foreach(['scheduled','in_progress','completed','on_hold','cancelled'] as $s)
                  <option value="{{ $s }}" {{ old('status',$project->status)===$s?'selected':'' }}>{{ ucwords(str_replace('_',' ',$s)) }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-600">Start Date</label>
              <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('Y-m-d') : '') }}">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-600">End Date</label>
              <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('Y-m-d') : '') }}">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">Technician</label>
              <input type="text" name="technician_name" class="form-control" value="{{ old('technician_name', $project->technician_name) }}">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">No. of Cameras</label>
              <input type="number" name="camera_count" class="form-control" value="{{ old('camera_count', $project->camera_count) }}" min="0">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">Contract Amount (Rs.)</label>
              <input type="number" name="contract_amount" step="0.01" class="form-control" value="{{ old('contract_amount', $project->contract_amount) }}">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">Advance Paid (Rs.)</label>
              <input type="number" name="advance_paid" step="0.01" class="form-control" value="{{ old('advance_paid', $project->advance_paid) }}">
            </div>
            <div class="col-12">
              <label class="form-label fw-600">Scope of Work</label>
              <textarea name="scope" class="form-control" rows="3">{{ old('scope', $project->scope) }}</textarea>
            </div>
            <div class="col-12">
              <label class="form-label fw-600">Notes</label>
              <textarea name="notes" class="form-control" rows="2">{{ old('notes', $project->notes) }}</textarea>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card form-card">
          <div class="card-header"><div class="section-icon"><i class="bx bx-save"></i></div> Save</div>
          <div class="card-body">
            <div class="mb-3 p-3 rounded" style="background:#f8f9fa;">
              <div class="small text-muted fw-600">Project No</div>
              <div class="fw-700 font-monospace text-primary">{{ $project->project_no }}</div>
              <div class="small text-muted mt-1">{{ $project->created_at->format('d M Y') }}</div>
            </div>
            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> Update Project</button>
              <a href="{{ route('admin.cctv.projects.show', $project) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
@endsection
