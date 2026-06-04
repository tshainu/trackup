@extends('layouts.admin')
@section('title', 'Edit Ticket – ' . $serviceTicket->ticket_no)

@push('styles')
<style>
  .hero-bar { background:linear-gradient(135deg,#ea5455,#c0392b); border-radius:16px; padding:1.25rem 1.75rem; color:#fff; margin-bottom:1.5rem; display:flex; align-items:center; gap:1rem; }
  .hero-bar .back-btn { width:38px; height:38px; border-radius:10px; background:rgba(255,255,255,.2); border:0; color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.2rem; text-decoration:none; transition:background .15s; }
  .hero-bar .back-btn:hover { background:rgba(255,255,255,.32); color:#fff; }
  .hero-bar h4 { margin:0; font-size:1.2rem; font-weight:700; }
  .hero-bar p { margin:0; opacity:.85; font-size:.85rem; }
  .form-card { border-radius:14px; border:0; box-shadow:0 2px 12px rgba(0,0,0,.06); margin-bottom:1.25rem; }
  .form-card .card-header { background:#f8f9fa; border-radius:14px 14px 0 0; padding:.85rem 1.25rem; font-weight:700; font-size:.875rem; display:flex; align-items:center; gap:.5rem; border-bottom:1px solid rgba(0,0,0,.06); }
  .section-icon { width:28px; height:28px; border-radius:8px; background:#fdeaea; color:#ea5455; display:flex; align-items:center; justify-content:center; font-size:.9rem; }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="hero-bar">
    <a href="{{ route('admin.cctv.service-tickets.show', $serviceTicket) }}" class="back-btn"><i class="bx bx-arrow-back"></i></a>
    <div><h4>Edit Ticket – {{ $serviceTicket->ticket_no }}</h4><p>{{ $serviceTicket->customer_name }}</p></div>
  </div>

  <form method="POST" action="{{ route('admin.cctv.service-tickets.update', $serviceTicket) }}">
    @csrf @method('PUT')
    <div class="row g-3">
      <div class="col-lg-8">
        <div class="card form-card">
          <div class="card-header"><div class="section-icon"><i class="bx bx-user"></i></div> Customer</div>
          <div class="card-body row g-3">
            <div class="col-md-6">
              <label class="form-label fw-600">Customer Name <span class="text-danger">*</span></label>
              <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name', $serviceTicket->customer_name) }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">Mobile</label>
              <input type="text" name="mobile" class="form-control" value="{{ old('mobile', $serviceTicket->mobile) }}">
            </div>
            <div class="col-12">
              <label class="form-label fw-600">Address</label>
              <textarea name="address" class="form-control" rows="2">{{ old('address', $serviceTicket->address) }}</textarea>
            </div>
          </div>
        </div>

        <div class="card form-card">
          <div class="card-header"><div class="section-icon"><i class="bx bx-support"></i></div> Ticket Details</div>
          <div class="card-body row g-3">
            <div class="col-md-4">
              <label class="form-label fw-600">Issue Type</label>
              <select name="issue_type" class="form-select">
                <option value="">— Select —</option>
                @foreach(['No Video','Poor Image','Camera Offline','DVR Issue','Cable Fault','Network Issue','Power Issue','HDD Failure','Remote View Issue','Other'] as $t)
                  <option value="{{ $t }}" {{ old('issue_type',$serviceTicket->issue_type)===$t?'selected':'' }}>{{ $t }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-600">Priority</label>
              <select name="priority" class="form-select">
                @foreach(['low','medium','high','urgent'] as $p)
                  <option value="{{ $p }}" {{ old('priority',$serviceTicket->priority)===$p?'selected':'' }}>{{ ucfirst($p) }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-600">Status</label>
              <select name="status" class="form-select">
                @foreach(['open','in_progress','resolved','closed'] as $s)
                  <option value="{{ $s }}" {{ old('status',$serviceTicket->status)===$s?'selected':'' }}>{{ ucwords(str_replace('_',' ',$s)) }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">Technician</label>
              <input type="text" name="technician_name" class="form-control" value="{{ old('technician_name', $serviceTicket->technician_name) }}">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">Scheduled Date</label>
              <input type="date" name="scheduled_date" class="form-control" value="{{ old('scheduled_date', $serviceTicket->scheduled_date ? \Carbon\Carbon::parse($serviceTicket->scheduled_date)->format('Y-m-d') : '') }}">
            </div>
            <div class="col-12">
              <label class="form-label fw-600">Issue Description</label>
              <textarea name="issue_description" class="form-control" rows="3">{{ old('issue_description', $serviceTicket->issue_description) }}</textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">Service Charge (Rs.)</label>
              <input type="number" name="service_charge" step="0.01" class="form-control" value="{{ old('service_charge', $serviceTicket->service_charge ?? 0) }}">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">Parts Cost (Rs.)</label>
              <input type="number" name="parts_cost" step="0.01" class="form-control" value="{{ old('parts_cost', $serviceTicket->parts_cost ?? 0) }}">
            </div>
            <div class="col-12">
              <label class="form-label fw-600">Resolution Notes</label>
              <textarea name="resolution_notes" class="form-control" rows="2">{{ old('resolution_notes', $serviceTicket->resolution_notes) }}</textarea>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card form-card">
          <div class="card-header"><div class="section-icon"><i class="bx bx-save"></i></div> Save</div>
          <div class="card-body">
            <div class="mb-3 p-3 rounded" style="background:#f8f9fa;">
              <div class="small text-muted fw-600">Ticket No</div>
              <div class="fw-700 font-monospace text-primary">{{ $serviceTicket->ticket_no }}</div>
              <div class="small text-muted mt-1">{{ $serviceTicket->created_at->format('d M Y') }}</div>
            </div>
            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> Update Ticket</button>
              <a href="{{ route('admin.cctv.service-tickets.show', $serviceTicket) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
@endsection
