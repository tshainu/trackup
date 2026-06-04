@extends('layouts.admin')
@section('title', 'New Repair Job')

@push('styles')
<style>
  .hero-bar { background:linear-gradient(135deg,#4b4b5a,#2d2d3a); border-radius:16px; padding:1.25rem 1.75rem; color:#fff; margin-bottom:1.5rem; display:flex; align-items:center; gap:1rem; }
  .hero-bar .back-btn { width:38px; height:38px; border-radius:10px; background:rgba(255,255,255,.2); border:0; color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.2rem; text-decoration:none; transition:background .15s; }
  .hero-bar .back-btn:hover { background:rgba(255,255,255,.32); color:#fff; }
  .hero-bar h4 { margin:0; font-size:1.2rem; font-weight:700; }
  .hero-bar p { margin:0; opacity:.85; font-size:.85rem; }
  .form-card { border-radius:14px; border:0; box-shadow:0 2px 12px rgba(0,0,0,.06); margin-bottom:1.25rem; }
  .form-card .card-header { background:#f8f9fa; border-radius:14px 14px 0 0; padding:.85rem 1.25rem; font-weight:700; font-size:.875rem; display:flex; align-items:center; gap:.5rem; border-bottom:1px solid rgba(0,0,0,.06); }
  .section-icon { width:28px; height:28px; border-radius:8px; background:#f0f0f5; color:#4b4b5a; display:flex; align-items:center; justify-content:center; font-size:.9rem; }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="hero-bar">
    <a href="{{ route('admin.cctv.repairs.index') }}" class="back-btn"><i class="bx bx-arrow-back"></i></a>
    <div><h4>New Repair Job</h4><p>Log a CCTV device for repair</p></div>
  </div>

  <form method="POST" action="{{ route('admin.cctv.repairs.store') }}">
    @csrf
    <div class="row g-3">
      <div class="col-lg-8">
        <div class="card form-card">
          <div class="card-header"><div class="section-icon"><i class="bx bx-user"></i></div> Customer</div>
          <div class="card-body row g-3">
            <div class="col-md-6">
              <label class="form-label fw-600">Customer Name <span class="text-danger">*</span></label>
              <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name') }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">Mobile <span class="text-danger">*</span></label>
              <input type="text" name="mobile" class="form-control" value="{{ old('mobile') }}" required>
            </div>
          </div>
        </div>

        <div class="card form-card">
          <div class="card-header"><div class="section-icon"><i class="bx bx-tool"></i></div> Device & Repair Info</div>
          <div class="card-body row g-3">
            <div class="col-md-4">
              <label class="form-label fw-600">Device Type <span class="text-danger">*</span></label>
              <select name="device_type" class="form-select" required>
                <option value="">— Select —</option>
                @foreach(['IP Camera','Analog Camera','DVR','NVR','PTZ Camera','HDD','Monitor','Other'] as $t)
                  <option value="{{ $t }}" {{ old('device_type')===$t?'selected':'' }}>{{ $t }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-600">Brand</label>
              <input type="text" name="brand" class="form-control" value="{{ old('brand') }}">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-600">Model</label>
              <input type="text" name="model" class="form-control" value="{{ old('model') }}">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">Serial No</label>
              <input type="text" name="serial_no" class="form-control font-monospace" value="{{ old('serial_no') }}">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">Status</label>
              <select name="status" class="form-select">
                @foreach(['pending','in_progress','completed','delivered','cancelled'] as $s)
                  <option value="{{ $s }}" {{ old('status','pending')===$s?'selected':'' }}>{{ ucwords(str_replace('_',' ',$s)) }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">Technician</label>
              <input type="text" name="technician_name" class="form-control" value="{{ old('technician_name') }}">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">Estimated Delivery</label>
              <input type="date" name="estimated_delivery" class="form-control" value="{{ old('estimated_delivery') }}">
            </div>
            <div class="col-12">
              <label class="form-label fw-600">Issue Description <span class="text-danger">*</span></label>
              <textarea name="issue_description" class="form-control" rows="3" required>{{ old('issue_description') }}</textarea>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-600">Repair Charge (Rs.)</label>
              <input type="number" name="repair_charge" step="0.01" class="form-control" value="{{ old('repair_charge', 0) }}">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-600">Parts Cost (Rs.)</label>
              <input type="number" name="parts_cost" step="0.01" class="form-control" value="{{ old('parts_cost', 0) }}">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-600">Advance (Rs.)</label>
              <input type="number" name="advance_paid" step="0.01" class="form-control" value="{{ old('advance_paid', 0) }}">
            </div>
            <div class="col-12">
              <label class="form-label fw-600">Repair Notes</label>
              <textarea name="repair_notes" class="form-control" rows="2">{{ old('repair_notes') }}</textarea>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card form-card">
          <div class="card-header"><div class="section-icon"><i class="bx bx-save"></i></div> Save</div>
          <div class="card-body">
            <p class="text-muted small mb-3">Repair number auto-generated.</p>
            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> Save Repair Job</button>
              <a href="{{ route('admin.cctv.repairs.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
@endsection
