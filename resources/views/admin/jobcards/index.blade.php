@extends('layouts.admin')
@section('title', 'Job Orders')
@section('page-title', 'Job Orders')
@section('breadcrumb')<li class="breadcrumb-item active">Job Orders</li>@endsection

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center py-3">
    <span><i class='bx bx-list-ul me-1'></i> All Job Orders</span>
    <a href="{{ route('admin.jobcards.create') }}" class="btn btn-sm" style="background:#7c4dff;color:#fff">
      <i class='bx bx-plus'></i> New Job Order
    </a>
  </div>
  <div class="card-body">
    <!-- Filters -->
    <form method="GET" class="row g-2 mb-3">
      <div class="col-md-4">
        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search order no, customer, phone, serial..." value="{{ request('search') }}" />
      </div>
      <div class="col-md-2">
        <select name="status" class="form-select form-select-sm">
          <option value="">All Status</option>
          @foreach(['Pending','In Progress','Completed','Not Completed'] as $s)
            <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ $s }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <select name="device" class="form-select form-select-sm">
          <option value="">All Devices</option>
          @foreach($devices as $d)
            <option value="{{ $d->device_name }}" {{ request('device') == $d->device_name ? 'selected' : '' }}>{{ $d->device_name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <button type="submit" class="btn btn-sm btn-secondary w-100">Filter</button>
      </div>
      <div class="col-md-2">
        <a href="{{ route('admin.jobcards.index') }}" class="btn btn-sm btn-outline-secondary w-100">Clear</a>
      </div>
    </form>

    <div class="table-responsive">
      <table class="table table-hover" id="jobsTable">
        <thead>
          <tr>
            <th>Order No</th>
            <th>Customer</th>
            <th>Phone</th>
            <th>Device</th>
            <th>Fault</th>
            <th>Date</th>
            <th>Amount</th>
            <th>Assigned To</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($jobs as $job)
          <tr>
            <td><span class="fw-semibold text-primary">{{ $job->order_no }}</span></td>
            <td>
              <div class="fw-semibold">{{ $job->customer_name }}</div>
              <small class="text-muted">{{ $job->customer_id }}</small>
            </td>
            <td>{{ $job->phone_no }}</td>
            <td>{{ $job->device_name }}<br><small class="text-muted">{{ $job->device_brand }}</small></td>
            <td><small>{{ Str::limit($job->device_fault, 22) }}</small></td>
            <td><small>{{ $job->date ? $job->date->format('d M Y') : '' }}</small></td>
            <td>Rs.{{ number_format($job->rupees, 0) }}</td>
            <td>
              @if($job->employee)
                <span class="badge bg-light text-dark">{{ $job->employee->employee_name }}</span>
              @else
                <span class="text-muted small">Unassigned</span>
              @endif
            </td>
            <td>
              @php $sc = ['Pending'=>'bg-label-warning','In Progress'=>'bg-label-info','Completed'=>'bg-label-success','Not Completed'=>'bg-label-danger']; @endphp
              <span class="badge {{ $sc[$job->status] ?? 'bg-secondary' }}">{{ $job->status ?: 'Pending' }}</span>
            </td>
            <td>
              <div class="d-flex gap-1">
                <a href="{{ route('admin.jobcards.show', $job) }}" class="btn btn-sm btn-outline-primary py-0" title="View"><i class='bx bx-eye'></i></a>
                <a href="{{ route('admin.jobcards.edit', $job) }}" class="btn btn-sm btn-outline-secondary py-0" title="Edit"><i class='bx bx-edit'></i></a>
                <form action="{{ route('admin.jobcards.destroy', $job) }}" method="POST" onsubmit="return confirm('Delete this job order?')">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger py-0" title="Delete"><i class='bx bx-trash'></i></button>
                </form>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="mt-2">{{ $jobs->links() }}</div>
  </div>
</div>
@endsection
@push('scripts')
<script>
$('#jobsTable').DataTable({ paging: false, info: false, searching: false, order: [] });
</script>
@endpush
