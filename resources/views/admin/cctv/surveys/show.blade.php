@extends('layouts.admin')
@section('title', 'Survey – ' . $survey->survey_no)

@push('styles')
<style>
  .hero-bar { background:linear-gradient(135deg,#00cfe8,#0090a8); border-radius:16px; padding:1.25rem 1.75rem; color:#fff; margin-bottom:1.5rem; display:flex; align-items:center; gap:1rem; flex-wrap:wrap; }
  .hero-bar .back-btn { width:38px; height:38px; border-radius:10px; background:rgba(255,255,255,.2); border:0; color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.2rem; text-decoration:none; transition:background .15s; }
  .hero-bar .back-btn:hover { background:rgba(255,255,255,.32); color:#fff; }
  .hero-bar h4 { margin:0; font-size:1.2rem; font-weight:700; }
  .section-card { border-radius:14px; border:0; box-shadow:0 2px 12px rgba(105,108,255,.08); margin-bottom:1.25rem; }
  .section-card .card-header { background:#f8f9fa; border-radius:14px 14px 0 0; padding:.85rem 1.25rem; font-weight:700; font-size:.875rem; display:flex; align-items:center; gap:.5rem; border-bottom:1px solid rgba(0,0,0,.06); }
  .section-icon { width:28px; height:28px; border-radius:8px; background:#e0f9fc; color:#00a4b8; display:flex; align-items:center; justify-content:center; font-size:.9rem; }
  .info-label { font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#a1acb8; margin-bottom:.25rem; }
  .info-value { font-size:.92rem; font-weight:500; color:#32325d; }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="hero-bar">
    <a href="{{ route('admin.cctv.surveys.index') }}" class="back-btn"><i class="bx bx-arrow-back"></i></a>
    <div class="flex-grow-1">
      <h4>{{ $survey->survey_no }} — {{ $survey->customer_name }}</h4>
      <div style="opacity:.85;font-size:.85rem;">
        @php $sc = ['pending'=>'warning','completed'=>'success','quoted'=>'info'][$survey->status] ?? 'secondary' @endphp
        <span class="badge bg-label-{{ $sc }}">{{ ucfirst($survey->status) }}</span>
        @if($survey->survey_date) <span class="ms-2">{{ \Carbon\Carbon::parse($survey->survey_date)->format('d M Y') }}</span> @endif
      </div>
    </div>
    <a href="{{ route('admin.cctv.surveys.edit', $survey) }}" class="btn btn-light btn-sm"><i class="bx bx-edit me-1"></i> Edit</a>
  </div>

  <div class="row g-3">
    <div class="col-lg-8">
      <div class="card section-card">
        <div class="card-header"><div class="section-icon"><i class="bx bx-user"></i></div> Customer</div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-sm-6"><div class="info-label">Name</div><div class="info-value">{{ $survey->customer_name }}</div></div>
            <div class="col-sm-6"><div class="info-label">Mobile</div><div class="info-value font-monospace">{{ $survey->mobile }}</div></div>
            <div class="col-12"><div class="info-label">Address</div><div class="info-value">{{ $survey->address ?? '—' }}</div></div>
          </div>
        </div>
      </div>

      <div class="card section-card">
        <div class="card-header"><div class="section-icon"><i class="bx bx-camera"></i></div> Survey Findings</div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-sm-4"><div class="info-label">Cameras</div><div class="info-value">{{ $survey->camera_count ?? '—' }}</div></div>
            <div class="col-sm-4"><div class="info-label">Type</div><div class="info-value">{{ $survey->camera_type ?? '—' }}</div></div>
            <div class="col-sm-4"><div class="info-label">Technician</div><div class="info-value">{{ $survey->technician_name ?? '—' }}</div></div>
            <div class="col-12"><div class="info-label">Observations</div><div class="info-value" style="white-space:pre-line">{{ $survey->observations ?? '—' }}</div></div>
            <div class="col-12"><div class="info-label">Recommendations</div><div class="info-value" style="white-space:pre-line">{{ $survey->recommendations ?? '—' }}</div></div>
            <div class="col-12"><div class="info-label">Notes</div><div class="info-value" style="white-space:pre-line">{{ $survey->notes ?? '—' }}</div></div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card section-card">
        <div class="card-header"><div class="section-icon"><i class="bx bx-info-circle"></i></div> Quick Info</div>
        <div class="card-body">
          <div class="mb-3"><div class="info-label">Survey No</div><div class="fw-700 font-monospace text-primary">{{ $survey->survey_no }}</div></div>
          <div class="mb-3"><div class="info-label">Status</div><span class="badge bg-label-{{ $sc }}">{{ ucfirst($survey->status) }}</span></div>
          <div class="mb-3"><div class="info-label">Survey Date</div><div class="info-value">{{ $survey->survey_date ? \Carbon\Carbon::parse($survey->survey_date)->format('d M Y') : '—' }}</div></div>
          <div class="mb-3"><div class="info-label">Created</div><div class="info-value">{{ $survey->created_at->format('d M Y') }}</div></div>
          <hr>
          <div class="d-grid gap-2">
            <a href="{{ route('admin.cctv.surveys.edit', $survey) }}" class="btn btn-primary btn-sm"><i class="bx bx-edit me-1"></i> Edit Survey</a>
            <a href="{{ route('admin.cctv.quotations.create', ['survey_id'=>$survey->id]) }}" class="btn btn-outline-success btn-sm"><i class="bx bx-file me-1"></i> Create Quotation</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
