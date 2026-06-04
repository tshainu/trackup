@extends('layouts.admin')

@section('title', 'Notifications')
@section('breadcrumb', 'Notifications')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h4 class="fw-bold mb-1">Notifications</h4>
    <p class="text-muted mb-0" style="font-size:.85rem;">All active alerts requiring attention</p>
  </div>
  @php $total = $dueToday->count() + $needAssistant->count() + $unpaidCompleted->count() + ($fieldCompleted?->count() ?? 0); @endphp
  @if($total > 0)
    <span class="badge bg-danger" style="font-size:.85rem;">{{ $total }} Active</span>
  @else
    <span class="badge bg-success" style="font-size:.85rem;">All clear</span>
  @endif
</div>

@if($total === 0)
  <div class="card">
    <div class="card-body text-center py-5">
      <i class="bx bx-check-circle text-success" style="font-size:3.5rem;"></i>
      <h5 class="mt-3 mb-1">You're all caught up!</h5>
      <p class="text-muted">No alerts requiring attention right now.</p>
    </div>
  </div>
@endif

{{-- ── Due Today ── --}}
@if($dueToday->count() > 0)
<div class="card mb-4">
  <div class="card-header d-flex align-items-center gap-2 py-3">
    <div style="width:32px;height:32px;background:#fff3cd;border-radius:50%;display:flex;align-items:center;justify-content:center;">
      <i class="bx bx-time-five text-warning" style="font-size:1.1rem;"></i>
    </div>
    <div>
      <h6 class="mb-0 fw-bold">Due Today</h6>
      <small class="text-muted">Devices scheduled for delivery today that are not yet complete</small>
    </div>
    <span class="badge bg-warning text-dark ms-auto">{{ $dueToday->count() }}</span>
  </div>
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead class="table-light">
        <tr>
          <th>Order No</th>
          <th>Device</th>
          <th>Customer</th>
          <th>Employee</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        @foreach($dueToday as $job)
        <tr>
          <td><span class="fw-semibold">#{{ $job->order_no }}</span></td>
          <td>{{ $job->device_name }} @if($job->device_brand)<small class="text-muted">({{ $job->device_brand }})</small>@endif</td>
          <td>{{ $job->customer_name }}<br><small class="text-muted">{{ $job->phone_no }}</small></td>
          <td>{{ $job->employee?->employee_name ?? '—' }}</td>
          <td>
            @php
              $sc = ['Pending'=>'warning','In Progress'=>'info','Completed'=>'success','Not Completed'=>'danger'];
              $badge = $sc[$job->status] ?? 'secondary';
            @endphp
            <span class="badge bg-label-{{ $badge }}">{{ $job->status }}</span>
          </td>
          <td>
            <a href="{{ route('admin.jobcards.edit', $job->id) }}" class="btn btn-sm btn-primary">
              <i class="bx bx-edit-alt me-1"></i> Update
            </a>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif

{{-- ── Need Assistant ── --}}
@if($needAssistant->count() > 0)
<div class="card mb-4">
  <div class="card-header d-flex align-items-center gap-2 py-3">
    <div style="width:32px;height:32px;background:#fde8e4;border-radius:50%;display:flex;align-items:center;justify-content:center;">
      <i class="bx bx-help-circle" style="color:#ff3e1d;font-size:1.1rem;"></i>
    </div>
    <div>
      <h6 class="mb-0 fw-bold">Needs Assistance</h6>
      <small class="text-muted">Staff have flagged these jobs as needing help</small>
    </div>
    <span class="badge bg-danger ms-auto">{{ $needAssistant->count() }}</span>
  </div>
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead class="table-light">
        <tr>
          <th>Order No</th>
          <th>Device</th>
          <th>Customer</th>
          <th>Employee</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($needAssistant as $job)
        <tr>
          <td><span class="fw-semibold">#{{ $job->order_no }}</span></td>
          <td>{{ $job->device_name }} @if($job->device_brand)<small class="text-muted">({{ $job->device_brand }})</small>@endif</td>
          <td>{{ $job->customer_name }}<br><small class="text-muted">{{ $job->phone_no }}</small></td>
          <td>{{ $job->employee?->employee_name ?? '—' }}</td>
          <td>
            @php $badge = $sc[$job->status] ?? 'secondary'; @endphp
            <span class="badge bg-label-{{ $badge }}">{{ $job->status }}</span>
          </td>
          <td class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.jobcards.edit', $job->id) }}" class="btn btn-sm btn-primary">
              <i class="bx bx-edit-alt me-1"></i> Edit
            </a>
            <form method="POST" action="{{ route('admin.notifications.dismiss-assistant', $job->id) }}">
              @csrf @method('PATCH')
              <button type="submit" class="btn btn-sm btn-outline-secondary">
                <i class="bx bx-x me-1"></i> Dismiss
              </button>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif

