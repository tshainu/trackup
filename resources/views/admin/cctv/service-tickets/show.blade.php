@extends('layouts.admin')
@section('title', 'Ticket – ' . $serviceTicket->ticket_no)

@push('styles')
<style>
  .hero-bar { background:linear-gradient(135deg,#ea5455,#c0392b); border-radius:16px; padding:1.25rem 1.75rem; color:#fff; margin-bottom:1.5rem; display:flex; align-items:center; gap:1rem; flex-wrap:wrap; }
  .hero-bar .back-btn { width:38px; height:38px; border-radius:10px; background:rgba(255,255,255,.2); border:0; color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.2rem; text-decoration:none; transition:background .15s; }
  .hero-bar .back-btn:hover { background:rgba(255,255,255,.32); color:#fff; }
  .hero-bar h4 { margin:0; font-size:1.2rem; font-weight:700; }
  .section-card { border-radius:14px; border:0; box-shadow:0 2px 12px rgba(105,108,255,.08); margin-bottom:1.25rem; }
  .section-card .card-header { background:#f8f9fa; border-radius:14px 14px 0 0; padding:.85rem 1.25rem; font-weight:700; font-size:.875rem; display:flex; align-items:center; gap:.5rem; border-bottom:1px solid rgba(0,0,0,.06); }
  .section-icon { width:28px; height:28px; border-radius:8px; background:#fdeaea; color:#ea5455; display:flex; align-items:center; justify-content:center; font-size:.9rem; }
  .info-label { font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#a1acb8; margin-bottom:.25rem; }
  .info-value { font-size:.92rem; font-weight:500; color:#32325d; }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="hero-bar">
    <a href="{{ route('admin.cctv.service-tickets.index') }}" class="back-btn"><i class="bx bx-arrow-back"></i></a>
    <div class="flex-grow-1">
      <h4>{{ $serviceTicket->ticket_no }} — {{ $serviceTicket->customer_name }}</h4>
      @php $sc = ['open'=>'warning','in_progress'=>'info','resolved'=>'success','closed'=>'secondary'][$serviceTicket->status] ?? 'secondary' @endphp
      @php $pc = ['low'=>'success','medium'=>'info','high'=>'warning','urgent'=>'danger'][$serviceTicket->priority ?? 'low'] ?? 'secondary' @endphp
      <div style="opacity:.85;font-size:.85rem;margin-top:4px;">
        <span class="badge bg-label-{{ $sc }}">{{ ucwords(str_replace('_',' ',$serviceTicket->status)) }}</span>
        <span class="badge bg-label-{{ $pc }} ms-1">{{ ucfirst($serviceTicket->priority ?? 'low') }}</span>
      </div>
    </div>
    <a href="{{ route('admin.cctv.service-tickets.edit', $serviceTicket) }}" class="btn btn-light btn-sm"><i class="bx bx-edit me-1"></i> Edit</a>
  </div>

  <div class="row g-3">
    <div class="col-lg-8">
      <div class="card section-card">
        <div class="card-header"><div class="section-icon"><i class="bx bx-user"></i></div> Customer</div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-sm-6"><div class="info-label">Name</div><div class="info-value">{{ $serviceTicket->customer_name }}</div></div>
            <div class="col-sm-6"><div class="info-label">Mobile</div><div class="info-value font-monospace">{{ $serviceTicket->mobile }}</div></div>
            <div class="col-12"><div class="info-label">Address</div><div class="info-value">{{ $serviceTicket->address ?? '—' }}</div></div>
          </div>
        </div>
      </div>

      <div class="card section-card">
        <div class="card-header"><div class="section-icon"><i class="bx bx-support"></i></div> Issue Details</div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-sm-4"><div class="info-label">Issue Type</div><div class="info-value">{{ $serviceTicket->issue_type ?? '—' }}</div></div>
            <div class="col-sm-4"><div class="info-label">Technician</div><div class="info-value">{{ $serviceTicket->technician_name ?? '—' }}</div></div>
            <div class="col-sm-4"><div class="info-label">Scheduled</div><div class="info-value">{{ $serviceTicket->scheduled_date ? \Carbon\Carbon::parse($serviceTicket->scheduled_date)->format('d M Y') : '—' }}</div></div>
            <div class="col-12"><div class="info-label">Issue Description</div><div class="info-value" style="white-space:pre-line">{{ $serviceTicket->issue_description }}</div></div>
            <div class="col-12"><div class="info-label">Resolution Notes</div><div class="info-value" style="white-space:pre-line">{{ $serviceTicket->resolution_notes ?? '—' }}</div></div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card section-card">
        <div class="card-header"><div class="section-icon"><i class="bx bx-rupee"></i></div> Charges</div>
        <div class="card-body">
          <div class="d-flex justify-content-between mb-2"><span class="text-muted small">Service Charge</span><span class="fw-600">Rs. {{ number_format($serviceTicket->service_charge ?? 0, 2) }}</span></div>
          <div class="d-flex justify-content-between mb-2"><span class="text-muted small">Parts Cost</span><span class="fw-600">Rs. {{ number_format($serviceTicket->parts_cost ?? 0, 2) }}</span></div>
          <div class="d-flex justify-content-between border-top pt-2 mt-1"><span class="fw-700">Total</span><span class="fw-700 text-primary">Rs. {{ number_format(($serviceTicket->service_charge ?? 0) + ($serviceTicket->parts_cost ?? 0), 2) }}</span></div>
        </div>
      </div>

      <div class="card section-card">
        <div class="card-header"><div class="section-icon"><i class="bx bx-info-circle"></i></div> Quick Info</div>
        <div class="card-body">
          <div class="mb-3"><div class="info-label">Ticket No</div><div class="fw-700 font-monospace text-primary">{{ $serviceTicket->ticket_no }}</div></div>
          <div class="mb-3"><div class="info-label">Status</div><span class="badge bg-label-{{ $sc }}">{{ ucwords(str_replace('_',' ',$serviceTicket->status)) }}</span></div>
          <div class="mb-3"><div class="info-label">Priority</div><span class="badge bg-label-{{ $pc }}">{{ ucfirst($serviceTicket->priority ?? 'low') }}</span></div>
          <div class="mb-3"><div class="info-label">Created</div><div class="info-value">{{ $serviceTicket->created_at->format('d M Y') }}</div></div>
          <hr>
          <div class="d-grid">
            <a href="{{ route('admin.cctv.service-tickets.edit', $serviceTicket) }}" class="btn btn-primary btn-sm"><i class="bx bx-edit me-1"></i> Edit Ticket</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
