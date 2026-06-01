@extends('layouts.superadmin')
@section('title', 'Shops')

@push('styles')
<style>
  .shop-card { border-radius:14px; border:1px solid #eee; padding:1.2rem; transition:box-shadow .2s,transform .2s; background:#fff; }
  .shop-card:hover { box-shadow:0 6px 24px rgba(0,0,0,.09); transform:translateY(-2px); }
  .shop-avatar { width:44px;height:44px;border-radius:11px;background:linear-gradient(135deg,#7c3aed,#a855f7);color:#fff;font-size:1.2rem;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
  .status-badge { font-size:.7rem;font-weight:700;padding:3px 10px;border-radius:20px; }
  .status-active    { background:#d1fae5;color:#065f46; }
  .status-suspended { background:#fee2e2;color:#991b1b; }
  .status-pending   { background:#fef3c7;color:#92400e; }
  .online-dot { width:8px;height:8px;border-radius:50%;background:#16a34a;display:inline-block;margin-right:4px;animation:pulse-green 1.5s infinite; }
  @keyframes pulse-green { 0%,100%{opacity:1} 50%{opacity:.4} }
  .filter-card { background:#fff;border-radius:14px;border:1px solid #eee;padding:1rem 1.25rem; }
  .stat-mini { border-radius:12px;padding:.9rem 1rem;text-align:center; }
  .view-toggle .btn { border-radius:8px!important;padding:.35rem .75rem; }
  .table-card { border-radius:14px;border:1px solid #eee;overflow:hidden; }
  .table-card table thead th { background:#f8f7ff;color:#6d6d6d;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.04em;padding:.85rem 1rem;border:none; }
  .table-card table tbody td { padding:.85rem 1rem;vertical-align:middle;border-bottom:1px solid #f1f1f1; }
  .table-card table tbody tr:last-child td { border-bottom:none; }
  .table-card table tbody tr:hover { background:#faf9ff; }
  .pagination .page-link { border-radius:8px!important;margin:0 2px;border:1px solid #eee;color:#7c3aed; }
  .pagination .page-item.active .page-link { background:linear-gradient(135deg,#7c3aed,#a855f7);border-color:transparent; }
</style>
@endpush

@section('content')

{{-- Header --}}
<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h4 class="fw-bold mb-1" style="color:#1e1040;">Shops</h4>
    <p class="text-muted mb-0" style="font-size:.85rem;">Manage all registered shops on the platform</p>
  </div>
  <a href="{{ route('superadmin.shops.create') }}" class="btn btn-sm" style="background:linear-gradient(135deg,#7c3aed,#a855f7);color:#fff;border:none;border-radius:10px;padding:.5rem 1.2rem;font-weight:700;">
    <i class="bx bx-plus me-1"></i> Add Shop
  </a>
</div>

{{-- Flash --}}
@if(session('success'))
  <div class="alert alert-success border-0 rounded-3 mb-3" style="background:#d1fae5;color:#065f46;">
    <i class="bx bx-check-circle me-2"></i>{!! session('success') !!}
  </div>
@endif
@if(session('error'))
  <div class="alert alert-danger border-0 rounded-3 mb-3">
    <i class="bx bx-x-circle me-2"></i>{{ session('error') }}
  </div>
@endif

{{-- Stat Row --}}
<div class="row g-3 mb-4">
  <div class="col-6 col-sm-3">
    <div class="stat-mini" style="background:#f0f0ff;">
      <div style="font-size:1.8rem;font-weight:800;color:#7c3aed;">{{ array_sum($statusCounts) }}</div>
      <div style="font-size:.75rem;color:#6d6d6d;font-weight:600;">Total Shops</div>
    </div>
  </div>
  <div class="col-6 col-sm-3">
    <div class="stat-mini" style="background:#d1fae5;">
      <div style="font-size:1.8rem;font-weight:800;color:#16a34a;">{{ $statusCounts['active'] ?? 0 }}</div>
      <div style="font-size:.75rem;color:#065f46;font-weight:600;">Active</div>
    </div>
  </div>
  <div class="col-6 col-sm-3">
    <div class="stat-mini" style="background:#fee2e2;">
      <div style="font-size:1.8rem;font-weight:800;color:#dc2626;">{{ $statusCounts['suspended'] ?? 0 }}</div>
      <div style="font-size:.75rem;color:#991b1b;font-weight:600;">Suspended</div>
    </div>
  </div>
  <div class="col-6 col-sm-3">
    <div class="stat-mini" style="background:#fef3c7;">
      <div style="font-size:1.8rem;font-weight:800;color:#d97706;">{{ $statusCounts['pending'] ?? 0 }}</div>
      <div style="font-size:.75rem;color:#92400e;font-weight:600;">Pending</div>
    </div>
  </div>
</div>

{{-- Filters --}}
<div class="filter-card mb-4">
  <form method="GET" action="{{ route('superadmin.shops.index') }}">
    <div class="row g-2 align-items-end">
      <div class="col-12 col-sm-4 col-lg-5">
        <label class="form-label text-muted" style="font-size:.75rem;font-weight:600;">SEARCH</label>
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Shop name, owner, email..."
               class="form-control form-control-sm" style="border-radius:8px;">
      </div>
      <div class="col-6 col-sm-2">
        <label class="form-label text-muted" style="font-size:.75rem;font-weight:600;">STATUS</label>
        <select name="status" class="form-select form-select-sm" style="border-radius:8px;">
          <option value="">All</option>
          <option value="active"    {{ request('status')==='active'    ? 'selected':'' }}>Active</option>
          <option value="suspended" {{ request('status')==='suspended' ? 'selected':'' }}>Suspended</option>
          <option value="pending"   {{ request('status')==='pending'   ? 'selected':'' }}>Pending</option>
        </select>
      </div>
      <div class="col-6 col-sm-2">
        <label class="form-label text-muted" style="font-size:.75rem;font-weight:600;">SORT</label>
        <select name="sort" class="form-select form-select-sm" style="border-radius:8px;">
          <option value="created_at"    {{ request('sort','created_at')==='created_at'    ? 'selected':'' }}>Newest</option>
          <option value="shop_name"     {{ request('sort')==='shop_name'     ? 'selected':'' }}>Name A-Z</option>
          <option value="last_active_at"{{ request('sort')==='last_active_at'? 'selected':'' }}>Last Active</option>
        </select>
      </div>
      <div class="col-12 col-sm-4 col-lg-3">
        <div class="d-flex gap-2 align-items-center justify-content-between">
          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-sm fw-bold" style="background:#7c3aed;color:#fff;border-radius:8px;">
              <i class="bx bx-filter-alt"></i> Filter
            </button>
            @if(request()->hasAny(['search','status','sort']))
              <a href="{{ route('superadmin.shops.index') }}" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;">
                Clear
              </a>
            @endif
          </div>
          {{-- View Toggle --}}
          <div class="view-toggle btn-group" role="group">
            <a href="{{ request()->fullUrlWithQuery(['view'=>'grid']) }}"
               class="btn btn-sm {{ request('view','grid')==='grid' ? 'btn-purple' : 'btn-outline-secondary' }}"
               style="{{ request('view','grid')==='grid' ? 'background:#7c3aed;color:#fff;' : '' }}">
              <i class="bx bx-grid-alt"></i>
            </a>
            <a href="{{ request()->fullUrlWithQuery(['view'=>'list']) }}"
               class="btn btn-sm {{ request('view')==='list' ? 'btn-purple' : 'btn-outline-secondary' }}"
               style="{{ request('view')==='list' ? 'background:#7c3aed;color:#fff;' : '' }}">
              <i class="bx bx-list-ul"></i>
            </a>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>

{{-- ===== GRID VIEW ===== --}}
@if(request('view','grid') === 'grid')
  @if($shops->count())
    <div class="row g-3">
      @foreach($shops as $shop)
        @php
          $isOnline = $shop->last_active_at && $shop->last_active_at->diffInMinutes(now()) <= 15;
          $sc = match($shop->status) {
            'active'    => 'status-active',
            'suspended' => 'status-suspended',
            'pending'   => 'status-pending',
            default     => '',
          };
        @endphp
        <div class="col-12 col-sm-6 col-xl-4">
          <div class="shop-card h-100 d-flex flex-column gap-3">
            {{-- Top --}}
            <div class="d-flex align-items-start justify-content-between gap-2">
              <div class="d-flex align-items-center gap-2">
                <div class="shop-avatar">{{ strtoupper(substr($shop->shop_name,0,1)) }}</div>
                <div style="min-width:0;">
                  <div class="fw-bold text-truncate" style="color:#1e1040;max-width:160px;">{{ $shop->shop_name }}</div>
                  <div class="text-muted" style="font-size:.8rem;">{{ $shop->owner_name }}</div>
                </div>
              </div>
              <div class="d-flex flex-column align-items-end gap-1" style="flex-shrink:0;">
                <span class="status-badge {{ $sc }}">{{ ucfirst($shop->status) }}</span>
                @if($isOnline)
                  <span style="font-size:.72rem;color:#16a34a;"><span class="online-dot"></span>Online</span>
                @endif
              </div>
            </div>

            {{-- Info --}}
            <div style="font-size:.8rem;color:#6d6d6d;" class="d-flex flex-column gap-1">
              @if($shop->email)
                <div class="d-flex align-items-center gap-2 text-truncate">
                  <i class="bx bx-envelope" style="color:#7c3aed;flex-shrink:0;"></i>
                  <span class="text-truncate">{{ $shop->email }}</span>
                </div>
              @endif
              @if($shop->phone)
                <div class="d-flex align-items-center gap-2">
                  <i class="bx bx-phone" style="color:#7c3aed;flex-shrink:0;"></i>
                  {{ $shop->phone }}
                </div>
              @endif
              @if($shop->city || $shop->country)
                <div class="d-flex align-items-center gap-2">
                  <i class="bx bx-map" style="color:#7c3aed;flex-shrink:0;"></i>
                  {{ collect([$shop->city,$shop->country])->filter()->implode(', ') }}
                </div>
              @endif
            </div>

            {{-- Code + Last active --}}
            <div class="d-flex align-items-center justify-content-between mt-auto">
              <span style="font-size:.72rem;background:#f0f0ff;color:#7c3aed;padding:2px 9px;border-radius:20px;font-weight:700;">{{ $shop->shop_code }}</span>
              <span style="font-size:.72rem;color:#aaa;">
                @if($shop->last_active_at) {{ $shop->last_active_at->diffForHumans() }} @else Never @endif
              </span>
            </div>

            {{-- Created date --}}
            <div style="font-size:.72rem;color:#bbb;">
              <i class="bx bx-calendar" style="color:#c4b5fd;"></i>
              Created {{ $shop->created_at->format('d M Y') }}
            </div>

            {{-- Actions --}}
            <div class="d-flex gap-2 pt-2" style="border-top:1px solid #f1f1f1;">
              <a href="{{ route('superadmin.shops.show', $shop) }}"
                 class="btn btn-sm flex-fill fw-semibold" style="background:#f0f0ff;color:#7c3aed;border-radius:8px;font-size:.78rem;">
                <i class="bx bx-show me-1"></i>View
              </a>
              <a href="{{ route('superadmin.shops.edit', $shop) }}"
                 class="btn btn-sm flex-fill fw-semibold" style="background:#eff6ff;color:#2563eb;border-radius:8px;font-size:.78rem;">
                <i class="bx bx-edit me-1"></i>Edit
              </a>
              <form method="POST" action="{{ route('superadmin.shops.destroy', $shop) }}"
                    onsubmit="return confirm('Delete {{ addslashes($shop->shop_name) }}? This cannot be undone.')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm" style="background:#fee2e2;color:#dc2626;border-radius:8px;font-size:.78rem;">
                  <i class="bx bx-trash"></i>
                </button>
              </form>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @else
    <div class="text-center py-5" style="background:#fff;border-radius:16px;border:1px solid #eee;">
      <i class="bx bx-store-alt" style="font-size:3rem;color:#d0d0d0;"></i>
      <p class="text-muted mt-2">No shops found.</p>
      <a href="{{ route('superadmin.shops.create') }}" class="btn btn-sm" style="background:#7c3aed;color:#fff;border-radius:8px;">Add First Shop</a>
    </div>
  @endif

{{-- ===== LIST VIEW ===== --}}
@else
  <div class="table-card">
    <table class="table mb-0" style="font-size:.85rem;">
      <thead>
        <tr>
          <th>Shop</th>
          <th class="d-none d-md-table-cell">Contact</th>
          <th class="d-none d-lg-table-cell">Location</th>
          <th>Status</th>
          <th class="d-none d-sm-table-cell">Last Active</th>
          <th class="d-none d-lg-table-cell">Created</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($shops as $shop)
          @php
            $isOnline = $shop->last_active_at && $shop->last_active_at->diffInMinutes(now()) <= 15;
            $sc = match($shop->status) {
              'active'    => 'status-active',
              'suspended' => 'status-suspended',
              'pending'   => 'status-pending',
              default     => '',
            };
          @endphp
          <tr>
            <td>
              <div class="d-flex align-items-center gap-2">
                <div class="shop-avatar" style="width:36px;height:36px;font-size:1rem;">
                  {{ strtoupper(substr($shop->shop_name,0,1)) }}
                </div>
                <div>
                  <div class="fw-semibold" style="color:#1e1040;">{{ $shop->shop_name }}</div>
                  <div class="text-muted" style="font-size:.75rem;">{{ $shop->owner_name }}</div>
                </div>
              </div>
            </td>
            <td class="d-none d-md-table-cell">
              <div style="color:#555;">{{ $shop->email }}</div>
              <div class="text-muted" style="font-size:.75rem;">{{ $shop->phone }}</div>
            </td>
            <td class="d-none d-lg-table-cell text-muted" style="font-size:.8rem;">
              {{ collect([$shop->city,$shop->country])->filter()->implode(', ') ?: '—' }}
            </td>
            <td>
              <span class="status-badge {{ $sc }}">{{ ucfirst($shop->status) }}</span>
              @if($isOnline)
                <div style="font-size:.7rem;color:#16a34a;margin-top:2px;"><span class="online-dot"></span>Online</div>
              @endif
            </td>
            <td class="d-none d-sm-table-cell text-muted" style="font-size:.78rem;">
              {{ $shop->last_active_at ? $shop->last_active_at->diffForHumans() : 'Never' }}
            </td>
            <td class="d-none d-lg-table-cell text-muted" style="font-size:.78rem;">
              {{ $shop->created_at->format('d M Y') }}
            </td>
            <td class="text-end">
              <div class="d-flex align-items-center justify-content-end gap-2">
                <a href="{{ route('superadmin.shops.show',$shop) }}" style="color:#7c3aed;font-size:.78rem;font-weight:600;text-decoration:none;">View</a>
                <a href="{{ route('superadmin.shops.edit',$shop) }}" style="color:#2563eb;font-size:.78rem;font-weight:600;text-decoration:none;">Edit</a>
                <form method="POST" action="{{ route('superadmin.shops.destroy',$shop) }}"
                      onsubmit="return confirm('Delete {{ addslashes($shop->shop_name) }}?')" class="d-inline">
                  @csrf @method('DELETE')
                  <button type="submit" style="background:none;border:none;color:#dc2626;font-size:.78rem;font-weight:600;cursor:pointer;padding:0;">Delete</button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="text-center py-5 text-muted">
              No shops found. <a href="{{ route('superadmin.shops.create') }}" style="color:#7c3aed;">Add one →</a>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
@endif

{{-- Pagination --}}
@if($shops->hasPages())
  <div class="d-flex justify-content-center mt-4">
    {{ $shops->appends(request()->query())->links('pagination::bootstrap-5') }}
  </div>
@endif

@endsection
