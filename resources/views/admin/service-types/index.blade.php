@extends('layouts.admin')
@section('title', 'Service Types')
@section('page-title', 'Service Types')
@section('breadcrumb')
  <li class="breadcrumb-item active">Service Types</li>
@endsection

@push('styles')
<style>
.st-header { background:linear-gradient(135deg,#059669,#047857);border-radius:14px;padding:22px 28px;color:#fff;margin-bottom:1.5rem; }
.st-header h4 { margin:0;font-weight:700; }
.st-header p  { margin:0;opacity:.85;font-size:.85rem; }
.st-card { border:0;border-radius:14px;box-shadow:0 2px 16px rgba(0,0,0,.07);margin-bottom:1rem; }
.st-card .card-body { padding:18px 20px; }
.st-icon-picker { display:flex;flex-wrap:wrap;gap:6px;margin-top:8px; }
.st-icon-opt { width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:8px;border:2px solid #e0e0e0;cursor:pointer;font-size:1.1rem;transition:.15s; }
.st-icon-opt:hover,.st-icon-opt.selected { border-color:#059669;background:#ecfdf5; }
.st-icon-hidden { display:none; }
.type-row { display:flex;align-items:center;gap:12px;padding:14px 0;border-bottom:1px solid #f5f5f5; }
.type-row:last-child { border-bottom:0; }
.type-icon-wrap { width:42px;height:42px;border-radius:10px;background:#ecfdf5;display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:#059669;flex-shrink:0; }
.type-name { flex:1;font-weight:600;font-size:.95rem; }
.type-charge { font-size:.85rem;color:#059669;font-weight:700;min-width:80px; }
.type-count { font-size:.75rem;color:#aaa; }
.badge-active   { background:#d1fae5;color:#065f46;padding:3px 10px;border-radius:10px;font-size:.72rem;font-weight:700; }
.badge-inactive { background:#f3f4f6;color:#9ca3af;padding:3px 10px;border-radius:10px;font-size:.72rem;font-weight:700; }
</style>
@endpush

@section('content')
<div class="st-header d-flex justify-content-between align-items-center">
  <div>
    <h4><i class='bx bx-wrench me-2'></i>Service Types</h4>
    <p>Manage categories for field service requests</p>
  </div>
  <button class="btn btn-light fw-bold" style="border-radius:10px;" data-bs-toggle="modal" data-bs-target="#addTypeModal">
    <i class='bx bx-plus me-1'></i>Add Type
  </button>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
  <div class="alert alert-danger alert-dismissible">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="card st-card">
  <div class="card-body">
    @forelse($types as $type)
    <div class="type-row">
      <div class="type-icon-wrap">
        <i class="bx {{ $type->icon ?? 'bx-wrench' }}"></i>
      </div>
      <div class="type-name">{{ $type->name }}</div>
      <div class="type-charge">Rs. {{ number_format($type->base_charge, 2) }}</div>
      <div class="type-count">{{ $type->complaints()->count() }} complaints</div>
      <span class="{{ $type->active ? 'badge-active' : 'badge-inactive' }}">
        {{ $type->active ? 'Active' : 'Inactive' }}
      </span>
      <div class="d-flex gap-1">
        {{-- Toggle --}}
        <form action="{{ route('admin.service-types.toggle', $type) }}" method="POST">
          @csrf @method('PATCH')
          <button type="submit" class="btn btn-sm {{ $type->active ? 'btn-outline-warning' : 'btn-outline-success' }}" style="border-radius:8px;font-size:.72rem;padding:3px 8px;">
            {{ $type->active ? 'Deactivate' : 'Activate' }}
          </button>
        </form>
        {{-- Edit --}}
        <button class="btn btn-sm btn-outline-primary" style="border-radius:8px;font-size:.72rem;padding:3px 8px;"
          onclick="openEditModal({{ $type->id }}, '{{ addslashes($type->name) }}', '{{ $type->icon }}', {{ $type->base_charge }}, {{ $type->active ? 'true' : 'false' }})">
          Edit
        </button>
        {{-- Delete --}}
        <form action="{{ route('admin.service-types.destroy', $type) }}" method="POST"
          onsubmit="return confirm('Delete {{ addslashes($type->name) }}?')">
          @csrf @method('DELETE')
          <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius:8px;font-size:.72rem;padding:3px 8px;">✕</button>
        </form>
      </div>
    </div>
    @empty
    <div class="text-center text-muted py-5">No service types yet. Add one above.</div>
    @endforelse
  </div>
</div>

{{-- ── Add Modal ── --}}
<div class="modal fade" id="addTypeModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content" style="border-radius:14px;">
      <div class="modal-header" style="background:linear-gradient(135deg,#059669,#047857);color:#fff;border-radius:14px 14px 0 0;">
        <h5 class="modal-title fw-bold"><i class='bx bx-plus-circle me-2'></i>Add Service Type</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('admin.service-types.store') }}" method="POST">
        @csrf
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" required placeholder="e.g. AC Repair, RO Service…" />
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Base Charge (Rs.)</label>
            <div class="input-group">
              <span class="input-group-text">Rs.</span>
              <input type="number" name="base_charge" class="form-control" min="0" step="0.01" value="0" />
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Icon</label>
            <input type="hidden" name="icon" id="addIconInput" value="bx-wrench" />
            <div class="st-icon-picker" id="addIconPicker">
              @php $icons = ['bx-wrench','bx-droplet','bx-sun','bx-plug','bx-water','bx-home','bx-cog','bx-chip','bx-alarm','bx-bolt','bx-shield','bx-car','bx-refrigerator','bx-tv','bx-wifi','bx-sitemap']; @endphp
              @foreach($icons as $icon)
              <div class="st-icon-opt {{ $icon === 'bx-wrench' ? 'selected' : '' }}" data-icon="{{ $icon }}" onclick="selectIcon(this,'addIconInput','addIconPicker')">
                <i class="bx {{ $icon }}"></i>
              </div>
              @endforeach
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn fw-bold" style="background:linear-gradient(135deg,#059669,#047857);color:#fff;border-radius:10px;">Add Type</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ── Edit Modal ── --}}
<div class="modal fade" id="editTypeModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content" style="border-radius:14px;">
      <div class="modal-header" style="background:linear-gradient(135deg,#059669,#047857);color:#fff;border-radius:14px 14px 0 0;">
        <h5 class="modal-title fw-bold"><i class='bx bx-edit me-2'></i>Edit Service Type</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="editTypeForm" method="POST">
        @csrf @method('PUT')
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="editTypeName" class="form-control" required />
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Base Charge (Rs.)</label>
            <div class="input-group">
              <span class="input-group-text">Rs.</span>
              <input type="number" name="base_charge" id="editTypeCharge" class="form-control" min="0" step="0.01" />
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Icon</label>
            <input type="hidden" name="icon" id="editIconInput" value="bx-wrench" />
            <div class="st-icon-picker" id="editIconPicker">
              @foreach($icons as $icon)
              <div class="st-icon-opt" data-icon="{{ $icon }}" onclick="selectIcon(this,'editIconInput','editIconPicker')">
                <i class="bx {{ $icon }}"></i>
              </div>
              @endforeach
            </div>
          </div>
          <div class="form-check form-switch mt-2">
            <input class="form-check-input" type="checkbox" name="active" id="editTypeActive" value="1">
            <label class="form-check-label fw-semibold" for="editTypeActive">Active</label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn fw-bold" style="background:linear-gradient(135deg,#059669,#047857);color:#fff;border-radius:10px;">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
function selectIcon(el, inputId, pickerId) {
  document.querySelectorAll('#' + pickerId + ' .st-icon-opt').forEach(o => o.classList.remove('selected'));
  el.classList.add('selected');
  document.getElementById(inputId).value = el.dataset.icon;
}

function openEditModal(id, name, icon, charge, active) {
  const form = document.getElementById('editTypeForm');
  form.action = `/admin/service-types/${id}`;
  document.getElementById('editTypeName').value   = name;
  document.getElementById('editTypeCharge').value = charge;
  document.getElementById('editTypeActive').checked = active;
  document.getElementById('editIconInput').value  = icon;
  // Highlight correct icon
  document.querySelectorAll('#editIconPicker .st-icon-opt').forEach(o => {
    o.classList.toggle('selected', o.dataset.icon === icon);
  });
  new bootstrap.Modal(document.getElementById('editTypeModal')).show();
}
</script>
@endpush
