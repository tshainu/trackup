@extends('layouts.admin')
@section('title', 'Repair – ' . $repair->repair_no)

@push('styles')
<style>
  .hero-bar { background:linear-gradient(135deg,#4b4b5a,#2d2d3a); border-radius:16px; padding:1.25rem 1.75rem; color:#fff; margin-bottom:1.5rem; display:flex; align-items:center; gap:1rem; flex-wrap:wrap; }
  .hero-bar .back-btn { width:38px; height:38px; border-radius:10px; background:rgba(255,255,255,.2); border:0; color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.2rem; text-decoration:none; transition:background .15s; }
  .hero-bar .back-btn:hover { background:rgba(255,255,255,.32); color:#fff; }
  .hero-bar h4 { margin:0; font-size:1.2rem; font-weight:700; }
  .section-card { border-radius:14px; border:0; box-shadow:0 2px 12px rgba(105,108,255,.08); margin-bottom:1.25rem; }
  .section-card .card-header { background:#f8f9fa; border-radius:14px 14px 0 0; padding:.85rem 1.25rem; font-weight:700; font-size:.875rem; display:flex; align-items:center; gap:.5rem; border-bottom:1px solid rgba(0,0,0,.06); }
  .section-icon { width:28px; height:28px; border-radius:8px; background:#f0f0f5; color:#4b4b5a; display:flex; align-items:center; justify-content:center; font-size:.9rem; }
  .info-label { font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#a1acb8; margin-bottom:.25rem; }
  .info-value { font-size:.92rem; font-weight:500; color:#32325d; }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="hero-bar">
    <a href="{{ route('admin.cctv.repairs.index') }}" class="back-btn"><i class="bx bx-arrow-back"></i></a>
    <div class="flex-grow-1">
      <h4>{{ $repair->repair_no }} — {{ $repair->customer_name }}</h4>
      @php $sc = ['pending'=>'warning','in_progress'=>'info','completed'=>'success','delivered'=>'primary','cancelled'=>'danger'][$repair->status] ?? 'secondary' @endphp
      <span class="badge bg-label-{{ $sc }}" style="margin-top:4px;">{{ ucwords(str_replace('_',' ',$repair->status)) }}</span>
    </div>
    <a href="{{ route('admin.cctv.repairs.edit', $repair) }}" class="btn btn-light btn-sm"><i class="bx bx-edit me-1"></i> Edit</a>
  </div>

  <div class="row g-3">
    <div class="col-lg-8">
      <div class="card section-card">
        <div class="card-header"><div class="section-icon"><i class="bx bx-user"></i></div> Customer</div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-sm-6"><div class="info-label">Name</div><div class="info-value">{{ $repair->customer_name }}</div></div>
            <div class="col-sm-6"><div class="info-label">Mobile</div><div class="info-value font-monospace">{{ $repair->mobile }}</div></div>
          </div>
        </div>
      </div>

      <div class="card section-card">
        <div class="card-header"><div class="section-icon"><i class="bx bx-tool"></i></div> Device Details</div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-sm-4"><div class="info-label">Device Type</div><div class="info-value">{{ $repair->device_type ?? '—' }}</div></div>
            <div class="col-sm-4"><div class="info-label">Brand</div><div class="info-value">{{ $repair->brand ?? '—' }}</div></div>
            <div class="col-sm-4"><div class="info-label">Model</div><div class="info-value">{{ $repair->model ?? '—' }}</div></div>
            <div class="col-sm-6"><div class="info-label">Serial No</div><div class="info-value font-monospace">{{ $repair->serial_no ?? '—' }}</div></div>
            <div class="col-sm-6"><div class="info-label">Technician</div><div class="info-value">{{ $repair->technician_name ?? '—' }}</div></div>
            <div class="col-12"><div class="info-label">Issue Description</div><div class="info-value" style="white-space:pre-line">{{ $repair->issue_description }}</div></div>
            <div class="col-12"><div class="info-label">Repair Notes</div><div class="info-value" style="white-space:pre-line">{{ $repair->repair_notes ?? '—' }}</div></div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card section-card">
        <div class="card-header"><div class="section-icon"><i class="bx bx-rupee"></i></div> Charges</div>
        <div class="card-body">
          <div class="d-flex justify-content-between mb-2 border-bottom pb-2"><span class="text-muted small">Repair Charge</span><span class="fw-600">Rs. {{ number_format($repair->repair_charge ?? 0, 2) }}</span></div>
          <div class="d-flex justify-content-between mb-2 border-bottom pb-2"><span class="text-muted small">Parts Cost</span><span class="fw-600">Rs. {{ number_format($repair->parts_cost ?? 0, 2) }}</span></div>
          <div class="d-flex justify-content-between mb-2 border-bottom pb-2"><span class="text-muted small">Advance</span><span class="text-success fw-600">Rs. {{ number_format($repair->advance_paid ?? 0, 2) }}</span></div>
          @php $total = ($repair->repair_charge ?? 0) + ($repair->parts_cost ?? 0); $balance = $total - ($repair->advance_paid ?? 0); @endphp
          <div class="d-flex justify-content-between pt-1"><span class="fw-700">Balance Due</span><span class="fw-700 {{ $balance > 0 ? 'text-danger' : 'text-success' }}">Rs. {{ number_format(max(0,$balance), 2) }}</span></div>
        </div>
      </div>

      <div class="card section-card">
        <div class="card-header"><div class="section-icon"><i class="bx bx-info-circle"></i></div> Quick Info</div>
        <div class="card-body">
          <div class="mb-3"><div class="info-label">Repair No</div><div class="fw-700 font-monospace text-primary">{{ $repair->repair_no }}</div></div>
          <div class="mb-3"><div class="info-label">Status</div><span class="badge bg-label-{{ $sc }}">{{ ucwords(str_replace('_',' ',$repair->status)) }}</span></div>
          <div class="mb-3"><div class="info-label">Est. Delivery</div><div class="info-value">{{ $repair->estimated_delivery ? \Carbon\Carbon::parse($repair->estimated_delivery)->format('d M Y') : '—' }}</div></div>
          <div class="mb-3"><div class="info-label">Received</div><div class="info-value">{{ $repair->created_at->format('d M Y') }}</div></div>
          <hr>
          <div class="d-grid">
            <a href="{{ route('admin.cctv.repairs.edit', $repair) }}" class="btn btn-primary btn-sm"><i class="bx bx-edit me-1"></i> Edit Repair</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
