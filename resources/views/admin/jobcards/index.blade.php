@extends('layouts.admin')
@section('title', 'Job Orders')
@section('page-title', 'Job Orders')
@section('breadcrumb')<li class="breadcrumb-item active">Job Orders</li>@endsection

@push('styles')
<style>
  .jobs-card {
    border-radius: 16px;
    border: 0;
    box-shadow: 0 2px 16px rgba(0,0,0,0.08);
  }

  /* ── Sortable headers ── */
  .sort-th {
    white-space: nowrap;
    user-select: none;
  }
  .sort-th a {
    color: inherit;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
  }
  .sort-th a:hover { color: #696cff; }
  .sort-arrows {
    display: inline-flex;
    flex-direction: column;
    line-height: 1;
    gap: 1px;
  }
  .sort-arrows .arr {
    font-size: 9px;
    color: #c5c6cb;
    transition: color .15s;
  }
  .sort-arrows .arr.active { color: #696cff; }

  /* ── Search & filter bar ── */
  .filter-bar {
    background: #f8f8fc;
    border-radius: 10px;
    padding: 14px 16px;
    margin-bottom: 18px;
  }

  /* ── Status badges ── */
  .status-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 5px;
  }

  /* ── Table row hover ── */
  .table-hover tbody tr:hover { background: #f5f5ff; }

  /* ── Action buttons ── */
  .action-btn { width: 30px; height: 30px; padding: 0; display:inline-flex; align-items:center; justify-content:center; border-radius: 8px; }

  /* ── Count badge ── */
  .total-count { font-size: .78rem; color: #8a8d93; }
</style>
@endpush

@section('content')

@php
  $badges = [
    'Pending'       => ['cls'=>'bg-label-warning',  'dot'=>'#ffab00', 'icon'=>'bx-time-five'],
    'In Progress'   => ['cls'=>'bg-label-info',     'dot'=>'#03c3ec', 'icon'=>'bx-loader-alt'],
    'Completed'     => ['cls'=>'bg-label-success',  'dot'=>'#71dd37', 'icon'=>'bx-check-circle'],
    'Not Completed' => ['cls'=>'bg-label-danger',   'dot'=>'#ff3e1d', 'icon'=>'bx-x-circle'],
  ];

  // Helper: build sort URL toggling direction
  function sortUrl(string $col, string $currentSort, string $currentDir): string {
    $dir = ($currentSort === $col && $currentDir === 'asc') ? 'desc' : 'asc';
    return request()->fullUrlWithQuery(['sort' => $col, 'dir' => $dir, 'page' => 1]);
  }
  function sortIcon(string $col, string $currentSort, string $currentDir): string {
    $upActive   = ($currentSort === $col && $currentDir === 'asc')  ? 'active' : '';
    $downActive = ($currentSort === $col && $currentDir === 'desc') ? 'active' : '';
    return '<span class="sort-arrows"><span class="arr '.$upActive.'">▲</span><span class="arr '.$downActive.'">▼</span></span>';
  }
@endphp

<div class="card jobs-card">

  {{-- ── Header ── --}}
  <div class="card-header d-flex justify-content-between align-items-center py-3 bg-white border-0">
    <div>
      <h5 class="mb-0 fw-bold"><i class='bx bx-list-ul me-1' style="color:#696cff"></i> All Job Orders</h5>
      <span class="total-count">{{ $jobs->total() }} total records</span>
    </div>
    <a href="{{ route('admin.jobcards.create') }}" class="btn btn-sm" style="background:linear-gradient(135deg,#696cff,#8c57ff);color:#fff;border-radius:8px;font-weight:600;">
      <i class='bx bx-plus me-1'></i> New Job Order
    </a>
  </div>

  <div class="card-body pt-0">

    {{-- ── Filter Bar ── --}}
    <form method="GET" class="filter-bar">
      <div class="row g-2 align-items-end">
        <div class="col-md-4">
          <label class="form-label small fw-semibold mb-1">Search</label>
          <div class="input-group input-group-sm">
            <span class="input-group-text bg-white"><i class='bx bx-search text-muted'></i></span>
            <input type="text" name="search" class="form-control"
              placeholder="Order no, customer, phone, serial…"
              value="{{ request('search') }}" />
          </div>
        </div>
        <div class="col-md-2">
          <label class="form-label small fw-semibold mb-1">Status</label>
          <select name="status" class="form-select form-select-sm">
            <option value="">All Status</option>
            @foreach(['Pending','In Progress','Completed','Not Completed'] as $s)
              <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label small fw-semibold mb-1">Device</label>
          <select name="device" class="form-select form-select-sm">
            <option value="">All Devices</option>
            @foreach($devices as $d)
              <option value="{{ $d->device_name }}" {{ request('device') == $d->device_name ? 'selected' : '' }}>{{ $d->device_name }}</option>
            @endforeach
          </select>
        </div>
        {{-- preserve sort params --}}
        @if(request('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif
        @if(request('dir'))<input type="hidden" name="dir" value="{{ request('dir') }}">@endif
        <div class="col-md-2 d-flex gap-2">
          <button type="submit" class="btn btn-sm btn-primary w-100"><i class='bx bx-filter-alt me-1'></i>Filter</button>
        </div>
        <div class="col-md-2">
          <a href="{{ route('admin.jobcards.index') }}" class="btn btn-sm btn-outline-secondary w-100">Clear</a>
        </div>
      </div>
    </form>

    {{-- ── Table ── --}}
    <table class="table table-hover align-middle mb-0 w-100" style="font-size:.85rem; table-layout:fixed;">
      <colgroup>
        <col style="width:7%">   {{-- Order No --}}
        <col style="width:13%">  {{-- Customer --}}
        <col style="width:10%">  {{-- Phone --}}
        <col style="width:15%">  {{-- Job Info --}}
        <col style="width:8%">   {{-- Date --}}
        <col style="width:9%">   {{-- Amount --}}
        <col style="width:12%">  {{-- Assigned --}}
        <col style="width:14%">  {{-- Status --}}
        <col style="width:12%">  {{-- Actions --}}
      </colgroup>
      <thead style="background:#f5f5ff;">
        <tr>
          <th class="sort-th ps-3">
            <a href="{{ sortUrl('order_no', $sort, $dir) }}">Order No {!! sortIcon('order_no', $sort, $dir) !!}</a>
          </th>
          <th class="sort-th">
            <a href="{{ sortUrl('customer_name', $sort, $dir) }}">Customer {!! sortIcon('customer_name', $sort, $dir) !!}</a>
          </th>
          <th class="sort-th">
            <a href="{{ sortUrl('phone_no', $sort, $dir) }}">Phone {!! sortIcon('phone_no', $sort, $dir) !!}</a>
          </th>
          <th class="sort-th">
            <a href="{{ sortUrl('device_name', $sort, $dir) }}">Job Info {!! sortIcon('device_name', $sort, $dir) !!}</a>
          </th>
          <th class="sort-th">
            <a href="{{ sortUrl('date', $sort, $dir) }}">Date {!! sortIcon('date', $sort, $dir) !!}</a>
          </th>
          <th class="sort-th">
            <a href="{{ sortUrl('rupees', $sort, $dir) }}">Amount {!! sortIcon('rupees', $sort, $dir) !!}</a>
          </th>
          <th style="white-space:nowrap;">Assigned To</th>
          <th class="sort-th">
            <a href="{{ sortUrl('status', $sort, $dir) }}">Status {!! sortIcon('status', $sort, $dir) !!}</a>
          </th>
          <th class="text-center" style="white-space:nowrap;">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($jobs as $job)
        @php $b = $badges[$job->status] ?? ['cls'=>'bg-label-secondary','dot'=>'#aaa','icon'=>'bx-circle']; @endphp
        <tr>
          <td class="ps-3" style="white-space:nowrap;">
            <span class="fw-bold text-primary" style="font-size:.82rem;">{{ $job->order_no }}</span>
          </td>
          <td>
            <div class="fw-semibold text-truncate">{{ $job->customer_name }}</div>
            <small class="text-muted">{{ $job->customer_id }}</small>
          </td>
          <td style="white-space:nowrap; font-size:.82rem;">{{ $job->phone_no }}</td>
          <td>
            <div class="fw-semibold text-truncate">{{ $job->device_name }}{{ $job->device_brand ? ' / '.$job->device_brand : '' }}</div>
            <small class="text-muted" style="font-size:.75rem; display:block; overflow:hidden; white-space:nowrap; text-overflow:ellipsis;">{{ $job->device_fault }}</small>
          </td>
          <td style="white-space:nowrap;"><small>{{ $job->date ? $job->date->format('d M Y') : '—' }}</small></td>
          <td class="fw-semibold" style="white-space:nowrap;">Rs.{{ number_format($job->rupees, 0) }}</td>
          <td>
            @if($job->employee)
              <span class="badge bg-label-secondary text-truncate" style="max-width:100%;">{{ $job->employee->employee_name ?? $job->employee->name }}</span>
            @else
              <span class="text-muted small">—</span>
            @endif
          </td>
          <td>
            <span class="badge {{ $b['cls'] }}" style="font-size:.72rem; white-space:nowrap;">{{ $job->status ?: 'Pending' }}</span>
          </td>
          <td>
            <div class="d-flex justify-content-center gap-1">
              <a href="{{ route('admin.jobcards.show', $job) }}" class="action-btn btn btn-outline-primary" title="View"><i class='bx bx-show'></i></a>
              <a href="{{ route('admin.jobcards.edit', $job) }}" class="action-btn btn btn-outline-secondary" title="Edit"><i class='bx bx-edit'></i></a>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="9" class="text-center py-5 text-muted" style="colspan:9">
            <i class='bx bx-inbox' style="font-size:2.5rem; display:block; margin-bottom:8px;"></i>
            No job orders found.
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>

    {{-- ── Pagination ── --}}
    @if($jobs->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-3 px-1">
      <small class="text-muted">
        Showing {{ $jobs->firstItem() }}–{{ $jobs->lastItem() }} of {{ $jobs->total() }}
      </small>
      {{ $jobs->links() }}
    </div>
    @endif

  </div>
</div>
@endsection
