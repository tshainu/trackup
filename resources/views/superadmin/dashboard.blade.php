@extends('layouts.superadmin')
@section('title','Root Dashboard')

@push('styles')
<style>
  .metric-row { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:1rem; margin-bottom:1.5rem; }
  .card-purple { background:linear-gradient(135deg,#7c3aed,#a855f7); color:#fff; }
  .card-green  { background:linear-gradient(135deg,#16a34a,#22c55e); color:#fff; }
  .card-red    { background:linear-gradient(135deg,#dc2626,#ef4444); color:#fff; }
  .card-amber  { background:linear-gradient(135deg,#d97706,#f59e0b); color:#fff; }
  .card-blue   { background:linear-gradient(135deg,#2563eb,#3b82f6); color:#fff; }
  .card-dark   { background:linear-gradient(135deg,#1e1040,#3b1d8a); color:#fff; }
  .stat-icon-wrap { width:48px;height:48px;border-radius:12px;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-size:1.4rem; }
</style>
@endpush

@section('content')

{{-- Page Header --}}
<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h4 class="fw-bold mb-1" style="color:#1e1040;">Root Dashboard</h4>
    <p class="text-muted mb-0" style="font-size:.85rem;">All shops overview &mdash; {{ now()->format('l, d M Y') }}</p>
  </div>
  <a href="{{ route('superadmin.shops.create') }}" class="btn btn-sm" style="background:linear-gradient(135deg,#7c3aed,#a855f7);color:#fff;border:none;border-radius:10px;padding:.5rem 1.2rem;font-weight:700;">
    <i class="bx bx-plus me-1"></i> New Shop
  </a>
</div>

{{-- Stat Cards --}}
<div class="metric-row">
  <div class="sa-stat-card card-purple shadow-sm">
    <div class="d-flex align-items-center gap-3">
      <div class="stat-icon-wrap"><i class="bx bx-store"></i></div>
      <div>
        <div class="stat-num">{{ $totalShops }}</div>
        <div class="stat-label">Total Shops</div>
      </div>
    </div>
  </div>
  <div class="sa-stat-card card-green shadow-sm">
    <div class="d-flex align-items-center gap-3">
      <div class="stat-icon-wrap"><i class="bx bx-check-circle"></i></div>
      <div>
        <div class="stat-num">{{ $activeShops }}</div>
        <div class="stat-label">Active</div>
      </div>
    </div>
  </div>
  <div class="sa-stat-card card-blue shadow-sm">
    <div class="d-flex align-items-center gap-3">
      <div class="stat-icon-wrap"><i class="bx bx-wifi"></i></div>
      <div>
        <div class="stat-num">{{ $onlineShops }}</div>
        <div class="stat-label">Online Now</div>
      </div>
    </div>
  </div>
  <div class="sa-stat-card card-red shadow-sm">
    <div class="d-flex align-items-center gap-3">
      <div class="stat-icon-wrap"><i class="bx bx-block"></i></div>
      <div>
        <div class="stat-num">{{ $suspendedShops }}</div>
        <div class="stat-label">Suspended</div>
      </div>
    </div>
  </div>
  <div class="sa-stat-card card-amber shadow-sm">
    <div class="d-flex align-items-center gap-3">
      <div class="stat-icon-wrap"><i class="bx bx-time"></i></div>
      <div>
        <div class="stat-num">{{ $pendingShops }}</div>
        <div class="stat-label">Pending</div>
      </div>
    </div>
  </div>
  <div class="sa-stat-card card-dark shadow-sm">
    <div class="d-flex align-items-center gap-3">
      <div class="stat-icon-wrap"><i class="bx bx-calendar-check"></i></div>
      <div>
        <div class="stat-num">{{ $thisMonth }}</div>
        <div class="stat-label">This Month</div>
      </div>
    </div>
  </div>
</div>

<div class="row g-4 mb-4">

  {{-- 12-Month Chart --}}
  <div class="col-xl-7">
    <div class="card border-0 shadow-sm h-100" style="border-radius:16px;">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div>
            <h6 class="fw-bold mb-0">Shop Registrations</h6>
            <small class="text-muted">Last 12 months</small>
          </div>
          <span class="badge" style="background:#7c3aed22;color:#7c3aed;font-size:.72rem;">{{ $thisYear }} this year</span>
        </div>
        <div class="chart-bar-wrap" style="height:100px;">
          @php $maxVal = max(array_column($monthlyData,'count') ?: [1]); if($maxVal==0) $maxVal=1; @endphp
          @foreach($monthlyData as $m)
            @php $pct = ($m['count']/$maxVal)*90; @endphp
            <div class="chart-bar-col">
              <div class="bar-val">{{ $m['count'] }}</div>
              <div class="bar" style="height:{{ max($pct,4) }}px;"></div>
              <div class="bar-label">{{ substr($m['month'],0,3) }}</div>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>

  {{-- This Month Summary --}}
  <div class="col-xl-5">
    <div class="card border-0 shadow-sm h-100" style="border-radius:16px;">
      <div class="card-body">
        <h6 class="fw-bold mb-3">This Month's Shops</h6>
        @if($thisMonthShops->isEmpty())
          <div class="text-center py-4 text-muted">
            <i class="bx bx-store" style="font-size:2.5rem;opacity:.3;display:block;"></i>
            <div style="font-size:.85rem;">No shops created this month</div>
          </div>
        @else
          <div style="max-height:200px;overflow-y:auto;">
            @foreach($thisMonthShops as $shop)
              <div class="d-flex align-items-center gap-2 py-2 border-bottom">
                <div class="shop-avatar" style="width:34px;height:34px;font-size:.85rem;border-radius:9px;">
                  {{ strtoupper(substr($shop->shop_name,0,1)) }}
                </div>
                <div class="flex-grow-1 min-w-0">
                  <div class="fw-semibold" style="font-size:.82rem;">{{ $shop->shop_name }}</div>
                  <div style="font-size:.72rem;color:#888;">{{ $shop->city ?? $shop->country }}</div>
                </div>
                <span class="badge @if($shop->status==='active') bg-success @elseif($shop->status==='suspended') bg-danger @else bg-warning @endif" style="font-size:.65rem;">
                  {{ ucfirst($shop->status) }}
                </span>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>
  </div>

</div>

{{-- Recent Shops Grid --}}
<div class="card border-0 shadow-sm" style="border-radius:16px;">
  <div class="card-body">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <h6 class="fw-bold mb-0">Recent Shops</h6>
      <a href="{{ route('superadmin.shops.index') }}" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;font-size:.78rem;">View All</a>
    </div>
    @if($recentShops->isEmpty())
      <div class="text-center py-5 text-muted">
        <i class="bx bx-store" style="font-size:3rem;opacity:.2;display:block;margin-bottom:.5rem;"></i>
        No shops yet. <a href="{{ route('superadmin.shops.create') }}">Create the first one</a>
      </div>
    @else
      <div class="row g-3">
        @foreach($recentShops as $shop)
          <div class="col-md-4 col-xl-3">
            <div class="shop-card">
              <div class="d-flex align-items-center gap-2 mb-2">
                <div class="shop-avatar">{{ strtoupper(substr($shop->shop_name,0,1)) }}</div>
                <div class="flex-grow-1 min-w-0">
                  <div class="fw-bold" style="font-size:.88rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $shop->shop_name }}</div>
                  <div style="font-size:.72rem;color:#888;">{{ $shop->shop_code }}</div>
                </div>
              </div>
              <div class="d-flex align-items-center justify-content-between">
                <span style="font-size:.75rem;color:#888;">
                  <span class="status-dot {{ $shop->isOnline() ? 'dot-online' : ($shop->status==='active' ? 'dot-active' : ($shop->status==='suspended' ? 'dot-suspended' : 'dot-pending')) }}"></span>
                  {{ $shop->isOnline() ? 'Online' : ucfirst($shop->status) }}
                </span>
                <a href="{{ route('superadmin.shops.show', $shop) }}" class="btn btn-sm" style="padding:2px 10px;font-size:.72rem;background:#7c3aed11;color:#7c3aed;border:none;border-radius:6px;">View</a>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @endif
  </div>
</div>

@endsection
