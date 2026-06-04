@extends('layouts.admin')
@section('title', 'Project – ' . $project->project_no)

@push('styles')
<style>
  .hero-bar { background:linear-gradient(135deg,#fd7e14,#e55a00); border-radius:16px; padding:1.25rem 1.75rem; color:#fff; margin-bottom:1.5rem; display:flex; align-items:center; gap:1rem; flex-wrap:wrap; }
  .hero-bar .back-btn { width:38px; height:38px; border-radius:10px; background:rgba(255,255,255,.2); border:0; color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.2rem; text-decoration:none; transition:background .15s; }
  .hero-bar .back-btn:hover { background:rgba(255,255,255,.32); color:#fff; }
  .hero-bar h4 { margin:0; font-size:1.2rem; font-weight:700; }
  .section-card { border-radius:14px; border:0; box-shadow:0 2px 12px rgba(105,108,255,.08); margin-bottom:1.25rem; }
  .section-card .card-header { background:#f8f9fa; border-radius:14px 14px 0 0; padding:.85rem 1.25rem; font-weight:700; font-size:.875rem; display:flex; align-items:center; gap:.5rem; border-bottom:1px solid rgba(0,0,0,.06); }
  .section-icon { width:28px; height:28px; border-radius:8px; background:#fff3e8; color:#fd7e14; display:flex; align-items:center; justify-content:center; font-size:.9rem; }
  .info-label { font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#a1acb8; margin-bottom:.25rem; }
  .info-value { font-size:.92rem; font-weight:500; color:#32325d; }
  .billing-row { display:flex; justify-content:space-between; padding:.35rem 0; font-size:.85rem; border-bottom:1px solid #f0f0f0; }
  .billing-row.total { font-weight:700; font-size:1rem; border-top:2px solid #e0e0e0; border-bottom:none; margin-top:4px; padding-top:.5rem; }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="hero-bar">
    <a href="{{ route('admin.cctv.projects.index') }}" class="back-btn"><i class="bx bx-arrow-back"></i></a>
    <div class="flex-grow-1">
      <h4>{{ $project->project_no }} — {{ $project->customer_name }}</h4>
      @php $sc = ['scheduled'=>'info','in_progress'=>'warning','completed'=>'success','on_hold'=>'secondary','cancelled'=>'danger'][$project->status] ?? 'secondary' @endphp
      <span class="badge bg-label-{{ $sc }}" style="margin-top:4px;">{{ ucwords(str_replace('_',' ',$project->status)) }}</span>
    </div>
    <a href="{{ route('admin.cctv.projects.edit', $project) }}" class="btn btn-light btn-sm"><i class="bx bx-edit me-1"></i> Edit</a>
  </div>

  <div class="row g-3">
    <div class="col-lg-8">
      <div class="card section-card">
        <div class="card-header"><div class="section-icon"><i class="bx bx-user"></i></div> Customer</div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-sm-6"><div class="info-label">Name</div><div class="info-value">{{ $project->customer_name }}</div></div>
            <div class="col-sm-6"><div class="info-label">Mobile</div><div class="info-value font-monospace">{{ $project->mobile }}</div></div>
            <div class="col-12"><div class="info-label">Address</div><div class="info-value">{{ $project->address ?? '—' }}</div></div>
          </div>
        </div>
      </div>

      <div class="card section-card">
        <div class="card-header"><div class="section-icon"><i class="bx bx-wrench"></i></div> Project Info</div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-sm-4"><div class="info-label">Technician</div><div class="info-value">{{ $project->technician_name ?? '—' }}</div></div>
            <div class="col-sm-4"><div class="info-label">Start Date</div><div class="info-value">{{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('d M Y') : '—' }}</div></div>
            <div class="col-sm-4"><div class="info-label">End Date</div><div class="info-value">{{ $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('d M Y') : '—' }}</div></div>
            <div class="col-sm-4"><div class="info-label">Cameras</div><div class="info-value">{{ $project->camera_count ?? '—' }}</div></div>
            <div class="col-12"><div class="info-label">Scope of Work</div><div class="info-value" style="white-space:pre-line">{{ $project->scope ?? '—' }}</div></div>
            <div class="col-12"><div class="info-label">Notes</div><div class="info-value" style="white-space:pre-line">{{ $project->notes ?? '—' }}</div></div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card section-card">
        <div class="card-header"><div class="section-icon"><i class="bx bx-rupee"></i></div> Financials</div>
        <div class="card-body">
          <div class="billing-row"><span class="text-muted">Contract Amount</span><span class="fw-600">Rs. {{ number_format($project->contract_amount ?? 0, 2) }}</span></div>
          <div class="billing-row"><span class="text-muted">Advance Paid</span><span class="text-success fw-600">Rs. {{ number_format($project->advance_paid ?? 0, 2) }}</span></div>
          <div class="billing-row total">
            <span>Balance Due</span>
            <span class="{{ ($project->contract_amount - $project->advance_paid) > 0 ? 'text-danger' : 'text-success' }}">
              Rs. {{ number_format(max(0, ($project->contract_amount ?? 0) - ($project->advance_paid ?? 0)), 2) }}
            </span>
          </div>
        </div>
      </div>

      <div class="card section-card">
        <div class="card-header"><div class="section-icon"><i class="bx bx-info-circle"></i></div> Quick Info</div>
        <div class="card-body">
          <div class="mb-3"><div class="info-label">Project No</div><div class="fw-700 font-monospace text-primary">{{ $project->project_no }}</div></div>
          <div class="mb-3"><div class="info-label">Status</div><span class="badge bg-label-{{ $sc }}">{{ ucwords(str_replace('_',' ',$project->status)) }}</span></div>
          <div class="mb-3"><div class="info-label">Created</div><div class="info-value">{{ $project->created_at->format('d M Y') }}</div></div>
          <hr>
          <div class="d-grid gap-2">
            <a href="{{ route('admin.cctv.projects.edit', $project) }}" class="btn btn-primary btn-sm"><i class="bx bx-edit me-1"></i> Edit Project</a>
            <a href="{{ route('admin.cctv.assets.create', ['project_id'=>$project->id]) }}" class="btn btn-outline-success btn-sm"><i class="bx bx-camera me-1"></i> Register Asset</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
