@extends('layouts.admin')
@section('title', 'CCTV Leads')

@push('styles')
<style>
  .stat-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:1.5rem; }
  @media(max-width:991px){.stat-grid{grid-template-columns:repeat(2,1fr);}}
  .stat-card { background:#fff; border-radius:14px; padding:16px 18px; display:flex; align-items:center; gap:14px; box-shadow:0 2px 12px rgba(0,0,0,.06); border-left:4px solid transparent; }
  .stat-icon { width:46px; height:46px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.35rem; flex-shrink:0; }
  .stat-num { font-size:1.65rem; font-weight:800; line-height:1.1; }
  .stat-lbl { font-size:.72rem; font-weight:600; color:#8592a3; text-transform:uppercase; letter-spacing:.04em; margin-top:1px; }
  .sc-blue   { border-color:#696cff; } .sc-blue .stat-icon   { background:#eef0ff; color:#696cff; } .sc-blue .stat-num   { color:#696cff; }
  .sc-orange { border-color:#fd7e14; } .sc-orange .stat-icon { background:#fff3e8; color:#fd7e14; } .sc-orange .stat-num { color:#fd7e14; }
  .sc-green  { border-color:#28c76f; } .sc-green .stat-icon  { background:#e8faf0; color:#28c76f; } .sc-green .stat-num  { color:#28c76f; }
  .sc-red    { border-color:#ea5455; } .sc-red .stat-icon    { background:#fdeaea; color:#ea5455; } .sc-red .stat-num    { color:#ea5455; }
  .sc-purple { border-color:#8c57ff; } .sc-purple .stat-icon { background:#f3eeff; color:#8c57ff; } .sc-purple .stat-num { color:#8c57ff; }
  .hero-bar { background:linear-gradient(135deg,#696cff,#8c57ff); border-radius:16px; padding:1.25rem 1.75rem; color:#fff; margin-bottom:1.5rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem; }
  .hero-bar h4 { margin:0; font-size:1.2rem; font-weight:700; }
  .hero-bar p  { margin:0; opacity:.85; font-size:.85rem; }
  .filter-tabs { display:flex; gap:8px; flex-wrap:wrap; }
  .filter-tab { padding:6px 16px; border-radius:20px; font-size:.8rem; font-weight:600; border:1.5px solid #d9dee3; color:#697a8d; background:#fff; text-decoration:none; transition:all .15s; }
  .filter-tab.active,.filter-tab:hover { background:#696cff; color:#fff; border-color:#696cff; }
  .badge-status { font-size:.72rem; padding:4px 10px; border-radius:20px; font-weight:600; }
  .bs-new      { background:#eef0ff; color:#696cff; }
  .bs-survey   { background:#fff3e8; color:#fd7e14; }
  .bs-surveyed { background:#e3f9e5; color:#28c76f; }
  .bs-quoted   { background:#f3eeff; color:#8c57ff; }
  .bs-approved { background:#e8faf0; color:#28c76f; }
  .bs-lost     { background:#fdeaea; color:#ea5455; }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  <div class="hero-bar">
    <div>
      <h4><i class="bx bx-user-plus me-2"></i>CCTV Leads</h4>
      <p>Manage and track all CCTV enquiries</p>
    </div>
    <a href="{{ route('admin.cctv.leads.create') }}" class="btn btn-light btn-sm fw-semibold">
      <i class="bx bx-plus me-1"></i> New Lead
    </a>
  </div>

  {{-- Stats --}}
  <div class="stat-grid">
    <div class="stat-card sc-blue">
      <div class="stat-icon"><i class="bx bx-user-plus"></i></div>
      <div><div class="stat-num">{{ $counts['all'] }}</div><div class="stat-lbl">Total</div></div>
    </div>
    <div class="stat-card sc-orange">
      <div class="stat-icon"><i class="bx bx-phone-call"></i></div>
      <div><div class="stat-num">{{ $counts['new'] }}</div><div class="stat-lbl">New</div></div>
    </div>
    <div class="stat-card sc-green">
      <div class="stat-icon"><i class="bx bx-check-circle"></i></div>
      <div><div class="stat-num">{{ $counts['approved'] }}</div><div class="stat-lbl">Approved</div></div>
    </div>
    <div class="stat-card sc-red">
      <div class="stat-icon"><i class="bx bx-x-circle"></i></div>
      <div><div class="stat-num">{{ $counts['lost'] }}</div><div class="stat-lbl">Lost</div></div>
    </div>
  </div>

  {{-- Filter + Search --}}
  <div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
      <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <div class="filter-tabs">
          <a href="{{ route('admin.cctv.leads.index') }}" class="filter-tab {{ $tab==='all'?'active':'' }}">All ({{ $counts['all'] }})</a>
          <a href="{{ route('admin.cctv.leads.index', ['tab'=>'new']) }}" class="filter-tab {{ $tab==='new'?'active':'' }}">New ({{ $counts['new'] }})</a>
          <a href="{{ route('admin.cctv.leads.index', ['tab'=>'survey']) }}" class="filter-tab {{ $tab==='survey'?'active':'' }}">Survey Scheduled ({{ $counts['survey'] }})</a>
          <a href="{{ route('admin.cctv.leads.index', ['tab'=>'surveyed']) }}" class="filter-tab {{ $tab==='surveyed'?'active':'' }}">Surveyed ({{ $counts['surveyed'] }})</a>
          <a href="{{ route('admin.cctv.leads.index', ['tab'=>'quoted']) }}" class="filter-tab {{ $tab==='quoted'?'active':'' }}">Quoted ({{ $counts['quoted'] }})</a>
          <a href="{{ route('admin.cctv.leads.index', ['tab'=>'approved']) }}" class="filter-tab {{ $tab==='approved'?'active':'' }}">Approved ({{ $counts['approved'] }})</a>
          <a href="{{ route('admin.cctv.leads.index', ['tab'=>'lost']) }}" class="filter-tab {{ $tab==='lost'?'active':'' }}">Lost ({{ $counts['lost'] }})</a>
        </div>
        <form method="GET" class="d-flex gap-2">
          @if($tab !== 'all')<input type="hidden" name="tab" value="{{ $tab }}">@endif
          <input type="text" name="q" value="{{ $search }}" class="form-control form-control-sm" placeholder="Search name / mobile…" style="width:220px">
          <button class="btn btn-primary btn-sm"><i class="bx bx-search"></i></button>
        </form>
      </div>
    </div>
  </div>

  {{-- Table --}}
  <div class="card border-0 shadow-sm">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Lead No</th>
            <th>Customer</th>
            <th>Mobile</th>
            <th>Type</th>
            <th>Source</th>
            <th>Status</th>
            <th>Date</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($leads as $lead)
          <tr>
            <td><span class="fw-bold text-primary font-monospace">{{ $lead->lead_no }}</span></td>
            <td><div class="fw-semibold">{{ $lead->customer_name }}</div></td>
            <td class="font-monospace small">{{ $lead->mobile }}</td>
            <td><span class="badge bg-label-secondary">{{ $lead->customer_type }}</span></td>
            <td>{{ $lead->inquiry_source ?? '—' }}</td>
            <td>
              @php
                $sc = [
                  'New Lead'           => 'bs-new',
                  'Survey Scheduled'   => 'bs-survey',
                  'Survey Completed'   => 'bs-surveyed',
                  'Quotation Sent'     => 'bs-quoted',
                  'Approved'           => 'bs-approved',
                  'Lost'               => 'bs-lost',
                ][$lead->status] ?? 'bg-secondary';
              @endphp
              <span class="badge-status {{ $sc }}">{{ $lead->status }}</span>
            </td>
            <td>{{ $lead->inquiry_date ? \Carbon\Carbon::parse($lead->inquiry_date)->format('d M Y') : $lead->created_at->format('d M Y') }}</td>
            <td class="text-end">
              <a href="{{ route('admin.cctv.leads.show', $lead) }}" class="btn btn-sm btn-outline-primary py-1 px-2"><i class="bx bx-show"></i></a>
              <a href="{{ route('admin.cctv.leads.edit', $lead) }}" class="btn btn-sm btn-outline-secondary py-1 px-2"><i class="bx bx-edit"></i></a>
            </td>
          </tr>
          @empty
          <tr><td colspan="8" class="text-center text-muted py-4">No leads found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($leads->hasPages())
    <div class="card-footer bg-transparent d-flex justify-content-end">
      {{ $leads->withQueryString()->links() }}
    </div>
    @endif
  </div>
</div>
@endsection
