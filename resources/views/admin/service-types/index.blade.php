@extends('layouts.admin')
@section('title', 'Service Types')

@push('styles')
<style>
  .st-hero {
    background: linear-gradient(135deg, #03c3ec 0%, #028bb6 100%);
    border-radius: 16px;
    padding: 1.5rem 2rem;
    color: #fff;
    margin-bottom: 1.5rem;
    position: relative;
    overflow: hidden;
  }
  .st-hero::after {
    content: '\ed3f';
    font-family: 'boxicons';
    position: absolute;
    right: -10px; top: -20px;
    font-size: 9rem;
    opacity: .08;
    pointer-events: none;
  }
  .st-hero h4 { font-size: 1.35rem; font-weight: 700; margin-bottom: .2rem; }
  .st-hero p  { opacity: .85; margin: 0; font-size: .88rem; }

  .st-add-btn {
    background: rgba(255,255,255,.2);
    border: 1.5px solid rgba(255,255,255,.5);
    color: #fff;
    border-radius: 10px;
    font-weight: 700;
    padding: .5rem 1.2rem;
    backdrop-filter: blur(4px);
    transition: background .15s;
    white-space: nowrap;
  }
  .st-add-btn:hover { background: rgba(255,255,255,.32); color: #fff; }

  .st-card {
    border-radius: 16px;
    border: 0;
    box-shadow: 0 2px 18px rgba(3,195,236,.1);
    overflow: hidden;
  }
  .st-card .card-header {
    background: #f8f8fc;
    border-bottom: 1px solid #ebebf5;
    padding: 1rem 1.5rem;
  }

  .table-hover tbody tr { transition: background .1s; }
  .table-hover tbody tr:hover { background: #f0fbff; }

  .st-name {
    font-weight: 700;
    font-size: .9rem;
    color: #32325d;
  }

  .st-charge {
    font-family: 'Courier New', monospace;
    font-size: .95rem;
    font-weight: 700;
    color: #03c3ec;
  }

  .toggle-btn {
    border: 0;
    border-radius: 20px;
    padding: .25rem .85rem;
    font-size: .72rem;
    font-weight: 700;
    cursor: pointer;
    transition: all .15s;
    letter-spacing: .03em;
  }
  .toggle-btn:hover { transform: translateY(-1px); box-shadow: 0 3px 8px rgba(0,0,0,.15); }

  .action-btn {
    width: 32px; height: 32px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    transition: all .15s;
  }
  .action-btn:hover { transform: translateY(-1px); }

  .empty-state {
    text-align: center;
    padding: 4rem 1rem;
  }
  .empty-icon {
    width: 72px; height: 72px;
    border-radius: 50%;
    background: linear-gradient(135deg, #e8f7ff, #d0efff);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 1rem;
    font-size: 2rem;
    color: #03c3ec;
  }

  /* Modal polish */
  .modal-content { border-radius: 16px; border: 0; overflow: hidden; }
  .modal-header  {
    background: linear-gradient(135deg, #e8f7ff, #d0efff);
    border-bottom: 1px solid rgba(0,0,0,.06);
    padding: 1.25rem 1.5rem;
  }
  .modal-header .modal-title { font-weight: 700; color: #0393b4; }
  .modal-body   { padding: 1.5rem; }
  .modal-footer { background: #fafafa; border-top: 1px solid rgba(0,0,0,.06); padding: 1rem 1.5rem; }

  .modal-form-label {
    font-size: .8rem;
    font-weight: 700;
    color: #566a7f;
    text-transform: uppercase;
    letter-spacing: .05em;
    margin-bottom: .4rem;
  }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  {{-- Hero --}}
  <div class="st-hero d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
      <h4><i class="bx bx-wrench me-2"></i>Service Types</h4>
      <p>Manage field service categories and base charges</p>
    </div>
    <div class="d-flex align-items-center gap-3">
      <div style="background:rgba(255,255,255,.18);border-radius:10px;padding:.35rem 1rem;font-size:.8rem;font-weight:700;backdrop-filter:blur(4px);">
        {{ $serviceTypes->count() }} Types
      </div>
      <button type="button" class="st-add-btn btn" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="bx bx-plus me-1"></i>Add Type
      </button>
    </div>
  </div>

  {{-- Alerts --}}
  @if(session('success'))
  <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
    <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif
  @if(session('error'))
  <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
    <i class="bx bx-error-circle me-1"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif
  @if($errors->any())
  <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
    <strong>Please fix:</strong>
    <ul class="mb-0 mt-1 ps-3">
      @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif

  {{-- Table card --}}
  <div class="card st-card">
    <div class="card-header d-flex align-items-center justify-content-between">
      <div>
        <h6 class="mb-0 fw-bold">All Service Types</h6>
        <div class="text-muted small">Click toggle to activate/deactivate</div>
      </div>
      <button type="button" class="btn btn-sm"
              style="background:linear-gradient(135deg,#03c3ec,#028bb6);color:#fff;border:0;border-radius:8px;font-weight:600;"
              data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="bx bx-plus me-1"></i>Add Type
      </button>
    </div>

    @if($serviceTypes->isEmpty())
    <div class="empty-state">
      <div class="empty-icon"><i class="bx bx-category-alt"></i></div>
      <div class="fw-semibold mb-1">No service types yet</div>
      <div class="text-muted small mb-3">Add your first service type to get started</div>
      <button type="button" class="btn btn-sm"
              style="background:linear-gradient(135deg,#03c3ec,#028bb6);color:#fff;border:0;border-radius:8px;font-weight:600;"
              data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="bx bx-plus me-1"></i>Add Service Type
      </button>
    </div>
    @else
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th class="ps-4">#</th>
            <th>Service Name</th>
            <th>Description</th>
            <th class="text-end">Base Charge</th>
            <th class="text-center">Status</th>
            <th class="text-end pe-4">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($serviceTypes as $i => $st)
          <tr class="{{ !$st->active ? 'opacity-60' : '' }}">
            <td class="ps-4 text-muted small">{{ $i + 1 }}</td>
            <td>
              <div class="st-name">{{ $st->name }}</div>
            </td>
            <td>
              <span class="text-muted small">{{ $st->description ?: '—' }}</span>
            </td>
            <td class="text-end">
              <span class="st-charge">Rs. {{ number_format($st->base_charge, 2) }}</span>
            </td>
            <td class="text-center">
              <form method="POST" action="{{ route('admin.service-types.toggle', $st) }}" class="d-inline">
                @csrf @method('PATCH')
                <button type="submit"
                        class="toggle-btn {{ $st->active ? 'bg-label-success text-success' : 'bg-label-secondary text-secondary' }}">
                  <i class="bx {{ $st->active ? 'bx-check' : 'bx-x' }} me-1"></i>
                  {{ $st->active ? 'Active' : 'Inactive' }}
                </button>
              </form>
            </td>
            <td class="text-end pe-4">
              <button type="button"
                      class="action-btn btn btn-sm btn-outline-primary me-1"
                      title="Edit"
                      onclick="openEdit({{ $st->id }}, '{{ addslashes($st->name) }}', {{ $st->base_charge }}, '{{ addslashes($st->description ?? '') }}', '{{ addslashes(json_encode($st->milestones ?? [])) }}')">
                <i class="bx bx-edit-alt"></i>
              </button>
              <form method="POST" action="{{ route('admin.service-types.destroy', $st) }}"
                    class="d-inline"
                    onsubmit="return confirm('Delete \'{{ addslashes($st->name) }}\'? This cannot be undone.')">
                @csrf @method('DELETE')
                <button type="submit" class="action-btn btn btn-sm btn-outline-danger" title="Delete">
                  <i class="bx bx-trash"></i>
                </button>
              </form>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @endif
  </div>
</div>

{{-- ═══ ADD MODAL ═══ --}}
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addModalLabel">
          <i class="bx bx-plus-circle me-2 text-info"></i>Add Service Type
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="{{ route('admin.service-types.store') }}">
        @csrf
        <div class="modal-body">
          <div class="mb-4">
            <label class="modal-form-label">Service Name <span class="text-danger">*</span></label>
            <input type="text" name="name" required
                   class="form-control"
                   placeholder="e.g. AC Service, RO Repair, CCTV Install"
                   value="{{ old('name') }}">
          </div>
          <div class="mb-4">
            <label class="modal-form-label">Description</label>
            <input type="text" name="description"
                   class="form-control"
                   placeholder="Short optional description"
                   value="{{ old('description') }}">
          </div>
          <div class="mb-2">
            <label class="modal-form-label">Base Charge (Rs.) <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text fw-bold">Rs.</span>
              <input type="number" step="0.01" min="0" name="base_charge" required
                     class="form-control font-monospace"
                     placeholder="0.00"
                     value="{{ old('base_charge') }}">
            </div>
          </div>
          {{-- Milestone Template Editor --}}
          <div class="mt-4">
            <label class="modal-form-label d-flex justify-content-between align-items-center">
              Ticket Milestone Steps
              <button type="button" class="btn btn-xs btn-outline-primary" onclick="addAddMilestoneRow()" style="font-size:.75rem;padding:2px 8px;">+ Add Step</button>
            </label>
            <div id="addMilestoneList" class="d-flex flex-column gap-2 mt-2"></div>
            <input type="hidden" name="milestones" id="addMilestonesJson">
            <p class="text-muted mt-1" style="font-size:.75rem;">These steps will auto-create on every new ticket for this service type.</p>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" onclick="resetAddMilestones()">Cancel</button>
          <button type="submit" class="btn fw-bold"
                  style="background:linear-gradient(135deg,#03c3ec,#028bb6);color:#fff;border:0;border-radius:8px;"
                  onclick="serializeAddMilestones()">
            <i class="bx bx-check me-1"></i>Add Service Type
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ═══ EDIT MODAL ═══ --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">
          <i class="bx bx-edit me-2 text-info"></i>Edit Service Type
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editForm" method="POST">
        @csrf @method('PUT')
        <div class="modal-body">
          <div class="mb-4">
            <label class="modal-form-label">Service Name <span class="text-danger">*</span></label>
            <input type="text" id="editName" name="name" required class="form-control"
                   placeholder="Service name">
          </div>
          <div class="mb-4">
            <label class="modal-form-label">Description</label>
            <input type="text" id="editDescription" name="description" class="form-control"
                   placeholder="Short optional description">
          </div>
          <div class="mb-2">
            <label class="modal-form-label">Base Charge (Rs.)</label>
            <div class="input-group">
              <span class="input-group-text fw-bold">Rs.</span>
              <input type="number" step="0.01" min="0" id="editCharge" name="base_charge"
                     required class="form-control font-monospace">
            </div>
          </div>
          {{-- Milestone Template Editor --}}
          <div class="mt-4">
            <label class="modal-form-label d-flex justify-content-between align-items-center">
              Ticket Milestone Steps
              <button type="button" class="btn btn-xs btn-outline-primary" onclick="addMilestoneRow()" style="font-size:.75rem;padding:2px 8px;">+ Add Step</button>
            </label>
            <input type="hidden" name="milestones" id="editMilestonesJson">
            <div id="editMilestonesList" class="d-flex flex-column gap-2 mt-1"></div>
            <p class="text-muted" style="font-size:.75rem;margin-top:4px;">These steps auto-create when a new ticket uses this service type.</p>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn fw-bold" onclick="serializeMilestones()"
                  style="background:linear-gradient(135deg,#03c3ec,#028bb6);color:#fff;border:0;border-radius:8px;">
            <i class="bx bx-save me-1"></i>Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
let milestoneData = [];

function openEdit(id, name, charge, description, milestonesJson) {
  document.getElementById('editForm').action = `/admin/service-types/${id}`;
  document.getElementById('editName').value        = name;
  document.getElementById('editCharge').value      = charge;
  document.getElementById('editDescription').value = description;
  milestoneData = milestonesJson ? JSON.parse(milestonesJson) : [];
  renderMilestones();
  new bootstrap.Modal(document.getElementById('editModal')).show();
}

function renderMilestones() {
  const list = document.getElementById('editMilestonesList');
  list.innerHTML = '';
  milestoneData.forEach((m, i) => {
    const row = document.createElement('div');
    row.className = 'd-flex gap-2 align-items-center';
    row.innerHTML = `<span class="text-muted" style="font-size:.8rem;min-width:20px;">${i+1}.</span>
      <input type="text" class="form-control form-control-sm milestone-title" value="${m.title}" placeholder="Step title" oninput="updateMilestone(${i}, this.value)">
      <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeMilestone(${i})" style="padding:2px 8px;"><i class="bx bx-x"></i></button>`;
    list.appendChild(row);
  });
}

function addMilestoneRow() {
  milestoneData.push({ title: '' });
  renderMilestones();
  const inputs = document.querySelectorAll('.milestone-title');
  if (inputs.length) inputs[inputs.length - 1].focus();
}

function updateMilestone(i, val) { milestoneData[i].title = val; }
function removeMilestone(i) { milestoneData.splice(i, 1); renderMilestones(); }
function serializeMilestones() {
  document.getElementById('editMilestonesJson').value = JSON.stringify(milestoneData.filter(m => m.title.trim()));
}

// ── Add modal milestone editor ──
let addMilestoneData = [];
function addAddMilestoneRow() {
  addMilestoneData.push({ title: '' });
  renderAddMilestones();
  const inputs = document.querySelectorAll('.add-milestone-title');
  if (inputs.length) inputs[inputs.length - 1].focus();
}
function renderAddMilestones() {
  const list = document.getElementById('addMilestoneList');
  list.innerHTML = addMilestoneData.map((m, i) => `
    <div class="d-flex gap-2 align-items-center">
      <input type="text" class="form-control form-control-sm add-milestone-title" value="${m.title}"
             placeholder="Step title" oninput="updateAddMilestone(${i}, this.value)">
      <button type="button" class="btn btn-xs btn-outline-danger" onclick="removeAddMilestone(${i})"
              style="font-size:.75rem;padding:2px 8px;white-space:nowrap;">✕</button>
    </div>`).join('');
}
function updateAddMilestone(i, val) { addMilestoneData[i].title = val; }
function removeAddMilestone(i) { addMilestoneData.splice(i, 1); renderAddMilestones(); }
function serializeAddMilestones() {
  document.getElementById('addMilestonesJson').value = JSON.stringify(addMilestoneData.filter(m => m.title.trim()));
}
function resetAddMilestones() { addMilestoneData = []; renderAddMilestones(); }
// Reset add modal when it closes
document.addEventListener('DOMContentLoaded', function() {
  const addModal = document.getElementById('addModal');
  if (addModal) addModal.addEventListener('hidden.bs.modal', resetAddMilestones);
});
</script>
@endpush
@endsection
