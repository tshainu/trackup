@extends('layouts.admin')
@section('title', 'Asset – ' . $asset->asset_no)

@push('styles')
<style>
  .hero-bar { background:linear-gradient(135deg,#28c76f,#1a8f4e); border-radius:16px; padding:1.25rem 1.75rem; color:#fff; margin-bottom:1.5rem; display:flex; align-items:center; gap:1rem; flex-wrap:wrap; }
  .hero-bar .back-btn { width:38px; height:38px; border-radius:10px; background:rgba(255,255,255,.2); border:0; color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.2rem; text-decoration:none; transition:background .15s; }
  .hero-bar .back-btn:hover { background:rgba(255,255,255,.32); color:#fff; }
  .hero-bar h4 { margin:0; font-size:1.2rem; font-weight:700; }
  .section-card { border-radius:14px; border:0; box-shadow:0 2px 12px rgba(105,108,255,.08); margin-bottom:1.25rem; }
  .section-card .card-header { background:#f8f9fa; border-radius:14px 14px 0 0; padding:.85rem 1.25rem; font-weight:700; font-size:.875rem; display:flex; align-items:center; gap:.5rem; border-bottom:1px solid rgba(0,0,0,.06); }
  .section-icon { width:28px; height:28px; border-radius:8px; background:#e8faf0; color:#28c76f; display:flex; align-items:center; justify-content:center; font-size:.9rem; }
  .info-label { font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#a1acb8; margin-bottom:.25rem; }
  .info-value { font-size:.92rem; font-weight:500; color:#32325d; }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="hero-bar">
    <a href="{{ route('admin.cctv.assets.index') }}" class="back-btn"><i class="bx bx-arrow-back"></i></a>
    <div class="flex-grow-1">
      <h4>{{ $asset->asset_no }} — {{ $asset->device_type }}</h4>
      @php $sc = ['active'=>'success','faulty'=>'danger','under_repair'=>'warning','decommissioned'=>'secondary'][$asset->status] ?? 'secondary' @endphp
      <span class="badge bg-label-{{ $sc }}" style="margin-top:4px;">{{ ucwords(str_replace('_',' ',$asset->status)) }}</span>
    </div>
    <a href="{{ route('admin.cctv.assets.edit', $asset) }}" class="btn btn-light btn-sm"><i class="bx bx-edit me-1"></i> Edit</a>
  </div>

  <div class="row g-3">
    <div class="col-lg-8">
      <div class="card section-card">
        <div class="card-header"><div class="section-icon"><i class="bx bx-user"></i></div> Customer / Site</div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-sm-6"><div class="info-label">Customer</div><div class="info-value">{{ $asset->customer_name }}</div></div>
            <div class="col-sm-6"><div class="info-label">Mobile</div><div class="info-value font-monospace">{{ $asset->mobile ?? '—' }}</div></div>
            <div class="col-12"><div class="info-label">Install Location</div><div class="info-value">{{ $asset->location ?? '—' }}</div></div>
          </div>
        </div>
      </div>

      <div class="card section-card">
        <div class="card-header"><div class="section-icon"><i class="bx bx-camera"></i></div> Device Details</div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-sm-4"><div class="info-label">Device Type</div><div class="info-value">{{ $asset->device_type }}</div></div>
            <div class="col-sm-4"><div class="info-label">Brand</div><div class="info-value">{{ $asset->brand ?? '—' }}</div></div>
            <div class="col-sm-4"><div class="info-label">Model</div><div class="info-value">{{ $asset->model ?? '—' }}</div></div>
            <div class="col-sm-6"><div class="info-label">Serial No</div><div class="info-value font-monospace">{{ $asset->serial_no ?? '—' }}</div></div>
            <div class="col-sm-6"><div class="info-label">Status</div><span class="badge bg-label-{{ $sc }}">{{ ucwords(str_replace('_',' ',$asset->status)) }}</span></div>
            <div class="col-sm-6"><div class="info-label">Install Date</div><div class="info-value">{{ $asset->install_date ? \Carbon\Carbon::parse($asset->install_date)->format('d M Y') : '—' }}</div></div>
            <div class="col-sm-6"><div class="info-label">Warranty Expiry</div><div class="info-value">{{ $asset->warranty_expiry ? \Carbon\Carbon::parse($asset->warranty_expiry)->format('d M Y') : '—' }}</div></div>
            <div class="col-12"><div class="info-label">Notes</div><div class="info-value" style="white-space:pre-line">{{ $asset->notes ?? '—' }}</div></div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card section-card">
        <div class="card-header"><div class="section-icon"><i class="bx bx-info-circle"></i></div> Quick Info</div>
        <div class="card-body">
          <div class="mb-3"><div class="info-label">Asset No</div><div class="fw-700 font-monospace text-primary">{{ $asset->asset_no }}</div></div>
          <div class="mb-3"><div class="info-label">Status</div><span class="badge bg-label-{{ $sc }}">{{ ucwords(str_replace('_',' ',$asset->status)) }}</span></div>
          <div class="mb-3"><div class="info-label">Registered</div><div class="info-value">{{ $asset->created_at->format('d M Y') }}</div></div>
          <hr>
          <div class="d-grid gap-2">
            <a href="{{ route('admin.cctv.assets.edit', $asset) }}" class="btn btn-primary btn-sm"><i class="bx bx-edit me-1"></i> Edit Asset</a>
            <a href="{{ route('admin.cctv.service-tickets.create', ['asset_id'=>$asset->id]) }}" class="btn btn-outline-danger btn-sm"><i class="bx bx-support me-1"></i> Raise Service Ticket</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
