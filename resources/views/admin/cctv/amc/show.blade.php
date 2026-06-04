@extends('layouts.admin')
@section('title', 'AMC – ' . $amc->amc_no)

@push('styles')
<style>
  .hero-bar { background:linear-gradient(135deg,#ffab00,#cc8800); border-radius:16px; padding:1.25rem 1.75rem; color:#fff; margin-bottom:1.5rem; display:flex; align-items:center; gap:1rem; flex-wrap:wrap; }
  .hero-bar .back-btn { width:38px; height:38px; border-radius:10px; background:rgba(255,255,255,.2); border:0; color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.2rem; text-decoration:none; transition:background .15s; }
  .hero-bar .back-btn:hover { background:rgba(255,255,255,.32); color:#fff; }
  .hero-bar h4 { margin:0; font-size:1.2rem; font-weight:700; }
  .section-card { border-radius:14px; border:0; box-shadow:0 2px 12px rgba(105,108,255,.08); margin-bottom:1.25rem; }
  .section-card .card-header { background:#f8f9fa; border-radius:14px 14px 0 0; padding:.85rem 1.25rem; font-weight:700; font-size:.875rem; display:flex; align-items:center; gap:.5rem; border-bottom:1px solid rgba(0,0,0,.06); }
  .section-icon { width:28px; height:28px; border-radius:8px; background:#fff8e6; color:#cc8800; display:flex; align-items:center; justify-content:center; font-size:.9rem; }
  .info-label { font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#a1acb8; margin-bottom:.25rem; }
  .info-value { font-size:.92rem; font-weight:500; color:#32325d; }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="hero-bar">
    <a href="{{ route('admin.cctv.amc.index') }}" class="back-btn"><i class="bx bx-arrow-back"></i></a>
    <div class="flex-grow-1">
      <h4>{{ $amc->amc_no }} — {{ $amc->customer_name }}</h4>
      @php $sc = ['active'=>'success','expired'=>'danger','cancelled'=>'secondary'][$amc->status] ?? 'secondary' @endphp
      <span class="badge bg-label-{{ $sc }}" style="margin-top:4px;">{{ ucfirst($amc->status) }}</span>
    </div>
    <a href="{{ route('admin.cctv.amc.edit', $amc) }}" class="btn btn-light btn-sm"><i class="bx bx-edit me-1"></i> Edit</a>
  </div>

  <div class="row g-3">
    <div class="col-lg-8">
      <div class="card section-card">
        <div class="card-header"><div class="section-icon"><i class="bx bx-user"></i></div> Customer</div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-sm-6"><div class="info-label">Name</div><div class="info-value">{{ $amc->customer_name }}</div></div>
            <div class="col-sm-6"><div class="info-label">Mobile</div><div class="info-value font-monospace">{{ $amc->mobile }}</div></div>
            <div class="col-12"><div class="info-label">Address</div><div class="info-value">{{ $amc->address ?? '—' }}</div></div>
          </div>
        </div>
      </div>

      <div class="card section-card">
        <div class="card-header"><div class="section-icon"><i class="bx bx-shield-quarter"></i></div> Contract Details</div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-sm-4"><div class="info-label">Start Date</div><div class="info-value">{{ $amc->start_date ? \Carbon\Carbon::parse($amc->start_date)->format('d M Y') : '—' }}</div></div>
            <div class="col-sm-4"><div class="info-label">End Date</div><div class="info-value">{{ $amc->end_date ? \Carbon\Carbon::parse($amc->end_date)->format('d M Y') : '—' }}</div></div>
            <div class="col-sm-4"><div class="info-label">Visits/Year</div><div class="info-value">{{ $amc->visits_per_year ?? '—' }}</div></div>
            <div class="col-sm-6"><div class="info-label">Contract Amount</div><div class="info-value fw-700 text-primary">Rs. {{ number_format($amc->contract_amount ?? 0, 2) }}</div></div>
            <div class="col-12"><div class="info-label">Scope / Coverage</div><div class="info-value" style="white-space:pre-line">{{ $amc->scope ?? '—' }}</div></div>
            <div class="col-12"><div class="info-label">Notes</div><div class="info-value" style="white-space:pre-line">{{ $amc->notes ?? '—' }}</div></div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card section-card">
        <div class="card-header"><div class="section-icon"><i class="bx bx-info-circle"></i></div> Quick Info</div>
        <div class="card-body">
          <div class="mb-3"><div class="info-label">AMC No</div><div class="fw-700 font-monospace text-primary">{{ $amc->amc_no }}</div></div>
          <div class="mb-3"><div class="info-label">Status</div><span class="badge bg-label-{{ $sc }}">{{ ucfirst($amc->status) }}</span></div>
          @if($amc->end_date)
          <div class="mb-3">
            <div class="info-label">Days Remaining</div>
            @php $days = now()->diffInDays(\Carbon\Carbon::parse($amc->end_date), false) @endphp
            <div class="fw-700 {{ $days < 0 ? 'text-danger' : ($days <= 30 ? 'text-warning' : 'text-success') }}">{{ $days < 0 ? 'Expired' : $days.' days' }}</div>
          </div>
          @endif
          <div class="mb-3"><div class="info-label">Created</div><div class="info-value">{{ $amc->created_at->format('d M Y') }}</div></div>
          <hr>
          <div class="d-grid gap-2">
            <a href="{{ route('admin.cctv.amc.edit', $amc) }}" class="btn btn-primary btn-sm"><i class="bx bx-edit me-1"></i> Edit AMC</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
