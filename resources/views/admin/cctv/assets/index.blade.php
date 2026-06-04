@extends('layouts.admin')
@section('title', 'Installed Assets')

@push('styles')
<style>
  .stat-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:1.5rem; }
  @media(max-width:991px){.stat-grid{grid-template-columns:repeat(2,1fr);}}
  .stat-card { background:#fff; border-radius:14px; padding:16px 18px; display:flex; align-items:center; gap:14px; box-shadow:0 2px 12px rgba(0,0,0,.06); border-left:4px solid transparent; }
  .stat-icon { width:46px; height:46px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.35rem; flex-shrink:0; }
  .stat-num { font-size:1.65rem; font-weight:800; line-height:1.1; }
  .stat-lbl { font-size:.72rem; font-weight:600; color:#8592a3; text-transform:uppercase; letter-spacing:.04em; margin-top:1px; }
  .sc-blue { border-color:#696cff; } .sc-blue .stat-icon { background:#eef0ff; color:#696cff; } .sc-blue .stat-num { color:#696cff; }
  .sc-green { border-color:#28c76f; } .sc-green .stat-icon { background:#e8faf0; color:#28c76f; } .sc-green .stat-num { color:#28c76f; }
  .sc-orange { border-color:#fd7e14; } .sc-orange .stat-icon { background:#fff3e8; color:#fd7e14; } .sc-orange .stat-num { color:#fd7e14; }
  .sc-red { border-color:#ea5455; } .sc-red .stat-icon { background:#fdeaea; color:#ea5455; } .sc-red .stat-num { color:#ea5455; }
  .hero-bar { background:linear-gradient(135deg,#28c76f,#1a8f4e); border-radius:16px; padding:1.25rem 1.75rem; color:#fff; margin-bottom:1.5rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem; }
  .hero-bar h4 { margin:0; font-size:1.2rem; font-weight:700; }
  .hero-bar p { margin:0; opacity:.85; font-size:.85rem; }
  .filter-tabs { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:1rem; }
  .filter-tab { padding:6px 16px; border-radius:20px; font-size:.8rem; font-weight:600; border:1.5px solid #d9dee3; color:#697a8d; background:#fff; cursor:pointer; text-decoration:none; transition:all .15s; }
  .filter-tab.active, .filter-tab:hover { background:#28c76f; color:#fff; border-color:#28c76f; }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="hero-bar">
    <div>
      <h4><i class="bx bx-camera me-2"></i>Installed Assets</h4>
      <p>All CCTV equipment at customer sites</p>
    </div>
    <a href="{{ route('admin.cctv.assets.create') }}" class="btn btn-light btn-sm fw-600">
      <i class="bx bx-plus me-1"></i> Register Asset
    </a>
  </div>

  <div class="stat-grid">
    <div class="stat-card sc-blue"><div class="stat-icon"><i class="bx bx-camera"></i></div><div><div class="stat-num">{{ $stats['total'] ?? 0 }}</div><div class="stat-lbl">Total</div></div></div>
    <div class="stat-card sc-green"><div class="stat-icon"><i class="bx bx-check-circle"></i></div><div><div class="stat-num">{{ $stats['active'] ?? 0 }}</div><div class="stat-lbl">Active</div></div></div>
    <div class="stat-card sc-orange"><div class="stat-icon"><i class="bx bx-error"></i></div><div><div class="stat-num">{{ $stats['faulty'] ?? 0 }}</div><div class="stat-lbl">Faulty</div></div></div>
    <div class="stat-card sc-red"><div class="stat-icon"><i class="bx bx-x-circle"></i></div><div><div class="stat-num">{{ $stats['decommissioned'] ?? 0 }}</div><div class="stat-lbl">Decommissioned</div></div></div>
  </div>

  <div class="card border-0 shadow-sm mb-3">
    <div class="card-body pb-2">
      <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <div class="filter-tabs">
          <a href="{{ route('admin.cctv.assets.index') }}" class="filter-tab {{ !request('status') ? 'active' : '' }}">All</a>
          @foreach(['active','faulty','under_repair','decommissioned'] as $s)
            <a href="{{ route('admin.cctv.assets.index', ['status'=>$s]) }}" class="filter-tab {{ request('status')===$s ? 'active' : '' }}">{{ ucwords(str_replace('_',' ',$s)) }}</a>
          @endforeach
        </div>
        <form method="GET" class="d-flex gap-2">
          @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
          <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Search serial / customer…" style="width:200px">
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
            <th>Asset No</th>
            <th>Customer</th>
            <th>Device Type</th>
            <th>Brand / Model</th>
            <th>Serial No</th>
            <th>Status</th>
            <th>Installed</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($assets as $asset)
          <tr>
            <td><span class="fw-700 text-primary font-monospace">{{ $asset->asset_no }}</span></td>
            <td><div class="fw-600">{{ $asset->customer_name }}</div></td>
            <td>{{ $asset->device_type ?? '—' }}</td>
            <td>{{ trim(($asset->brand ?? '').' '.($asset->model ?? '')) ?: '—' }}</td>
            <td class="font-monospace small">{{ $asset->serial_no ?? '—' }}</td>
            <td>
              @php $sc = ['active'=>'success','faulty'=>'danger','under_repair'=>'warning','decommissioned'=>'secondary'][$asset->status] ?? 'secondary' @endphp
              <span class="badge bg-label-{{ $sc }}">{{ ucwords(str_replace('_',' ',$asset->status)) }}</span>
            </td>
            <td>{{ $asset->install_date ? \Carbon\Carbon::parse($asset->install_date)->format('d M Y') : '—' }}</td>
            <td class="text-end">
              <a href="{{ route('admin.cctv.assets.show', $asset) }}" class="btn btn-sm btn-outline-primary py-1 px-2"><i class="bx bx-show"></i></a>
              <a href="{{ route('admin.cctv.assets.edit', $asset) }}" class="btn btn-sm btn-outline-secondary py-1 px-2"><i class="bx bx-edit"></i></a>
            </td>
          </tr>
          @empty
          <tr><td colspan="8" class="text-center text-muted py-4">No assets found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($assets->hasPages())
    <div class="card-footer bg-transparent d-flex justify-content-end">{{ $assets->withQueryString()->links() }}</div>
    @endif
  </div>
</div>
@endsection
