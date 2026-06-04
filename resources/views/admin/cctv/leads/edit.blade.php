@extends('layouts.admin')
@section('title', 'Edit Lead – ' . $lead->lead_no)

@push('styles')
<style>
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
    <a href="{{ route('admin.cctv.leads.show', $lead) }}" class="back-btn"><i class="bx bx-arrow-back"></i></a>
    <div>
      <h4>Edit Lead – {{ $lead->lead_no }}</h4>
      <p>{{ $lead->customer_name }}</p>
    </div>
  </div>

  <form method="POST" action="{{ route('admin.cctv.leads.update', $lead) }}">
    @csrf @method('PUT')
    <div class="row g-3">
      <div class="col-lg-8">
        <div class="card form-card">
          <div class="card-header"><div class="section-icon"><i class="bx bx-user"></i></div> Customer Details</div>
          <div class="card-body row g-3">
            <div class="col-md-6">
              <label class="form-label fw-600">Customer Name <span class="text-danger">*</span></label>
              <input type="text" name="customer_name" class="form-control @error('customer_name') is-invalid @enderror" value="{{ old('customer_name', $lead->customer_name) }}" required>
              @error('customer_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">Mobile <span class="text-danger">*</span></label>
              <input type="text" name="mobile" class="form-control @error('mobile') is-invalid @enderror" value="{{ old('mobile', $lead->mobile) }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">Email</label>
              <input type="email" name="email" class="form-control" value="{{ old('email', $lead->email) }}">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">Location / Area</label>
              <input type="text" name="location" class="form-control" value="{{ old('location', $lead->location) }}">
            </div>
            <div class="col-12">
              <label class="form-label fw-600">Address</label>
              <textarea name="address" class="form-control" rows="2">{{ old('address', $lead->address) }}</textarea>
            </div>
          </div>
        </div>

        <div class="card form-card">
          <div class="card-header"><div class="section-icon"><i class="bx bx-detail"></i></div> Lead Details</div>
          <div class="card-body row g-3">
            <div class="col-md-4">
              <label class="form-label fw-600">Source</label>
              <select name="source" class="form-select">
                <option value="">— Select —</option>
                @foreach(['Walk-in','Phone','WhatsApp','Referral','Facebook','Instagram','Website','Other'] as $src)
                  <option value="{{ $src }}" {{ old('source',$lead->source)===$src?'selected':'' }}>{{ $src }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-600">Status</label>
              <select name="status" class="form-select">
                @foreach(['new','contacted','qualified','converted','lost'] as $s)
                  <option value="{{ $s }}" {{ old('status',$lead->status)===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-600">Follow Up Date</label>
              <input type="date" name="follow_up_date" class="form-control" value="{{ old('follow_up_date', $lead->follow_up_date ? $lead->follow_up_date->format('Y-m-d') : '') }}">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">Requirement Type</label>
              <input type="text" name="requirement_type" class="form-control" value="{{ old('requirement_type', $lead->requirement_type) }}">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">Budget (Rs.)</label>
              <input type="number" name="budget" step="0.01" class="form-control" value="{{ old('budget', $lead->budget) }}">
            </div>
            <div class="col-12">
              <label class="form-label fw-600">Notes</label>
              <textarea name="notes" class="form-control" rows="3">{{ old('notes', $lead->notes) }}</textarea>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card form-card">
          <div class="card-header"><div class="section-icon"><i class="bx bx-save"></i></div> Save</div>
          <div class="card-body">
            <div class="mb-3 p-3 rounded" style="background:#f8f9fa;">
              <div class="small text-muted fw-600">Lead No</div>
              <div class="fw-700 font-monospace text-primary">{{ $lead->lead_no }}</div>
              <div class="small text-muted mt-1">Created {{ $lead->created_at->format('d M Y') }}</div>
            </div>
            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> Update Lead</button>
              <a href="{{ route('admin.cctv.leads.show', $lead) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
@endsection
