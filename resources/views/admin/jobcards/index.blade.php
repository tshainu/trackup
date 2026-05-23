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
    <form method="GET" id="filterForm" class="filter-bar">
      <div class="row g-2 align-items-end">
        <div class="col-md-4">
          <label class="form-label small fw-semibold mb-1">Search</label>
          <div class="input-group input-group-sm">
            <span class="input-group-text bg-white"><i class='bx bx-search text-muted' id="searchIcon"></i></span>
            <input type="text" name="search" id="liveSearch" class="form-control"
              placeholder="Order no, name, phone, device…"
              value="{{ request('search') }}" autocomplete="off" />
            <button type="button" class="btn btn-outline-secondary" id="clearSearch"
              style="display:{{ request('search') ? 'flex' : 'none' }};align-items:center;padding:0 8px">
              <i class='bx bx-x'></i>
            </button>
          </div>
        </div>
        <div class="col-md-2">
          <label class="form-label small fw-semibold mb-1">Status</label>
          <select name="status" class="form-select form-select-sm" id="statusFilter">
            <option value="">All Status</option>
            @foreach(['Pending','In Progress','Completed','Not Completed'] as $s)
              <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label small fw-semibold mb-1">Device</label>
          <select name="device" class="form-select form-select-sm" id="deviceFilter">
            <option value="">All Devices</option>
            @foreach($devices as $d)
              <option value="{{ $d->device_name }}" {{ request('device') == $d->device_name ? 'selected' : '' }}>{{ $d->device_name }}</option>
            @endforeach
          </select>
        </div>
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

    {{-- live-search no-results row (hidden by default) --}}
    <div id="liveNoResults" class="text-center py-4 text-muted" style="display:none!important">
      <i class='bx bx-search-alt' style="font-size:2rem;display:block;margin-bottom:6px;opacity:.4"></i>
      No results match "<span id="liveNoResultsQ"></span>"
    </div>

    {{-- ── Table ── --}}
    <table class="table table-hover align-middle mb-0 w-100" style="font-size:.85rem; table-layout:fixed;">
      <colgroup>
        <col style="width:7%">   {{-- Order No --}}
        <col style="width:14%">  {{-- Customer --}}
        <col style="width:10%">  {{-- Phone --}}
        <col style="width:16%">  {{-- Job Info --}}
        <col style="width:8%">   {{-- Date --}}
        <col style="width:9%">   {{-- Amount --}}
        <col style="width:13%">  {{-- Assigned --}}
        <col style="width:11%">  {{-- Status --}}
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
        <tr class="job-row" data-search="{{ strtolower($job->order_no.' '.$job->customer_name.' '.$job->phone_no.' '.$job->device_name.' '.($job->device_brand ?? '').' '.($job->device_fault ?? '').' '.($job->serial_no ?? '').' '.$job->status) }}">
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
          <td style="white-space:nowrap;"><small>{{ $job->date ? $job->date->format('d.m.Y') : '—' }}</small></td>
          <td class="fw-semibold" style="white-space:nowrap;">Rs.{{ number_format($job->rupees, 0) }}</td>
          <td>
            @if($job->employee)
              <span class="badge bg-label-secondary text-truncate" style="max-width:100%;">{{ $job->employee->employee_name ?? $job->employee->name }}</span>
            @else
              <span class="text-muted small">—</span>
            @endif
          </td>
          <td>
            @php
              $short = ['Pending'=>'Pending','In Progress'=>'In Prog.','Completed'=>'Done','Not Completed'=>'Not Done'];
              $label = $short[$job->status] ?? ($job->status ?: 'Pending');
            @endphp
            <span class="badge {{ $b['cls'] }}" style="font-size:.70rem; white-space:nowrap; display:inline-block; width:68px; text-align:center; overflow:hidden; text-overflow:ellipsis; padding:4px 0;">{{ $label }}</span>
          </td>
          <td>
            <div class="d-flex justify-content-center gap-1">
              <button type="button" class="action-btn btn btn-outline-primary view-job-btn" title="View" data-id="{{ $job->id }}"><i class='bx bx-show'></i></button>
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

