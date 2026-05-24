@extends('layouts.admin')
@section('title', 'New Field Complaint')
@section('page-title', 'New Field Complaint')
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.field-complaints.index') }}">Field Complaints</a></li>
  <li class="breadcrumb-item active">New</li>
@endsection

@push('styles')
<style>
.fc-form-header { background:linear-gradient(135deg,#f59e0b,#d97706);border-radius:14px 14px 0 0;padding:20px 24px;color:#fff;display:flex;align-items:center;gap:14px; }
.fc-form-header .ico { width:44px;height:44px;background:rgba(255,255,255,.2);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.4rem; }
.fc-form-header h5 { margin:0;font-weight:700; }
.fc-form-header p  { margin:0;opacity:.85;font-size:.82rem; }
.fc-card { border:0;border-radius:0 0 14px 14px;box-shadow:0 4px 24px rgba(245,158,11,.13); }
.section-divider { font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#d97706;border-bottom:2px solid #fef3c7;padding-bottom:8px;margin:24px 0 16px;display:flex;align-items:center;gap:8px; }
</style>
@endpush

@section('content')
<div style="max-width:780px">

<div class="fc-form-header">
  <div class="ico"><i class='bx bx-map-pin'></i></div>
  <div>
    <h5>New Field Complaint</h5>
    <p>Log a new on-site service request</p>
  </div>
</div>

<div class="card fc-card">
  <div class="card-body p-4">

    @if($errors->any())
    <div class="alert alert-danger mb-3">
      <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <form action="{{ route('admin.field-complaints.store') }}" method="POST">
      @csrf

      {{-- Customer Info --}}
      <div class="section-divider"><i class='bx bx-user'></i> Customer Info</div>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label fw-semibold">Customer Name <span class="text-danger">*</span></label>
          <input type="text" name="customer_name" class="form-control @error('customer_name') is-invalid @enderror"
            value="{{ old('customer_name') }}" required placeholder="Full name" />
          @error('customer_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">Phone <span class="text-danger">*</span></label>
          <input type="text" name="phone_no" class="form-control @error('phone_no') is-invalid @enderror"
            value="{{ old('phone_no') }}" required placeholder="07X XXX XXXX" />
          @error('phone_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-12">
          <label class="form-label fw-semibold">Address <span class="text-danger">*</span></label>
          <input type="text" name="address" class="form-control @error('address') is-invalid @enderror"
            value="{{ old('address') }}" required placeholder="Street, City" />
          @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-12">
          <label class="form-label fw-semibold">Location Notes</label>
          <input type="text" name="location_notes" class="form-control"
            value="{{ old('location_notes') }}" placeholder="Landmarks, floor number, directions…" />
        </div>
      </div>

      {{-- Service Details --}}
      <div class="section-divider"><i class='bx bx-wrench'></i> Service Details</div>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label fw-semibold">Service Type</label>
          <select name="service_type_id" class="form-select" id="serviceTypeSelect">
            <option value="">— Select type —</option>
            @foreach($serviceTypes as $st)
              <option value="{{ $st->id }}" data-charge="{{ $st->base_charge }}"
                {{ old('service_type_id') == $st->id ? 'selected' : '' }}>
                {{ $st->name }} (Rs. {{ number_format($st->base_charge,2) }})
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold">Priority</label>
          <select name="priority" class="form-select">
            @foreach(['Low','Normal','High','Urgent'] as $p)
              <option value="{{ $p }}" {{ old('priority','Normal')==$p?'selected':'' }}>{{ $p }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold">Scheduled Date</label>
          <input type="date" name="scheduled_date" class="form-control" value="{{ old('scheduled_date') }}" />
        </div>
        <div class="col-12">
          <label class="form-label fw-semibold">Problem Description</label>
          <textarea name="description" class="form-control" rows="3" placeholder="Describe the issue in detail…">{{ old('description') }}</textarea>
        </div>
        <div class="col-12">
          <label class="form-label fw-semibold">Remark / Internal Notes</label>
          <input type="text" name="remark" class="form-control" value="{{ old('remark') }}" placeholder="Internal notes (not shown to customer)" />
        </div>
      </div>

      {{-- Payment --}}
      <div class="section-divider"><i class='bx bx-money'></i> Advance Payment</div>
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label fw-semibold">Advance Amount (Rs.)</label>
          <div class="input-group">
            <span class="input-group-text">Rs.</span>
            <input type="number" name="advance_amount" class="form-control" value="{{ old('advance_amount', 0) }}" min="0" step="0.01" />
          </div>
        </div>
      </div>

      <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn btn-warning px-4 fw-bold" style="border-radius:10px;color:#fff;">
          <i class='bx bx-save me-1'></i>Log Complaint
        </button>
        <a href="{{ route('admin.field-complaints.index') }}" class="btn btn-outline-secondary px-4 fw-bold" style="border-radius:10px;">Cancel</a>
      </div>
    </form>
  </div>
</div>
</div>
@endsection
