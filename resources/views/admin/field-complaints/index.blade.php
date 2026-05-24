@extends('layouts.admin')
@section('title', 'Field Complaints')
@section('page-title', 'Field Complaints')
@section('breadcrumb')
  <li class="breadcrumb-item active">Field Complaints</li>
@endsection

@push('styles')
<style>
.fc-header { background:linear-gradient(135deg,#f59e0b,#d97706); border-radius:14px; padding:22px 28px; color:#fff; margin-bottom:1.5rem; }
.fc-header h4 { margin:0;font-weight:700;font-size:1.3rem; }
.fc-header p  { margin:0;opacity:.85;font-size:.85rem; }
.tab-pills { display:flex;gap:6px;flex-wrap:wrap;margin-bottom:1.2rem; }
.tab-pill { padding:5px 14px;border-radius:20px;font-size:.78rem;font-weight:600;border:0;cursor:pointer;background:#f0f0f0;color:#555;text-decoration:none;transition:.15s; }
.tab-pill:hover,.tab-pill.active { background:#f59e0b;color:#fff; }
.tab-pill .cnt { background:rgba(0,0,0,.12);border-radius:10px;padding:1px 6px;margin-left:4px;font-size:.72rem; }
.fc-table th { font-size:.72rem;text-transform:uppercase;letter-spacing:.05em;color:#888;font-weight:600;border-top:0; }
.status-badge { display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:12px;font-size:.72rem;font-weight:600; }
.s-pending    { background:#fff3cd;color:#856404; }
.s-assigned   { background:#cfe2ff;color:#084298; }
.s-inprogress { background:#d1ecf1;color:#0c5460; }
.s-completed  { background:#d1e7dd;color:#0a5c36; }
.s-billed     { background:#e2d9f3;color:#5a2d82; }
.s-cancelled  { background:#f8d7da;color:#842029; }
.priority-dot { width:8px;height:8px;border-radius:50%;display:inline-block; }
.p-low    { background:#adb5bd; }
.p-normal { background:#0d6efd; }
.p-high   { background:#fd7e14; }
.p-urgent { background:#dc3545; }
.action-btn { padding:4px 10px;border-radius:8px;font-size:.75rem;font-weight:600;border:0;cursor:pointer;text-decoration:none; }
</style>
@endpush

@section('content')
<div class="fc-header d-flex justify-content-between align-items-center">
  <div>
    <h4><i class='bx bx-map-pin me-2'></i>Field Complaints</h4>
    <p>Manage on-site service requests &amp; field staff assignments</p>
  </div>
  <a href="{{ route('admin.field-complaints.create') }}" class="btn btn-light fw-bold" style="border-radius:10px;">
    <i class='bx bx-plus me-1'></i>New Complaint
  </a>
</div>

{{-- Tabs --}}
<div class="tab-pills">
  @php
    $tabs = [
      'all'        => ['All', $counts['all']],
      'pending'    => ['Pending', $counts['pending']],
      'assigned'   => ['Assigned', $counts['assigned']],
      'inprogress' => ['In Progress', $counts['inprogress']],
      'completed'  => ['Completed', $counts['completed']],
      'billed'     => ['Billed', $counts['billed']],
    ];
  @endphp
  @foreach($tabs as $key => [$label, $cnt])
    <a href="{{ route('admin.field-complaints.index', ['tab'=>$key, 'q'=>$search]) }}"
       class="tab-pill {{ $tab === $key ? 'active' : '' }}">
      {{ $label }}<span class="cnt">{{ $cnt }}</span>
    </a>
  @endforeach
</div>

{{-- Search --}}
<form method="GET" class="mb-3 d-flex gap-2" style="max-width:420px;">
  <input type="hidden" name="tab" value="{{ $tab }}">
  <input type="text" name="q" class="form-control form-control-sm" placeholder="Search name, phone, complaint no…" value="{{ $search }}">
  <button class="btn btn-sm btn-outline-secondary">Search</button>
  @if($search)<a href="{{ route('admin.field-complaints.index', ['tab'=>$tab]) }}" class="btn btn-sm btn-outline-danger">✕</a>@endif
</form>

@if(session('success'))
  <div class="alert alert-success alert-dismissible">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="card" style="border-radius:14px;border:0;box-shadow:0 2px 16px rgba(0,0,0,.07);">
  <div class="table-responsive">
    <table class="table fc-table mb-0">
      <thead><tr>
        <th>No.</th>
        <th>Customer</th>
        <th>Service</th>
        <th>Priority</th>
        <th>Assigned To</th>
        <th>Scheduled</th>
        <th>Status</th>
        <th>Grand Total</th>
        <th>Balance</th>
        <th></th>
      </tr></thead>
      <tbody>
        @forelse($complaints as $fc)
        @php
          $fc->load('items');
          $statusClass = match($fc->status) {
            'Pending'     => 's-pending',
            'Assigned'    => 's-assigned',
            'In Progress' => 's-inprogress',
            'Completed'   => 's-completed',
            'Billed'      => 's-billed',
            'Cancelled'   => 's-cancelled',
            default       => ''
          };
          $prioClass = match($fc->priority) {
            'Low'    => 'p-low',
            'Normal' => 'p-normal',
            'High'   => 'p-high',
            'Urgent' => 'p-urgent',
            default  => 'p-normal'
          };
        @endphp
        <tr>
          <td><a href="{{ route('admin.field-complaints.show', $fc) }}" class="fw-bold text-decoration-none">{{ $fc->complaint_no }}</a></td>
          <td>
            <div class="fw-semibold">{{ $fc->customer_name }}</div>
            <small class="text-muted">{{ $fc->phone_no }}</small>
          </td>
          <td>{{ $fc->service_type_name ?? '—' }}</td>
          <td><span class="priority-dot {{ $prioClass }}"></span> {{ $fc->priority }}</td>
          <td>{{ $fc->assignedEmployee?->employee_name ?? '<span class="text-muted">Unassigned</span>' }}</td>
          <td>{{ $fc->scheduled_date?->format('d M Y') ?? '—' }}</td>
          <td><span class="status-badge {{ $statusClass }}">{{ $fc->status }}</span></td>
          <td>Rs. {{ number_format($fc->grand_total,2) }}</td>
          <td>
            @if($fc->balance > 0)
              <span class="text-danger fw-semibold">Rs. {{ number_format($fc->balance,2) }}</span>
            @else
              <span class="text-success fw-semibold">Paid</span>
            @endif
          </td>
          <td>
            <a href="{{ route('admin.field-complaints.show', $fc) }}" class="action-btn btn btn-sm btn-outline-primary">View</a>
          </td>
        </tr>
        @empty
        <tr><td colspan="10" class="text-center text-muted py-5">No complaints found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($complaints->hasPages())
  <div class="card-footer bg-transparent border-top-0 pt-0 pb-3 px-3">
    {{ $complaints->appends(['tab'=>$tab,'q'=>$search])->links() }}
  </div>
  @endif
</div>
@endsection
