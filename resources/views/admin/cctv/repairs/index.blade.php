@extends('layouts.admin')
@section('title', 'CCTV Repairs')

@push('styles')
<style>
  .stat-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:1.5rem; }
  @media(max-width:991px){.stat-grid{grid-template-columns:repeat(2,1fr);}}
  .stat-card { background:#fff; border-radius:14px; padding:16px 18px; display:flex; align-items:center; gap:14px; box-shadow:0 2px 12px rgba(0,0,0,.06); border-left:4px solid transparent; }
  .stat-icon { width:46px; height:46px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.35rem; flex-shrink:0; }
  .stat-num { font-size:1.65rem; font-weight:800; line-height:1.1; }
  .stat-lbl { font-size:.72rem; font-weight:600; color:#8592a3; text-transform:uppercase; letter-spacing:.04em; margin-top:1px; }
  .sc-blue { border-color:#696cff; } .sc-blue .stat-icon { background:#eef0ff; color:#696cff; } .sc-blue .stat-num { color:#696cff; }
  .sc-orange { border-color:#fd7e14; } .sc-orange .stat-icon { background:#fff3e8; color:#fd7e14; } .sc-orange .stat-num { color:#fd7e14; }
  .sc-green { border-color:#28c76f; } .sc-green .stat-icon { background:#e8faf0; color:#28c76f; } .sc-green .stat-num { color:#28c76f; }
  .sc-purple { border-color:#8c57ff; } .sc-purple .stat-icon { background:#f3eeff; color:#8c57ff; } .sc-purple .stat-num { color:#8c57ff; }
  .hero-bar { background:linear-gradient(135deg,#4b4b5a,#2d2d3a); border-radius:16px; padding:1.25rem 1.75rem; color:#fff; margin-bottom:1.5rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem; }
  .hero-bar h4 { margin:0; font-size:1.2rem; font-weight:700; }
  .hero-bar p { margin:0; opacity:.85; font-size:.85rem; }
  .filter-tabs { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:1rem; }
  .filter-tab { padding:6px 16px; border-radius:20px; font-size:.8rem; font-weight:600; border:1.5px solid #d9dee3; color:#697a8d; background:#fff; cursor:pointer; text-decoration:none; transition:all .15s; }
  .filter-tab.active, .filter-tab:hover { background:#4b4b5a; color:#fff; border-color:#4b4b5a; }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="hero-bar">
    <div>
      <h4><i class="bx bx-tool me-2"></i>Repair Jobs</h4>
      <p>Track CCTV device repair jobs</p>
    </div>
    <a href="{{ route('admin.cctv.repairs.create') }}" class="btn btn-light btn-sm fw-600">
      <i class="bx bx-plus me-1"></i> New Repair
    </a>
  </div>

  <div class="stat-grid">
    <div class="stat-card sc-blue"><div class="stat-icon"><i class="bx bx-tool"></i></div><div><div class="stat-num">{{ $stats['total'] ?? 0 }}</div><div class="stat-lbl">Total</div></div></div>
    <div class="stat-card sc-orange"><div class="stat-icon"><i class="bx bx-time"></i></div><div><div class="stat-num">{{ $stats['pending'] ?? 0 }}</div><div class="stat-lbl">Pending</div></div></div>
    <div class="stat-card sc-purple"><div class="stat-icon"><i class="bx bx-cog"></i></div><div><div class="stat-num">{{ $stats['in_progress'] ?? 0 }}</div><div class="stat-lbl">In Progress</div></div></div>
    <div class="stat-card sc-green"><div class="stat-icon"><i class="bx bx-check-circle"></i></div><div><div class="stat-num">{{ $stats['completed'] ?? 0 }}</div><div class="stat-lbl">Completed</div></div></div>
  </div>

  <div class="card border-0 shadow-sm mb-3">
    <div class="card-body pb-2">
      <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <div class="filter-tabs">
          <a href="{{ route('admin.cctv.repairs.index') }}" class="filter-tab {{ !request('status') ? 'active' : '' }}">All</a>
          @foreach(['pending','in_progress','completed','delivered','cancelled'] as $s)
            <a href="{{ route('admin.cctv.repairs.index', ['status'=>$s]) }}" class="filter-tab {{ request('status')===$s ? 'active' : '' }}">{{ ucwords(str_replace('_',' ',$s)) }}</a>
          @endforeach
        </div>
        <form method="GET" class="d-flex gap-2">
          @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
          <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Search…" style="width:200px">
          <button class="btn btn-primary btn-sm"><i class="bx bx-search"></i></button>
        </form>
      </div>
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Repair No</th>
            <th>Customer</th>
            <th>Device</th>
            <th>Issue</th>
            <th>Status</th>
            <th>Technician</th>
            <th>Charge (Rs.)</th>
            <th>Date</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($repairs as $repair)
          <tr>
            <td><span class="fw-700 text-primary font-monospace">{{ $repair->repair_no }}</span></td>
            <td><div class="fw-600">{{ $repair->customer_name }}</div></td>
            <td>{{ $repair->device_type ?? '—' }}</td>
            <td class="text-truncate" style="max-width:150px">{{ $repair->issue_description ?? '—' }}</td>
            <td>
              @php $sc = ['pending'=>'warning','in_progress'=>'info','completed'=>'success','delivered'=>'primary','cancelled'=>'danger'][$repair->status] ?? 'secondary' @endphp
              <span class="badge bg-label-{{ $sc }}">{{ ucwords(str_replace('_',' ',$repair->status)) }}</span>
            </td>
            <td>{{ $repair->technician_name ?? '—' }}</td>
            <td>{{ $repair->repair_charge ? number_format($repair->repair_charge, 2) : '—' }}</td>
            <td>{{ $repair->created_at->format('d M Y') }}</td>
            <td class="text-end">
              <a href="{{ route('admin.cctv.repairs.show', $repair) }}" class="btn btn-sm btn-outline-primary py-1 px-2"><i class="bx bx-show"></i></a>
              <a href="{{ route('admin.cctv.repairs.edit', $repair) }}" class="btn btn-sm btn-outline-secondary py-1 px-2"><i class="bx bx-edit"></i></a>
            </td>
          </tr>
          @empty
          <tr><td colspan="9" class="text-center text-muted py-4">No repair jobs found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($repairs->hasPages())
    <div class="card-footer bg-transparent d-flex justify-content-end">{{ $repairs->withQueryString()->links() }}</div>
    @endif
  </div>
</div>
@endsection
