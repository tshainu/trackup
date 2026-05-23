@extends('layouts.admin')
@section('title', 'Invoices')
@section('page-title', 'Invoice Management')
@section('breadcrumb')<li class="breadcrumb-item active">Invoices</li>@endsection

@push('styles')
<style>
.inv-hero {
  background: linear-gradient(135deg, #696cff 0%, #8c57ff 60%, #a855f7 100%);
  border-radius: 18px;
  padding: 32px 36px;
  color: #fff;
  margin-bottom: 28px;
  position: relative;
  overflow: hidden;
}
.inv-hero::after {
  content: '\f157';
  font-family: 'boxicons';
  position: absolute;
  right: 36px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 120px;
  opacity: .08;
  line-height: 1;
}
.inv-hero h1 { font-size: 1.8rem; font-weight: 800; margin: 0 0 6px; }
.inv-hero p  { margin: 0; opacity: .8; font-size: .92rem; }
.search-wrap { position: relative; }
.search-wrap .search-icon { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #696cff; font-size: 1.2rem; }
.search-wrap input { padding-left: 46px; border-radius: 12px; height: 50px; font-size: 1rem; border: 2px solid #e7e7ff; }
.search-wrap input:focus { border-color: #696cff; box-shadow: 0 0 0 3px rgba(105,108,255,.15); }
.result-card {
  background: #fff;
  border-radius: 14px;
  border: 1px solid #f0f0ff;
  padding: 16px 20px;
  display: flex; align-items: center; gap: 16px;
  transition: all .2s;
  cursor: pointer;
  text-decoration: none;
  color: inherit;
}
.result-card:hover { border-color: #696cff; box-shadow: 0 4px 18px rgba(105,108,255,.15); transform: translateY(-1px); }
.result-icon {
  width: 48px; height: 48px; border-radius: 12px;
  background: linear-gradient(135deg, #696cff22, #a855f722);
  display: flex; align-items: center; justify-content: center;
  font-size: 1.4rem; color: #696cff; flex-shrink: 0;
}
.result-order { font-weight: 800; font-size: .95rem; color: #333; }
.result-customer { font-size: .82rem; color: #555; margin-top: 2px; }
.result-meta { font-size: .75rem; color: #aaa; margin-top: 4px; display:flex; gap: 12px; flex-wrap: wrap; }
.result-meta span { display:flex; align-items:center; gap: 4px; }
.result-right { margin-left: auto; text-align: right; flex-shrink: 0; }
.result-amount { font-size: 1.1rem; font-weight: 800; color: #696cff; }
.result-status { display: inline-block; margin-top: 4px; }
.badge-paid   { background: #d1fae5; color: #065f46; border-radius: 20px; padding: 3px 10px; font-size: .72rem; font-weight: 700; }
.badge-unpaid { background: #fee2e2; color: #991b1b; border-radius: 20px; padding: 3px 10px; font-size: .72rem; font-weight: 700; }
.badge-partial{ background: #fef3c7; color: #92400e; border-radius: 20px; padding: 3px 10px; font-size: .72rem; font-weight: 700; }
.empty-state { text-align: center; padding: 60px 20px; color: #aaa; }
.empty-state i { font-size: 4rem; display: block; margin-bottom: 14px; color: #d0d0ff; }
.quick-filter-btn { border-radius: 20px; font-size: .78rem; font-weight: 600; padding: 5px 14px; border: 1.5px solid #e0e0f5; background: #fff; color: #555; cursor: pointer; transition: all .15s; }
.quick-filter-btn:hover, .quick-filter-btn.active { background: #696cff; border-color: #696cff; color: #fff; }
</style>
@endpush

@section('content')
<div class="inv-hero">
  <h1><i class='bx bx-receipt' style="vertical-align:middle;margin-right:10px;font-size:1.6rem"></i>Invoices</h1>
  <p>Search by Order No., Invoice No., NIC, Phone, Customer Name, or Device</p>
</div>

{{-- Search Bar --}}
<div class="card" style="border-radius:16px;border:0;box-shadow:0 2px 16px rgba(0,0,0,.07);margin-bottom:24px">
  <div class="card-body p-4">
    <form method="GET" action="{{ route('admin.invoices.index') }}" id="searchForm">
      <div class="search-wrap">
        <i class='bx bx-search search-icon'></i>
        <input type="text" name="q" id="searchInput" value="{{ $query }}"
               class="form-control"
               placeholder="Search: order no., invoice no., NIC, phone, customer name, device/brand…"
               autocomplete="off" autofocus>
      </div>
      <div class="d-flex flex-wrap gap-2 mt-3 align-items-center">
        <span style="font-size:.78rem;color:#aaa;font-weight:600">QUICK SEARCH:</span>
        @foreach(['Pending','In Progress','Completed'] as $s)
          <button type="button" class="quick-filter-btn" onclick="appendSearch('{{ $s }}')">{{ $s }}</button>
        @endforeach
        <button type="button" class="quick-filter-btn" onclick="appendSearch('unpaid')">Unpaid</button>
        <div class="ms-auto">
          <button type="submit" class="btn btn-primary" style="border-radius:10px;padding:8px 24px">
            <i class='bx bx-search me-1'></i> Search
          </button>
          @if($query)
          <a href="{{ route('admin.invoices.index') }}" class="btn btn-outline-secondary ms-2" style="border-radius:10px">Clear</a>
          @endif
        </div>
      </div>
    </form>
  </div>
</div>

{{-- Results --}}
@if($query !== '')
  <div class="d-flex align-items-center justify-content-between mb-3">
    <div style="font-size:.82rem;color:#888">
      <strong style="color:#333">{{ $results->count() }}</strong> result{{ $results->count() !== 1 ? 's' : '' }} for <strong>"{{ $query }}"</strong>
    </div>
  </div>

  @if($results->isEmpty())
    <div class="card" style="border-radius:16px;border:0;box-shadow:0 2px 12px rgba(0,0,0,.06)">
      <div class="empty-state">
        <i class='bx bx-search-alt'></i>
        <div style="font-size:1.1rem;font-weight:700;color:#555;margin-bottom:8px">No records found</div>
        <p style="font-size:.85rem">Try searching with a different order no., NIC, phone number or device name</p>
      </div>
    </div>
  @else
    <div class="d-flex flex-column gap-2">
      @foreach($results as $job)
        @php
          $job->load('invoiceItems');
          $grand  = $job->grand_total;
          $paid   = (float)$job->paid_amount;
          $balance= $job->balance;
          $payStatus = $paid >= $grand && $grand > 0 ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid');
          $statusColors = ['Pending'=>'warning','In Progress'=>'info','Completed'=>'success','Not Completed'=>'danger'];
        @endphp
        <a href="{{ route('admin.invoices.show', $job) }}" class="result-card">
          <div class="result-icon"><i class='bx bx-file-blank'></i></div>
          <div style="flex:1;min-width:0">
            <div class="result-order">
              {{ $job->order_no }}
              @if($job->invoice_no)
                <span style="font-size:.75rem;font-weight:500;color:#aaa;margin-left:8px">{{ $job->invoice_no }}</span>
              @else
                <span style="font-size:.73rem;color:#d0a020;margin-left:8px;font-weight:600">No Invoice Yet</span>
              @endif
            </div>
            <div class="result-customer">{{ $job->customer_name }}</div>
            <div class="result-meta">
              <span><i class='bx bx-phone'></i> {{ $job->phone_no }}</span>
              @if($job->customer_nic)
              <span><i class='bx bx-id-card'></i> {{ $job->customer_nic }}</span>
              @endif
              <span><i class='bx bx-chip'></i> {{ $job->device_name }}{{ $job->device_brand ? ' · '.$job->device_brand : '' }}</span>
              <span><i class='bx bx-calendar'></i> {{ $job->date ? $job->date->format('d M Y') : '—' }}</span>
            </div>
          </div>
          <div class="result-right">
            <div class="result-amount">Rs. {{ number_format($grand, 2) }}</div>
            <div class="result-status">
              @if($payStatus === 'paid')
                <span class="badge-paid">✓ Paid</span>
              @elseif($payStatus === 'partial')
                <span class="badge-partial">⚡ Partial · Rs.{{ number_format($balance,2) }} due</span>
              @else
                <span class="badge-unpaid">● Unpaid</span>
              @endif
            </div>
            <div style="margin-top:6px">
              <span class="badge bg-label-{{ $statusColors[$job->status] ?? 'secondary' }}" style="font-size:.72rem">{{ $job->status }}</span>
            </div>
          </div>
          <div style="flex-shrink:0;color:#696cff"><i class='bx bx-chevron-right' style="font-size:1.4rem"></i></div>
        </a>
      @endforeach
    </div>
  @endif

@else
  {{-- Default state: show recent invoices --}}
  @php
    $recent = \App\Models\JobCard::with('invoiceItems')
              ->whereNotNull('invoice_no')
              ->orderByDesc('id')->limit(10)->get();
    $pendingPayment = \App\Models\JobCard::with('invoiceItems')
              ->where('payment_received', false)
              ->where('status', 'Completed')
              ->orderByDesc('id')->limit(5)->get();
  @endphp

  <div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
      @php $totalInvoiced = \App\Models\JobCard::whereNotNull('invoice_no')->count(); @endphp
      <div class="card text-center p-3" style="border-radius:14px;border:0;box-shadow:0 2px 12px rgba(0,0,0,.07)">
        <div style="font-size:2rem;font-weight:800;color:#696cff">{{ $totalInvoiced }}</div>
        <div style="font-size:.78rem;color:#888;font-weight:600;text-transform:uppercase;letter-spacing:.06em">Total Invoiced</div>
      </div>
    </div>
    <div class="col-sm-6 col-xl-3">
      @php $totalPaid = \App\Models\JobCard::where('payment_received', true)->count(); @endphp
      <div class="card text-center p-3" style="border-radius:14px;border:0;box-shadow:0 2px 12px rgba(0,0,0,.07)">
        <div style="font-size:2rem;font-weight:800;color:#71dd37">{{ $totalPaid }}</div>
        <div style="font-size:.78rem;color:#888;font-weight:600;text-transform:uppercase;letter-spacing:.06em">Paid</div>
      </div>
    </div>
    <div class="col-sm-6 col-xl-3">
      @php $unpaid = \App\Models\JobCard::where('payment_received', false)->where('status','Completed')->count(); @endphp
      <div class="card text-center p-3" style="border-radius:14px;border:0;box-shadow:0 2px 12px rgba(0,0,0,.07)">
        <div style="font-size:2rem;font-weight:800;color:#ff3e1d">{{ $unpaid }}</div>
        <div style="font-size:.78rem;color:#888;font-weight:600;text-transform:uppercase;letter-spacing:.06em">Awaiting Payment</div>
      </div>
    </div>
    <div class="col-sm-6 col-xl-3">
      @php $totalRevenue = \App\Models\JobCard::sum('paid_amount'); @endphp
      <div class="card text-center p-3" style="border-radius:14px;border:0;box-shadow:0 2px 12px rgba(0,0,0,.07)">
        <div style="font-size:1.5rem;font-weight:800;color:#696cff">Rs. {{ number_format($totalRevenue,0) }}</div>
        <div style="font-size:.78rem;color:#888;font-weight:600;text-transform:uppercase;letter-spacing:.06em">Total Collected</div>
      </div>
    </div>
  </div>

  @if($pendingPayment->count())
  <div class="card mb-4" style="border-radius:16px;border:0;box-shadow:0 2px 12px rgba(0,0,0,.07)">
    <div class="card-body p-4">
      <div class="d-flex align-items-center gap-2 mb-3">
        <i class='bx bx-time-five' style="font-size:1.2rem;color:#ff3e1d"></i>
        <span style="font-weight:700;font-size:.9rem">Completed Jobs Awaiting Payment</span>
      </div>
      <div class="d-flex flex-column gap-2">
        @foreach($pendingPayment as $job)
          @php $job->load('invoiceItems'); @endphp
          <a href="{{ route('admin.invoices.show', $job) }}" class="result-card" style="padding:12px 16px">
            <div class="result-icon" style="width:38px;height:38px;font-size:1.1rem"><i class='bx bx-file-blank'></i></div>
            <div style="flex:1">
              <div class="result-order">{{ $job->order_no }}</div>
              <div class="result-customer">{{ $job->customer_name }} · {{ $job->phone_no }}</div>
            </div>
            <div class="result-right">
              <div class="result-amount">Rs. {{ number_format($job->grand_total, 2) }}</div>
              <span class="badge-unpaid">Unpaid</span>
            </div>
            <i class='bx bx-chevron-right' style="color:#696cff;font-size:1.3rem"></i>
          </a>
        @endforeach
      </div>
    </div>
  </div>
  @endif

  @if($recent->count())
  <div class="card" style="border-radius:16px;border:0;box-shadow:0 2px 12px rgba(0,0,0,.07)">
    <div class="card-body p-4">
      <div class="d-flex align-items-center gap-2 mb-3">
        <i class='bx bx-history' style="font-size:1.2rem;color:#696cff"></i>
        <span style="font-weight:700;font-size:.9rem">Recent Invoices</span>
      </div>
      <div class="d-flex flex-column gap-2">
        @foreach($recent as $job)
          @php
            $grand  = $job->grand_total;
            $paid   = (float)$job->paid_amount;
            $balance= $job->balance;
            $payStatus = $paid >= $grand && $grand > 0 ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid');
          @endphp
          <a href="{{ route('admin.invoices.show', $job) }}" class="result-card" style="padding:12px 16px">
            <div class="result-icon" style="width:38px;height:38px;font-size:1.1rem"><i class='bx bx-file-blank'></i></div>
            <div style="flex:1">
              <div class="result-order">{{ $job->order_no }} <span style="font-size:.75rem;color:#aaa;font-weight:500">{{ $job->invoice_no }}</span></div>
              <div class="result-customer">{{ $job->customer_name }} · {{ $job->device_name }}</div>
            </div>
            <div class="result-right">
              <div class="result-amount">Rs. {{ number_format($grand, 2) }}</div>
              @if($payStatus === 'paid')
                <span class="badge-paid">✓ Paid</span>
              @elseif($payStatus === 'partial')
                <span class="badge-partial">⚡ Partial</span>
              @else
                <span class="badge-unpaid">● Unpaid</span>
              @endif
            </div>
            <i class='bx bx-chevron-right' style="color:#696cff;font-size:1.3rem"></i>
          </a>
        @endforeach
      </div>
    </div>
  </div>
  @endif

@endif
@endsection

@push('scripts')
<script>
// Live search on enter
document.getElementById('searchInput').addEventListener('keydown', function(e) {
  if (e.key === 'Enter') document.getElementById('searchForm').submit();
});
function appendSearch(val) {
  document.getElementById('searchInput').value = val;
  document.getElementById('searchForm').submit();
}
</script>
@endpush
