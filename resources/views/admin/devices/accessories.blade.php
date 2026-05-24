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

.acc-card {
  background:#fff; border-radius:14px;
  border:1.5px solid #ebebff;
  box-shadow:0 2px 12px rgba(108,92,231,.07);
  padding:24px 28px;
}

.acc-add-row {
  display:flex;gap:10px;align-items:center;margin-bottom:22px;
}
.acc-add-row input {
  border:1.5px solid #e0e0f0;border-radius:10px;
  padding:9px 14px;font-size:.9rem;flex:1;outline:none;
  transition:.15s;
}
.acc-add-row input:focus { border-color:#696cff;box-shadow:0 0 0 3px rgba(108,92,231,.1); }
.acc-add-btn {
  white-space:nowrap;
  background:linear-gradient(135deg,#696cff,#8c57ff);color:#fff;
  border:0;border-radius:10px;padding:9px 20px;font-weight:700;font-size:.88rem;
  cursor:pointer;transition:.15s;
}
.acc-add-btn:hover { opacity:.88; }
.acc-add-btn:disabled { opacity:.5;cursor:default; }

.acc-tags-row { display:flex;flex-wrap:wrap;gap:8px; }

.acc-tag {
  display:inline-flex;align-items:center;gap:5px;
  background:#f0f0ff;border:1.5px solid #d0d0f0;
  border-radius:20px;padding:5px 14px 5px 16px;
  font-size:.84rem;font-weight:600;color:#4a4a8a;
  transition:.15s;
}
.acc-tag .rm-btn {
  background:none;border:none;padding:0;cursor:pointer;
  color:#a0a0c0;line-height:1;font-size:1rem;
  display:flex;align-items:center;transition:.15s;
}
.acc-tag .rm-btn:hover { color:#e04040; }

.acc-empty {
  color:#b0b0c8;font-size:.88rem;font-style:italic;
  padding:30px 0;text-align:center;width:100%;
}
.acc-count { font-size:.8rem;color:#888;margin-bottom:14px; }
</style>
@endpush

@section('content')
<div class="acc-header">
  <div>
    <h4><i class='bx bx-package me-2'></i>Accessories Received</h4>
    <p>Common accessories list — used across all job cards</p>
  </div>
  <a href="{{ route('admin.devices.index') }}" class="btn btn-light btn-sm fw-semibold">
    <i class='bx bx-devices me-1'></i> Devices & Brands
  </a>
</div>

<div class="acc-card">
  <div class="acc-add-row">
    <input type="text" id="accInput" placeholder="Type accessory name and press Enter..."
           maxlength="100" onkeydown="if(event.key==='Enter'){event.preventDefault();addAcc()}"/>
    <button class="acc-add-btn" id="addBtn" onclick="addAcc()"><i class='bx bx-plus me-1'></i>Add</button>
  </div>

  <div class="acc-count" id="accCount">{{ $accessories->count() }} {{ Str::plural('accessory', $accessories->count()) }}</div>

  <div class="acc-tags-row" id="accTags">
    @forelse($accessories as $acc)
    <span class="acc-tag" id="acc-tag-{{ $acc->id }}">
      {{ $acc->accessory_name }}
      <button type="button" class="rm-btn" onclick="deleteAcc({{ $acc->id }})" title="Remove">
        <i class='bx bx-x'></i>
      </button>
    </span>
    @empty
    <div class="acc-empty" id="accEmpty"><i class='bx bx-package d-block mb-2' style="font-size:2rem"></i>No accessories yet. Add one above.</div>
    @endforelse
  </div>
</div>
@endsection

@push('scripts')
<script>
const storeUrl   = '{{ route("admin.devices.accessories.store") }}';
const destroyUrl = '/admin/devices/accessories/';
const csrfToken  = document.querySelector('meta[name="csrf-token"]').content;

async function addAcc() {
  const input = document.getElementById('accInput');
  const btn   = document.getElementById('addBtn');
  const name  = input.value.trim();
  if (!name) { input.focus(); return; }

  btn.disabled = true;
  try {
    const res  = await fetch(storeUrl, {
      method: 'POST',
      headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':csrfToken, 'Accept':'application/json' },
      body: JSON.stringify({ accessory_name: name })
    });
    const data = await res.json();
    if (!res.ok) {
      alert(data.errors?.accessory_name?.[0] || data.message || 'Error');
      btn.disabled = false;
      return;
    }

    document.getElementById('accEmpty')?.remove();

    const tag = `<span class="acc-tag" id="acc-tag-${data.id}">
      ${escHtml(data.accessory_name)}
      <button type="button" class="rm-btn" onclick="deleteAcc(${data.id})" title="Remove"><i class='bx bx-x'></i></button>
    </span>`;
    document.getElementById('accTags').insertAdjacentHTML('beforeend', tag);
    updateCount(1);
    input.value = '';
    input.focus();
  } catch(e) { alert('Request failed'); }
  btn.disabled = false;
}

async function deleteAcc(id) {
  if (!confirm('Remove this accessory?')) return;
  try {
    const res = await fetch(destroyUrl + id, {
      method: 'DELETE',
      headers: { 'X-CSRF-TOKEN':csrfToken, 'Accept':'application/json' }
    });
    if (!res.ok) { alert('Error'); return; }
    document.getElementById('acc-tag-'+id)?.remove();
    updateCount(-1);
    if (!document.querySelector('#accTags .acc-tag')) {
      document.getElementById('accTags').innerHTML =
        `<div class="acc-empty" id="accEmpty"><i class='bx bx-package d-block mb-2' style="font-size:2rem"></i>No accessories yet. Add one above.</div>`;
    }
  } catch(e) { alert('Request failed'); }
}

function updateCount(delta) {
  const el  = document.getElementById('accCount');
  const cur = parseInt(el.textContent) || 0;
  const n   = cur + delta;
  el.textContent = n + ' ' + (n === 1 ? 'accessory' : 'accessories');
}

function escHtml(s) {
  return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>
@endpush