{{-- ── Unpaid Completed ── --}}
@if($unpaidCompleted->count() > 0)
<div class="card mb-4">
  <div class="card-header d-flex align-items-center gap-2 py-3">
    <div style="width:32px;height:32px;background:#e3f9e5;border-radius:50%;display:flex;align-items:center;justify-content:center;">
      <i class="bx bx-money" style="color:#28a745;font-size:1.1rem;"></i>
    </div>
    <div>
      <h6 class="mb-0 fw-bold">Payment Pending</h6>
      <small class="text-muted">Completed jobs where payment has not been received</small>
    </div>
    <span class="badge bg-success ms-auto">{{ $unpaidCompleted->count() }}</span>
  </div>
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead class="table-light">
        <tr>
          <th>Order No</th>
          <th>Device</th>
          <th>Customer</th>
          <th>Amount</th>
          <th>Completed</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($unpaidCompleted as $job)
        <tr>
          <td><span class="fw-semibold">#{{ $job->order_no }}</span></td>
          <td>{{ $job->device_name }} @if($job->device_brand)<small class="text-muted">({{ $job->device_brand }})</small>@endif</td>
          <td>{{ $job->customer_name }}<br><small class="text-muted">{{ $job->phone_no }}</small></td>
          <td><span class="fw-semibold text-success">Rs. {{ number_format($job->rupees) }}</span></td>
          <td><small class="text-muted">{{ $job->updated_at?->diffForHumans() }}</small></td>
          <td class="d-flex gap-2 flex-wrap">
            <form method="POST" action="{{ route('admin.notifications.payment', $job->id) }}">
              @csrf @method('PATCH')
              <button type="submit" class="btn btn-sm btn-success">
                <i class="bx bx-check me-1"></i> Mark Paid
              </button>
            </form>
            <a href="{{ route('admin.jobcards.edit', $job->id) }}" class="btn btn-sm btn-outline-primary">
              <i class="bx bx-edit-alt me-1"></i> Edit
            </a>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif

{{-- ── Field Tickets Unpaid ── --}}
@if(isset($fieldCompleted) && $fieldCompleted->count() > 0)
<div class="card mb-4">
  <div class="card-header d-flex align-items-center gap-2 py-3">
    <div style="width:32px;height:32px;background:#fff3cd;border-radius:50%;display:flex;align-items:center;justify-content:center;">
      <i class="bx bx-map-pin" style="color:#d97706;font-size:1.1rem;"></i>
    </div>
    <div>
      <h6 class="mb-0 fw-bold">Field Services — Payment Pending</h6>
      <small class="text-muted">Completed field tickets awaiting payment</small>
    </div>
    <span class="badge ms-auto" style="background:#f59e0b;color:#fff;">{{ $fieldCompleted->count() }}</span>
  </div>
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead class="table-light">
        <tr><th>Ticket No</th><th>Customer</th><th>Service</th><th>Paid So Far</th><th>Action</th></tr>
      </thead>
      <tbody>
        @foreach($fieldCompleted as $fc)
        <tr>
          <td><span class="fw-semibold">{{ $fc->complaint_no }}</span></td>
          <td>{{ $fc->customer_name }}</td>
          <td>{{ $fc->service_type_name ?? '—' }}</td>
          <td>Rs. {{ number_format($fc->paid_amount, 2) }}</td>
          <td>
            <a href="{{ route('admin.field-complaints.show', $fc->id) }}" class="btn btn-sm btn-outline-warning" style="color:#d97706;">
              <i class='bx bx-show me-1'></i>View
            </a>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif

@endsection
