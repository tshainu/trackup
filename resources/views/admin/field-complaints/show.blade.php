@extends('layouts.admin')
@section('title', 'Complaint ' . $fieldComplaint->complaint_no)

@push('styles')
<style>
  .fc-show-hero {
    border-radius: 16px;
    padding: 1.25rem 1.75rem;
    color: #fff;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
    position: relative;
    overflow: hidden;
  }
  .fc-show-hero::after {
    content: '\ecd3';
    font-family: 'boxicons';
    position: absolute;
    right: -10px; top: -20px;
    font-size: 9rem;
    opacity: .07;
    pointer-events: none;
  }
  .fc-show-hero .back-btn {
    width: 38px; height: 38px;
    border-radius: 10px;
    background: rgba(255,255,255,.2);
    border: 0; color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem;
    flex-shrink: 0;
    text-decoration: none;
    transition: background .15s;
  }
  .fc-show-hero .back-btn:hover { background: rgba(255,255,255,.32); color: #fff; }

  .section-card {
    border-radius: 14px;
    border: 0;
    box-shadow: 0 2px 12px rgba(105,108,255,.08);
    margin-bottom: 1.25rem;
  }
  .section-card .card-header {
    border-radius: 14px 14px 0 0;
    padding: .85rem 1.25rem;
    display: flex;
    align-items: center;
    gap: .6rem;
    border-bottom: 1px solid rgba(0,0,0,.06);
    font-weight: 600;
  }
  .section-card .card-header .header-icon {
    width: 30px; height: 30px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: .95rem; flex-shrink: 0;
  }

  .info-label {
    font-size: .7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: #a1acb8;
    margin-bottom: .25rem;
  }
  .info-value { font-size: .92rem; font-weight: 500; color: #32325d; }

  .billing-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: .85rem;
    padding: .3rem 0;
  }
  .billing-row .label { color: #697a8d; }
  .billing-row.total  { font-size: 1rem; font-weight: 700; border-top: 2px solid #e0e0e0; margin-top: .25rem; padding-top: .5rem; }
  .billing-row.paid   .value { color: #28a745; }
  .billing-row.balance-due .value { color: #ff3e1d; }
  .billing-row.balance-ok  .value { color: #28a745; }

  .right-card {
    border-radius: 14px;
    border: 0;
    box-shadow: 0 2px 12px rgba(105,108,255,.08);
    margin-bottom: 1.25rem;
    overflow: hidden;
  }
  .right-card .card-header {
    padding: .85rem 1.25rem;
    font-weight: 600;
    font-size: .875rem;
    border-bottom: 1px solid rgba(0,0,0,.06);
    display: flex;
    align-items: center;
    gap: .5rem;
  }
  .right-card .card-body { padding: 1.25rem; }

  .status-timeline {
    display: flex;
    flex-direction: column;
    gap: 4px;
  }
  .status-option {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 12px;
    border-radius: 8px;
    cursor: pointer;
    transition: background .15s;
  }
  .status-option:hover { background: #f5f5ff; }
  .status-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
</style>
@endpush

@section('content')
@php
  $fc = $fieldComplaint;
  $statusBadge = [
    'Pending'    =>'warning','Assigned'=>'info','In Progress'=>'primary',
    'Completed'  =>'success','Billed'=>'purple','Cancelled'=>'danger',
  ][$fc->status] ?? 'secondary';
  $priBadge = ['Low'=>'secondary','Normal'=>'info','High'=>'warning','Urgent'=>'danger'][$fc->priority] ?? 'secondary';
  $heroBg = [
    'Pending'    => 'linear-gradient(135deg,#ffab00,#ff8c00)',
    'Assigned'   => 'linear-gradient(135deg,#03c3ec,#028bb6)',
    'In Progress'=> 'linear-gradient(135deg,#696cff,#8c57ff)',
    'Completed'  => 'linear-gradient(135deg,#28a745,#1e7e34)',
    'Billed'     => 'linear-gradient(135deg,#6f42c1,#a855f7)',
    'Cancelled'  => 'linear-gradient(135deg,#ff3e1d,#c82333)',
  ][$fc->status] ?? 'linear-gradient(135deg,#696cff,#8c57ff)';
@endphp

<div class="container-xxl flex-grow-1 container-p-y">

  @if(session('success'))
  <div class="alert alert-success alert-dismissible mb-4"><i class="bx bx-check-circle me-1"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  @endif
  @if(session('error'))
  <div class="alert alert-danger alert-dismissible mb-4"><i class="bx bx-x-circle me-1"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  @endif

  {{-- Hero bar --}}
  <div class="fc-show-hero" style="background:{{ $heroBg }};">
    <a href="{{ route('admin.field-complaints.index') }}" class="back-btn">
      <i class="bx bx-chevron-left"></i>
    </a>
    <div class="flex-grow-1">
      <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
        <span class="fw-bold font-monospace" style="font-size:1.3rem;letter-spacing:.04em;">{{ $fc->complaint_no }}</span>
        <span class="badge bg-white text-dark fw-bold" style="font-size:.78rem;">{{ $fc->status }}</span>
        <span class="badge" style="background:rgba(255,255,255,.25);font-size:.72rem;">{{ $fc->priority }} Priority</span>
      </div>
      <div style="opacity:.85;font-size:.82rem;">
        {{ $fc->customer_name }} &bull; {{ $fc->phone_no }} &bull; Logged {{ $fc->created_at->diffForHumans() }}
      </div>
    </div>
    <a href="{{ route('admin.field-complaints.invoice', $fc) }}" target="_blank"
       class="btn fw-semibold"
       style="background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.5);color:#fff;border-radius:10px;backdrop-filter:blur(4px);">
      <i class="bx bx-receipt me-1"></i>Invoice
    </a>
  </div>

  <div class="row g-4">

    {{-- ═══ LEFT COLUMN ═══ --}}
    <div class="col-xl-8">

      {{-- Customer --}}
      <div class="card section-card">
        <div class="card-header" style="background:linear-gradient(135deg,#eef2ff,#f5f0ff);">
          <div class="header-icon" style="background:#696cff20;color:#696cff;"><i class="bx bx-user"></i></div>
          <span style="color:#696cff;">Customer</span>
          @if($fc->customer)
          <span class="badge bg-label-secondary font-monospace small ms-1">{{ $fc->customer->customer_id }}</span>
          @endif
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-sm-6">
              <div class="info-label">Name</div>
              <div class="info-value fw-semibold">{{ $fc->customer_name }}</div>
            </div>
            <div class="col-sm-6">
              <div class="info-label">Phone</div>
              <a href="tel:{{ $fc->phone_no }}" class="info-value fw-semibold text-primary text-decoration-none">
                <i class="bx bx-phone-call me-1"></i>{{ $fc->phone_no }}
              </a>
            </div>
            <div class="col-12">
              <div class="info-label">Address</div>
              <div class="info-value">{{ $fc->address ?: '—' }}</div>
            </div>
            @if($fc->location_notes)
            <div class="col-12">
              <div class="info-label">Location Notes</div>
              <div class="info-value fst-italic text-muted">{{ $fc->location_notes }}</div>
            </div>
            @endif
          </div>

          @if($fc->gps_lat && $fc->gps_lng)
          <div class="mt-3 p-3 rounded-3 d-flex align-items-center justify-content-between"
               style="background:linear-gradient(135deg,#ecfdf5,#d1fae5);border:1px solid #6ee7b7;">
            <div class="d-flex align-items-center gap-2">
              <i class="bx bxs-map-pin text-success fs-5"></i>
              <div>
                <div class="fw-semibold small">{{ $fc->gps_label ?: 'GPS Location' }}</div>
                <div class="font-monospace text-muted" style="font-size:.78rem;">{{ $fc->gps_lat }}, {{ $fc->gps_lng }}</div>
              </div>
            </div>
            <a href="{{ $fc->googleMapsUrl() }}" target="_blank" class="btn btn-sm btn-success">
              <i class="bx bx-link-external me-1"></i>Open Maps
            </a>
          </div>
          @endif

          @if($fc->customer && $fc->customer->fieldComplaints()->count() > 1)
          <div class="alert alert-warning small mt-3 mb-0 py-2">
            <i class="bx bx-info-circle me-1"></i>
            This customer has {{ $fc->customer->fieldComplaints()->count() - 1 }} other visit(s) on record
          </div>
          @endif
        </div>
      </div>

      {{-- Service Details --}}
      <div class="card section-card">
        <div class="card-header" style="background:linear-gradient(135deg,#e8f7ff,#d0efff);">
          <div class="header-icon" style="background:#03c3ec20;color:#03c3ec;"><i class="bx bx-wrench"></i></div>
          <span style="color:#0393b4;">Service Details</span>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-sm-6">
              <div class="info-label">Service Type</div>
              <div class="info-value fw-semibold">{{ $fc->service_type_name ?: '—' }}</div>
            </div>
            <div class="col-sm-6">
              <div class="info-label">Scheduled Date</div>
              <div class="info-value fw-semibold">{{ $fc->scheduled_date?->format('d M Y') ?? '—' }}</div>
            </div>
            @if($fc->description)
            <div class="col-12">
              <div class="info-label">Issue Description</div>
              <div class="p-3 rounded-3 small" style="background:#f8f8fc;border-left:3px solid #696cff;">
                {{ $fc->description }}
              </div>
            </div>
            @endif
            @if($fc->completion_notes)
            <div class="col-12">
              <div class="info-label">Completion Notes</div>
              <div class="p-3 rounded-3 small" style="background:#f0fdf4;border-left:3px solid #28a745;">
                {{ $fc->completion_notes }}
              </div>
            </div>
            @endif
            @if($fc->assigned_to)
            <div class="col-sm-6">
              <div class="info-label">Assigned To</div>
              <div class="info-value fw-semibold">
                <i class="bx bx-user-check me-1 text-info"></i>{{ $fc->assignedEmployee?->employee_name }}
              </div>
            </div>
            <div class="col-sm-6">
              <div class="info-label">Assigned At</div>
              <div class="info-value">{{ $fc->assigned_at?->format('d M Y, g:i A') ?? '—' }}</div>
            </div>
            @endif
          </div>
        </div>
      </div>

      {{-- Billing --}}
      <div class="card section-card">
        <div class="card-header" style="background:linear-gradient(135deg,#fffbeb,#fef3c7);">
          <div class="header-icon" style="background:#ffab0020;color:#e6a817;"><i class="bx bx-receipt"></i></div>
          <span style="color:#b78105;">Billing</span>
          @if(!in_array($fc->status, ['Billed','Cancelled']))
          <button class="btn btn-sm btn-warning ms-auto" data-bs-toggle="modal" data-bs-target="#editBillingModal"
                  style="border-radius:8px;">
            <i class="bx bx-edit me-1"></i>Edit Billing
          </button>
          @endif
        </div>
        <div class="card-body">
          @if($fc->items->isNotEmpty())
          <div class="table-responsive mb-3">
            <table class="table table-sm table-hover mb-0" style="font-size:.85rem;">
              <thead class="table-light">
                <tr>
                  <th>Description</th>
                  <th class="text-center">Qty</th>
                  <th class="text-end">Unit Price</th>
                  <th class="text-end">Total</th>
                </tr>
              </thead>
              <tbody>
                @foreach($fc->items as $item)
                <tr>
                  <td>{{ $item->description }}</td>
                  <td class="text-center">{{ $item->qty }}</td>
                  <td class="text-end font-monospace">{{ number_format($item->unit_price,2) }}</td>
                  <td class="text-end fw-semibold font-monospace">{{ number_format($item->total,2) }}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          @endif

          <div class="ms-auto" style="max-width:300px;">
            <div class="billing-row">
              <span class="label">Service Charge</span>
              <span class="font-monospace">Rs. {{ number_format($fc->service_charge,2) }}</span>
            </div>
            @if($fc->items->isNotEmpty())
            <div class="billing-row">
              <span class="label">Parts / Labour</span>
              <span class="font-monospace">Rs. {{ number_format($fc->items->sum('total'),2) }}</span>
            </div>
            @endif
            @if($fc->discount > 0)
            <div class="billing-row" style="color:#ff3e1d;">
              <span class="label">Discount</span>
              <span class="font-monospace">− Rs. {{ number_format($fc->discount,2) }}</span>
            </div>
            @endif
            <div class="billing-row total">
              <span>Grand Total</span>
              <span class="font-monospace">Rs. {{ number_format($fc->grand_total,2) }}</span>
            </div>
            <div class="billing-row paid">
              <span class="label">Paid</span>
              <span class="value font-monospace">Rs. {{ number_format($fc->paid_amount,2) }}</span>
            </div>
            <div class="billing-row {{ $fc->balance > 0 ? 'balance-due' : 'balance-ok' }}">
              <span class="label fw-semibold">Balance</span>
              <span class="value font-monospace fw-bold">Rs. {{ number_format($fc->balance,2) }}</span>
            </div>
          </div>
        </div>
      </div>

      {{-- Payment History --}}
      @if($fc->paymentLogs->isNotEmpty())
      <div class="card section-card">
        <div class="card-header" style="background:#f8f8fc;">
          <div class="header-icon" style="background:#28a74520;color:#28a745;"><i class="bx bx-history"></i></div>
          <span>Payment History</span>
          <span class="badge bg-label-success ms-auto">{{ $fc->paymentLogs->count() }} payment(s)</span>
        </div>
        <div class="table-responsive">
          <table class="table table-sm table-hover mb-0" style="font-size:.85rem;">
            <tbody>
              @foreach($fc->paymentLogs as $pl)
              <tr>
                <td class="ps-4 text-muted">{{ $pl->paid_at->format('d M Y, g:i A') }}</td>
                <td>{{ $pl->note ?: 'Payment' }}</td>
                <td class="text-end pe-4 fw-semibold text-success font-monospace">
                  Rs. {{ number_format($pl->amount,2) }}
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      @endif

    </div>{{-- /left --}}

    {{-- ═══ RIGHT COLUMN ═══ --}}
    <div class="col-xl-4">

      {{-- Assign Field Staff --}}
      @if(!in_array($fc->status, ['Completed','Billed','Cancelled']))
      <div class="right-card card">
        <div class="card-header" style="background:linear-gradient(135deg,#e8f7ff,#d0efff);">
          <div style="width:28px;height:28px;border-radius:7px;background:#03c3ec20;color:#03c3ec;display:flex;align-items:center;justify-content:center;font-size:.9rem;">
            <i class="bx bx-user-check"></i>
          </div>
          <span style="color:#0393b4;">Assign Field Staff</span>
        </div>
        <div class="card-body">
          <form method="POST" action="{{ route('admin.field-complaints.assign', $fc) }}" class="d-flex flex-column gap-3">
            @csrf @method('PATCH')
            <div>
              <label class="form-label fw-semibold small">Staff Member</label>
              <select name="assigned_to" required class="form-select form-select-sm">
                <option value="">— Select staff —</option>
                @foreach($fieldStaff as $emp)
                <option value="{{ $emp->id }}" {{ $fc->assigned_to == $emp->id ? 'selected' : '' }}>
                  {{ $emp->employee_name }}
                </option>
                @endforeach
              </select>
            </div>
            <div>
              <label class="form-label fw-semibold small">Scheduled Date</label>
              <input type="date" name="scheduled_date" value="{{ $fc->scheduled_date?->format('Y-m-d') }}"
                     class="form-control form-control-sm">
            </div>
            <button class="btn btn-sm fw-semibold"
                    style="background:linear-gradient(135deg,#03c3ec,#028bb6);color:#fff;border:0;border-radius:8px;">
              <i class="bx bx-check me-1"></i>Assign
            </button>
          </form>
        </div>
      </div>
      @endif

      {{-- Update Status --}}
      @if($fc->status !== 'Cancelled')
      <div class="right-card card">
        <div class="card-header" style="background:#f8f8fc;">
          <div style="width:28px;height:28px;border-radius:7px;background:#69697020;color:#697a8d;display:flex;align-items:center;justify-content:center;font-size:.9rem;">
            <i class="bx bx-refresh"></i>
          </div>
          <span>Update Status</span>
        </div>
        <div class="card-body">
          <form method="POST" action="{{ route('admin.field-complaints.status', $fc) }}" class="d-flex flex-column gap-3">
            @csrf @method('PATCH')
            <div>
              <label class="form-label fw-semibold small">New Status</label>
              <select name="status" class="form-select form-select-sm">
                @foreach(['Pending','Assigned','In Progress','Completed','Billed','Cancelled'] as $s)
                <option value="{{ $s }}" {{ $fc->status === $s ? 'selected' : '' }}>{{ $s }}</option>
                @endforeach
              </select>
            </div>
            <div>
              <label class="form-label fw-semibold small">Notes</label>
              <textarea name="completion_notes" rows="2"
                        placeholder="Completion / cancellation notes…"
                        class="form-control form-control-sm">{{ $fc->completion_notes }}</textarea>
            </div>
            <button class="btn btn-sm btn-secondary fw-semibold" style="border-radius:8px;">
              <i class="bx bx-save me-1"></i>Update Status
            </button>
          </form>
        </div>
      </div>
      @endif

      {{-- Record Payment --}}
      @if($fc->balance > 0)
      <div class="right-card card" style="border:2px solid #28a74540 !important;">
        <div class="card-header" style="background:linear-gradient(135deg,#f0fdf4,#d1fae5);">
          <div style="width:28px;height:28px;border-radius:7px;background:#28a74520;color:#28a745;display:flex;align-items:center;justify-content:center;font-size:.9rem;">
            <i class="bx bx-money"></i>
          </div>
          <span style="color:#1e7e34;">Record Payment</span>
          <span class="ms-auto badge bg-label-danger small">Rs. {{ number_format($fc->balance,2) }} due</span>
        </div>
        <div class="card-body">
          <form method="POST" action="{{ route('admin.field-complaints.payment', $fc) }}" class="d-flex flex-column gap-3">
            @csrf
            <div>
              <label class="form-label fw-semibold small">Amount (Rs.)</label>
              <div class="input-group input-group-sm">
                <span class="input-group-text fw-semibold">Rs.</span>
                <input type="number" step="0.01" name="amount_paid"
                       placeholder="0.00" required max="{{ $fc->balance }}"
                       class="form-control font-monospace">
              </div>
            </div>
            <div>
              <label class="form-label fw-semibold small">Note</label>
              <input type="text" name="note" placeholder="e.g. Cash, Transfer…" class="form-control form-control-sm">
            </div>
            <button class="btn btn-sm fw-semibold"
                    style="background:linear-gradient(135deg,#28a745,#1e7e34);color:#fff;border:0;border-radius:8px;box-shadow:0 4px 10px rgba(40,167,69,.3);">
              <i class="bx bx-check-circle me-1"></i>Record Payment
            </button>
          </form>
        </div>
      </div>
      @endif

      {{-- Remark --}}
      @if($fc->remark)
      <div class="right-card card">
        <div class="card-header" style="background:#f8f8fc;">
          <div style="width:28px;height:28px;border-radius:7px;background:#69697020;color:#697a8d;display:flex;align-items:center;justify-content:center;font-size:.9rem;">
            <i class="bx bx-note"></i>
          </div>
          <span>Internal Remark</span>
        </div>
        <div class="card-body">
          <p class="small text-muted mb-0 fst-italic">{{ $fc->remark }}</p>
        </div>
      </div>
      @endif

      {{-- Delete --}}
      @if(in_array($fc->status, ['Pending','Cancelled']))
      <form method="POST" action="{{ route('admin.field-complaints.destroy', $fc) }}"
            onsubmit="return confirm('Delete complaint {{ $fc->complaint_no }}? This cannot be undone.')">
        @csrf @method('DELETE')
        <button class="btn btn-outline-danger w-100" style="border-radius:10px;">
          <i class="bx bx-trash me-1"></i>Delete Complaint
        </button>
      </form>
      @endif

    </div>{{-- /right --}}
  </div>
</div>

{{-- Edit Billing Modal --}}
<div class="modal fade" id="editBillingModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content" style="border-radius:16px;border:0;overflow:hidden;">
      <div class="modal-header" style="background:linear-gradient(135deg,#fffbeb,#fef3c7);border-bottom:1px solid rgba(0,0,0,.06);">
        <h5 class="modal-title fw-bold"><i class="bx bx-edit me-2 text-warning"></i>Edit Billing</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="{{ route('admin.field-complaints.update', $fc) }}">
        @csrf @method('PUT')
        <input type="hidden" name="customer_name" value="{{ $fc->customer_name }}">
        <input type="hidden" name="phone_no" value="{{ $fc->phone_no }}">
        <input type="hidden" name="address" value="{{ $fc->address }}">
        <input type="hidden" name="priority" value="{{ $fc->priority }}">

        <div class="modal-body p-4">
          <div class="row g-3 mb-4">
            <div class="col-sm-6">
              <label class="form-label fw-semibold small">Service Charge (Rs.)</label>
              <div class="input-group">
                <span class="input-group-text fw-semibold">Rs.</span>
                <input type="number" step="0.01" name="service_charge"
                       value="{{ $fc->service_charge }}" class="form-control font-monospace">
              </div>
            </div>
            <div class="col-sm-6">
              <label class="form-label fw-semibold small">Discount (Rs.)</label>
              <div class="input-group">
                <span class="input-group-text fw-semibold">Rs.</span>
                <input type="number" step="0.01" name="discount"
                       value="{{ $fc->discount }}" class="form-control font-monospace">
              </div>
            </div>
          </div>

          <div class="d-flex align-items-center justify-content-between mb-3">
            <label class="form-label fw-semibold mb-0">Parts / Labour Items</label>
            <button type="button" id="addItemBtn"
                    class="btn btn-sm btn-outline-primary" style="border-radius:8px;">
              <i class="bx bx-plus me-1"></i>Add Item
            </button>
          </div>

          <div id="itemsContainer" class="d-flex flex-column gap-2">
            @foreach($fc->items as $i => $item)
            <div class="d-flex gap-2 align-items-center item-row p-2 rounded-2"
                 style="background:#f8f8fc;">
              <input type="text" name="items[{{ $i }}][description]" value="{{ $item->description }}"
                     placeholder="Description" class="form-control form-control-sm" required>
              <input type="number" name="items[{{ $i }}][qty]" value="{{ $item->qty }}"
                     placeholder="Qty" min="1" class="form-control form-control-sm" style="width:70px;" required>
              <input type="number" step="0.01" name="items[{{ $i }}][unit_price]" value="{{ $item->unit_price }}"
                     placeholder="Price" class="form-control form-control-sm font-monospace" style="width:110px;" required>
              <button type="button" class="removeItem btn btn-sm btn-outline-danger flex-shrink-0" style="border-radius:7px;">
                <i class="bx bx-x"></i>
              </button>
            </div>
            @endforeach
          </div>
        </div>

        <div class="modal-footer" style="background:#fafafa;">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-warning fw-semibold">
            <i class="bx bx-save me-1"></i>Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
let itemIdx = {{ $fc->items->count() }};
document.getElementById('addItemBtn').addEventListener('click', function () {
  const container = document.getElementById('itemsContainer');
  const row = document.createElement('div');
  row.className = 'd-flex gap-2 align-items-center item-row p-2 rounded-2';
  row.style.background = '#f8f8fc';
  row.innerHTML = `
    <input type="text" name="items[${itemIdx}][description]" placeholder="Description" class="form-control form-control-sm" required>
    <input type="number" name="items[${itemIdx}][qty]" placeholder="Qty" min="1" value="1" class="form-control form-control-sm" style="width:70px;" required>
    <input type="number" step="0.01" name="items[${itemIdx}][unit_price]" placeholder="Price" class="form-control form-control-sm font-monospace" style="width:110px;" required>
    <button type="button" class="removeItem btn btn-sm btn-outline-danger flex-shrink-0" style="border-radius:7px;"><i class="bx bx-x"></i></button>`;
  container.appendChild(row);
  itemIdx++;
});
document.getElementById('itemsContainer').addEventListener('click', function (e) {
  if (e.target.closest('.removeItem')) e.target.closest('.item-row').remove();
});
</script>
@endpush
@endsection
