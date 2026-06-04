@extends('layouts.admin')
@section('title', 'Edit Item – ' . $inventory->item_name)

@push('styles')
<style>
  .hero-bar { background:linear-gradient(135deg,#696cff,#4a4de8); border-radius:16px; padding:1.25rem 1.75rem; color:#fff; margin-bottom:1.5rem; display:flex; align-items:center; gap:1rem; }
  .hero-bar .back-btn { width:38px; height:38px; border-radius:10px; background:rgba(255,255,255,.2); border:0; color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.2rem; text-decoration:none; transition:background .15s; }
  .hero-bar .back-btn:hover { background:rgba(255,255,255,.32); color:#fff; }
  .hero-bar h4 { margin:0; font-size:1.2rem; font-weight:700; }
  .hero-bar p { margin:0; opacity:.85; font-size:.85rem; }
  .form-card { border-radius:14px; border:0; box-shadow:0 2px 12px rgba(0,0,0,.06); margin-bottom:1.25rem; }
  .form-card .card-header { background:#f8f9fa; border-radius:14px 14px 0 0; padding:.85rem 1.25rem; font-weight:700; font-size:.875rem; display:flex; align-items:center; gap:.5rem; border-bottom:1px solid rgba(0,0,0,.06); }
  .section-icon { width:28px; height:28px; border-radius:8px; background:#eef0ff; color:#696cff; display:flex; align-items:center; justify-content:center; font-size:.9rem; }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="hero-bar">
    <a href="{{ route('admin.cctv.inventory.show', $inventory) }}" class="back-btn"><i class="bx bx-arrow-back"></i></a>
    <div><h4>Edit Item</h4><p>{{ $inventory->item_name }}</p></div>
  </div>

  <form method="POST" action="{{ route('admin.cctv.inventory.update', $inventory) }}">
    @csrf @method('PUT')
    <div class="row g-3">
      <div class="col-lg-8">
        <div class="card form-card">
          <div class="card-header"><div class="section-icon"><i class="bx bx-package"></i></div> Item Details</div>
          <div class="card-body row g-3">
            <div class="col-md-8">
              <label class="form-label fw-600">Item Name <span class="text-danger">*</span></label>
              <input type="text" name="item_name" class="form-control" value="{{ old('item_name', $inventory->item_name) }}" required>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-600">Category</label>
              <select name="category" class="form-select">
                <option value="">— Select —</option>
                @foreach(['Camera','DVR/NVR','Cable','Power Supply','Accessory','Other'] as $cat)
                  <option value="{{ $cat }}" {{ old('category',$inventory->category)===$cat?'selected':'' }}>{{ $cat }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-600">Brand</label>
              <input type="text" name="brand" class="form-control" value="{{ old('brand', $inventory->brand) }}">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-600">Model</label>
              <input type="text" name="model" class="form-control" value="{{ old('model', $inventory->model) }}">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-600">Unit</label>
              <select name="unit" class="form-select">
                @foreach(['Pcs','Meter','Box','Set','Pair','Roll'] as $u)
                  <option value="{{ $u }}" {{ old('unit',$inventory->unit)===$u?'selected':'' }}>{{ $u }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-600">Quantity <span class="text-danger">*</span></label>
              <input type="number" name="quantity" class="form-control" value="{{ old('quantity', $inventory->quantity) }}" min="0" required>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-600">Min Stock Alert</label>
              <input type="number" name="min_stock" class="form-control" value="{{ old('min_stock', $inventory->min_stock) }}" min="0">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-600">Unit Price (Rs.)</label>
              <input type="number" name="unit_price" step="0.01" class="form-control" value="{{ old('unit_price', $inventory->unit_price ?? 0) }}">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-600">Cost Price (Rs.)</label>
              <input type="number" name="cost_price" step="0.01" class="form-control" value="{{ old('cost_price', $inventory->cost_price ?? 0) }}">
            </div>
            <div class="col-12">
              <label class="form-label fw-600">Description / Notes</label>
              <textarea name="description" class="form-control" rows="3">{{ old('description', $inventory->description) }}</textarea>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card form-card">
          <div class="card-header"><div class="section-icon"><i class="bx bx-save"></i></div> Save</div>
          <div class="card-body">
            <div class="mb-3 p-3 rounded" style="background:#f8f9fa;">
              <div class="small text-muted fw-600">Item Code</div>
              <div class="fw-700 font-monospace text-primary">{{ $inventory->item_code }}</div>
              <div class="small text-muted mt-2">Current Stock: <strong>{{ $inventory->quantity }} {{ $inventory->unit }}</strong></div>
            </div>
            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> Update Item</button>
              <a href="{{ route('admin.cctv.inventory.show', $inventory) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
@endsection
