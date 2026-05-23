@extends('layouts.admin')
@section('title', 'Employees')
@section('page-title', 'Team')
@section('breadcrumb')<li class="breadcrumb-item active">Employees</li>@endsection

@push('styles')
<style>
.emp-page-header {
  background: linear-gradient(135deg,#696cff 0%,#8c57ff 60%,#a855f7 100%);
  border-radius: 14px;
  padding: 24px 28px;
  color: #fff;
  display: flex; align-items: center; justify-content: space-between;
  flex-wrap: wrap; gap: 12px;
  margin-bottom: 24px;
}
.emp-page-header h4 { margin:0; font-weight:700; font-size:1.3rem; }
.emp-page-header p  { margin:0; opacity:.8; font-size:.85rem; }
.emp-stat-pills { display:flex; gap:12px; flex-wrap:wrap; }
.emp-stat-pill  {
  background: rgba(255,255,255,.18);
  border: 1px solid rgba(255,255,255,.3);
  border-radius: 20px; padding: 5px 16px;
  font-size: .82rem; font-weight:600;
}

/* Search bar */
.emp-toolbar { display:flex; gap:12px; align-items:center; flex-wrap:wrap; margin-bottom:20px; }
.emp-search-wrap { position:relative; flex:1; min-width:200px; }
.emp-search-wrap .form-control { padding-left: 38px; border-radius:10px; }
.emp-search-wrap .bx { position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#aaa; font-size:1.1rem; }
.emp-filter-btn {
  border-radius:10px; border:1.5px solid #e0e0e0; background:#fff;
  color:#555; padding:7px 18px; font-size:.85rem; font-weight:600;
  cursor:pointer; transition:.15s;
}
.emp-filter-btn.active { border-color:#696cff; background:#f0f0ff; color:#696cff; }

/* Card grid */
.emp-grid { display:grid; grid-template-columns: repeat(auto-fill, minmax(230px,1fr)); gap:18px; }

/* Employee Card */
.emp-card {
  background:#fff;
  border-radius:16px;
  box-shadow: 0 2px 16px rgba(108,92,231,.09);
  overflow: hidden;
  transition: transform .18s, box-shadow .18s;
  border: 1.5px solid #f0f0ff;
  position: relative;
}
.emp-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 28px rgba(108,92,231,.16);
}
.emp-card-top {
  background: linear-gradient(135deg,#696cff11,#8c57ff11);
  padding: 22px 20px 14px;
  text-align: center;
  border-bottom: 1px solid #f0f0ff;
}
.emp-avatar-wrap {
  position: relative; display: inline-block; margin-bottom: 10px;
}
.emp-avatar {
  width: 72px; height: 72px;
  border-radius: 50%;
  object-fit: cover;
  border: 3px solid #fff;
  box-shadow: 0 3px 14px rgba(108,92,231,.22);
}
.emp-avatar-placeholder {
  width: 72px; height: 72px;
  border-radius: 50%;
  background: linear-gradient(135deg,#696cff,#8c57ff);
  display: flex; align-items: center; justify-content: center;
  font-size: 1.6rem; font-weight: 700; color: #fff;
  border: 3px solid #fff;
  box-shadow: 0 3px 14px rgba(108,92,231,.22);
}
.emp-status-dot {
  position: absolute; bottom: 4px; right: 4px;
  width: 14px; height: 14px;
  border-radius: 50%; border: 2.5px solid #fff;
}
.emp-status-dot.active   { background: #28a745; }
.emp-status-dot.inactive { background: #dc3545; }
.emp-name { font-weight: 700; font-size: .97rem; color: #2d2d3a; margin:0 0 3px; }
.emp-role-badge {
  display: inline-block;
  padding: 2px 12px; border-radius:20px;
  font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing:.05em;
}
.role-technician  { background:#e8f5e9; color:#2e7d32; }
.role-helper      { background:#e3f2fd; color:#1565c0; }
.role-supervisor  { background:#fff3e0; color:#e65100; }
.role-other       { background:#f3e5f5; color:#6a1b9a; }

.emp-card-body { padding: 14px 18px; }
.emp-info-row { display:flex; align-items:center; gap:8px; font-size:.81rem; color:#666; margin-bottom:6px; }
.emp-info-row .bx { font-size:.98rem; color:#696cff; flex-shrink:0; }
.emp-info-row span { white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }

.emp-card-actions {
  display: flex; gap: 8px; padding: 12px 18px;
  border-top: 1px solid #f5f5ff;
}
.emp-card-actions .btn { flex:1; font-size:.78rem; font-weight:600; border-radius:8px; padding:5px 0; }

/* Empty state */
.emp-empty {
  text-align:center; padding: 60px 20px; color:#aaa;
}
.emp-empty .bx { font-size: 3.5rem; display:block; margin-bottom:12px; color:#ddd; }

/* Status toggle badge in header */
.status-badge {
  display:inline-flex; align-items:center; gap:5px;
  font-size:.75rem; font-weight:600; padding:3px 10px;
  border-radius:20px;
}
.status-badge.active   { background:#e8f5e9; color:#2e7d32; }
.status-badge.inactive { background:#fdecea; color:#c62828; }
</style>
@endpush

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
  <i class='bx bx-check-circle me-1'></i>{{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Page Header --}}
<div class="emp-page-header">
  <div>
    <h4><i class='bx bx-group me-2'></i>Team Members</h4>
    <p>Manage your workshop staff and technicians</p>
  </div>
  <div class="d-flex align-items-center gap-3 flex-wrap">
    <div class="emp-stat-pills">
      <span class="emp-stat-pill"><i class='bx bx-user-check me-1'></i>{{ $employees->where('status','active')->count() }} Active</span>
      <span class="emp-stat-pill"><i class='bx bx-group me-1'></i>{{ $employees->total() }} Total</span>
    </div>
    <a href="{{ route('admin.employees.create') }}" class="btn btn-light fw-bold" style="border-radius:10px;">
      <i class='bx bx-user-plus me-1'></i>Add Employee
    </a>
  </div>
</div>

{{-- Toolbar --}}
<div class="emp-toolbar">
  <div class="emp-search-wrap">
    <i class='bx bx-search'></i>
    <input type="text" id="empSearch" class="form-control" placeholder="Search by name, phone, role..." />
  </div>
  <button class="emp-filter-btn active" data-filter="all">All</button>
  <button class="emp-filter-btn" data-filter="active">Active</button>
  <button class="emp-filter-btn" data-filter="inactive">Inactive</button>
</div>

{{-- Grid --}}
<div class="emp-grid" id="empGrid">
  @forelse($employees as $emp)
  @php
    $initials = strtoupper(substr($emp->employee_name, 0, 1));
    $roleClass = match(strtolower($emp->role)) {
      'technician' => 'role-technician',
      'helper'     => 'role-helper',
      'supervisor' => 'role-supervisor',
      default      => 'role-other',
    };
  @endphp
  <div class="emp-card"
       data-name="{{ strtolower($emp->employee_name) }}"
       data-phone="{{ $emp->phone_no_1 }}"
       data-role="{{ strtolower($emp->role) }}"
       data-status="{{ $emp->status }}">

    <div class="emp-card-top">
      <div class="emp-avatar-wrap">
        @if($emp->photo)
          <img src="{{ asset('storage/'.$emp->photo) }}" alt="{{ $emp->employee_name }}" class="emp-avatar" />
        @else
          <div class="emp-avatar-placeholder">{{ $initials }}</div>
        @endif
        <span class="emp-status-dot {{ $emp->status }}"></span>
      </div>
      <div class="emp-name">{{ $emp->employee_name }}</div>
      <span class="emp-role-badge {{ $roleClass }}">{{ ucfirst($emp->role) }}</span>
    </div>

    <div class="emp-card-body">
      <div class="emp-info-row">
        <i class='bx bx-id-card'></i>
        <span>{{ $emp->user_id }}</span>
      </div>
      @if($emp->phone_no_1)
      <div class="emp-info-row">
        <i class='bx bx-phone'></i>
        <span>{{ $emp->phone_no_1 }}</span>
      </div>
      @endif
      @if($emp->email)
      <div class="emp-info-row">
        <i class='bx bx-envelope'></i>
        <span>{{ $emp->email }}</span>
      </div>
      @endif
      <div class="emp-info-row">
        <i class='bx bx-user-circle'></i>
        <span><code style="font-size:.78rem">{{ $emp->user_name }}</code></span>
      </div>
      <div class="mt-2">
        <span class="status-badge {{ $emp->status }}">
          <i class='bx {{ $emp->status === "active" ? "bx-check-circle" : "bx-x-circle" }}'></i>
          {{ ucfirst($emp->status) }}
        </span>
      </div>
    </div>

    <div class="emp-card-actions">
      <a href="{{ route('admin.employees.edit', $emp) }}" class="btn btn-outline-primary">
        <i class='bx bx-edit me-1'></i>Edit
      </a>
      <form action="{{ route('admin.employees.destroy', $emp) }}" method="POST"
            onsubmit="return confirm('Remove {{ $emp->employee_name }}?')">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-outline-danger" style="border-radius:8px;font-size:.78rem;font-weight:600;padding:5px 14px;">
          <i class='bx bx-trash'></i>
        </button>
      </form>
    </div>

  </div>
  @empty
  <div class="emp-empty" style="grid-column:1/-1">
    <i class='bx bx-user-x'></i>
    <div class="fw-semibold mb-1">No employees yet</div>
    <a href="{{ route('admin.employees.create') }}" class="btn btn-primary btn-sm mt-2">Add First Employee</a>
  </div>
  @endforelse
</div>

{{-- Pagination --}}
<div class="mt-4">{{ $employees->links() }}</div>

@endsection

@push('scripts')
<script>
// Search
const search = document.getElementById('empSearch');
const cards  = document.querySelectorAll('.emp-card');
let activeFilter = 'all';

function applyFilters() {
  const q = search.value.toLowerCase().trim();
  cards.forEach(c => {
    const matchSearch = !q ||
      c.dataset.name.includes(q) ||
      (c.dataset.phone||'').includes(q) ||
      c.dataset.role.includes(q);
    const matchFilter = activeFilter === 'all' || c.dataset.status === activeFilter;
    c.style.display = (matchSearch && matchFilter) ? '' : 'none';
  });
}

search.addEventListener('input', applyFilters);

document.querySelectorAll('.emp-filter-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    document.querySelectorAll('.emp-filter-btn').forEach(b => b.classList.remove('active'));
    this.classList.add('active');
    activeFilter = this.dataset.filter;
    applyFilters();
  });
});
</script>
@endpush
