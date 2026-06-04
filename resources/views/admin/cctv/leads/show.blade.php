@extends('layouts.admin')
@section('title', 'Lead – ' . $lead->lead_no)

@push('styles')
<style>
  .hero-bar { background:linear-gradient(135deg,#696cff,#8c57ff); border-radius:16px; padding:1.25rem 1.75rem; color:#fff; margin-bottom:1.5rem; display:flex; align-items:center; gap:1rem; flex-wrap:wrap; }
  .hero-bar .back-btn { width:38px; height:38px; border-radius:10px; background:rgba(255,255,255,.2); border:0; color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.2rem; flex-shrink:0; text-decoration:none; transition:background .15s; }
  .hero-bar .back-btn:hover { background:rgba(255,255,255,.32); color:#fff; }
  .hero-bar h4 { margin:0; font-size:1.2rem; font-weight:700; }
  .section-card { border-radius:14px; border:0; box-shadow:0 2px 12px rgba(105,108,255,.08); margin-bottom:1.25rem; }
  .section-card .card-header { background:#f8f9fa; border-radius:14px 14px 0 0; padding:.85rem 1.25rem; font-weight:700; font-size:.875rem; display:flex; align-items:center; gap:.5rem; border-bottom:1px solid rgba(0,0,0,.06); }
  .section-icon { width:28px; height:28px; border-radius:8px; background:#eef0ff; color:#696cff; display:flex; align-items:center; justify-content:center; font-size:.9rem; }
  .info-label { font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#a1acb8; margin-bottom:.25rem; }
  .info-value { font-size:.92rem; font-weight:500; color:#32325d; }
  .badge-new { background:#eef0ff; color:#696cff; }
  .badge-contacted { background:#fff3e8; color:#fd7e14; }
  .badge-qualified { background:#e8faf0; color:#28c76f; }
  .badge-converted { background:#f3eeff; color:#8c57ff; }
  .badge-lost { background:#fdeaea; color:#ea5455; }
  .status-badge { font-size:.78rem; padding:5px 14px; border-radius:20px; font-weight:600; }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="hero-bar">
    <a href="{{ route('admin.cctv.leads.index') }}" class="back-btn"><i class="bx bx-arrow-back"></i></a>
    <div class="flex-grow-1">
      <h4>{{ $lead->lead_no }} — {{ $lead->customer_name }}</h4>
      <div class="d-flex align-items-center gap-2 mt-1" style="opacity:.9;">
        @php $sc = ['new'=>'badge-new','contacted'=>'badge-contacted','qualified'=>'badge-qualified','converted'=>'badge-converted','lost'=>'badge-lost'][$lead->status] ?? '' @endphp
        <span class="status-badge {{ $sc }}">{{ ucfirst($lead->status) }}</span>
        <span style="opacity:.7;font-size:.82rem;">{{ $lead->source ?? '' }}</span>
      </div>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('admin.cctv.leads.edit', $lead) }}" class="btn btn-light btn-sm"><i class="bx bx-edit me-1"></i> Edit</a>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-lg-8">
      <div class="card section-card">
        <div class="card-header"><div class="section-icon"><i class="bx bx-user"></i></div> Customer Information</div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-sm-6"><div class="info-label">Name</div><div class="info-value">{{ $lead->customer_name }}</div></div>
            <div class="col-sm-6"><div class="info-label">Mobile</div><div class="info-value font-monospace">{{ $lead->mobile }}</div></div>
            <div class="col-sm-6"><div class="info-label">Email</div><div class="info-value">{{ $lead->email ?? '—' }}</div></div>
            <div class="col-sm-6"><div class="info-label">Location</div><div class="info-value">{{ $lead->location ?? '—' }}</div></div>
            <div class="col-12"><div class="info-label">Address</div><div class="info-value">{{ $lead->address ?? '—' }}</div></div>
          </div>
        </div>
      </div>

      <div class="card section-card">
        <div class="card-header"><div class="section-icon"><i class="bx bx-detail"></i></div> Lead Details</div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-sm-4"><div class="info-label">Requirement</div><div class="info-value">{{ $lead->requirement_type ?? '—' }}</div></div>
            <div class="col-sm-4"><div class="info-label">Budget</div><div class="info-value">{{ $lead->budget ? 'Rs. '.number_format($lead->budget,2) : '—' }}</div></div>
            <div class="col-sm-4"><div class="info-label">Follow Up</div><div class="info-value">{{ $lead->follow_up_date ? $lead->follow_up_date->format('d M Y') : '—' }}</div></div>
            <div class="col-12"><div class="info-label">Notes</div><div class="info-value" style="white-space:pre-line">{{ $lead->notes ?? '—' }}</div></div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card section-card">
        <div class="card-header"><div class="section-icon"><i class="bx bx-info-circle"></i></div> Quick Info</div>
        <div class="card-body">
          <div class="mb-3">
            <div class="info-label">Lead Number</div>
            <div class="fw-700 font-monospace text-primary">{{ $lead->lead_no }}</div>
          </div>
          <div class="mb-3">
            <div class="info-label">Status</div>
            <span class="status-badge {{ $sc }}">{{ ucfirst($lead->status) }}</span>
          </div>
          <div class="mb-3">
            <div class="info-label">Source</div>
            <div class="info-value">{{ $lead->source ?? '—' }}</div>
          </div>
          <div class="mb-3">
            <div class="info-label">Created</div>
            <div class="info-value">{{ $lead->created_at->format('d M Y, h:i A') }}</div>
          </div>
          <div class="mb-3">
            <div class="info-label">Last Updated</div>
            <div class="info-value">{{ $lead->updated_at->format('d M Y, h:i A') }}</div>
          </div>
          <hr>
          <div class="d-grid gap-2">
            <a href="{{ route('admin.cctv.leads.edit', $lead) }}" class="btn btn-primary btn-sm"><i class="bx bx-edit me-1"></i> Edit Lead</a>
            <a href="{{ route('admin.cctv.surveys.create', ['lead_id'=>$lead->id]) }}" class="btn btn-outline-success btn-sm"><i class="bx bx-clipboard me-1"></i> Create Survey</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
