@extends('layouts.admin')
@section('title', 'Edit AMC – ' . $amc->amc_no)

@push('styles')
<style>
  .hero-bar { background:linear-gradient(135deg,#ffab00,#cc8800); border-radius:16px; padding:1.25rem 1.75rem; color:#fff; margin-bottom:1.5rem; display:flex; align-items:center; gap:1rem; }
  .hero-bar .back-btn { width:38px; height:38px; border-radius:10px; background:rgba(255,255,255,.2); border:0; color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.2rem; text-decoration:none; transition:background .15s; }
  .hero-bar .back-btn:hover { background:rgba(255,255,255,.32); color:#fff; }
  .hero-bar h4 { margin:0; font-size:1.2rem; font-weight:700; }
  .hero-bar p { margin:0; opacity:.85; font-size:.85rem; }
  .form-card { border-radius:14px; border:0; box-shadow:0 2px 12px rgba(0,0,0,.06); margin-bottom:1.25rem; }
  .form-card .card-header { background:#f8f9fa; border-radius:14px 14px 0 0; padding:.85rem 1.25rem; font-weight:700; font-size:.875rem; display:flex; align-items:center; gap:.5rem; border-bottom:1px solid rgba(0,0,0,.06); }
  .section-icon { width:28px; height:28px; border-radius:8px; background:#fff8e6; color:#cc8800; display:flex; align-items:center; justify-content:center; font-size:.9rem; }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="hero-bar">
    <a href="{{ route('admin.cctv.amc.show', $amc) }}" class="back-btn"><i class="bx bx-arrow-back"></i></a>
    <div><h4>Edit AMC – {{ $amc->amc_no }}</h4><p>{{ $amc->customer_name }}</p></div>
  </div>

  <form method="POST" action="{{ route('admin.cctv.amc.update', $amc) }}">
    @csrf @method('PUT')
    <div class="row g-3">
      <div class="col-lg-8">
        <div class="card form-card">
          <div class="card-header"><div class="section-icon"><i class="bx bx-user"></i></div> Customer</div>
          <div class="card-body row g-3">
            <div class="col-md-6">
              <label class="form-label fw-600">Customer Name <span class="text-danger">*</span></label>
              <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name', $amc->customer_name) }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">Mobile</label>
              <input type="text" name="mobile" class="form-control" value="{{ old('mobile', $amc->mobile) }}">
            </div>
            <div class="col-12">
              <label class="form-label fw-600">Address</label>
              <textarea name="address" class="form-control" rows="2">{{ old('address', $amc->address) }}</textarea>
            </div>
          </div>
        </div>

        <div class="card form-card">
          <div class="card-header"><div class="section-icon"><i class="bx bx-shield-quarter"></i></div> Contract Details</div>
          <div class="card-body row g-3">
            <div class="col-md-4">
              <label class="form-label fw-600">Status</label>
              <select name="status" class="form-select">
                @foreach(['active','expired','cancelled'] as $s)
                  <option value="{{ $s }}" {{ old('status',$amc->status)===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-600">Start Date</label>
              <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $amc->start_date ? \Carbon\Carbon::parse($amc->start_date)->format('Y-m-d') : '') }}">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-600">End Date</label>
              <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $amc->end_date ? \Carbon\Carbon::parse($amc->end_date)->format('Y-m-d') : '') }}">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">Contract Amount (Rs.)</label>
              <input type="number" name="contract_amount" step="0.01" class="form-control" value="{{ old('contract_amount', $amc->contract_amount) }}">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">Visits/Year</label>
              <input type="number" name="visits_per_year" class="form-control" value="{{ old('visits_per_year', $amc->visits_per_year) }}" min="1">
            </div>
            <div class="col-12">
              <label class="form-label fw-600">Scope / Coverage</label>
              <textarea name="scope" class="form-control" rows="3">{{ old('scope', $amc->scope) }}</textarea>
            </div>
            <div class="col-12">
              <label class="form-label fw-600">Notes</label>
              <textarea name="notes" class="form-control" rows="2">{{ old('notes', $amc->notes) }}</textarea>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card form-card">
          <div class="card-header"><div class="section-icon"><i class="bx bx-save"></i></div> Save</div>
          <div class="card-body">
            <div class="mb-3 p-3 rounded" style="background:#f8f9fa;">
              <div class="small text-muted fw-600">AMC No</div>
              <div class="fw-700 font-monospace text-primary">{{ $amc->amc_no }}</div>
              <div class="small text-muted mt-1">{{ $amc->created_at->format('d M Y') }}</div>
            </div>
            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> Update AMC</button>
              <a href="{{ route('admin.cctv.amc.show', $amc) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
@endsection
