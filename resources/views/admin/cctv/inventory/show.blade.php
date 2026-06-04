@extends('layouts.admin')
@section('title', 'Item – ' . $inventory->item_name)

@push('styles')
<style>
  .hero-bar { background:linear-gradient(135deg,#696cff,#4a4de8); border-radius:16px; padding:1.25rem 1.75rem; color:#fff; margin-bottom:1.5rem; display:flex; align-items:center; gap:1rem; flex-wrap:wrap; }
  .hero-bar .back-btn { width:38px; height:38px; border-radius:10px; background:rgba(255,255,255,.2); border:0; color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.2rem; text-decoration:none; transition:background .15s; }
  .hero-bar .back-btn:hover { background:rgba(255,255,255,.32); color:#fff; }
  .hero-bar h4 { margin:0; font-size:1.2rem; font-weight:700; }
  .section-card { border-radius:14px; border:0; box-shadow:0 2px 12px rgba(105,108,255,.08); margin-bottom:1.25rem; }
  .section-card .card-header { background:#f8f9fa; border-radius:14px 14px 0 0; padding:.85rem 1.25rem; font-weight:700; font-size:.875rem; display:flex; align-items:center; gap:.5rem; border-bottom:1px solid rgba(0,0,0,.06); }
  .section-icon { width:28px; height:28px; border-radius:8px; background:#eef0ff; color:#696cff; display:flex; align-items:center; justify-content:center; font-size:.9rem; }
  .info-label { font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#a1acb8; margin-bottom:.25rem; }
  .info-value { font-size:.92rem; font-weight:500; color:#32325d; }
  .stock-big { font-size:3rem; font-weight:900; line-height:1; }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="hero-bar">
    <a href="{{ route('admin.cctv.inventory.index') }}" class="back-btn"><i class="bx bx-arrow-back"></i></a>
    <div class="flex-grow-1">
      <h4>{{ $inventory->item_name }}</h4>
      <div style="opacity:.85;font-size:.85rem;">{{ $inventory->item_code }} &bull; {{ $inventory->category ?? 'Uncategorized' }}</div>
    </div>
    <a href="{{ route('admin.cctv.inventory.edit', $inventory) }}" class="btn btn-light btn-sm"><i class="bx bx-edit me-1"></i> Edit</a>
  </div>

  <div class="row g-3">
    <div class="col-lg-8">
      <div class="card section-card">
        <div class="card-header"><div class="section-icon"><i class="bx bx-package"></i></div> Item Details</div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-sm-4"><div class="info-label">Category</div><div class="info-value">{{ $inventory->category ?? '—' }}</div></div>
            <div class="col-sm-4"><div class="info-label">Brand</div><div class="info-value">{{ $inventory->brand ?? '—' }}</div></div>
            <div class="col-sm-4"><div class="info-label">Model</div><div class="info-value">{{ $inventory->model ?? '—' }}</div></div>
            <div class="col-sm-4"><div class="info-label">Unit</div><div class="info-value">{{ $inventory->unit ?? 'Pcs' }}</div></div>
            <div class="col-sm-4"><div class="info-label">Unit Price</div><div class="info-value">Rs. {{ number_format($inventory->unit_price ?? 0, 2) }}</div></div>
            <div class="col-sm-4"><div class="info-label">Cost Price</div><div class="info-value">Rs. {{ number_format($inventory->cost_price ?? 0, 2) }}</div></div>
            <div class="col-12"><div class="info-label">Description</div><div class="info-value" style="white-space:pre-line">{{ $inventory->description ?? '—' }}</div></div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card section-card">
        <div class="card-header"><div class="section-icon"><i class="bx bx-bar-chart"></i></div> Stock Status</div>
        <div class="card-body text-center">
          @php
            $stockStatus = $inventory->quantity <= 0 ? 'out' : ($inventory->quantity <= ($inventory->min_stock ?? 5) ? 'low' : 'ok');
            $stockColor = $stockStatus === 'out' ? '#ea5455' : ($stockStatus === 'low' ? '#fd7e14' : '#28c76f');
          @endphp
          <div class="stock-big" style="color:{{ $stockColor }};">{{ $inventory->quantity }}</div>
          <div class="text-muted small mt-1">{{ $inventory->unit ?? 'Pcs' }} in stock</div>
          @if($stockStatus === 'out')
            <div class="mt-2"><span class="badge bg-label-danger fs-6">Out of Stock</span></div>
          @elseif($stockStatus === 'low')
            <div class="mt-2"><span class="badge bg-label-warning fs-6">Low Stock</span></div>
            <div class="text-muted small mt-1">Min threshold: {{ $inventory->min_stock }}</div>
          @else
            <div class="mt-2"><span class="badge bg-label-success fs-6">In Stock</span></div>
          @endif
        </div>
      </div>

      <div class="card section-card">
        <div class="card-header"><div class="section-icon"><i class="bx bx-info-circle"></i></div> Quick Info</div>
        <div class="card-body">
          <div class="mb-3"><div class="info-label">Item Code</div><div class="fw-700 font-monospace text-primary">{{ $inventory->item_code }}</div></div>
          <div class="mb-3"><div class="info-label">Min Stock</div><div class="info-value">{{ $inventory->min_stock ?? '—' }}</div></div>
          <div class="mb-3"><div class="info-label">Added</div><div class="info-value">{{ $inventory->created_at->format('d M Y') }}</div></div>
          <hr>
          <div class="d-grid">
            <a href="{{ route('admin.cctv.inventory.edit', $inventory) }}" class="btn btn-primary btn-sm"><i class="bx bx-edit me-1"></i> Edit Item</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
