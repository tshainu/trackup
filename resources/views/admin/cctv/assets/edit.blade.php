@extends('layouts.admin')
@section('title', 'Edit Asset – ' . $asset->asset_no)

@push('styles')
<style>
  .hero-bar { background:linear-gradient(135deg,#28c76f,#1a8f4e); border-radius:16px; padding:1.25rem 1.75rem; color:#fff; margin-bottom:1.5rem; display:flex; align-items:center; gap:1rem; }
  .hero-bar .back-btn { width:38px; height:38px; border-radius:10px; background:rgba(255,255,255,.2); border:0; color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.2rem; text-decoration:none; transition:background .15s; }
  .hero-bar .back-btn:hover { background:rgba(255,255,255,.32); color:#fff; }
  .hero-bar h4 { margin:0; font-size:1.2rem; font-weight:700; }
  .hero-bar p { margin:0; opacity:.85; font-size:.85rem; }
  .form-card { border-radius:14px; border:0; box-shadow:0 2px 12px rgba(0,0,0,.06); margin-bottom:1.25rem; }
  .form-card .card-header { background:#f8f9fa; border-radius:14px 14px 0 0; padding:.85rem 1.25rem; font-weight:700; font-size:.875rem; display:flex; align-items:center; gap:.5rem; border-bottom:1px solid rgba(0,0,0,.06); }
  .section-icon { width:28px; height:28px; border-radius:8px; background:#e8faf0; color:#28c76f; display:flex; align-items:center; justify-content:center; font-size:.9rem; }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="hero-bar">
    <a href="{{ route('admin.cctv.assets.show', $asset) }}" class="back-btn"><i class="bx bx-arrow-back"></i></a>
    <div><h4>Edit Asset – {{ $asset->asset_no }}</h4><p>{{ $asset->customer_name }}</p></div>
  </div>

  <form method="POST" action="{{ route('admin.cctv.assets.update', $asset) }}">
    @csrf @method('PUT')
    <div class="row g-3">
      <div class="col-lg-8">
        <div class="card form-card">
          <div class="card-header"><div class="section-icon"><i class="bx bx-user"></i></div> Customer / Site</div>
          <div class="card-body row g-3">
            <div class="col-md-6">
              <label class="form-label fw-600">Customer Name <span class="text-danger">*</span></label>
              <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name', $asset->customer_name) }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">Mobile</label>
              <input type="text" name="mobile" class="form-control" value="{{ old('mobile', $asset->mobile) }}">
            </div>
            <div class="col-12">
              <label class="form-label fw-600">Install Location</label>
              <input type="text" name="location" class="form-control" value="{{ old('location', $asset->location) }}">
            </div>
          </div>
        </div>

        <div class="card form-card">
          <div class="card-header"><div class="section-icon"><i class="bx bx-camera"></i></div> Device Information</div>
          <div class="card-body row g-3">
            <div class="col-md-4">
              <label class="form-label fw-600">Device Type <span class="text-danger">*</span></label>
              <select name="device_type" class="form-select" required>
                <option value="">— Select —</option>
                @foreach(['IP Camera','Analog Camera','DVR','NVR','PTZ Camera','Dome Camera','Bullet Camera','HDD','Monitor','Cable','Other'] as $t)
                  <option value="{{ $t }}" {{ old('device_type',$asset->device_type)===$t?'selected':'' }}>{{ $t }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-600">Brand</label>
              <input type="text" name="brand" class="form-control" value="{{ old('brand', $asset->brand) }}">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-600">Model</label>
              <input type="text" name="model" class="form-control" value="{{ old('model', $asset->model) }}">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">Serial Number</label>
              <input type="text" name="serial_no" class="form-control font-monospace" value="{{ old('serial_no', $asset->serial_no) }}">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">Status</label>
              <select name="status" class="form-select">
                @foreach(['active','faulty','under_repair','decommissioned'] as $s)
                  <option value="{{ $s }}" {{ old('status',$asset->status)===$s?'selected':'' }}>{{ ucwords(str_replace('_',' ',$s)) }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">Install Date</label>
              <input type="date" name="install_date" class="form-control" value="{{ old('install_date', $asset->install_date ? \Carbon\Carbon::parse($asset->install_date)->format('Y-m-d') : '') }}">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">Warranty Expiry</label>
              <input type="date" name="warranty_expiry" class="form-control" value="{{ old('warranty_expiry', $asset->warranty_expiry ? \Carbon\Carbon::parse($asset->warranty_expiry)->format('Y-m-d') : '') }}">
            </div>
            <div class="col-12">
              <label class="form-label fw-600">Notes</label>
              <textarea name="notes" class="form-control" rows="2">{{ old('notes', $asset->notes) }}</textarea>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card form-card">
          <div class="card-header"><div class="section-icon"><i class="bx bx-save"></i></div> Save</div>
          <div class="card-body">
            <div class="mb-3 p-3 rounded" style="background:#f8f9fa;">
              <div class="small text-muted fw-600">Asset No</div>
              <div class="fw-700 font-monospace text-primary">{{ $asset->asset_no }}</div>
            </div>
            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> Update Asset</button>
              <a href="{{ route('admin.cctv.assets.show', $asset) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
@endsection
