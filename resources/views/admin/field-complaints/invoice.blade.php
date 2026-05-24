@extends('layouts.admin')
@section('title', 'Invoice ' . ($fieldComplaint->invoice_no ?? $fieldComplaint->complaint_no))

@push('styles')
<style>
  .invoice-card {
    border-radius: 16px;
    border: 0;
    box-shadow: 0 4px 32px rgba(105,108,255,.12);
    overflow: hidden;
  }
  .invoice-header {
    background: linear-gradient(135deg, #696cff 0%, #5a67f2 60%, #8c57ff 100%);
    padding: 2rem 2.5rem;
    position: relative;
    overflow: hidden;
  }
  .invoice-header::before {
    content: '';
    position: absolute;
    width: 300px; height: 300px;
    border-radius: 50%;
    background: rgba(255,255,255,.06);
    top: -100px; right: -60px;
    pointer-events: none;
  }
  .invoice-header::after {
    content: '';
    position: absolute;
    width: 200px; height: 200px;
    border-radius: 50%;
    background: rgba(255,255,255,.04);
    bottom: -80px; left: 30px;
    pointer-events: none;
  }
  .invoice-header * { position: relative; z-index: 1; }

  .invoice-body { padding: 2rem 2.5rem; }

  .section-label {
    font-size: .68rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .1em;
    color: #a1acb8;
    margin-bottom: .5rem;
  }

  .invoice-table th {
    background: #f4f4ff;
    font-size: .75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .05em;
    color: #697a8d;
    border-top: 0;
    padding: .75rem 1rem;
  }
  .invoice-table td { padding: .75rem 1rem; vertical-align: middle; font-size: .88rem; }
  .invoice-table tbody tr:last-child td { border-bottom: 0; }

  .totals-table td { padding: .4rem 0; font-size: .875rem; }
  .totals-table .grand-total td {
    font-size: 1.05rem;
    font-weight: 700;
    border-top: 2px solid #e0e0e0;
    padding-top: .6rem;
  }
  .totals-table .balance-due td { color: #ff3e1d; font-weight: 700; }
  .totals-table .balance-ok  td { color: #28a745; font-weight: 600; }
  .totals-table .paid-row    td { color: #28a745; }

  .status-pill {
    display: inline-block;
    padding: .25rem .9rem;
    border-radius: 20px;
    font-size: .72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .05em;
  }

  .invoice-footer {
    background: #f8f8fc;
    border-top: 1px solid #ebebf5;
    padding: 1.25rem 2.5rem;
    text-align: center;
  }

  @media print {
    body * { visibility: hidden; }
    #invoicePrint, #invoicePrint * { visibility: visible; }
    #invoicePrint { position: absolute; inset: 0; }
    .no-print { display: none !important; }
  }
</style>
@endpush

@section('content')
@php
  $fc    = $fieldComplaint;
  $store = \App\Models\StoreInfo::first();
  $isPaid = $fc->balance <= 0;
@endphp

<div class="container-xxl flex-grow-1 container-p-y">

  {{-- Toolbar --}}
  <div class="no-print d-flex align-items-center justify-content-between mb-4">
    <a href="{{ route('admin.field-complaints.show', $fc) }}"
       class="btn btn-icon btn-sm btn-outline-secondary" style="border-radius:10px;">
      <i class="bx bx-chevron-left"></i>
    </a>
    <div class="d-flex align-items-center gap-2">
      <span class="badge {{ $isPaid ? 'bg-label-success' : 'bg-label-danger' }} px-3 py-2" style="font-size:.8rem;">
        {{ $isPaid ? 'Paid' : 'Balance Due: Rs. '.number_format($fc->balance,2) }}
      </span>
      <button onclick="window.print()"
              class="btn btn-primary fw-semibold"
              style="border-radius:10px;background:linear-gradient(135deg,#696cff,#8c57ff);border:0;box-shadow:0 4px 12px rgba(105,108,255,.4);">
        <i class="bx bx-printer me-1"></i>Print Invoice
      </button>
    </div>
  </div>

  <div id="invoicePrint" class="row justify-content-center">
    <div class="col-xl-8 col-lg-10">
      <div class="invoice-card card">

        {{-- Gradient Header --}}
        <div class="invoice-header">
          <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
              @if($store?->logo)
              <img src="{{ asset('storage/'.$store->logo) }}" alt="Logo"
                   style="height:44px;margin-bottom:10px;border-radius:8px;background:rgba(255,255,255,.15);padding:3px;">
              @endif
              <div class="text-white fw-bold" style="font-size:1.3rem;line-height:1.2;">
                {{ $store?->store_name ?? config('app.name') }}
              </div>
              @if($store?->store_address)
              <div class="text-white small mt-1" style="opacity:.8;">{{ $store->store_address }}</div>
              @endif
              @if($store?->phone_no1)
              <div class="text-white small" style="opacity:.8;">{{ $store->phone_no1 }}</div>
              @endif
            </div>
            <div class="text-end">
              <div class="text-white mb-2" style="font-size:.65rem;text-transform:uppercase;letter-spacing:.15em;opacity:.7;">
                Field Service Invoice
              </div>
              <div class="text-white font-monospace fw-bold" style="font-size:1.5rem;letter-spacing:.05em;">
                {{ $fc->invoice_no ?? $fc->complaint_no }}
              </div>
              <div class="text-white small mt-1" style="opacity:.8;">
                {{ $fc->invoice_date?->format('d M Y') ?? now()->format('d M Y') }}
              </div>
              <div class="mt-2">
                <span class="status-pill {{ $isPaid ? '' : '' }}"
                      style="background:{{ $isPaid ? 'rgba(40,167,69,.3)' : 'rgba(255,62,29,.3)' }};
                             color:#fff;border:1px solid rgba(255,255,255,.3);">
                  {{ $isPaid ? 'PAID' : 'UNPAID' }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <div class="invoice-body">

          {{-- Bill To + Ref --}}
          <div class="row mb-4">
            <div class="col-sm-6 mb-3 mb-sm-0">
              <div class="section-label">Bill To</div>
              <div class="fw-bold" style="font-size:1.05rem;">{{ $fc->customer_name }}</div>
              <div class="text-muted small mt-1">
                <i class="bx bx-phone-call me-1"></i>{{ $fc->phone_no }}
              </div>
              @if($fc->address)
              <div class="text-muted small">
                <i class="bx bx-map me-1"></i>{{ $fc->address }}
              </div>
              @endif
              @if($fc->gps_lat && $fc->gps_lng)
              <a href="{{ $fc->googleMapsUrl() }}" target="_blank"
                 class="small text-success d-inline-flex align-items-center gap-1 mt-1">
                <i class="bx bxs-map-pin"></i>{{ $fc->gps_label ?? 'View on Maps' }}
              </a>
              @endif
            </div>
            <div class="col-sm-6">
              <div class="section-label">Service Details</div>
              <div class="d-flex flex-column gap-1" style="font-size:.85rem;">
                <div class="d-flex justify-content-between">
                  <span class="text-muted">Complaint #</span>
                  <span class="fw-semibold font-monospace text-primary">{{ $fc->complaint_no }}</span>
                </div>
                <div class="d-flex justify-content-between">
                  <span class="text-muted">Service Type</span>
                  <span class="fw-semibold">{{ $fc->service_type_name ?: 'Field Service' }}</span>
                </div>
                @if($fc->assignedEmployee)
                <div class="d-flex justify-content-between">
                  <span class="text-muted">Technician</span>
                  <span class="fw-semibold">{{ $fc->assignedEmployee->employee_name }}</span>
                </div>
                @endif
                @if($fc->scheduled_date)
                <div class="d-flex justify-content-between">
                  <span class="text-muted">Visit Date</span>
                  <span class="fw-semibold">{{ $fc->scheduled_date->format('d M Y') }}</span>
                </div>
                @endif
              </div>
            </div>
          </div>

          {{-- Divider --}}
          <hr style="border-color:#ebebf5;margin:1.5rem 0;">

          {{-- Items Table --}}
          <div class="table-responsive mb-4">
            <table class="table invoice-table" style="border:1px solid #ebebf5;border-radius:10px;overflow:hidden;">
              <thead>
                <tr>
                  <th style="width:36px;">#</th>
                  <th>Description</th>
                  <th class="text-center" style="width:60px;">Qty</th>
                  <th class="text-end" style="width:110px;">Unit Price</th>
                  <th class="text-end" style="width:120px;">Amount</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="text-muted">1</td>
                  <td>
                    <div class="fw-semibold">{{ $fc->service_type_name ?? 'Service Charge' }}</div>
                    @if($fc->description)
                    <div class="text-muted small">{{ Str::limit($fc->description, 80) }}</div>
                    @endif
                  </td>
                  <td class="text-center">1</td>
                  <td class="text-end font-monospace">{{ number_format($fc->service_charge,2) }}</td>
                  <td class="text-end font-monospace fw-semibold">{{ number_format($fc->service_charge,2) }}</td>
                </tr>
                @foreach($fc->items as $i => $item)
                <tr>
                  <td class="text-muted">{{ $i+2 }}</td>
                  <td class="fw-semibold">{{ $item->description }}</td>
                  <td class="text-center">{{ $item->qty }}</td>
                  <td class="text-end font-monospace">{{ number_format($item->unit_price,2) }}</td>
                  <td class="text-end font-monospace fw-semibold">{{ number_format($item->total,2) }}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          {{-- Totals --}}
          <div class="d-flex justify-content-end mb-4">
            <div style="min-width:280px;">
              <table class="table totals-table table-borderless mb-0">
                <tr>
                  <td class="text-muted">Subtotal</td>
                  <td class="text-end font-monospace">Rs. {{ number_format($fc->subtotal,2) }}</td>
                </tr>
                @if($fc->discount > 0)
                <tr class="text-danger">
                  <td>Discount</td>
                  <td class="text-end font-monospace">− Rs. {{ number_format($fc->discount,2) }}</td>
                </tr>
                @endif
                <tr class="grand-total">
                  <td>Grand Total</td>
                  <td class="text-end font-monospace">Rs. {{ number_format($fc->grand_total,2) }}</td>
                </tr>
                <tr class="paid-row">
                  <td><i class="bx bx-check-circle me-1"></i>Paid</td>
                  <td class="text-end font-monospace">Rs. {{ number_format($fc->paid_amount,2) }}</td>
                </tr>
                <tr class="{{ $fc->balance > 0 ? 'balance-due' : 'balance-ok' }}">
                  <td><strong>Balance Due</strong></td>
                  <td class="text-end font-monospace"><strong>Rs. {{ number_format($fc->balance,2) }}</strong></td>
                </tr>
              </table>
            </div>
          </div>

          {{-- Payment History --}}
          @if($fc->paymentLogs->isNotEmpty())
          <div class="p-3 rounded-3 mb-4" style="background:#f8f8fc;border:1px solid #ebebf5;">
            <div class="section-label">Payment History</div>
            @foreach($fc->paymentLogs as $pl)
            <div class="d-flex justify-content-between align-items-center py-1 border-bottom" style="font-size:.83rem;">
              <span class="text-muted">
                <i class="bx bx-calendar me-1"></i>{{ $pl->paid_at->format('d M Y') }}
                @if($pl->note) &bull; {{ $pl->note }}@endif
              </span>
              <span class="font-monospace fw-semibold text-success">Rs. {{ number_format($pl->amount,2) }}</span>
            </div>
            @endforeach
          </div>
          @endif

        </div>

        {{-- Footer --}}
        <div class="invoice-footer">
          <p class="text-muted small mb-1">
            Thank you for choosing <strong>{{ $store?->store_name ?? config('app.name') }}</strong>
          </p>
          <p class="text-muted small mb-0">
            @if($store?->phone_no1) {{ $store->phone_no1 }} @endif
            @if($store?->phone_no1 && $store?->store_address) &bull; @endif
            @if($store?->store_address) {{ $store->store_address }} @endif
          </p>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