{{-- ══════════════════════════════════════════
     VIEW JOB ORDER MODAL
══════════════════════════════════════════ --}}
<div class="modal fade" id="jobViewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content" style="border:0;border-radius:16px;overflow:hidden">
      <div id="jvm-header" style="padding:20px 24px;color:#fff;background:linear-gradient(135deg,#696cff,#8c57ff)">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
          <div>
            <div style="font-size:.7rem;opacity:.75;text-transform:uppercase;letter-spacing:.1em;margin-bottom:2px">Job Order</div>
            <div id="jvm-order-no" style="font-size:1.4rem;font-weight:800;letter-spacing:1px">#—</div>
            <div id="jvm-cust-id" style="font-size:.78rem;opacity:.8;margin-top:2px"></div>
          </div>
          <div class="d-flex align-items-center gap-2">
            <span id="jvm-status-badge" class="badge" style="font-size:.85rem;padding:6px 14px"></span>
            <span id="jvm-priority-badge" style="border-radius:20px;padding:4px 12px;font-size:.75rem;font-weight:700;border:1px solid rgba(255,255,255,.4);background:rgba(255,255,255,.15)"></span>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
        </div>
      </div>
      <div class="modal-body p-4" id="jvm-body">
        <div class="text-center py-5">
          <div class="spinner-border text-primary" role="status"></div>
          <div class="mt-2 text-muted small">Loading…</div>
        </div>
      </div>
      <div class="modal-footer border-0" style="background:#f8f8ff;padding:14px 24px">
        <a id="jvm-edit-btn" href="#" class="btn btn-sm" style="background:linear-gradient(135deg,#696cff,#8c57ff);color:#fff;border-radius:9px;font-weight:600;padding:8px 22px">
          <i class='bx bx-edit me-1'></i>Edit
        </a>
        <a id="jvm-print-btn" href="#" target="_blank" class="btn btn-sm btn-outline-secondary" style="border-radius:9px;font-weight:600">
          <i class='bx bx-printer me-1'></i>View Full
        </a>
        <button type="button" class="btn btn-sm btn-outline-danger ms-auto" style="border-radius:9px" data-bs-dismiss="modal">
          <i class='bx bx-x me-1'></i>Close
        </button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
  .jvm-info-row { display:flex; padding:7px 0; border-bottom:1px solid #f0f0f8; }
  .jvm-info-row:last-child { border-bottom:none; }
  .jvm-label { width:38%; font-size:.78rem; font-weight:700; color:#999; text-transform:uppercase; letter-spacing:.04em; padding-right:8px; }
  .jvm-value { flex:1; font-size:.87rem; color:#333; font-weight:500; word-break:break-word; }
  .jvm-section-head {
    font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em;
    color:#696cff; margin-bottom:10px; padding-bottom:8px;
    border-bottom:2px solid #ebebff; display:flex; align-items:center; gap:7px;
  }
</style>
@endpush

@push('scripts')
<script>
/* ── Live Search ── */
(function () {
  const input      = document.getElementById('liveSearch');
  const clearBtn   = document.getElementById('clearSearch');
  const icon       = document.getElementById('searchIcon');
  const rows       = document.querySelectorAll('.job-row');
  const noResults  = document.getElementById('liveNoResults');
  const noResultsQ = document.getElementById('liveNoResultsQ');
  const tbody      = document.querySelector('table tbody');
  let debounceTimer;

  function runFilter() {
    const q = input.value.trim().toLowerCase();

    // Show/hide clear button
    clearBtn.style.display = q ? 'flex' : 'none';

    // If empty, show all and reset
    if (!q) {
      rows.forEach(r => r.style.display = '');
      noResults.style.setProperty('display', 'none', 'important');
      icon.className = 'bx bx-search text-muted';
      return;
    }

    icon.className = 'bx bx-loader-alt text-primary bx-spin';

    let visible = 0;
    rows.forEach(row => {
      const hay = row.dataset.search || '';
      const match = q.split(' ').every(word => hay.includes(word));
      row.style.display = match ? '' : 'none';
      if (match) visible++;
    });

    icon.className = 'bx bx-search text-muted';

    if (visible === 0) {
      noResultsQ.textContent = input.value.trim();
      noResults.style.setProperty('display', 'block', 'important');
    } else {
      noResults.style.setProperty('display', 'none', 'important');
    }
  }

  input.addEventListener('input', function () {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(runFilter, 120);
  });

  clearBtn.addEventListener('click', function () {
    input.value = '';
    runFilter();
    input.focus();
  });

  // Run on load in case search is pre-filled from server
  if (input.value.trim()) runFilter();
})();

const jobShowBaseUrl = '{{ url("admin/jobcards") }}';
const jobEditBaseUrl = '{{ url("admin/jobcards") }}';

const statusColors = {
  'Pending':       '#ffab00',
  'In Progress':   '#03c3ec',
  'Completed':     '#71dd37',
  'Not Completed': '#ff3e1d',
};
const statusBadgeClass = {
  'Pending':       'bg-label-warning',
  'In Progress':   'bg-label-info',
  'Completed':     'bg-label-success',
  'Not Completed': 'bg-label-danger',
};
const headerGradients = {
  'Low':    'linear-gradient(135deg,#2d6a09,#71dd37)',
  'Normal': 'linear-gradient(135deg,#696cff,#8c57ff 60%,#a855f7)',
  'High':   'linear-gradient(135deg,#7a4800,#ffab00)',
  'Urgent': 'linear-gradient(135deg,#8a0000,#ff3e1d 60%,#ff7043)',
};

function fmt(v) { return (v !== null && v !== undefined && v !== '') ? v : '—'; }
function fmtDate(d) {
  if (!d) return '—';
  const s = d.split('T')[0];
  const parts = s.split('-');
  if (parts.length === 3) {
    const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    return `${parseInt(parts[2])} ${months[parseInt(parts[1])-1]} ${parts[0]}`;
  }
  return d;
}

document.querySelectorAll('.view-job-btn').forEach(btn => {
  btn.addEventListener('click', function () { openJobModal(this.dataset.id); });
});

function openJobModal(id) {
  const modalEl = document.getElementById('jobViewModal');
  const modal   = bootstrap.Modal.getOrCreateInstance(modalEl);

  // Reset
  document.getElementById('jvm-order-no').textContent     = '#—';
  document.getElementById('jvm-cust-id').textContent       = '';
  document.getElementById('jvm-status-badge').className    = 'badge';
  document.getElementById('jvm-status-badge').textContent  = '';
  document.getElementById('jvm-priority-badge').textContent = '';
  document.getElementById('jvm-header').style.background   = 'linear-gradient(135deg,#696cff,#8c57ff)';
  document.getElementById('jvm-body').innerHTML = `
    <div class="text-center py-5">
      <div class="spinner-border text-primary" role="status"></div>
      <div class="mt-2 text-muted small">Loading…</div>
    </div>`;

  modal.show();

  fetch(`${jobShowBaseUrl}/${id}`, {
    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
  })
  .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
  .then(j => {
    const priority = j.priority || 'Normal';
    const status   = j.status   || 'Pending';
    const sc       = statusBadgeClass[status] || 'bg-secondary';
    const grad     = headerGradients[priority] || headerGradients['Normal'];

    document.getElementById('jvm-header').style.background = grad;
    document.getElementById('jvm-order-no').textContent    = '# ' + j.order_no;
    document.getElementById('jvm-cust-id').textContent     = j.customer_id || '';

    const sb = document.getElementById('jvm-status-badge');
    sb.className   = 'badge ' + sc;
    sb.textContent = status;

    const pb = document.getElementById('jvm-priority-badge');
    pb.innerHTML = `<span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:rgba(255,255,255,.9);margin-right:5px"></span>${priority} Priority`;

    document.getElementById('jvm-edit-btn').href  = `${jobEditBaseUrl}/${j.id}/edit`;
    document.getElementById('jvm-print-btn').href = `${jobShowBaseUrl}/${j.id}`;

    const empName    = j.employee ? (j.employee.employee_name || j.employee.name || '—') : '—';
    const accessories = fmt(j.accessories);
    const needAss    = j.need_assistant
      ? `<span class="badge bg-label-warning">Yes</span>`
      : `<span class="text-muted">No</span>`;
    const amount = (j.rupees != null)
      ? 'Rs. ' + parseFloat(j.rupees).toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})
      : '—';

    document.getElementById('jvm-body').innerHTML = `
      <div class="row g-3">
        <div class="col-md-6">
          <div class="jvm-section-head"><i class='bx bx-user'></i> Customer</div>
          <div class="jvm-info-row"><div class="jvm-label">Name</div><div class="jvm-value">${fmt(j.customer_name)}</div></div>
          <div class="jvm-info-row"><div class="jvm-label">Phone</div><div class="jvm-value">${fmt(j.phone_no)}</div></div>
          <div class="jvm-info-row"><div class="jvm-label">NIC</div><div class="jvm-value">${fmt(j.customer_nic)}</div></div>
          <div class="jvm-info-row"><div class="jvm-label">Email</div><div class="jvm-value">${fmt(j.customer_email)}</div></div>
          <div class="jvm-info-row"><div class="jvm-label">Address</div><div class="jvm-value">${fmt(j.customer_address)}</div></div>
          <div class="jvm-info-row"><div class="jvm-label">Date of Birth</div><div class="jvm-value">${fmt(j.customer_dob)}</div></div>
          <div class="jvm-info-row"><div class="jvm-label">Received</div><div class="jvm-value">${fmtDate(j.date)}</div></div>
          <div class="jvm-info-row"><div class="jvm-label">Est. Delivery</div><div class="jvm-value">${fmtDate(j.estimated_delivery)}</div></div>
        </div>
        <div class="col-md-6">
          <div class="jvm-section-head"><i class='bx bx-chip'></i> Device & Repair</div>
          <div class="jvm-info-row"><div class="jvm-label">Device</div><div class="jvm-value">${fmt(j.device_name)}</div></div>
          <div class="jvm-info-row"><div class="jvm-label">Brand</div><div class="jvm-value">${fmt(j.device_brand)}</div></div>
          <div class="jvm-info-row"><div class="jvm-label">Serial / IMEI</div><div class="jvm-value">${fmt(j.serial_no)}</div></div>
          <div class="jvm-info-row"><div class="jvm-label">Device Age</div><div class="jvm-value">${j.device_age ? j.device_age + ' yrs' : '—'}</div></div>
          <div class="jvm-info-row"><div class="jvm-label">Fault</div><div class="jvm-value">${fmt(j.device_fault)}</div></div>
          <div class="jvm-info-row"><div class="jvm-label">Issue</div><div class="jvm-value">${fmt(j.issue)}</div></div>
          <div class="jvm-info-row"><div class="jvm-label">Amount</div><div class="jvm-value"><strong style="color:#696cff">${amount}</strong></div></div>
          <div class="jvm-info-row"><div class="jvm-label">Assigned To</div><div class="jvm-value">${empName}</div></div>
          <div class="jvm-info-row"><div class="jvm-label">Accessories</div><div class="jvm-value">${accessories}</div></div>
          <div class="jvm-info-row"><div class="jvm-label">Need Assistant</div><div class="jvm-value">${needAss}</div></div>
          <div class="jvm-info-row"><div class="jvm-label">Remark</div><div class="jvm-value">${fmt(j.remark)}</div></div>
        </div>
      </div>`;
  })
  .catch(err => {
    document.getElementById('jvm-body').innerHTML = `<div class="alert alert-danger m-3">Failed to load job order. Please try again.</div>`;
    console.error(err);
  });
}
</script>
@endpush
