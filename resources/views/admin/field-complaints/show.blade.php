@extends('layouts.admin')
@section('title', $fieldComplaint->complaint_no)
@section('page-title', $fieldComplaint->complaint_no)
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.field-complaints.index') }}">Field Complaints</a></li>
  <li class="breadcrumb-item active">{{ $fieldComplaint->complaint_no }}</li>
@endsection

@push('styles')
<style>
.fc-show-header { background:linear-gradient(135deg,#f59e0b,#d97706);border-radius:14px;padding:22px 28px;color:#fff;margin-bottom:1.5rem; }
.fc-show-header h4 { margin:0;font-weight:700; }
.fc-show-header .meta { opacity:.88;font-size:.85rem;margin-top:4px; }
.status-badge { display:inline-flex;align-items:center;gap:4px;padding:4px 12px;border-radius:12px;font-size:.78rem;font-weight:700; }
.s-pending    { background:#fff3cd;color:#856404; }
.s-assigned   { background:#cfe2ff;color:#084298; }
.s-inprogress { background:#d1ecf1;color:#0c5460; }
.s-completed  { background:#d1e7dd;color:#0a5c36; }
.s-billed     { background:#e2d9f3;color:#5a2d82; }
.s-cancelled  { background:#f8d7da;color:#842029; }
.info-card { border:0;border-radius:14px;box-shadow:0 2px 16px rgba(0,0,0,.07);margin-bottom:1.25rem; }
.info-card .card-header { background:#fafafa;border-bottom:1px solid #f0f0f0;border-radius:14px 14px 0 0;padding:14px 20px;font-weight:700;font-size:.85rem;text-transform:uppercase;letter-spacing:.05em;color:#666; }
.dl-row { display:flex;padding:8px 0;border-bottom:1px solid #f8f8f8;gap:12px; }
.dl-row:last-child { border-bottom:0; }
.dl-label { width:140px;flex-shrink:0;font-size:.8rem;color:#888;font-weight:600; }
.dl-value { font-size:.88rem;color:#333;font-weight:500; }
.priority-badge { padding:2px 10px;border-radius:10px;font-size:.72rem;font-weight:700; }
.p-low    { background:#e9ecef;color:#495057; }
.p-normal { background:#cfe2ff;color:#084298; }
.p-high   { background:#ffe5d0;color:#c35a00; }
.p-urgent { background:#f8d7da;color:#842029; }
.item-table th { font-size:.72rem;text-transform:uppercase;color:#888;font-weight:600;border-top:0; }
.total-row td { font-weight:700; }
.payment-log-item { padding:8px 0;border-bottom:1px solid #f5f5f5;display:flex;justify-content:space-between;align-items:center; }
.payment-log-item:last-child { border-bottom:0; }
.section-action-btn { font-size:.8rem;font-weight:600;border-radius:8px; }
</style>
@endpush

@section('content')

@if(session('success'))
  <div class="alert alert-success alert-dismissible mb-3">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
  <div class="alert alert-danger alert-dismissible mb-3">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

@php
  $statusClass = match($fieldComplaint->status) {
    'Pending'     => 's-pending',
    'Assigned'    => 's-assigned',
    'In Progress' => 's-inprogress',
    'Completed'   => 's-completed',
    'Billed'      => 's-billed',
    'Cancelled'   => 's-cancelled',
    default       => ''
  };
  $prioClass = match($fieldComplaint->priority) {
    'Low'    => 'p-low',
    'Normal' => 'p-normal',
    'High'   => 'p-high',
    'Urgent' => 'p-urgent',
    default  => 'p-normal'
  };
@endphp

<div class="fc-show-header d-flex justify-content-between align-items-start">
  <div>
    <h4><i class='bx bx-map-pin me-2'></i>{{ $fieldComplaint->complaint_no }}</h4>
    <div class="meta">
      Logged {{ $fieldComplaint->created_at->format('d M Y, h:i A') }} &nbsp;·&nbsp;
      <span class="status-badge {{ $statusClass }}">{{ $fieldComplaint->status }}</span>
      &nbsp;·&nbsp;
      <span class="priority-badge {{ $prioClass }}">{{ $fieldComplaint->priority }}</span>
    </div>
  </div>
  <div class="d-flex gap-2 flex-wrap">
    @if($fieldComplaint->status === 'Completed' || $fieldComplaint->status === 'Billed')
      <a href="{{ route('admin.field-complaints.invoice', $fieldComplaint) }}" target="_blank"
         class="btn btn-light fw-bold" style="border-radius:10px;">
        <i class='bx bx-receipt me-1'></i>Invoice
      </a>
    @endif
    <form action="{{ route('admin.field-complaints.destroy', $fieldComplaint) }}" method="POST"
      onsubmit="return confirm('Delete this complaint?')">
      @csrf @method('DELETE')
      <button type="submit" class="btn btn-outline-danger fw-bold" style="border-radius:10px;">
        <i class='bx bx-trash'></i>
      </button>
    </form>
  </div>
</div>

<div class="row g-4">
  <!-- LEFT column -->
  <div class="col-lg-7">

    {{-- Customer Info --}}
    <div class="card info-card">
      <div class="card-header">Customer Info</div>
      <div class="card-body px-4 py-3">
        <div class="dl-row"><span class="dl-label">Name</span><span class="dl-value">{{ $fieldComplaint->customer_name }}</span></div>
        <div class="dl-row"><span class="dl-label">Phone</span><span class="dl-value">{{ $fieldComplaint->phone_no }}</span></div>
        <div class="dl-row"><span class="dl-label">Address</span><span class="dl-value">{{ $fieldComplaint->address }}</span></div>
        @if($fieldComplaint->location_notes)
        <div class="dl-row"><span class="dl-label">Location Notes</span><span class="dl-value">{{ $fieldComplaint->location_notes }}</span></div>
        @endif
        @if($fieldComplaint->description)
        <div class="dl-row"><span class="dl-label">Problem</span><span class="dl-value">{{ $fieldComplaint->description }}</span></div>
        @endif
        @if($fieldComplaint->remark)
        <div class="dl-row"><span class="dl-label">Remark</span><span class="dl-value">{{ $fieldComplaint->remark }}</span></div>
        @endif
        @if($fieldComplaint->completion_notes)
        <div class="dl-row"><span class="dl-label">Completion Notes</span><span class="dl-value">{{ $fieldComplaint->completion_notes }}</span></div>
        @endif
      </div>
    </div>

    {{-- Service & Assignment --}}
    <div class="card info-card">
      <div class="card-header d-flex justify-content-between align-items-center">
        Service & Assignment
        <button class="btn btn-sm btn-outline-warning section-action-btn" data-bs-toggle="modal" data-bs-target="#assignModal">
          <i class='bx bx-user-check me-1'></i>{{ $fieldComplaint->assigned_to ? 'Reassign' : 'Assign' }}
        </button>
      </div>
      <div class="card-body px-4 py-3">
        <div class="dl-row"><span class="dl-label">Service Type</span><span class="dl-value">{{ $fieldComplaint->service_type_name ?? '—' }}</span></div>
        <div class="dl-row"><span class="dl-label">Scheduled</span><span class="dl-value">{{ $fieldComplaint->scheduled_date?->format('d M Y') ?? '—' }}</span></div>
        <div class="dl-row"><span class="dl-label">Assigned To</span><span class="dl-value">
          {{ $fieldComplaint->assignedEmployee?->employee_name ?? '—' }}
          @if($fieldComplaint->assigned_at)<small class="text-muted"> · {{ $fieldComplaint->assigned_at->format('d M, h:i A') }}</small>@endif
        </span></div>
        @if($fieldComplaint->completed_at)
        <div class="dl-row"><span class="dl-label">Completed At</span><span class="dl-value">{{ $fieldComplaint->completed_at->format('d M Y, h:i A') }}</span></div>
        @endif
      </div>
    </div>

    {{-- Edit Details --}}
    <div class="card info-card">
      <div class="card-header">Edit Details / Line Items</div>
      <div class="card-body p-4">
        <form action="{{ route('admin.field-complaints.update', $fieldComplaint) }}" method="POST">
          @csrf @method('PUT')
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold small">Customer Name</label>
              <input type="text" name="customer_name" class="form-control form-control-sm" value="{{ $fieldComplaint->customer_name }}" required />
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold small">Phone</label>
              <input type="text" name="phone_no" class="form-control form-control-sm" value="{{ $fieldComplaint->phone_no }}" required />
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold small">Address</label>
              <input type="text" name="address" class="form-control form-control-sm" value="{{ $fieldComplaint->address }}" required />
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold small">Location Notes</label>
              <input type="text" name="location_notes" class="form-control form-control-sm" value="{{ $fieldComplaint->location_notes }}" />
            </div>
            <div class="col-md-5">
              <label class="form-label fw-semibold small">Service Type</label>
              <select name="service_type_id" class="form-select form-select-sm" id="editServiceType">
                <option value="">— None —</option>
                @foreach($serviceTypes as $st)
                  <option value="{{ $st->id }}" data-charge="{{ $st->base_charge }}"
                    {{ $fieldComplaint->service_type_id == $st->id ? 'selected' : '' }}>
                    {{ $st->name }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold small">Priority</label>
              <select name="priority" class="form-select form-select-sm">
                @foreach(['Low','Normal','High','Urgent'] as $p)
                  <option value="{{ $p }}" {{ $fieldComplaint->priority == $p ? 'selected' : '' }}>{{ $p }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold small">Scheduled</label>
              <input type="date" name="scheduled_date" class="form-control form-control-sm"
                value="{{ $fieldComplaint->scheduled_date?->format('Y-m-d') }}" />
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold small">Service Charge (Rs.)</label>
              <input type="number" name="service_charge" class="form-control form-control-sm" id="editServiceCharge"
                value="{{ $fieldComplaint->service_charge }}" min="0" step="0.01" />
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold small">Discount (Rs.)</label>
              <input type="number" name="discount" class="form-control form-control-sm"
                value="{{ $fieldComplaint->discount }}" min="0" step="0.01" />
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold small">Description</label>
              <textarea name="description" class="form-control form-control-sm" rows="2">{{ $fieldComplaint->description }}</textarea>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold small">Remark</label>
              <input type="text" name="remark" class="form-control form-control-sm" value="{{ $fieldComplaint->remark }}" />
            </div>
          </div>

          {{-- Line Items --}}
          <div class="mt-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="fw-semibold small text-uppercase text-muted" style="letter-spacing:.05em;">Parts / Labour Items</span>
              <button type="button" class="btn btn-sm btn-outline-warning" id="addItemBtn" style="font-size:.75rem;border-radius:8px;">+ Add Item</button>
            </div>
            <table class="table table-sm" id="itemsTable">
              <thead><tr>
                <th style="font-size:.72rem;color:#888;">Description</th>
                <th style="font-size:.72rem;color:#888;width:60px;">Qty</th>
                <th style="font-size:.72rem;color:#888;width:100px;">Unit Price</th>
                <th style="font-size:.72rem;color:#888;width:80px;">Total</th>
                <th style="width:32px;"></th>
              </tr></thead>
              <tbody id="itemsBody">
                @foreach($fieldComplaint->items as $i => $item)
                <tr class="item-row">
                  <td><input type="text" name="items[{{ $i }}][description]" class="form-control form-control-sm" value="{{ $item->description }}" required /></td>
                  <td><input type="number" name="items[{{ $i }}][qty]" class="form-control form-control-sm item-qty" value="{{ $item->qty }}" min="1" required /></td>
                  <td><input type="number" name="items[{{ $i }}][unit_price]" class="form-control form-control-sm item-price" value="{{ $item->unit_price }}" min="0" step="0.01" required /></td>
                  <td><input type="text" class="form-control form-control-sm item-total bg-light" value="{{ number_format($item->total,2) }}" readonly /></td>
                  <td><button type="button" class="btn btn-sm btn-outline-danger remove-item" style="border-radius:6px;padding:2px 6px;">✕</button></td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          <div class="d-flex gap-2 mt-3">
            <button type="submit" class="btn btn-warning px-4 fw-bold" style="border-radius:10px;color:#fff;font-size:.85rem;">
              <i class='bx bx-save me-1'></i>Save Changes
            </button>
          </div>
        </form>
      </div>
    </div>

  </div>

  <!-- RIGHT column -->
  <div class="col-lg-5">

    {{-- Status Update --}}
    <div class="card info-card">
      <div class="card-header">Update Status</div>
      <div class="card-body p-4">
        <form action="{{ route('admin.field-complaints.status', $fieldComplaint) }}" method="POST">
          @csrf @method('PATCH')
          <div class="mb-3">
            <select name="status" class="form-select">
              @foreach(['Pending','Assigned','In Progress','Completed','Billed','Cancelled'] as $s)
                <option value="{{ $s }}" {{ $fieldComplaint->status === $s ? 'selected' : '' }}>{{ $s }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3" id="completionNotesWrap" style="{{ $fieldComplaint->status !== 'Completed' ? 'display:none' : '' }}">
            <label class="form-label fw-semibold small">Completion Notes</label>
            <textarea name="completion_notes" class="form-control form-control-sm" rows="2" placeholder="What was done…">{{ $fieldComplaint->completion_notes }}</textarea>
          </div>
          <button type="submit" class="btn btn-warning fw-bold w-100" style="border-radius:10px;color:#fff;">
            <i class='bx bx-refresh me-1'></i>Update Status
          </button>
        </form>
      </div>
    </div>

    {{-- Financials --}}
    <div class="card info-card">
      <div class="card-header">Financials</div>
      <div class="card-body px-4 py-3">
        <div class="dl-row"><span class="dl-label">Service Charge</span><span class="dl-value">Rs. {{ number_format($fieldComplaint->service_charge,2) }}</span></div>
        <div class="dl-row"><span class="dl-label">Items Total</span><span class="dl-value">Rs. {{ number_format($fieldComplaint->items->sum('total'),2) }}</span></div>
        @if($fieldComplaint->discount > 0)
        <div class="dl-row"><span class="dl-label">Discount</span><span class="dl-value text-danger">− Rs. {{ number_format($fieldComplaint->discount,2) }}</span></div>
        @endif
        <div class="dl-row"><span class="dl-label fw-bold">Grand Total</span><span class="dl-value fw-bold fs-5">Rs. {{ number_format($fieldComplaint->grand_total,2) }}</span></div>
        <div class="dl-row"><span class="dl-label">Paid</span><span class="dl-value text-success fw-semibold">Rs. {{ number_format($fieldComplaint->paid_amount,2) }}</span></div>
        <div class="dl-row"><span class="dl-label">Balance</span>
          <span class="dl-value fw-bold {{ $fieldComplaint->balance > 0 ? 'text-danger' : 'text-success' }}">
            {{ $fieldComplaint->balance > 0 ? 'Rs. '.number_format($fieldComplaint->balance,2) : 'Fully Paid ✓' }}
          </span>
        </div>
      </div>
    </div>

    {{-- Record Payment --}}
    @if($fieldComplaint->balance > 0)
    <div class="card info-card">
      <div class="card-header">Record Payment</div>
      <div class="card-body p-4">
        <form action="{{ route('admin.field-complaints.payment', $fieldComplaint) }}" method="POST">
          @csrf
          <div class="mb-3">
            <label class="form-label fw-semibold small">Amount Received (Rs.)</label>
            <div class="input-group input-group-sm">
              <span class="input-group-text">Rs.</span>
              <input type="number" name="amount_paid" class="form-control"
                value="{{ number_format($fieldComplaint->balance, 2, '.', '') }}"
                min="0.01" step="0.01" required />
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold small">Note</label>
            <input type="text" name="note" class="form-control form-control-sm" placeholder="e.g. Cash, Card, Instalment…" />
          </div>
          <button type="submit" class="btn btn-success fw-bold w-100" style="border-radius:10px;">
            <i class='bx bx-check-circle me-1'></i>Record Payment
          </button>
        </form>
      </div>
    </div>
    @endif

    {{-- Payment History --}}
    @if($fieldComplaint->paymentLogs->isNotEmpty())
    <div class="card info-card">
      <div class="card-header">Payment History</div>
      <div class="card-body px-4 py-2">
        @foreach($fieldComplaint->paymentLogs as $log)
        <div class="payment-log-item">
          <div>
            <div class="fw-semibold small">Rs. {{ number_format($log->amount,2) }}</div>
            <small class="text-muted">{{ $log->note }} · {{ \Carbon\Carbon::parse($log->paid_at)->format('d M Y') }}</small>
          </div>
          <span class="badge bg-success-subtle text-success">Paid</span>
        </div>
        @endforeach
      </div>
    </div>
    @endif

  </div>
</div>

{{-- Assign Modal --}}
<div class="modal fade" id="assignModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content" style="border-radius:14px;">
      <div class="modal-header" style="background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;border-radius:14px 14px 0 0;">
        <h5 class="modal-title fw-bold"><i class='bx bx-user-check me-2'></i>Assign Field Staff</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('admin.field-complaints.assign', $fieldComplaint) }}" method="POST">
        @csrf @method('PATCH')
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label fw-semibold">Select Field Staff</label>
            <select name="assigned_to" class="form-select" required>
              <option value="">— Choose staff member —</option>
              @foreach($fieldStaff as $emp)
                <option value="{{ $emp->id }}" {{ $fieldComplaint->assigned_to == $emp->id ? 'selected' : '' }}>
                  {{ $emp->employee_name }} ({{ ucfirst($emp->role) }})
                </option>
              @endforeach
            </select>
            @if($fieldStaff->isEmpty())
              <small class="text-warning mt-1 d-block">No outbound field staff found. Add employees with type "Outbound Field Staff".</small>
            @endif
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Scheduled Date</label>
            <input type="date" name="scheduled_date" class="form-control"
              value="{{ $fieldComplaint->scheduled_date?->format('Y-m-d') }}" />
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-warning fw-bold" style="color:#fff;">
            <i class='bx bx-check me-1'></i>Assign
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
// Status → show/hide completion notes
document.querySelector('[name="status"]')?.addEventListener('change', function () {
  document.getElementById('completionNotesWrap').style.display = this.value === 'Completed' ? '' : 'none';
});

// Line items
let rowIdx = {{ $fieldComplaint->items->count() }};

document.getElementById('addItemBtn')?.addEventListener('click', function () {
  const tbody = document.getElementById('itemsBody');
  const row = document.createElement('tr');
  row.className = 'item-row';
  row.innerHTML = `
    <td><input type="text" name="items[${rowIdx}][description]" class="form-control form-control-sm" required /></td>
    <td><input type="number" name="items[${rowIdx}][qty]" class="form-control form-control-sm item-qty" value="1" min="1" required /></td>
    <td><input type="number" name="items[${rowIdx}][unit_price]" class="form-control form-control-sm item-price" value="0" min="0" step="0.01" required /></td>
    <td><input type="text" class="form-control form-control-sm item-total bg-light" value="0.00" readonly /></td>
    <td><button type="button" class="btn btn-sm btn-outline-danger remove-item" style="border-radius:6px;padding:2px 6px;">✕</button></td>
  `;
  tbody.appendChild(row);
  rowIdx++;
  attachRowListeners(row);
});

function attachRowListeners(row) {
  row.querySelectorAll('.item-qty, .item-price').forEach(inp => {
    inp.addEventListener('input', () => {
      const qty   = parseFloat(row.querySelector('.item-qty').value)   || 0;
      const price = parseFloat(row.querySelector('.item-price').value) || 0;
      row.querySelector('.item-total').value = (qty * price).toFixed(2);
    });
  });
  row.querySelector('.remove-item')?.addEventListener('click', () => row.remove());
}

// Attach to existing rows
document.querySelectorAll('.item-row').forEach(attachRowListeners);

// Service type → update service charge
document.getElementById('editServiceType')?.addEventListener('change', function () {
  const charge = this.options[this.selectedIndex]?.dataset.charge;
  if (charge !== undefined) {
    document.getElementById('editServiceCharge').value = parseFloat(charge).toFixed(2);
  }
});
</script>
@endpush
