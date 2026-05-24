@extends('layouts.admin')
@section('title', 'Job Order – ' . $jobCard->order_no)
@section('page-title', 'Job Order Details')
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.jobcards.index') }}">Job Orders</a></li>
  <li class="breadcrumb-item active">{{ $jobCard->order_no }}</li>
@endsection

@push('styles')
<style>
  /* Reuse the same modal styles — this page IS the modal content rendered full-page */
  .jov-header {
    background: linear-gradient(135deg,#696cff,#8c57ff 60%,#a855f7);
    border-radius: 14px;
    padding: 20px 26px;
    color: #fff;
    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;
    margin-bottom: 20px;
  }
  .jov-order { font-size: 1.5rem; font-weight: 800; letter-spacing: 1px; }
  .jov-sub { font-size: .78rem; opacity: .75; text-transform: uppercase; letter-spacing: .08em; }
  .info-row { display: flex; padding: 8px 0; border-bottom: 1px solid #f0f0f8; }
  .info-row:last-child { border-bottom: none; }
  .info-label { width: 38%; font-size: .8rem; font-weight: 700; color: #888; text-transform: uppercase; letter-spacing: .04em; padding-right: 8px; }
  .info-value { flex: 1; font-size: .88rem; color: #333; font-weight: 500; }
  .section-head-alt {
    font-size: .78rem; font-weight: 700; text-transform: uppercase; letter-spacing: .1em;
    color: #696cff; margin-bottom: 12px; padding-bottom: 8px;
    border-bottom: 2px solid #ebebff; display: flex; align-items: center; gap: 8px;
  }
</style>
@endpush

@section('content')
@php
  $statusColors = ['Pending'=>'#ffab00','In Progress'=>'#03c3ec','Completed'=>'#71dd37','Not Completed'=>'#ff3e1d','Cancelled'=>'#8592a3'];
  $priorityColors = ['Low'=>'#71dd37','Normal'=>'#03c3ec','High'=>'#ffab00','Urgent'=>'#ff3e1d'];
  $sc = ['Pending'=>'bg-label-warning','In Progress'=>'bg-label-info','Completed'=>'bg-label-success','Not Completed'=>'bg-label-danger'];
@endphp

<div class="jov-header">
  <div>
    <div class="jov-sub">Job Order</div>
    <div class="jov-order"># {{ $jobCard->order_no }}</div>
    <div style="margin-top:4px;font-size:.8rem;opacity:.8">{{ $jobCard->customer_id }}</div>
  </div>
  <div style="display:flex;flex-direction:column;align-items:flex-end;gap:8px">
    <span class="badge {{ $sc[$jobCard->status] ?? 'bg-secondary' }}" style="font-size:.85rem;padding:6px 14px;">{{ $jobCard->status }}</span>
    @if($jobCard->priority)
      <span style="background:{{ $priorityColors[$jobCard->priority] ?? '#ccc' }}22;border:1px solid {{ $priorityColors[$jobCard->priority] ?? '#ccc' }};color:{{ $priorityColors[$jobCard->priority] ?? '#555' }};border-radius:20px;padding:3px 12px;font-size:.75rem;font-weight:700;">
        <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:{{ $priorityColors[$jobCard->priority] ?? '#ccc' }};margin-right:5px"></span>{{ $jobCard->priority }} Priority
      </span>
    @endif
  </div>
</div>

<div class="row g-3">
  <div class="col-lg-6">
    <div class="card h-100" style="border-radius:14px;border:0;box-shadow:0 2px 12px rgba(0,0,0,.07)">
      <div class="card-body p-4">
        <div class="section-head-alt"><i class='bx bx-user'></i> Customer</div>
        <div class="info-row"><div class="info-label">Name</div><div class="info-value">{{ $jobCard->customer_name }}</div></div>
        <div class="info-row"><div class="info-label">Phone</div><div class="info-value">{{ $jobCard->phone_no }}</div></div>
        <div class="info-row"><div class="info-label">NIC</div><div class="info-value">{{ $jobCard->customer_nic ?: '—' }}</div></div>
        <div class="info-row"><div class="info-label">Email</div><div class="info-value">{{ $jobCard->customer_email ?: '—' }}</div></div>
        <div class="info-row"><div class="info-label">Address</div><div class="info-value">{{ $jobCard->customer_address ?: '—' }}</div></div>
        <div class="info-row"><div class="info-label">Date of Birth</div><div class="info-value">{{ $jobCard->customer_dob ?: '—' }}</div></div>
        <div class="info-row"><div class="info-label">Received</div><div class="info-value">{{ $jobCard->date ? $jobCard->date->format('d M Y') : '—' }}</div></div>
        <div class="info-row"><div class="info-label">Est. Delivery</div><div class="info-value">{{ $jobCard->estimated_delivery ? $jobCard->estimated_delivery->format('d M Y') : '—' }}</div></div>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card h-100" style="border-radius:14px;border:0;box-shadow:0 2px 12px rgba(0,0,0,.07)">
      <div class="card-body p-4">
        <div class="section-head-alt"><i class='bx bx-chip'></i> Device & Repair</div>
        <div class="info-row"><div class="info-label">Device</div><div class="info-value">{{ $jobCard->device_name }}</div></div>
        <div class="info-row"><div class="info-label">Brand</div><div class="info-value">{{ $jobCard->device_brand ?: '—' }}</div></div>
        <div class="info-row"><div class="info-label">Serial / IMEI</div><div class="info-value">{{ $jobCard->serial_no ?: '—' }}</div></div>
        <div class="info-row"><div class="info-label">Device Age</div><div class="info-value">{{ $jobCard->device_age ? $jobCard->device_age . ' yrs' : '—' }}</div></div>
        <div class="info-row"><div class="info-label">Fault</div><div class="info-value">{{ $jobCard->device_fault ?: '—' }}</div></div>
        <div class="info-row"><div class="info-label">Issue</div><div class="info-value">{{ $jobCard->issue ?: '—' }}</div></div>
        <div class="info-row"><div class="info-label">Est. Cost</div><div class="info-value"><strong style="color:#696cff">Rs. {{ number_format($jobCard->rupees ?? 0, 2) }}</strong></div></div>
        @if((float)$jobCard->advance_amount > 0)
        <div class="info-row"><div class="info-label">Advance Paid</div><div class="info-value"><span style="color:#28a745;font-weight:700">Rs. {{ number_format($jobCard->advance_amount, 2) }}</span></div></div>
        <div class="info-row"><div class="info-label">Balance Due</div><div class="info-value"><span style="color:#ff3e1d;font-weight:700">Rs. {{ number_format($jobCard->balance, 2) }}</span></div></div>
        @endif
        <div class="info-row"><div class="info-label">Assigned To</div><div class="info-value">{{ optional($jobCard->employee)->employee_name ?? '—' }}</div></div>
        <div class="info-row"><div class="info-label">Accessories</div><div class="info-value">{{ $jobCard->accessories ?: '—' }}</div></div>
        <div class="info-row"><div class="info-label">Need Assistant</div><div class="info-value">{{ $jobCard->need_assistant ? '<span class="badge bg-label-warning">Yes</span>' : 'No' }}</div></div>
        <div class="info-row"><div class="info-label">Remark</div><div class="info-value">{{ $jobCard->remark ?: '—' }}</div></div>
      </div>
    </div>
  </div>
</div>

@if($jobCard->status === 'Cancelled')
<div class="alert mt-3 mb-0 d-flex align-items-start gap-3" style="background:#fff0ee;border:1.5px solid #ffb3a8;border-radius:12px;padding:16px 20px">
  <i class='bx bx-block' style="font-size:1.4rem;color:#ff3e1d;margin-top:2px;flex-shrink:0"></i>
  <div>
    <div style="font-weight:700;font-size:.9rem;color:#c0392b;margin-bottom:4px">Order Cancelled</div>
    @if($jobCard->cancelled_reason)
    <div style="font-size:.85rem;color:#555;margin-bottom:4px"><strong>Reason:</strong> {{ $jobCard->cancelled_reason }}</div>
    @endif
    @if($jobCard->cancelled_at)
    <div style="font-size:.78rem;color:#888"><i class='bx bx-time me-1'></i>{{ $jobCard->cancelled_at->format('d M Y, H:i') }}</div>
    @endif
  </div>
</div>
@endif

<div class="d-flex gap-2 mt-3 flex-wrap">
  <a href="{{ route('admin.jobcards.edit', $jobCard) }}" class="btn" style="background:linear-gradient(135deg,#696cff,#8c57ff);color:#fff;border-radius:10px;font-weight:600;padding:8px 24px">
    <i class='bx bx-edit me-1'></i>Edit
  </a>
  <a href="{{ route('admin.invoices.show', $jobCard) }}" class="btn btn-outline-primary" style="border-radius:10px;font-weight:600;padding:8px 24px">
    <i class='bx bx-receipt me-1'></i>{{ $jobCard->invoice_no ? 'View Invoice' : 'Generate Invoice' }}
  </a>
  <a href="{{ route('admin.jobcards.index') }}" class="btn btn-outline-secondary" style="border-radius:10px;font-weight:600;padding:8px 24px">
    <i class='bx bx-arrow-back me-1'></i>Back
  </a>
</div>
@endsection
