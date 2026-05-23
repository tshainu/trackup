@extends('layouts.admin')
@section('title', 'Device Management')
@section('page-title', 'Device Management')
@section('breadcrumb')<li class="breadcrumb-item active">Devices</li>@endsection

@push('styles')
<style>
/* Header */
.dv-header {
  background: linear-gradient(135deg,#696cff 0%,#8c57ff 60%,#a855f7 100%);
  border-radius: 14px; padding:22px 26px; color:#fff;
  display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;
  margin-bottom:24px;
}
.dv-header h4 { margin:0;font-weight:700; }
.dv-header p  { margin:0;opacity:.8;font-size:.85rem; }

/* Quick-add card */
.dv-add-card {
  background:#fff; border-radius:14px;
  border: 2px dashed #d0d0f0;
  padding: 22px;
  transition: border-color .2s;
}
.dv-add-card:focus-within { border-color:#696cff; }
.dv-add-card .label { font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#696cff;margin-bottom:10px; }
.dv-add-input-row { display:flex;gap:8px;align-items:center; }
.dv-add-input-row .form-control { border-radius:10px;border:1.5px solid #e0e0e0; }
.dv-add-input-row .form-control:focus { border-color:#696cff;box-shadow:0 0 0 3px rgba(108,92,231,.12); }
.dv-add-btn {
  white-space:nowrap;
  background:linear-gradient(135deg,#696cff,#8c57ff); color:#fff;
  border:0;border-radius:10px;padding:8px 18px;font-weight:700;font-size:.85rem;
  transition:.15s;cursor:pointer;
}
.dv-add-btn:hover { opacity:.88; }
.dv-add-btn:disabled { opacity:.5;cursor:default; }

/* Device list */
.dv-list { display:flex;flex-direction:column;gap:14px; }

.dv-item {
  background:#fff; border-radius:14px;
  border:1.5px solid #ebebff;
  box-shadow: 0 2px 12px rgba(108,92,231,.07);
  overflow:hidden;
  transition: box-shadow .18s;
}
.dv-item:hover { box-shadow: 0 4px 20px rgba(108,92,231,.13); }

.dv-item-head {
  display:flex;align-items:center;justify-content:space-between;
  padding: 14px 18px;
  background: linear-gradient(135deg,#696cff09,#8c57ff09);
  border-bottom:1px solid #f0f0ff;
  cursor: pointer;
  gap:12px;
}
.dv-item-head-left { display:flex;align-items:center;gap:12px; }
.dv-device-ico {
  width:38px;height:38px;border-radius:10px;
  background:linear-gradient(135deg,#696cff,#8c57ff);
  display:flex;align-items:center;justify-content:center;
  color:#fff;font-size:1.1rem;flex-shrink:0;
}
.dv-device-name { font-weight:700;font-size:.95rem;color:#2d2d3a; }
.dv-device-counts { font-size:.75rem;color:#999;margin-top:1px; }
.dv-chevron { color:#aaa;transition:transform .2s;font-size:1.1rem; }
.dv-item.open .dv-chevron { transform:rotate(180deg); }

.dv-item-body { display:none;padding:16px 18px; }
.dv-item.open .dv-item-body { display:block; }

/* Tag sections */
.dv-section-label { font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#888;margin-bottom:8px; }
.dv-tags-row { display:flex;flex-wrap:wrap;gap:7px;margin-bottom:10px;min-height:28px;align-items:center; }

.dv-tag {
  display:inline-flex;align-items:center;gap:5px;
  padding:4px 12px;border-radius:20px;
  font-size:.78rem;font-weight:600;
  transition:.15s;
}
.dv-tag-brand  { background:#e3f2fd;color:#1565c0; }
.dv-tag-fault  { background:#fff3e0;color:#e65100; }
.dv-tag .rm-btn {
  background:none;border:none;padding:0;margin:0;
  cursor:pointer;font-size:.7rem;color:inherit;opacity:.6;
  display:flex;align-items:center;line-height:1;
  transition:.12s;
}
.dv-tag .rm-btn:hover { opacity:1; }

/* Inline add row inside device */
.dv-inline-add {
  display:flex;gap:7px;align-items:center;margin-top:6px;
}
.dv-inline-add input { flex:1;border-radius:8px;border:1.5px solid #e0e0e0;padding:5px 12px;font-size:.82rem; }
.dv-inline-add input:focus { outline:none;border-color:#696cff;box-shadow:0 0 0 2px rgba(108,92,231,.1); }
.dv-inline-add .add-tag-btn {
  background:none;border:2px solid currentColor;border-radius:8px;
  padding:4px 12px;font-size:.78rem;font-weight:700;cursor:pointer;transition:.15s;
}
.brand-add-btn { color:#1565c0; }
.brand-add-btn:hover { background:#e3f2fd; }
.fault-add-btn { color:#e65100; }
.fault-add-btn:hover { background:#fff3e0; }

.dv-item-del-btn {
  background:none;border:1.5px solid #ffcdd2;color:#c62828;
  border-radius:8px;padding:5px 10px;font-size:.78rem;cursor:pointer;
  transition:.15s;flex-shrink:0;
}
.dv-item-del-btn:hover { background:#ffebee; }

/* Empty */
.dv-empty { text-align:center;padding:40px 20px;color:#bbb; }
.dv-empty .bx { font-size:3rem;display:block;margin-bottom:10px;color:#ddd; }

/* Spinner */
.dv-spinner {
  display:none;width:16px;height:16px;
  border:2px solid rgba(255,255,255,.3);border-top-color:#fff;
  border-radius:50%;animation:spin .6s linear infinite;
}
@keyframes spin { to { transform:rotate(360deg); } }

/* Alert toast */
.dv-toast {
  position:fixed;bottom:24px;right:24px;z-index:9999;
  background:#28a745;color:#fff;padding:12px 20px;
  border-radius:10px;font-weight:600;font-size:.85rem;
  box-shadow:0 4px 16px rgba(0,0,0,.15);
  transform:translateY(80px);opacity:0;
  transition:all .3s;pointer-events:none;
}
.dv-toast.show { transform:translateY(0);opacity:1; }
.dv-toast.error { background:#dc3545; }
</style>
@endpush

@section('content')

{{-- Header --}}
<div class="dv-header">
  <div>
    <h4><i class='bx bx-devices me-2'></i>Device Management</h4>
    <p>Manage device types, brands, and fault categories</p>
  </div>
  <div style="background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.3);border-radius:12px;padding:8px 20px;font-weight:700;">
    {{ $devices->count() }} Device Types
  </div>
</div>

{{-- Add new device type --}}
<div class="dv-add-card mb-4">
  <div class="label"><i class='bx bx-plus-circle me-1'></i>Add New Device Type</div>
  <div class="dv-add-input-row">
    <input type="text" id="newDeviceName" class="form-control" placeholder="e.g. Microwave, Printer, CCTV Camera..."
           maxlength="100" autocomplete="off" />
    <button type="button" class="dv-add-btn" id="addDeviceBtn" onclick="addDevice()">
      <span id="addDeviceSpinner" class="dv-spinner me-1"></span>
      <i class='bx bx-plus me-1'></i>Add Device
    </button>
  </div>
</div>

{{-- Device list --}}
<div class="dv-list" id="deviceList">
  @forelse($devices as $device)
  <div class="dv-item" id="dv-{{ $device->id }}">
    <div class="dv-item-head" onclick="toggleDevice({{ $device->id }})">
      <div class="dv-item-head-left">
        <div class="dv-device-ico"><i class='bx bx-chip'></i></div>
        <div>
          <div class="dv-device-name">{{ $device->device_name }}</div>
          <div class="dv-device-counts" id="counts-{{ $device->id }}">
            {{ $device->brands->count() }} brand(s) &nbsp;·&nbsp; {{ $device->faults->count() }} fault(s)
          </div>
        </div>
      </div>
      <div class="d-flex align-items-center gap-10" style="gap:10px">
        <form action="{{ route('admin.devices.destroy', $device->id) }}" method="POST"
              onsubmit="return confirm('Delete \'{{ $device->device_name }}\' and all its brands/faults?')" onclick="event.stopPropagation()">
          @csrf @method('DELETE')
          <button type="submit" class="dv-item-del-btn"><i class='bx bx-trash'></i></button>
        </form>
        <i class='bx bx-chevron-down dv-chevron'></i>
      </div>
    </div>
    <div class="dv-item-body">
      <div class="row g-3">
        {{-- Brands --}}
        <div class="col-md-6">
          <div class="dv-section-label"><i class='bx bx-purchase-tag me-1'></i>Brands</div>
          <div class="dv-tags-row" id="brands-{{ $device->id }}">
            @foreach($device->brands as $brand)
            <span class="dv-tag dv-tag-brand" id="brand-tag-{{ $brand->id }}">
              {{ $brand->device_brand }}
              <button type="button" class="rm-btn" onclick="deleteBrand({{ $brand->id }}, {{ $device->id }})" title="Remove">
                <i class='bx bx-x'></i>
              </button>
            </span>
            @endforeach
            @if($device->brands->count() === 0)
            <span class="text-muted" style="font-size:.78rem" id="brands-empty-{{ $device->id }}">No brands yet</span>
            @endif
          </div>
          <div class="dv-inline-add">
            <input type="text" id="brand-input-{{ $device->id }}" placeholder="Add brand..." maxlength="80"
                   onkeydown="if(event.key==='Enter'){event.preventDefault();addBrand({{ $device->id }})}" />
            <button type="button" class="add-tag-btn brand-add-btn" onclick="addBrand({{ $device->id }})">+ Add</button>
          </div>
        </div>
        {{-- Faults --}}
        <div class="col-md-6">
          <div class="dv-section-label"><i class='bx bx-error-circle me-1'></i>Fault Types</div>
          <div class="dv-tags-row" id="faults-{{ $device->id }}">
            @foreach($device->faults as $fault)
            <span class="dv-tag dv-tag-fault" id="fault-tag-{{ $fault->id }}">
              {{ $fault->device_fault }}
              <button type="button" class="rm-btn" onclick="deleteFault({{ $fault->id }}, {{ $device->id }})" title="Remove">
                <i class='bx bx-x'></i>
              </button>
            </span>
            @endforeach
            @if($device->faults->count() === 0)
            <span class="text-muted" style="font-size:.78rem" id="faults-empty-{{ $device->id }}">No faults yet</span>
            @endif
          </div>
          <div class="dv-inline-add">
            <input type="text" id="fault-input-{{ $device->id }}" placeholder="Add fault..." maxlength="100"
                   onkeydown="if(event.key==='Enter'){event.preventDefault();addFault({{ $device->id }})}" />
            <button type="button" class="add-tag-btn fault-add-btn" onclick="addFault({{ $device->id }})">+ Add</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  @empty
  <div class="dv-empty" id="emptyState">
    <i class='bx bx-devices'></i>
    <div class="fw-semibold">No device types yet</div>
    <div class="mt-1" style="font-size:.82rem">Add your first device type above</div>
  </div>
  @endforelse
</div>

{{-- Toast --}}
<div class="dv-toast" id="dvToast"></div>

@endsection

@push('scripts')
<script>
const csrf = '{{ csrf_token() }}';
const routes = {
  deviceStore:   '{{ route("admin.devices.store") }}',
  brandStore:    '{{ route("admin.devices.brands.store") }}',
  faultStore:    '{{ route("admin.devices.faults.store") }}',
  brandDestroy:  '/admin/devices/brands/',
  faultDestroy:  '/admin/devices/faults/',
  deviceDestroy: '/admin/devices/',
};

function toast(msg, isError=false) {
  const t = document.getElementById('dvToast');
  t.textContent = msg;
  t.className = 'dv-toast' + (isError?' error':'');
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 2800);
}

function toggleDevice(id) {
  document.getElementById('dv-'+id).classList.toggle('open');
}

// ── Add Device Type ──
async function addDevice() {
  const input = document.getElementById('newDeviceName');
  const name  = input.value.trim();
  if (!name) { input.focus(); return; }

  const btn = document.getElementById('addDeviceBtn');
  const spin = document.getElementById('addDeviceSpinner');
  btn.disabled = true; spin.style.display='inline-block';

  try {
    const res = await fetch(routes.deviceStore, {
      method:'POST',
      headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf,'Accept':'application/json'},
      body: JSON.stringify({device_name: name})
    });
    if (!res.ok) {
      const err = await res.json();
      toast(err.message || (err.errors?.device_name?.[0]) || 'Error', true);
      return;
    }
    const data = await res.json();
    input.value = '';
    appendDeviceCard(data.id, data.device_name);
    document.getElementById('emptyState')?.remove();
    toast('"' + name + '" added successfully!');
  } catch(e) { toast('Network error', true); }
  finally { btn.disabled=false; spin.style.display='none'; }
}

function appendDeviceCard(id, name) {
  const tpl = `
  <div class="dv-item open" id="dv-${id}">
    <div class="dv-item-head" onclick="toggleDevice(${id})">
      <div class="dv-item-head-left">
        <div class="dv-device-ico"><i class='bx bx-chip'></i></div>
        <div>
          <div class="dv-device-name">${escHtml(name)}</div>
          <div class="dv-device-counts" id="counts-${id}">0 brand(s) · 0 fault(s)</div>
        </div>
      </div>
      <div class="d-flex align-items-center" style="gap:10px">
        <button type="button" class="dv-item-del-btn" onclick="event.stopPropagation();deleteDevice(${id},'${escHtml(name)}')"><i class='bx bx-trash'></i></button>
        <i class='bx bx-chevron-down dv-chevron'></i>
      </div>
    </div>
    <div class="dv-item-body">
      <div class="row g-3">
        <div class="col-md-6">
          <div class="dv-section-label"><i class='bx bx-purchase-tag me-1'></i>Brands</div>
          <div class="dv-tags-row" id="brands-${id}">
            <span class="text-muted" style="font-size:.78rem" id="brands-empty-${id}">No brands yet</span>
          </div>
          <div class="dv-inline-add">
            <input type="text" id="brand-input-${id}" placeholder="Add brand..." maxlength="80"
                   onkeydown="if(event.key==='Enter'){event.preventDefault();addBrand(${id})}" />
            <button type="button" class="add-tag-btn brand-add-btn" onclick="addBrand(${id})">+ Add</button>
          </div>
        </div>
        <div class="col-md-6">
          <div class="dv-section-label"><i class='bx bx-error-circle me-1'></i>Fault Types</div>
          <div class="dv-tags-row" id="faults-${id}">
            <span class="text-muted" style="font-size:.78rem" id="faults-empty-${id}">No faults yet</span>
          </div>
          <div class="dv-inline-add">
            <input type="text" id="fault-input-${id}" placeholder="Add fault..." maxlength="100"
                   onkeydown="if(event.key==='Enter'){event.preventDefault();addFault(${id})}" />
            <button type="button" class="add-tag-btn fault-add-btn" onclick="addFault(${id})">+ Add</button>
          </div>
        </div>
      </div>
    </div>
  </div>`;
  document.getElementById('deviceList').insertAdjacentHTML('afterbegin', tpl);
  // scroll to new card
  document.getElementById('dv-'+id).scrollIntoView({behavior:'smooth',block:'nearest'});
}

// ── Delete Device (JS fallback for dynamically added) ──
function deleteDevice(id, name) {
  if (!confirm('Delete "' + name + '" and all its brands/faults?')) return;
  const form = document.createElement('form');
  form.method='POST'; form.action='/admin/devices/'+id;
  form.innerHTML=`<input name="_token" value="${csrf}"><input name="_method" value="DELETE">`;
  document.body.appendChild(form); form.submit();
}

// ── Add Brand (inline, no page reload) ──
async function addBrand(deviceId) {
  const input = document.getElementById('brand-input-'+deviceId);
  const name  = input.value.trim();
  if (!name) { input.focus(); return; }

  try {
    const res = await fetch(routes.brandStore, {
      method:'POST',
      headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf,'Accept':'application/json'},
      body: JSON.stringify({device_list_id: deviceId, device_brand: name})
    });
    if (!res.ok) { toast('Error adding brand', true); return; }
    const data = await res.json();
    input.value = '';
    // Remove empty placeholder
    document.getElementById('brands-empty-'+deviceId)?.remove();
    // Append tag
    const tag = `<span class="dv-tag dv-tag-brand" id="brand-tag-${data.id}">
      ${escHtml(name)}
      <button type="button" class="rm-btn" onclick="deleteBrand(${data.id},${deviceId})" title="Remove"><i class='bx bx-x'></i></button>
    </span>`;
    document.getElementById('brands-'+deviceId).insertAdjacentHTML('beforeend', tag);
    updateCounts(deviceId);
    toast('Brand "'+name+'" added!');
  } catch(e) { toast('Network error', true); }
}

// ── Delete Brand ──
async function deleteBrand(brandId, deviceId) {
  if (!confirm('Remove this brand?')) return;
  try {
    const res = await fetch('/admin/devices/brands/'+brandId, {
      method:'POST',
      headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf,'Accept':'application/json'},
      body: JSON.stringify({_method:'DELETE'})
    });
    if (!res.ok) { toast('Error removing brand', true); return; }
    document.getElementById('brand-tag-'+brandId)?.remove();
    const tagsRow = document.getElementById('brands-'+deviceId);
    if (!tagsRow.querySelector('.dv-tag-brand')) {
      tagsRow.insertAdjacentHTML('afterbegin','<span class="text-muted" style="font-size:.78rem" id="brands-empty-'+deviceId+'">No brands yet</span>');
    }
    updateCounts(deviceId);
    toast('Brand removed');
  } catch(e) { toast('Network error', true); }
}

// ── Add Fault ──
async function addFault(deviceId) {
  const input = document.getElementById('fault-input-'+deviceId);
  const name  = input.value.trim();
  if (!name) { input.focus(); return; }

  try {
    const res = await fetch(routes.faultStore, {
      method:'POST',
      headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf,'Accept':'application/json'},
      body: JSON.stringify({device_list_id: deviceId, device_fault: name})
    });
    if (!res.ok) { toast('Error adding fault', true); return; }
    const data = await res.json();
    input.value = '';
    document.getElementById('faults-empty-'+deviceId)?.remove();
    const tag = `<span class="dv-tag dv-tag-fault" id="fault-tag-${data.id}">
      ${escHtml(name)}
      <button type="button" class="rm-btn" onclick="deleteFault(${data.id},${deviceId})" title="Remove"><i class='bx bx-x'></i></button>
    </span>`;
    document.getElementById('faults-'+deviceId).insertAdjacentHTML('beforeend', tag);
    updateCounts(deviceId);
    toast('Fault "'+name+'" added!');
  } catch(e) { toast('Network error', true); }
}

// ── Delete Fault ──
async function deleteFault(faultId, deviceId) {
  if (!confirm('Remove this fault?')) return;
  try {
    const res = await fetch('/admin/devices/faults/'+faultId, {
      method:'POST',
      headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf,'Accept':'application/json'},
      body: JSON.stringify({_method:'DELETE'})
    });
    if (!res.ok) { toast('Error removing fault', true); return; }
    document.getElementById('fault-tag-'+faultId)?.remove();
    const tagsRow = document.getElementById('faults-'+deviceId);
    if (!tagsRow.querySelector('.dv-tag-fault')) {
      tagsRow.insertAdjacentHTML('afterbegin','<span class="text-muted" style="font-size:.78rem" id="faults-empty-'+deviceId+'">No faults yet</span>');
    }
    updateCounts(deviceId);
    toast('Fault removed');
  } catch(e) { toast('Network error', true); }
}

function updateCounts(deviceId) {
  const brands = document.getElementById('brands-'+deviceId)?.querySelectorAll('.dv-tag-brand').length || 0;
  const faults = document.getElementById('faults-'+deviceId)?.querySelectorAll('.dv-tag-fault').length || 0;
  const el = document.getElementById('counts-'+deviceId);
  if (el) el.textContent = brands+' brand(s) · '+faults+' fault(s)';
}

function escHtml(s) {
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// Enter key on device input
document.getElementById('newDeviceName').addEventListener('keydown', function(e) {
  if (e.key === 'Enter') { e.preventDefault(); addDevice(); }
});
</script>
@endpush
