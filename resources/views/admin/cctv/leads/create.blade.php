@extends('layouts.admin')
@section('title', 'New CCTV Lead')

@push('styles')
<style>
  :root { --c-primary:#696cff; --c-primary-border:#c5c7ff; --c-primary-soft:#f0f0ff; --c-text:#566a7f; --c-muted:#a1acbb; --c-border:#d9dee3; --c-danger:#ff3e1d; }
  .hero-bar { background:linear-gradient(135deg,#696cff,#8c57ff); border-radius:16px; padding:1.25rem 1.75rem; color:#fff; margin-bottom:1.5rem; display:flex; align-items:center; gap:1rem; }
  .hero-bar .back-btn { width:38px; height:38px; border-radius:10px; background:rgba(255,255,255,.2); border:0; color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.2rem; flex-shrink:0; text-decoration:none; transition:background .15s; }
  .hero-bar .back-btn:hover { background:rgba(255,255,255,.32); color:#fff; }
  .hero-bar h4 { margin:0; font-size:1.2rem; font-weight:700; }
  .hero-bar p { margin:0; opacity:.85; font-size:.85rem; }
  .form-card { border-radius:14px; border:0; box-shadow:0 2px 12px rgba(105,108,255,.08); margin-bottom:1.25rem; }
  .form-card .card-header { background:#f8f9fa; border-radius:14px 14px 0 0; padding:.85rem 1.25rem; font-weight:700; font-size:.875rem; display:flex; align-items:center; gap:.5rem; border-bottom:1px solid rgba(0,0,0,.06); }
  .section-icon { width:28px; height:28px; border-radius:8px; background:#eef0ff; color:#696cff; display:flex; align-items:center; justify-content:center; font-size:.9rem; }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="hero-bar">
    <a href="{{ route('admin.cctv.leads.index') }}" class="back-btn"><i class="bx bx-arrow-back"></i></a>
    <div>
      <h4>New CCTV Lead</h4>
      <p>Capture a new CCTV enquiry</p>
    </div>
  </div>

  <form method="POST" action="{{ route('admin.cctv.leads.store') }}">
    @csrf
    <div class="row g-3">
      <div class="col-lg-8">

        {{-- Customer Info --}}
        <div class="card form-card">
          <div class="card-header">
            <div class="section-icon"><i class="bx bx-user"></i></div> Customer Details
          </div>
          <div class="card-body row g-3">
            <div class="col-md-6">
              <label class="form-label fw-600">Customer Name <span class="text-danger">*</span></label>
              <input type="text" name="customer_name" class="form-control @error('customer_name') is-invalid @enderror" value="{{ old('customer_name') }}" required>
              @error('customer_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">Mobile <span class="text-danger">*</span></label>
              <input type="text" name="mobile" class="form-control @error('mobile') is-invalid @enderror" value="{{ old('mobile') }}" required>
              @error('mobile')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">Email</label>
              <input type="email" name="email" class="form-control" value="{{ old('email') }}">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">Location / Area</label>
              <input type="text" name="location" class="form-control" value="{{ old('location') }}">
            </div>
            <div class="col-12">
              <label class="form-label fw-600">Address</label>
              <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
            </div>
          </div>
        </div>

        {{-- Lead Details --}}
        <div class="card form-card">
          <div class="card-header">
            <div class="section-icon"><i class="bx bx-detail"></i></div> Lead Details
          </div>
          <div class="card-body row g-3">
            <div class="col-md-4">
              <label class="form-label fw-600">Source</label>
              <select name="source" class="form-select">
                <option value="">— Select —</option>
                @foreach(['Walk-in','Phone','WhatsApp','Referral','Facebook','Instagram','Website','Other'] as $src)
                  <option value="{{ $src }}" {{ old('source')===$src?'selected':'' }}>{{ $src }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-600">Status</label>
              <select name="status" class="form-select">
                @foreach(['new','contacted','qualified','converted','lost'] as $s)
                  <option value="{{ $s }}" {{ old('status','new')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-600">Follow Up Date</label>
              <input type="date" name="follow_up_date" class="form-control" value="{{ old('follow_up_date') }}">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">Requirement Type</label>
              <input type="text" name="requirement_type" class="form-control" placeholder="e.g. 4CH Home, 8CH Office" value="{{ old('requirement_type') }}">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">Budget (Rs.)</label>
              <input type="number" name="budget" step="0.01" class="form-control" value="{{ old('budget') }}">
            </div>
            <div class="col-12">
              <label class="form-label fw-600">Notes</label>
              <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
            </div>
          </div>
        </div>

      </div>
      <div class="col-lg-4">
        <div class="card form-card">
          <div class="card-header"><div class="section-icon"><i class="bx bx-save"></i></div> Save</div>
          <div class="card-body">
            <p class="text-muted small mb-3">Lead number will be auto-generated on save.</p>
            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> Save Lead</button>
              <a href="{{ route('admin.cctv.leads.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
@endsection
