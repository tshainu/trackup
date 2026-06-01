@extends('layouts.admin')
@section('title', 'Label Settings')
@section('page-title', 'Label Settings')
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.store.edit') }}">Settings</a></li>
  <li class="breadcrumb-item active">Label Settings</li>
@endsection

@push('styles')
<style>
.label-header {
  background: linear-gradient(135deg,#f59e0b 0%,#ef4444 100%);
  border-radius:14px;padding:24px 28px;color:#fff;
  display:flex;align-items:center;gap:18px;flex-wrap:wrap;
  margin-bottom:28px;
}
.label-header .label-ico {
  width:56px;height:56px;background:rgba(255,255,255,.2);border-radius:16px;
  display:flex;align-items:center;justify-content:center;font-size:1.7rem;flex-shrink:0;
}
.label-preview-wrap {
  display:flex;align-items:center;justify-content:center;
  background:#f4f5fa;border-radius:12px;padding:32px;min-height:160px;
}
.label-preview {
  background:#fff;
  border:2px dashed #d0d3e0;
  border-radius:6px;
  display:flex;
  flex-direction:column;
  align-items:center;
  justify-content:center;
  padding:8px 12px;
  gap:4px;
  transition:all .3s;
  overflow:hidden;
}
.label-preview .prev-shop { font-weight:700; color:#222; }
.label-preview .prev-fault { color:#555; }
.label-preview .prev-barcode { margin-top:4px; }
.label-preview .prev-order { font-size:9px; color:#888; }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  {{-- Header --}}
  <div class="label-header">
    <div class="label-ico"><i class='bx bx-barcode' style="font-size:1.7rem"></i></div>
    <div>
      <h4 class="mb-1" style="font-weight:700">Label Settings</h4>
      <p class="mb-0" style="opacity:.85">Configure the size and font of printed sticker labels.</p>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class='bx bx-check-circle me-2'></i>{{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <div class="row g-4">
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-header"><h5 class="mb-0"><i class='bx bx-ruler me-2'></i>Label Dimensions</h5></div>
        <div class="card-body">
          <form method="POST" action="{{ route('admin.label-settings.update') }}">
            @csrf
            @method('PUT')

            <div class="row g-3">
              <div class="col-6">
                <label class="form-label fw-semibold">Width (mm)</label>
                <input type="number" name="width_mm" class="form-control @error('width_mm') is-invalid @enderror"
                  value="{{ old('width_mm', $settings->width_mm) }}" min="10" max="300" step="0.5" id="input-width">
                @error('width_mm')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-6">
                <label class="form-label fw-semibold">Height (mm)</label>
                <input type="number" name="height_mm" class="form-control @error('height_mm') is-invalid @enderror"
                  value="{{ old('height_mm', $settings->height_mm) }}" min="10" max="300" step="0.5" id="input-height">
                @error('height_mm')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-6">
                <label class="form-label fw-semibold">Font Size (pt)</label>
                <input type="number" name="font_size" class="form-control @error('font_size') is-invalid @enderror"
                  value="{{ old('font_size', $settings->font_size) }}" min="6" max="24" id="input-font">
                @error('font_size')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-12 mt-2">
                <button type="submit" class="btn btn-primary px-4">
                  <i class='bx bx-save me-1'></i> Save Settings
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

    {{-- Live Preview --}}
    <div class="col-md-6">
      <div class="card shadow-sm h-100">
        <div class="card-header"><h5 class="mb-0"><i class='bx bx-show me-2'></i>Label Preview</h5></div>
        <div class="card-body d-flex flex-column justify-content-center">
          <div class="label-preview-wrap">
            <div class="label-preview" id="label-preview">
              <div class="prev-shop" id="prev-shop-name" style="font-size:11px">Your Shop Name</div>
              <div class="prev-fault" id="prev-fault" style="font-size:10px">Device Fault / Issue</div>
              <svg class="prev-barcode" id="prev-barcode"></svg>
              <div class="prev-order" id="prev-order">ORD-0001</div>
            </div>
          </div>
          <p class="text-muted text-center mt-2 mb-0" style="font-size:12px">
            Preview is approximate. Actual print will be <span id="prev-dims">{{ $settings->width_mm }}×{{ $settings->height_mm }}mm</span>.
          </p>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<script>
const PX_PER_MM = 3.7795;

function updatePreview() {
  const w  = parseFloat(document.getElementById('input-width').value)  || 62;
  const h  = parseFloat(document.getElementById('input-height').value) || 29;
  const fs = parseInt(document.getElementById('input-font').value)     || 10;

  const px_w = Math.round(w * PX_PER_MM);
  const px_h = Math.round(h * PX_PER_MM);

  const box = document.getElementById('label-preview');
  box.style.width  = px_w + 'px';
  box.style.height = px_h + 'px';

  document.getElementById('prev-shop-name').style.fontSize = (fs + 2) + 'px';
  document.getElementById('prev-fault').style.fontSize     = fs + 'px';
  document.getElementById('prev-order').style.fontSize     = (fs - 2) + 'px';

  document.getElementById('prev-dims').textContent = w + '×' + h + 'mm';

  try {
    JsBarcode('#prev-barcode', 'ORD-0001', {
      format: 'CODE128',
      width: 1.2,
      height: Math.max(20, px_h * 0.38),
      displayValue: false,
      margin: 2,
    });
  } catch(e) {}
}

['input-width','input-height','input-font'].forEach(id => {
  document.getElementById(id).addEventListener('input', updatePreview);
});

updatePreview();
</script>
@endpush
