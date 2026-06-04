@extends('layouts.admin')
@section('title', 'CCTV Leads')

@push('styles')
<style>
  .stat-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 14px; margin-bottom: 1.5rem; }
  @media(max-width:991px){.stat-grid{grid-template-columns:repeat(2,1fr);}}
  @media(max-width:575px){.stat-grid{grid-template-columns:repeat(2,1fr);}}
  .stat-card { background:#fff; border-radius:14px; padding:16px 18px; display:flex; align-items:center; gap:14px; box-shadow:0 2px 12px rgba(0,0,0,.06); border-left:4px solid transparent; transition:transform .15s,box-shadow .15s; }
  .stat-card:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(0,0,0,.1); }
  .stat-icon { width:46px; height:46px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.35rem; flex-shrink:0; }
  .stat-num { font-size:1.65rem; font-weight:800; line-height:1.1; }
  .stat-lbl { font-size:.72rem; font-weight:600; color:#8592a3; text-transform:uppercase; letter-spacing:.04em; margin-top:1px; }
  .sc-blue { border-color:#696cff; } .sc-blue .stat-icon { background:#eef0ff; color:#696cff; } .sc-blue .stat-num { color:#696cff; }
  .sc-orange { border-color:#fd7e14; } .sc-orange .stat-icon { background:#fff3e8; color:#fd7e14; } .sc-orange .stat-num { color:#fd7e14; }
  .sc-green { border-color:#28c76f; } .sc-green .stat-icon { background:#e8faf0; color:#28c76f; } .sc-green .stat-num { color:#28c76f; }
  .sc-red { border-color:#ea5455; } .sc-red .stat-icon { background:#fdeaea; color:#ea5455; } .sc-red .stat-num { color:#ea5455; }
  .sc-purple { border-color:#8c57ff; } .sc-purple .stat-icon { background:#f3eeff; color:#8c57ff; } .sc-purple .stat-num { color:#8c57ff; }
  .hero-bar { background:linear-gradient(135deg,#696cff,#8c57ff); border-radius:16px; padding:1.25rem 1.75rem; color:#fff; margin-bottom:1.5rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem; }
  .hero-bar h4 { margin:0; font-size:1.2rem; font-weight:700; }
  .hero-bar p { margin:0; opacity:.85; font-size:.85rem; }
  .badge-lead { font-size:.72rem; padding:4px 10px; border-radius:20px; font-weight:600; }
  .badge-new { background:#eef0ff; color:#696cff; }
  .badge-contacted { background:#fff3e8; color:#fd7e14; }
  .badge-qualified { background:#e8faf0; color:#28c76f; }
  .badge-converted { background:#f3eeff; color:#8c57ff; }
  .badge-lost { background:#fdeaea; color:#ea5455; }
  .filter-tabs { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:1rem; }
  .filter-tab { padding:6px 16px; border-radius:20px; font-size:.8rem; font-weight:600; border:1.5px solid #d9dee3; color:#697a8d; background:#fff; cursor:pointer; text-decoration:none; transition:all .15s; }
  .filter-tab.active, .filter-tab:hover { background:#696cff; color:#fff; border-color:#696cff; }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  {{-- Hero --}}
  <div class="hero-bar">
    <div>
      <h4><i class="bx bx-user-plus me-2"></i>CCTV Leads</h4>
      <p>Manage and track all CCTV enquiries</p>
    </div>
    <a href="{{ route('admin.cctv.leads.create') }}" class="btn btn-light btn-sm fw-600">
      <i class="bx bx-plus me-1"></i> New Lead
    </a>
  </div>

  {{-- Stats --}}
  <div class="stat-grid">
    <div class="stat-card sc-blue">
      <div class="stat-icon"><i class="bx bx-user-plus"></i></div>
      <div><div class="stat-num">{{ $stats['total'] ?? 0 }}</div><div class="stat-lbl">Total Leads</div></div>
    </div>
    <div class="stat-card sc-orange">
      <div class="stat-icon"><i class="bx bx-phone-call"></i></div>
      <div><div class="stat-num">{{ $stats['new'] ?? 0 }}</div><div class="stat-lbl">New</div></div>
    </div>
    <div class="stat-card sc-green">
      <div class="stat-icon"><i class="bx bx-check-circle"></i></div>
      <div><div class="stat-num">{{ $stats['qualified'] ?? 0 }}</div><div class="stat-lbl">Qualified</div></div>
    </div>
    <div class="stat-card sc-purple">
      <div class="stat-icon"><i class="bx bx-transfer"></i></div>
      <div><div class="stat-num">{{ $stats['converted'] ?? 0 }}</div><div class="stat-lbl">Converted</div></div>
    </div>
  </div>

  {{-- Filter + Search --}}
  <div class="card border-0 shadow-sm mb-3">
    <div class="card-body pb-2">
      <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <div class="filter-tabs">
          <a href="{{ route('admin.cctv.leads.index') }}" class="filter-tab {{ !request('status') ? 'active' : '' }}">All</a>
          @foreach(['new','contacted','qualified','converted','lost'] as $s)
            <a href="{{ route('admin.cctv.leads.index', ['status'=>$s]) }}" class="filter-tab {{ request('status')===$s ? 'active' : '' }}">{{ ucfirst($s) }}</a>
          @endforeach
        </div>
        <form method="GET" class="d-flex gap-2">
          @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
          <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Search name / mobile…" style="width:220px">
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
            <th>Source</th>
            <th>Status</th>
            <th>Follow Up</th>
            <th>Created</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($leads as $lead)
          <tr>
            <td><span class="fw-700 text-primary font-monospace">{{ $lead->lead_no }}</span></td>
            <td>
              <div class="fw-600">{{ $lead->customer_name }}</div>
              @if($lead->location)<div class="text-muted small">{{ $lead->location }}</div>@endif
            </td>
            <td class="font-monospace small">{{ $lead->mobile }}</td>
            <td>{{ $lead->source ?? '—' }}</td>
            <td>
              @php $sc = ['new'=>'badge-new','contacted'=>'badge-contacted','qualified'=>'badge-qualified','converted'=>'badge-converted','lost'=>'badge-lost'][$lead->status] ?? 'badge-secondary' @endphp
              <span class="badge-lead {{ $sc }}">{{ ucfirst($lead->status) }}</span>
            </td>
            <td>{{ $lead->follow_up_date ? \Carbon\Carbon::parse($lead->follow_up_date)->format('d M Y') : '—' }}</td>
            <td>{{ $lead->created_at->format('d M Y') }}</td>
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
