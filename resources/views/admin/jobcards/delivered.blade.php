@extends('layouts.admin')
@section('breadcrumb')<li class="breadcrumb-item active">Delivered Orders</li>@endsection

@push('styles')
<style>
.filter-bar { background:#fff; border-radius:12px; padding:16px 20px; margin-bottom:20px; box-shadow:0 1px 6px rgba(0,0,0,.06); }
.table-card  { background:#fff; border-radius:14px; overflow:hidden; box-shadow:0 1px 8px rgba(0,0,0,.07); }
.table thead th { background:#f4f5fb; font-size:.76rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; color:#6c757d; border:0; padding:12px 14px; white-space:nowrap; }
.table tbody tr:hover { background:#f8f9ff; }
.table tbody td { vertical-align:middle; font-size:.88rem; padding:11px 14px; border-color:#f0f0f0; }
.badge-delivered { background:#ede8ff; color:#6f42c1; font-size:.72rem; font-weight:700; padding:3px 9px; border-radius:20px; }
.sort-link { color:inherit; text-decoration:none; }
.sort-link:hover { color:#6f42c1; }
.page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; }
.page-header h4 { font-weight:800; margin:0; }
.stat-chip { background:#ede8ff; color:#6f42c1; border-radius:20px; padding:4px 14px; font-size:.8rem; font-weight:700; }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  {{-- Header --}}
  <div class="page-header">
    <div class="d-flex align-items-center gap-3">
      <div style="width:42px;height:42px;border-radius:12px;background:linear-gradient(135deg,#6f42c1,#a855f7);display:flex;align-items:center;justify-content:center;">
        <i class='bx bx-package' style="font-size:1.3rem;color:#fff;"></i>
      </div>
      <div>
        <h4>Delivered Orders</h4>
        <small class="text-muted">Archived completed &amp; delivered job cards</small>
      </div>
    </div>
    <span class="stat-chip"><i class='bx bx-check-circle me-1'></i>{{ $orders->total() }} Records</span>
  </div>

  {{-- Filters --}}
  <form method="GET" class="filter-bar">
    <div class="row g-2 align-items-end">
      <div class="col-md-4">
        <label for="del-search" class="form-label small fw-semibold mb-1">Search</label>
        <div class="input-group input-group-sm">
          <span class="input-group-text bg-white"><i class='bx bx-search text-muted'></i></span>
          <input type="text" id="del-search" name="search" class="form-control"
            placeholder="Order no, name, phone, device…"
            value="{{ request('search') }}" autocomplete="off" />
        </div>
      </div>
      <div class="col-md-3">
        <label for="del-device" class="form-label small fw-semibold mb-1">Device</label>
        <select name="device" id="del-device" class="form-select form-select-sm">
          <option value="">All Devices</option>
          @foreach($devices as $d)
            <option value="{{ $d }}" {{ request('device') == $d ? 'selected' : '' }}>{{ $d }}</option>
          @endforeach
        </select>
      </div>
      @if(request('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif
      @if(request('dir'))<input type="hidden" name="dir" value="{{ request('dir') }}">@endif
      <div class="col-md-2">
        <button type="submit" class="btn btn-sm btn-primary w-100"><i class='bx bx-filter-alt me-1'></i>Filter</button>
      </div>
      <div class="col-md-2">
        <a href="{{ route('admin.jobcards.delivered') }}" class="btn btn-sm btn-outline-secondary w-100">Clear</a>
      </div>
    </div>
  </form>

  {{-- Table --}}
  <div class="table-card">
    <div class="table-responsive">
      <table class="table mb-0">
        <thead>
          <tr>
            @php
              function delSort($col, $label, $sort, $dir) {
                $newDir = ($sort === $col && $dir === 'asc') ? 'desc' : 'asc';
                $icon = $sort === $col ? ($dir === 'asc' ? '↑' : '↓') : '';
                $url = request()->fullUrlWithQuery(['sort' => $col, 'dir' => $newDir]);
                return "<a href=\"$url\" class=\"sort-link\">$label <span style='opacity:.5'>$icon</span></a>";
              }
            @endphp
            <th>{!! delSort('order_no','Order No',$sort,$dir) !!}</th>
            <th>{!! delSort('customer_name','Customer',$sort,$dir) !!}</th>
            <th>{!! delSort('device_name','Device',$sort,$dir) !!}</th>
            <th>{!! delSort('grand_total','Amount',$sort,$dir) !!}</th>
            <th>Paid</th>
            <th>{!! delSort('delivered_at','Delivered At',$sort,$dir) !!}</th>
            <th>Status</th>
            <th>Receipt</th>
          </tr>
        </thead>
        <tbody>
          @forelse($orders as $order)
          <tr>
            <td><span class="fw-bold text-primary">{{ $order->order_no }}</span></td>
            <td>
              <div class="fw-semibold">{{ $order->customer_name }}</div>
              <small class="text-muted">{{ $order->phone_no }}</small>
            </td>
            <td>
              <div>{{ $order->device_name }}</div>
              @if($order->device_brand)<small class="text-muted">{{ $order->device_brand }}</small>@endif
            </td>
            <td class="fw-semibold">Rs. {{ number_format($order->grand_total, 2) }}</td>
            <td class="text-success fw-semibold">Rs. {{ number_format($order->paid_amount, 2) }}</td>
            <td>
              @if($order->delivered_at)
                <div>{{ \Carbon\Carbon::parse($order->delivered_at)->format('d M Y') }}</div>
                <small class="text-muted">{{ \Carbon\Carbon::parse($order->delivered_at)->format('h:i A') }}</small>
              @else
                <span class="text-muted">—</span>
              @endif
            </td>
            <td><span class="badge-delivered"><i class='bx bx-package me-1'></i>Delivered</span></td>
            <td>
              <a href="{{ route('admin.jobcards.receipt', ['type' => 'delivered', 'id' => $order->id]) }}"
                 target="_blank" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;">
                <i class='bx bx-printer'></i>
              </a>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="8" class="text-center py-5 text-muted">
              <i class='bx bx-package' style="font-size:2.5rem;opacity:.3;display:block;margin-bottom:8px;"></i>
              No delivered orders found.
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Pagination --}}
    @if($orders->hasPages())
    <div class="d-flex justify-content-center py-3">
      {{ $orders->links() }}
    </div>
    @endif
  </div>

</div>
@endsection
