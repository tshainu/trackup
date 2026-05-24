@extends('layouts.admin')
@section('title', 'Accessories Received')
@section('page-title', 'Accessories Received')
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.devices.index') }}">Devices & Brands</a></li>
  <li class="breadcrumb-item active">Accessories Received</li>
@endsection

@push('styles')
<style>
.acc-header {
  background: linear-gradient(135deg,#696cff 0%,#8c57ff 60%,#a855f7 100%);
  border-radius:14px; padding:22px 26px; color:#fff;
  display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;
  margin-bottom:24px;
}
.acc-header h4 { margin:0;font-weight:700; }
.acc-header p  { margin:0;opacity:.8;font-size:.85rem; }

.acc-device-card {
  background:#fff;
  border-radius:14px;
  border:1.5px solid #ebebff;
  box-shadow: 0 2px 12px rgba(108,92,231,.07);
  overflow:hidden;
  margin-bottom:14px;
  transition: box-shadow .18s;
}
.acc-device-card:hover { box-shadow: 0 4px 20px rgba(108,92,231,.13); }

.acc-device-header {
  padding:14px 20px;
  background:linear-gradient(90deg,#f5f5ff 0%,#fff 100%);
  border-bottom:1.5px solid #ebebff;
  display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;
}
.acc-device-name {
  font-weight:700;font-size:1rem;color:#3d3d3d;
  display:flex;align-items:center;gap:8px;
}
.acc-device-name i { color:#696cff;font-size:1.15rem; }
.acc-count-badge {
  background:#696cff;color:#fff;border-radius:20px;
  padding:2px 10px;font-size:.72rem;font-weight:700;
}

.acc-body { padding:16px 20px; }

.acc-tags-row { display:flex;flex-wrap:wrap;gap:8px;margin-bottom:12px; }

.acc-tag {
  display:inline-flex;align-items:center;gap:5px;
  background:#f0f0ff;border:1.5px solid #d0d0f0;
  border-radius:20px;padding:4px 12px 4px 14px;
  font-size:.82rem;font-weight:600;color:#4a4a8a;
  transition:.15s;
}
.acc-tag .rm-btn {
  background:none;border:none;padding:0;cursor:pointer;
  color:#a0a0c0;line-height:1;font-size:.95rem;
  display:flex;align-items:center;
  transition:.15s;
}
.acc-tag .rm-btn:hover { color:#e04040; }

.acc-empty { color:#aaa;font-size:.82rem;font-style:italic;padding:4px 0; }

.acc-add-row {
  display:flex;gap:8px;align-items:center;margin-top:4px;
}
.acc-add-row input {
  border:1.5px solid #e0e0f0;border-radius:10px;
  padding:7px 12px;font-size:.85rem;flex:1;outline:none;
  transition:.15s;
}
.acc-add-row input:focus { border-color:#696cff;box-shadow:0 0 0 3px rgba(108,92,231,.1); }
.acc-add-btn {
  white-space:nowrap;
  background:linear-gradient(135deg,#696cff,#8c57ff);color:#fff;
  border:0;border-radius:10px;padding:7px 16px;font-weight:700;font-size:.82rem;
  cursor:pointer;transition:.15s;
}
.acc-add-btn:hover { opacity:.88; }
.acc-add-btn:disabled { opacity:.5;cursor:default; }

.acc-no-devices { text-align:center;padding:60px 20px;color:#aaa; }
.acc-no-devices i { font-size:3rem;display:block;margin-bottom:12px;color:#d0d0e0; }
</style>
@endpush

@section('content')
<div class="acc-header">
  <div>
    <h4><i class='bx bx-package me-2'></i>Accessories Received</h4>
    <p>Manage accessories per device type</p>
  </div>
  <a href="{{ route('admin.devices.index') }}" class="btn btn-light btn-sm fw-semibold">
    <i class='bx bx-devices me-1'></i> Devices & Brands
  </a>
</div>

@if($devices->count() === 0)
  <div class="acc-no-devices">
    <i class='bx bx-devices'></i>
    No devices found. Add devices first.
    <br><a href="{{ route('admin.devices.index') }}" class="btn btn-primary mt-3">Go to Devices</a>
  </div>
@else
  <div id="acc-device-list">
    @foreach($devices as $device)
    <div class="acc-device-card" id="acc-device-{{ $device->id }}">
      <div class="acc-device-header">
        <div class="acc-device-name">
          <i class='bx bx-chip'></i>
          {{ $device->device_name }}
        </div>
        <span class="acc-count-badge" id="acc-badge-{{ $device->id }}">
          {{ $device->accessories->count() }} {{ Str::plural('accessory', $device->accessories->count()) }}
        </span>
      </div>
      <div class="acc-body">
        <div class="acc-tags-row" id="acc-tags-{{ $device->id }}">
          @foreach($device->accessories as $acc)
          <span class="acc-tag" id="acc-tag-{{ $acc->id }}">
            {{ $acc->accessory_name }}
            <button type="button" class="rm-btn" onclick="deleteAcc({{ $acc->id }}, {{ $device->id }})" title="Remove">
              <i class='bx bx-x'></i>
            </button>
          </span>
          @endforeach
          @if($device->accessories->count() === 0)
          <span class="acc-empty" id="acc-empty-{{ $device->id }}">No accessories yet</span>
          @endif
        </div>
        <div class="acc-add-row">
          <input type="text" id="acc-input-{{ $device->id }}"
                 placeholder="Type accessory name..."
                 maxlength="100"
                 onkeydown="if(event.key==='Enter'){event.preventDefault();addAcc({{ $device->id }})}"/>
          <button class="acc-add-btn" onclick="addAcc({{ $device->id }})">+ Add</button>
        </div>
      </div>
    </div>
    @endforeach
  </div>
@endif
@endsection

@push('scripts')
<script>
const storeUrl   = '{{ route("admin.devices.accessories.store") }}';
const destroyUrl = '/admin/devices/accessories/';
const csrfToken  = document.querySelector('meta[name="csrf-token"]').content;

async function addAcc(deviceId) {
  const input = document.getElementById('acc-input-'+deviceId);
  const name  = input.value.trim();
  if (!name) return;

  const btn = input.nextElementSibling;
  btn.disabled = true;

  try {
    const res = await fetch(storeUrl, {
      method: 'POST',
      headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':csrfToken, 'Accept':'application/json' },
      body: JSON.stringify({ device_list_id: deviceId, accessory_name: name })
    });
    const data = await res.json();
    if (!res.ok) { alert(data.message || 'Error'); btn.disabled=false; return; }

    document.getElementById('acc-empty-'+deviceId)?.remove();

    const tag = `<span class="acc-tag" id="acc-tag-${data.id}">
      ${escHtml(data.accessory_name)}
      <button type="button" class="rm-btn" onclick="deleteAcc(${data.id},${deviceId})" title="Remove">
        <i class='bx bx-x'></i>
      </button>
    </span>`;
    document.getElementById('acc-tags-'+deviceId).insertAdjacentHTML('beforeend', tag);

    updateBadge(deviceId, 1);
    input.value = '';
  } catch(e) { alert('Request failed'); }
  btn.disabled = false;
}

async function deleteAcc(accId, deviceId) {
  if (!confirm('Remove this accessory?')) return;
  try {
    const res = await fetch(destroyUrl+accId, {
      method: 'DELETE',
      headers: { 'X-CSRF-TOKEN':csrfToken, 'Accept':'application/json' }
    });
    if (!res.ok) { alert('Error'); return; }

    document.getElementById('acc-tag-'+accId)?.remove();
    updateBadge(deviceId, -1);

    const row = document.getElementById('acc-tags-'+deviceId);
    if (row && row.querySelectorAll('.acc-tag').length === 0) {
      row.insertAdjacentHTML('afterbegin',
        `<span class="acc-empty" id="acc-empty-${deviceId}">No accessories yet</span>`);
    }
  } catch(e) { alert('Request failed'); }
}

function updateBadge(deviceId, delta) {
  const badge = document.getElementById('acc-badge-'+deviceId);
  if (!badge) return;
  const cur = parseInt(badge.textContent) || 0;
  const next = cur + delta;
  badge.textContent = next + ' ' + (next === 1 ? 'accessory' : 'accessories');
}

function escHtml(str) {
  return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>
@endpush
