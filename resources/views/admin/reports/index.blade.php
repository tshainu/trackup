@extends('layouts.admin')
@section('title', 'Reports')
@section('page-title', 'Reports & Analytics')
@section('breadcrumb')<li class="breadcrumb-item active">Reports</li>@endsection

@push('styles')
<style>
/* ── Layout ── */
.rpt-wrap { max-width: 1200px; margin: 0 auto; }

/* ── Hero ── */
.rpt-hero {
  background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
  border-radius: 20px; padding: 28px 32px; color: #fff;
  margin-bottom: 24px; position: relative; overflow: hidden;
  display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;
}
.rpt-hero::before {
  content: ''; position: absolute; right: -40px; top: -40px;
  width: 260px; height: 260px; border-radius: 50%;
  background: rgba(105,108,255,.12); pointer-events: none;
}
.rpt-hero::after {
  content: ''; position: absolute; right: 80px; bottom: -60px;
  width: 180px; height: 180px; border-radius: 50%;
  background: rgba(168,85,247,.10); pointer-events: none;
}
.rpt-hero h1 { font-size: 1.6rem; font-weight: 800; margin: 0 0 4px; }
.rpt-hero p  { margin: 0; opacity: .7; font-size: .88rem; }

/* ── Period pills ── */
.period-pills { display: flex; gap: 6px; flex-wrap: wrap; position: relative; z-index: 1; }
.period-pill {
  padding: 6px 16px; border-radius: 20px; font-size: .78rem; font-weight: 700;
  border: 1.5px solid rgba(255,255,255,.2); color: rgba(255,255,255,.7);
  background: rgba(255,255,255,.06); cursor: pointer; text-decoration: none;
  transition: all .15s;
}
.period-pill:hover { background: rgba(255,255,255,.14); color: #fff; border-color: rgba(255,255,255,.4); }
.period-pill.active { background: #696cff; border-color: #696cff; color: #fff; }

/* ── Report tabs ── */
.rpt-tabs {
  display: flex; gap: 4px; flex-wrap: wrap;
  background: #f4f4ff; border-radius: 16px; padding: 6px;
  margin-bottom: 22px;
}
.rpt-tab {
  flex: 1; min-width: 120px; padding: 10px 12px; border-radius: 12px;
  text-align: center; font-size: .78rem; font-weight: 700;
  color: #888; cursor: pointer; text-decoration: none; border: none;
  background: none; transition: all .15s; white-space: nowrap;
  display: flex; align-items: center; justify-content: center; gap: 6px;
}
.rpt-tab:hover { background: #ebebff; color: #696cff; }
.rpt-tab.active { background: #fff; color: #696cff; box-shadow: 0 2px 12px rgba(105,108,255,.15); }
.rpt-tab i { font-size: 1.1rem; }

/* ── Stat cards ── */
.stat-cards { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 14px; margin-bottom: 22px; }
.stat-card {
  background: #fff; border-radius: 14px; padding: 18px 16px;
  box-shadow: 0 2px 12px rgba(0,0,0,.06); border: 1px solid #f0f0ff;
  display: flex; flex-direction: column; gap: 4px;
}
.stat-card .sc-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; margin-bottom: 8px; }
.stat-card .sc-val  { font-size: 1.7rem; font-weight: 800; line-height: 1; }
.stat-card .sc-lbl  { font-size: .72rem; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; color: #aaa; }
.stat-card .sc-sub  { font-size: .78rem; color: #aaa; margin-top: 2px; }

/* ── Tables ── */
.rpt-table-wrap { background: #fff; border-radius: 16px; border: 1px solid #f0f0ff; box-shadow: 0 2px 12px rgba(0,0,0,.05); overflow: hidden; }
.rpt-table-head { padding: 18px 20px 14px; border-bottom: 1px solid #f0f0ff; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px; }
.rpt-table-head h3 { font-size: .9rem; font-weight: 800; margin: 0; color: #333; display: flex; align-items: center; gap: 8px; }
.rpt-table { width: 100%; border-collapse: collapse; font-size: .82rem; }
.rpt-table thead tr { background: #f8f8fc; }
.rpt-table thead th { padding: 10px 14px; text-align: left; font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; color: #696cff; white-space: nowrap; }
.rpt-table tbody td { padding: 10px 14px; border-bottom: 1px solid #f5f5ff; vertical-align: middle; }
.rpt-table tbody tr:last-child td { border-bottom: none; }
.rpt-table tbody tr:hover { background: #fafaff; }
.rpt-table .num { text-align: right; font-variant-numeric: tabular-nums; }
.rpt-table .mono { font-family: monospace; font-size: .78rem; }

/* ── Badges ── */
.st-badge { display: inline-block; border-radius: 20px; padding: 3px 10px; font-size: .7rem; font-weight: 700; }
.st-pending     { background: #fff3cd; color: #856404; }
.st-progress    { background: #cff4fc; color: #055160; }
.st-completed   { background: #d1e7dd; color: #0a3622; }
.st-broken      { background: #f8d7da; color: #842029; }
.st-not         { background: #f8d7da; color: #842029; }
.pay-paid       { background: #d1fae5; color: #065f46; border-radius: 20px; padding: 3px 10px; font-size: .7rem; font-weight: 700; }
.pay-partial    { background: #fef3c7; color: #92400e; border-radius: 20px; padding: 3px 10px; font-size: .7rem; font-weight: 700; }
.pay-unpaid     { background: #fee2e2; color: #991b1b; border-radius: 20px; padding: 3px 10px; font-size: .7rem; font-weight: 700; }

/* ── Empty state ── */
.rpt-empty { text-align: center; padding: 50px 20px; color: #bbb; }
.rpt-empty i { font-size: 3.5rem; display: block; margin-bottom: 12px; color: #d8d8ff; }
.rpt-empty p { font-size: .9rem; font-weight: 600; color: #aaa; margin: 0; }

/* ── Revenue bar ── */
.rev-bar-wrap { height: 8px; background: #f0f0ff; border-radius: 8px; overflow: hidden; }
.rev-bar      { height: 100%; border-radius: 8px; background: linear-gradient(90deg, #696cff, #a855f7); transition: width .4s; }

/* ── Staff card ── */
.staff-card { background: #fff; border-radius: 16px; border: 1px solid #f0f0ff; box-shadow: 0 2px 12px rgba(0,0,0,.05); padding: 20px; margin-bottom: 16px; }
.staff-avatar { width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #696cff22, #a855f722); display: flex; align-items: center; justify-content: center; font-size: 1.3rem; color: #696cff; flex-shrink: 0; }
.staff-metric { text-align: center; padding: 10px 14px; border-radius: 10px; }
.staff-metric .sm-val { font-size: 1.4rem; font-weight: 800; }
.staff-metric .sm-lbl { font-size: .68rem; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; color: #aaa; margin-top: 2px; }

/* ── Overdue badge ── */
.overdue-days { background: #fee2e2; color: #991b1b; border-radius: 8px; padding: 3px 8px; font-size: .72rem; font-weight: 700; }

/* ── Custom date ── */
.custom-date-row { display: flex; gap: 10px; align-items: flex-end; flex-wrap: wrap; margin-top: 10px; }

/* ── Print ── */
@media print {
  .rpt-hero, .rpt-tabs, .period-pills, .no-print, .custom-date-row { display: none !important; }
  .rpt-table-wrap { box-shadow: none; border: 1px solid #ccc; }
}
</style>
@endpush

@section('content')
<div class="rpt-wrap">

{{-- Hero + Period Picker --}}
<div class="rpt-hero">
  <div>
    <h1><i class='bx bx-bar-chart-alt-2' style="vertical-align:middle;margin-right:10px"></i>Reports & Analytics</h1>
    <p>
      @php
        $periodLabels = ['today'=>'Today','week'=>'This Week','month'=>'This Month','year'=>'This Year','custom'=>'Custom Range'];
        $fromFormatted = \Carbon\Carbon::parse($from)->format('d M Y');
        $toFormatted   = \Carbon\Carbon::parse($to)->format('d M Y');
      @endphp
      Showing: <strong>{{ $periodLabels[$period] ?? 'Custom' }}</strong>
      @if($period === 'custom' || $from !== $to)
        &nbsp;·&nbsp; {{ $fromFormatted }} → {{ $toFormatted }}
      @else
        &nbsp;·&nbsp; {{ $fromFormatted }}
      @endif
    </p>
  </div>
  <div class="period-pills">
    @foreach(['today'=>'Today','week'=>'This Week','month'=>'This Month','year'=>'This Year','custom'=>'Custom'] as $p=>$lbl)
      <a href="{{ route('admin.reports.index', array_merge(request()->except(['period','from','to']), ['period'=>$p, 'report'=>$report])) }}"
         class="period-pill {{ $period===$p ? 'active' : '' }}">{{ $lbl }}</a>
    @endforeach
  </div>
</div>

{{-- Custom date row --}}
@if($period === 'custom')
<div class="card no-print" style="border-radius:14px;border:0;box-shadow:0 2px 12px rgba(0,0,0,.07);margin-bottom:20px">
  <div class="card-body p-3">
    <form method="GET" action="{{ route('admin.reports.index') }}" class="custom-date-row">
      <input type="hidden" name="period" value="custom">
      <input type="hidden" name="report" value="{{ $report }}">
      <div>
        <label style="font-size:.75rem;font-weight:700;color:#888;display:block;margin-bottom:4px">FROM</label>
        <input type="date" name="from" value="{{ $from }}" class="form-control" style="border-radius:8px;width:160px">
      </div>
      <div>
        <label style="font-size:.75rem;font-weight:700;color:#888;display:block;margin-bottom:4px">TO</label>
        <input type="date" name="to" value="{{ $to }}" class="form-control" style="border-radius:8px;width:160px">
      </div>
      <button type="submit" class="btn btn-primary" style="border-radius:8px">Apply</button>
    </form>
  </div>
</div>
@endif

{{-- Report Tabs --}}
<div class="rpt-tabs no-print">
  @php
    $tabs = [
      'jobs'        => ['icon'=>'bx-clipboard','label'=>'Job Report'],
      'payment'     => ['icon'=>'bx-dollar-circle','label'=>'Payment'],
      'revenue'     => ['icon'=>'bx-line-chart','label'=>'Revenue'],
      'status'      => ['icon'=>'bx-pie-chart-alt-2','label'=>'Status'],
      'overdue'     => ['icon'=>'bx-alarm-exclamation','label'=>'Overdue'],
      'undelivered' => ['icon'=>'bx-package','label'=>'Undelivered'],
      'staff'       => ['icon'=>'bx-group','label'=>'Staff'],
    ];
  @endphp
  @foreach($tabs as $key => $tab)
    <a href="{{ route('admin.reports.index', array_merge(request()->except('report'), ['report'=>$key])) }}"
       class="rpt-tab {{ $report===$key ? 'active' : '' }}">
      <i class='bx {{ $tab['icon'] }}'></i>{{ $tab['label'] }}
    </a>
  @endforeach
</div>

{{-- ══════════════════════════════════
     1. JOB REPORT
     ══════════════════════════════════ --}}
@if($report === 'jobs')
<div class="stat-cards">
  @php
    $sc = [
      ['val'=>$jobSummary['total'],         'lbl'=>'Total Jobs',    'color'=>'#696cff','bg'=>'#ebebff','icon'=>'bx-clipboard'],
      ['val'=>$jobSummary['completed'],     'lbl'=>'Completed',     'color'=>'#059669','bg'=>'#d1fae5','icon'=>'bx-check-circle'],
      ['val'=>$jobSummary['in_progress'],   'lbl'=>'In Progress',   'color'=>'#0ea5e9','bg'=>'#e0f2fe','icon'=>'bx-loader-circle'],
      ['val'=>$jobSummary['pending'],       'lbl'=>'Pending',       'color'=>'#f59e0b','bg'=>'#fef3c7','icon'=>'bx-time'],
      ['val'=>$jobSummary['not_completed'], 'lbl'=>'Not Completed', 'color'=>'#ef4444','bg'=>'#fee2e2','icon'=>'bx-x-circle'],
      ['val'=>$jobSummary['broken'],        'lbl'=>'Broken',        'color'=>'#dc2626','bg'=>'#ffe4e6','icon'=>'bx-error'],
    ];
  @endphp
  @foreach($sc as $s)
  <div class="stat-card">
    <div class="sc-icon" style="background:{{ $s['bg'] }};color:{{ $s['color'] }}"><i class='bx {{ $s['icon'] }}'></i></div>
    <div class="sc-val" style="color:{{ $s['color'] }}">{{ $s['val'] }}</div>
    <div class="sc-lbl">{{ $s['lbl'] }}</div>
  </div>
  @endforeach
</div>

<div class="row g-3 mb-4">
  {{-- Top Devices --}}
  @if($jobSummary['by_device']->count())
  <div class="col-md-6">
    <div class="rpt-table-wrap">
      <div class="rpt-table-head"><h3><i class='bx bx-chip'></i> Top Devices</h3></div>
      <div style="padding:14px 20px">
        @php $maxDev = $jobSummary['by_device']->max() ?: 1; @endphp
        @foreach($jobSummary['by_device'] as $device => $cnt)
        <div style="margin-bottom:10px">
          <div style="display:flex;justify-content:space-between;font-size:.8rem;margin-bottom:3px">
            <span style="font-weight:600;color:#333">{{ $device ?: 'Unknown' }}</span>
            <span style="color:#696cff;font-weight:700">{{ $cnt }}</span>
          </div>
          <div class="rev-bar-wrap"><div class="rev-bar" style="width:{{ round($cnt/$maxDev*100) }}%"></div></div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
  @endif
  {{-- Top Brands --}}
  @if($jobSummary['by_brand']->count())
  <div class="col-md-6">
    <div class="rpt-table-wrap">
      <div class="rpt-table-head"><h3><i class='bx bx-purchase-tag'></i> Top Brands</h3></div>
      <div style="padding:14px 20px">
        @php $maxBr = $jobSummary['by_brand']->max() ?: 1; @endphp
        @foreach($jobSummary['by_brand'] as $brand => $cnt)
        <div style="margin-bottom:10px">
          <div style="display:flex;justify-content:space-between;font-size:.8rem;margin-bottom:3px">
            <span style="font-weight:600;color:#333">{{ $brand }}</span>
            <span style="color:#a855f7;font-weight:700">{{ $cnt }}</span>
          </div>
          <div class="rev-bar-wrap"><div class="rev-bar" style="width:{{ round($cnt/$maxBr*100) }}%;background:linear-gradient(90deg,#a855f7,#ec4899)"></div></div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
  @endif
</div>

<div class="rpt-table-wrap">
  <div class="rpt-table-head">
    <h3><i class='bx bx-list-ul'></i> All Jobs &nbsp;<span style="font-size:.75rem;font-weight:500;color:#aaa">{{ $jobs->count() }} records</span></h3>
    <button onclick="window.print()" class="btn btn-sm btn-outline-secondary no-print" style="border-radius:8px;font-size:.78rem"><i class='bx bx-printer me-1'></i>Print</button>
  </div>
  @if($jobs->isEmpty())
    <div class="rpt-empty"><i class='bx bx-search-alt'></i><p>No jobs in this period</p></div>
  @else
  <div style="overflow-x:auto">
    <table class="rpt-table">
      <thead><tr>
        <th>Order No.</th><th>Customer</th><th>Device</th><th>Brand</th>
        <th>Fault</th><th>Staff</th><th>Date</th><th>Status</th><th class="num">Charge</th>
      </tr></thead>
      <tbody>
        @foreach($jobs as $j)
        @php
          $stClass = match($j->status) {
            'Completed'=>'st-completed','In Progress'=>'st-progress',
            'Pending'=>'st-pending','Broken'=>'st-broken', default=>'st-not'
          };
        @endphp
        <tr>
          <td><a href="{{ route('admin.invoices.show',$j) }}" class="mono" style="color:#696cff;text-decoration:none">{{ $j->order_no }}</a></td>
          <td><div style="font-weight:600;font-size:.82rem">{{ $j->customer_name }}</div><div style="font-size:.72rem;color:#aaa">{{ $j->phone_no }}</div></td>
          <td>{{ $j->device_name }}</td>
          <td>{{ $j->device_brand ?: '—' }}</td>
          <td style="max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $j->device_fault ?: '—' }}</td>
          <td>{{ $j->employee?->name ?? '<span style="color:#ccc">Unassigned</span>' }}</td>
          <td style="white-space:nowrap">{{ $j->date?->format('d M Y') }}</td>
          <td><span class="st-badge {{ $stClass }}">{{ $j->status }}</span></td>
          <td class="num">Rs. {{ number_format($j->rupees, 2) }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endif
</div>

{{-- ══════════════════════════════════
     2. PAYMENT REPORT
     ══════════════════════════════════ --}}
@elseif($report === 'payment')
<div class="stat-cards">
  <div class="stat-card">
    <div class="sc-icon" style="background:#d1fae5;color:#059669"><i class='bx bx-dollar-circle'></i></div>
    <div class="sc-val" style="color:#059669">Rs.{{ number_format($paymentSummary['total_collected'],0) }}</div>
    <div class="sc-lbl">Total Collected</div>
  </div>
  <div class="stat-card">
    <div class="sc-icon" style="background:#ebebff;color:#696cff"><i class='bx bx-receipt'></i></div>
    <div class="sc-val" style="color:#696cff">{{ $paymentSummary['transactions'] }}</div>
    <div class="sc-lbl">Jobs with Payments</div>
  </div>
  <div class="stat-card">
    <div class="sc-icon" style="background:#d1fae5;color:#059669"><i class='bx bx-check-shield'></i></div>
    <div class="sc-val" style="color:#059669">{{ $paymentSummary['fully_paid_count'] }}</div>
    <div class="sc-lbl">Fully Paid</div>
  </div>
  <div class="stat-card">
    <div class="sc-icon" style="background:#fef3c7;color:#f59e0b"><i class='bx bx-credit-card'></i></div>
    <div class="sc-val" style="color:#f59e0b">{{ $paymentSummary['partial_count'] }}</div>
    <div class="sc-lbl">Partial</div>
  </div>
</div>

{{-- Per-transaction log --}}
@if($paymentLogs->isNotEmpty())
<div class="rpt-table-wrap mb-4">
  <div class="rpt-table-head">
    <h3><i class='bx bx-transfer'></i> Payment Transactions &nbsp;<span style="font-size:.75rem;font-weight:500;color:#aaa">{{ $paymentLogs->count() }} entries</span></h3>
    <button onclick="window.print()" class="btn btn-sm btn-outline-secondary no-print" style="border-radius:8px;font-size:.78rem"><i class='bx bx-printer me-1'></i>Print</button>
  </div>
  <div style="overflow-x:auto">
    <table class="rpt-table">
      <thead><tr><th>Date &amp; Time</th><th>Order No.</th><th>Customer</th><th>Device</th><th>Note</th><th class="num">Amount (Rs.)</th></tr></thead>
      <tbody>
        @foreach($paymentLogs as $log)
        <tr>
          <td style="white-space:nowrap">{{ $log->paid_at->format('d M Y, h:i A') }}</td>
          <td><a href="{{ route('admin.invoices.show',$log->jobCard) }}" class="mono" style="color:#696cff;text-decoration:none">{{ $log->jobCard?->order_no }}</a></td>
          <td>{{ $log->jobCard?->customer_name }}</td>
          <td>{{ $log->jobCard?->device_name }}</td>
          <td>{{ $log->note ?: '—' }}</td>
          <td class="num" style="font-weight:700;color:#059669">{{ number_format($log->amount,2) }}</td>
        </tr>
        @endforeach
        <tr style="background:#f8f8fc;font-weight:700">
          <td colspan="5" style="text-align:right;color:#696cff;font-size:.8rem">TOTAL</td>
          <td class="num" style="color:#696cff">{{ number_format($paymentLogs->sum('amount'),2) }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
@endif

{{-- Job-wise breakdown --}}
<div class="rpt-table-wrap">
  <div class="rpt-table-head"><h3><i class='bx bx-list-ol'></i> Job-wise Payment Breakdown</h3></div>
  @if($paymentJobs->isEmpty())
    <div class="rpt-empty"><i class='bx bx-dollar'></i><p>No payments recorded in this period</p></div>
  @else
  <div style="overflow-x:auto">
    <table class="rpt-table">
      <thead><tr>
        <th>Order No.</th><th>Customer</th><th>Device</th><th class="num">Grand Total</th>
        <th class="num">Paid</th><th class="num">Balance</th><th>Status</th>
      </tr></thead>
      <tbody>
        @foreach($paymentJobs as $j)
        @php
          $ps = $j->payment_received ? 'paid' : ($j->paid_amount > 0 ? 'partial' : 'unpaid');
        @endphp
        <tr>
          <td><a href="{{ route('admin.invoices.show',$j) }}" class="mono" style="color:#696cff;text-decoration:none">{{ $j->order_no }}</a></td>
          <td><div style="font-weight:600">{{ $j->customer_name }}</div><div style="font-size:.72rem;color:#aaa">{{ $j->phone_no }}</div></td>
          <td>{{ $j->device_name }}</td>
          <td class="num">{{ number_format($j->grand_total,2) }}</td>
          <td class="num" style="color:#059669;font-weight:700">{{ number_format($j->paid_amount,2) }}</td>
          <td class="num" style="color:{{ $j->balance>0 ? '#ef4444' : '#059669' }};font-weight:700">{{ number_format($j->balance,2) }}</td>
          <td><span class="pay-{{ $ps }}">{{ ucfirst($ps) }}</span></td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endif
</div>

{{-- ══════════════════════════════════
     3. REVENUE REPORT
     ══════════════════════════════════ --}}
@elseif($report === 'revenue')
<div class="stat-cards">
  @php
    $rsc = [
      ['val'=>'Rs.'.number_format($revenueSummary['grand_total_billed'],0), 'lbl'=>'Total Billed',    'color'=>'#696cff','bg'=>'#ebebff','icon'=>'bx-wallet'],
      ['val'=>'Rs.'.number_format($revenueSummary['total_collected'],0),   'lbl'=>'Total Collected', 'color'=>'#059669','bg'=>'#d1fae5','icon'=>'bx-dollar-circle'],
      ['val'=>'Rs.'.number_format($revenueSummary['outstanding'],0),       'lbl'=>'Outstanding',     'color'=>'#ef4444','bg'=>'#fee2e2','icon'=>'bx-error-circle'],
      ['val'=>'Rs.'.number_format($revenueSummary['fully_paid'],0),        'lbl'=>'Fully Paid',      'color'=>'#059669','bg'=>'#d1fae5','icon'=>'bx-check-circle'],
      ['val'=>'Rs.'.number_format($revenueSummary['partial_paid'],0),      'lbl'=>'Partial Paid',    'color'=>'#f59e0b','bg'=>'#fef3c7','icon'=>'bx-credit-card'],
      ['val'=>$revenueSummary['unpaid_count'],                             'lbl'=>'Unpaid Jobs',     'color'=>'#ef4444','bg'=>'#fee2e2','icon'=>'bx-x-circle'],
    ];
  @endphp
  @foreach($rsc as $s)
  <div class="stat-card">
    <div class="sc-icon" style="background:{{ $s['bg'] }};color:{{ $s['color'] }}"><i class='bx {{ $s['icon'] }}'></i></div>
    <div class="sc-val" style="color:{{ $s['color'] }};font-size:{{ strlen((string)$s['val'])>8?'1.1rem':'1.7rem' }}">{{ $s['val'] }}</div>
    <div class="sc-lbl">{{ $s['lbl'] }}</div>
  </div>
  @endforeach
</div>

{{-- Collection rate bar --}}
@php $collRate = $revenueSummary['grand_total_billed'] > 0 ? round($revenueSummary['total_collected'] / $revenueSummary['grand_total_billed'] * 100) : 0; @endphp
<div class="card mb-4" style="border-radius:16px;border:0;box-shadow:0 2px 12px rgba(0,0,0,.06);padding:20px 24px">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
    <span style="font-weight:700;font-size:.88rem">Collection Rate</span>
    <span style="font-size:1.3rem;font-weight:800;color:#696cff">{{ $collRate }}%</span>
  </div>
  <div style="height:12px;background:#f0f0ff;border-radius:8px;overflow:hidden">
    <div style="height:100%;width:{{ $collRate }}%;background:linear-gradient(90deg,#696cff,#059669);border-radius:8px;transition:width .5s"></div>
  </div>
  <div style="display:flex;justify-content:space-between;font-size:.72rem;color:#aaa;margin-top:6px">
    <span>Rs. 0</span><span>Rs. {{ number_format($revenueSummary['grand_total_billed'],0) }}</span>
  </div>
</div>

<div class="rpt-table-wrap">
  <div class="rpt-table-head">
    <h3><i class='bx bx-line-chart'></i> Revenue Detail &nbsp;<span style="font-size:.75rem;font-weight:500;color:#aaa">{{ $revenueJobs->count() }} jobs</span></h3>
    <button onclick="window.print()" class="btn btn-sm btn-outline-secondary no-print" style="border-radius:8px;font-size:.78rem"><i class='bx bx-printer me-1'></i>Print</button>
  </div>
  @if($revenueJobs->isEmpty())
    <div class="rpt-empty"><i class='bx bx-line-chart'></i><p>No jobs in this period</p></div>
  @else
  <div style="overflow-x:auto">
    <table class="rpt-table">
      <thead><tr>
        <th>Order No.</th><th>Customer</th><th>Device</th><th>Date</th>
        <th class="num">Billed</th><th class="num">Paid</th><th class="num">Balance</th><th>Pay Status</th>
      </tr></thead>
      <tbody>
        @foreach($revenueJobs as $j)
        @php $ps = $j->payment_received ? 'paid' : ($j->paid_amount > 0 ? 'partial' : 'unpaid'); @endphp
        <tr>
          <td><a href="{{ route('admin.invoices.show',$j) }}" class="mono" style="color:#696cff;text-decoration:none">{{ $j->order_no }}</a></td>
          <td>{{ $j->customer_name }}</td>
          <td>{{ $j->device_name }}</td>
          <td style="white-space:nowrap">{{ $j->date?->format('d M Y') }}</td>
          <td class="num">{{ number_format($j->grand_total,2) }}</td>
          <td class="num" style="color:#059669;font-weight:{{ $j->paid_amount>0?'700':'400' }}">{{ number_format($j->paid_amount,2) }}</td>
          <td class="num" style="color:{{ $j->balance>0?'#ef4444':'#059669' }};font-weight:700">{{ number_format($j->balance,2) }}</td>
          <td><span class="pay-{{ $ps }}">{{ ucfirst($ps) }}</span></td>
        </tr>
        @endforeach
        <tr style="background:#f8f8fc;font-weight:700;font-size:.82rem">
          <td colspan="4" style="text-align:right;color:#696cff">TOTALS</td>
          <td class="num" style="color:#696cff">{{ number_format($revenueSummary['grand_total_billed'],2) }}</td>
          <td class="num" style="color:#059669">{{ number_format($revenueSummary['total_collected'],2) }}</td>
          <td class="num" style="color:#ef4444">{{ number_format($revenueSummary['outstanding'],2) }}</td>
          <td></td>
        </tr>
      </tbody>
    </table>
  </div>
  @endif
</div>

{{-- ══════════════════════════════════
     4. STATUS REPORT
     ══════════════════════════════════ --}}
@elseif($report === 'status')
@php
  $allStatuses = ['Pending','In Progress','Completed','Not Completed','Broken'];
  $statusConfig = [
    'Pending'       => ['color'=>'#f59e0b','bg'=>'#fef3c7','icon'=>'bx-time'],
    'In Progress'   => ['color'=>'#0ea5e9','bg'=>'#e0f2fe','icon'=>'bx-loader-circle'],
    'Completed'     => ['color'=>'#059669','bg'=>'#d1fae5','icon'=>'bx-check-circle'],
    'Not Completed' => ['color'=>'#ef4444','bg'=>'#fee2e2','icon'=>'bx-x-circle'],
    'Broken'        => ['color'=>'#dc2626','bg'=>'#ffe4e6','icon'=>'bx-error'],
  ];
  $total = $statusJobs->count() ?: 1;
@endphp
<div class="stat-cards">
  @foreach($allStatuses as $st)
  @php $cnt = $statusGroups->get($st,collect())->count(); $cfg = $statusConfig[$st]; @endphp
  <div class="stat-card">
    <div class="sc-icon" style="background:{{ $cfg['bg'] }};color:{{ $cfg['color'] }}"><i class='bx {{ $cfg['icon'] }}'></i></div>
    <div class="sc-val" style="color:{{ $cfg['color'] }}">{{ $cnt }}</div>
    <div class="sc-lbl">{{ $st }}</div>
    <div style="margin-top:6px">
      <div class="rev-bar-wrap" style="height:5px"><div class="rev-bar" style="width:{{ $total>1?round($cnt/($statusJobs->count()?:1)*100):0 }}%;background:{{ $cfg['color'] }}"></div></div>
      <div style="font-size:.69rem;color:#aaa;margin-top:3px">{{ $total>1?round($cnt/($statusJobs->count()?:1)*100):0 }}% of jobs</div>
    </div>
  </div>
  @endforeach
</div>

@foreach($allStatuses as $st)
@php $grp = $statusGroups->get($st, collect()); @endphp
@if($grp->isNotEmpty())
<div class="rpt-table-wrap mb-4">
  <div class="rpt-table-head">
    <h3>
      <span class="st-badge {{ match($st){'Completed'=>'st-completed','In Progress'=>'st-progress','Pending'=>'st-pending','Broken'=>'st-broken',default=>'st-not'} }}" style="font-size:.78rem">{{ $st }}</span>
      &nbsp;<span style="font-size:.75rem;font-weight:500;color:#aaa">{{ $grp->count() }} jobs</span>
    </h3>
  </div>
  <div style="overflow-x:auto">
    <table class="rpt-table">
      <thead><tr><th>Order No.</th><th>Customer</th><th>Device</th><th>Staff</th><th>Date</th><th class="num">Charge</th><th>Pay</th></tr></thead>
      <tbody>
        @foreach($grp->sortByDesc('date') as $j)
        @php $ps = $j->payment_received ? 'paid' : ($j->paid_amount > 0 ? 'partial' : 'unpaid'); @endphp
        <tr>
          <td><a href="{{ route('admin.invoices.show',$j) }}" class="mono" style="color:#696cff;text-decoration:none">{{ $j->order_no }}</a></td>
          <td><div style="font-weight:600">{{ $j->customer_name }}</div><div style="font-size:.72rem;color:#aaa">{{ $j->phone_no }}</div></td>
          <td>{{ $j->device_name }}{{ $j->device_brand?' / '.$j->device_brand:'' }}</td>
          <td>{{ $j->employee?->name ?? '—' }}</td>
          <td>{{ $j->date?->format('d M Y') }}</td>
          <td class="num">{{ number_format($j->rupees,2) }}</td>
          <td><span class="pay-{{ $ps }}">{{ ucfirst($ps) }}</span></td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif
@endforeach
@if($statusJobs->isEmpty())
  <div class="rpt-table-wrap"><div class="rpt-empty"><i class='bx bx-pie-chart-alt-2'></i><p>No jobs in this period</p></div></div>
@endif

{{-- ══════════════════════════════════
     5. DELIVERY OVERDUE
     ══════════════════════════════════ --}}
@elseif($report === 'overdue')
<div class="stat-cards">
  <div class="stat-card">
    <div class="sc-icon" style="background:#fee2e2;color:#ef4444"><i class='bx bx-alarm-exclamation'></i></div>
    <div class="sc-val" style="color:#ef4444">{{ $overdueJobs->count() }}</div>
    <div class="sc-lbl">Overdue Jobs</div>
  </div>
  <div class="stat-card">
    <div class="sc-icon" style="background:#fef3c7;color:#f59e0b"><i class='bx bx-time-five'></i></div>
    @php $maxDays = $overdueJobs->map(fn($j)=>now()->diffInDays($j->estimated_delivery))->max() @endphp
    <div class="sc-val" style="color:#f59e0b">{{ $maxDays ?? 0 }}</div>
    <div class="sc-lbl">Max Days Overdue</div>
  </div>
  <div class="stat-card">
    <div class="sc-icon" style="background:#fee2e2;color:#ef4444"><i class='bx bx-dollar'></i></div>
    <div class="sc-val" style="color:#ef4444;font-size:1.1rem">Rs.{{ number_format($overdueJobs->sum(fn($j)=>$j->balance),0) }}</div>
    <div class="sc-lbl">Outstanding on Overdue</div>
  </div>
</div>
<div class="rpt-table-wrap">
  <div class="rpt-table-head">
    <h3><i class='bx bx-alarm-exclamation' style="color:#ef4444"></i> Delivery Overdue <span style="font-size:.75rem;font-weight:500;color:#aaa">— estimated delivery date passed</span></h3>
    <button onclick="window.print()" class="btn btn-sm btn-outline-secondary no-print" style="border-radius:8px;font-size:.78rem"><i class='bx bx-printer me-1'></i>Print</button>
  </div>
  @if($overdueJobs->isEmpty())
    <div class="rpt-empty"><i class='bx bx-check-circle' style="color:#d1fae5"></i><p style="color:#059669">No overdue deliveries!</p></div>
  @else
  <div style="overflow-x:auto">
    <table class="rpt-table">
      <thead><tr>
        <th>Order No.</th><th>Customer</th><th>Device</th><th>Fault</th>
        <th>Staff</th><th>Est. Delivery</th><th>Days Overdue</th><th>Status</th><th class="num">Balance</th>
      </tr></thead>
      <tbody>
        @foreach($overdueJobs as $j)
        @php $daysOver = now()->diffInDays($j->estimated_delivery); @endphp
        <tr>
          <td><a href="{{ route('admin.invoices.show',$j) }}" class="mono" style="color:#696cff;text-decoration:none">{{ $j->order_no }}</a></td>
          <td><div style="font-weight:600">{{ $j->customer_name }}</div><div style="font-size:.72rem;color:#aaa">{{ $j->phone_no }}</div></td>
          <td>{{ $j->device_name }}</td>
          <td>{{ $j->device_fault ?: '—' }}</td>
          <td>{{ $j->employee?->name ?? '—' }}</td>
          <td style="white-space:nowrap;color:#ef4444;font-weight:600">{{ $j->estimated_delivery->format('d M Y') }}</td>
          <td><span class="overdue-days">{{ $daysOver }} day{{ $daysOver != 1?'s':'' }}</span></td>
          <td>
            @php $stClass = match($j->status){'Completed'=>'st-completed','In Progress'=>'st-progress','Pending'=>'st-pending','Broken'=>'st-broken',default=>'st-not'}; @endphp
            <span class="st-badge {{ $stClass }}">{{ $j->status }}</span>
          </td>
          <td class="num" style="color:{{ $j->balance>0?'#ef4444':'#059669' }};font-weight:700">Rs.{{ number_format($j->balance,2) }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endif
</div>

{{-- ══════════════════════════════════
     6. UNDELIVERED ITEMS
     ══════════════════════════════════ --}}
@elseif($report === 'undelivered')
<div class="stat-cards">
  <div class="stat-card">
    <div class="sc-icon" style="background:#ebebff;color:#696cff"><i class='bx bx-package'></i></div>
    <div class="sc-val" style="color:#696cff">{{ $undeliveredSummary['total'] }}</div>
    <div class="sc-lbl">Completed, Not Delivered</div>
  </div>
  <div class="stat-card">
    <div class="sc-icon" style="background:#d1fae5;color:#059669"><i class='bx bx-check-shield'></i></div>
    <div class="sc-val" style="color:#059669">{{ $undeliveredSummary['paid'] }}</div>
    <div class="sc-lbl">Fully Paid</div>
  </div>
  <div class="stat-card">
    <div class="sc-icon" style="background:#fee2e2;color:#ef4444"><i class='bx bx-error-circle'></i></div>
    <div class="sc-val" style="color:#ef4444">{{ $undeliveredSummary['outstanding'] }}</div>
    <div class="sc-lbl">With Outstanding</div>
  </div>
  <div class="stat-card">
    <div class="sc-icon" style="background:#fee2e2;color:#ef4444"><i class='bx bx-dollar'></i></div>
    <div class="sc-val" style="color:#ef4444;font-size:1.1rem">Rs.{{ number_format($undeliveredSummary['amount_due'],0) }}</div>
    <div class="sc-lbl">Total Amount Due</div>
  </div>
</div>
<div class="rpt-table-wrap">
  <div class="rpt-table-head">
    <h3><i class='bx bx-package'></i> Undelivered Completed Items
      <span style="font-size:.75rem;font-weight:400;color:#aaa">— repaired but not handed over</span>
    </h3>
    <button onclick="window.print()" class="btn btn-sm btn-outline-secondary no-print" style="border-radius:8px;font-size:.78rem"><i class='bx bx-printer me-1'></i>Print</button>
  </div>
  @if($undeliveredJobs->isEmpty())
    <div class="rpt-empty"><i class='bx bx-check-circle' style="color:#d1fae5"></i><p style="color:#059669">All completed items have been delivered!</p></div>
  @else
  <div style="overflow-x:auto">
    <table class="rpt-table">
      <thead><tr>
        <th>Order No.</th><th>Customer</th><th>Phone</th><th>Device</th>
        <th>Completed Date</th><th>Staff</th><th class="num">Total</th><th class="num">Paid</th><th class="num">Balance</th><th>Pay</th>
      </tr></thead>
      <tbody>
        @foreach($undeliveredJobs as $j)
        @php $ps = $j->payment_received ? 'paid' : ($j->paid_amount > 0 ? 'partial' : 'unpaid'); @endphp
        <tr>
          <td><a href="{{ route('admin.invoices.show',$j) }}" class="mono" style="color:#696cff;text-decoration:none">{{ $j->order_no }}</a></td>
          <td style="font-weight:600">{{ $j->customer_name }}</td>
          <td>{{ $j->phone_no }}</td>
          <td>{{ $j->device_name }}{{ $j->device_brand?' / '.$j->device_brand:'' }}</td>
          <td>{{ $j->updated_at?->format('d M Y') }}</td>
          <td>{{ $j->employee?->name ?? '—' }}</td>
          <td class="num">{{ number_format($j->grand_total,2) }}</td>
          <td class="num" style="color:#059669;font-weight:{{ $j->paid_amount>0?'700':'400' }}">{{ number_format($j->paid_amount,2) }}</td>
          <td class="num" style="color:{{ $j->balance>0?'#ef4444':'#059669' }};font-weight:700">{{ number_format($j->balance,2) }}</td>
          <td><span class="pay-{{ $ps }}">{{ ucfirst($ps) }}</span></td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endif
</div>

{{-- ══════════════════════════════════
     7. STAFF REPORT
     ══════════════════════════════════ --}}
@elseif($report === 'staff')
<div class="stat-cards">
  <div class="stat-card">
    <div class="sc-icon" style="background:#ebebff;color:#696cff"><i class='bx bx-group'></i></div>
    <div class="sc-val" style="color:#696cff">{{ $employees->count() }}</div>
    <div class="sc-lbl">Active Staff</div>
  </div>
  <div class="stat-card">
    <div class="sc-icon" style="background:#d1fae5;color:#059669"><i class='bx bx-clipboard'></i></div>
    <div class="sc-val" style="color:#059669">{{ $staffData->sum('total') }}</div>
    <div class="sc-lbl">Total Jobs (period)</div>
  </div>
  <div class="stat-card">
    <div class="sc-icon" style="background:#d1fae5;color:#059669"><i class='bx bx-check-circle'></i></div>
    <div class="sc-val" style="color:#059669">{{ $staffData->sum('completed') }}</div>
    <div class="sc-lbl">Completed</div>
  </div>
  <div class="stat-card">
    <div class="sc-icon" style="background:#fef3c7;color:#f59e0b"><i class='bx bx-help-circle'></i></div>
    <div class="sc-val" style="color:#f59e0b">{{ $staffData->sum('need_assist') }}</div>
    <div class="sc-lbl">Need Assistance</div>
  </div>
  <div class="stat-card">
    <div class="sc-icon" style="background:#fee2e2;color:#ef4444"><i class='bx bx-error'></i></div>
    <div class="sc-val" style="color:#ef4444">{{ $staffData->sum('broken') }}</div>
    <div class="sc-lbl">Marked Broken</div>
  </div>
</div>

@foreach($staffData as $data)
@if($data['total'] > 0)
<div class="staff-card">
  <div class="d-flex align-items-center gap-3 mb-4 flex-wrap">
    <div class="staff-avatar"><i class='bx bx-user'></i></div>
    <div style="flex:1">
      <div style="font-size:1.05rem;font-weight:800;color:#333">{{ $data['employee']->name }}</div>
      <div style="font-size:.78rem;color:#aaa">{{ $data['employee']->role ?? 'Technician' }}</div>
    </div>
    <div class="d-flex gap-3 flex-wrap">
      @foreach([
        ['val'=>$data['total'],      'lbl'=>'Total',      'color'=>'#696cff','bg'=>'#ebebff'],
        ['val'=>$data['completed'],  'lbl'=>'Completed',  'color'=>'#059669','bg'=>'#d1fae5'],
        ['val'=>$data['in_progress'],'lbl'=>'In Progress','color'=>'#0ea5e9','bg'=>'#e0f2fe'],
        ['val'=>$data['pending'],    'lbl'=>'Pending',    'color'=>'#f59e0b','bg'=>'#fef3c7'],
        ['val'=>$data['broken'],     'lbl'=>'Broken',     'color'=>'#ef4444','bg'=>'#fee2e2'],
        ['val'=>$data['need_assist'],'lbl'=>'Need Assist','color'=>'#8b5cf6','bg'=>'#ede9fe'],
      ] as $m)
      <div class="staff-metric" style="background:{{ $m['bg'] }}">
        <div class="sm-val" style="color:{{ $m['color'] }}">{{ $m['val'] }}</div>
        <div class="sm-lbl">{{ $m['lbl'] }}</div>
      </div>
      @endforeach
    </div>
  </div>

  {{-- Jobs table --}}
  @if($data['jobs']->isNotEmpty())
  <div style="overflow-x:auto">
    <table class="rpt-table" style="font-size:.78rem">
      <thead><tr>
        <th>Order No.</th><th>Customer</th><th>Device</th><th>Fault</th>
        <th>Date</th><th>Status</th><th>Need Assist</th><th class="num">Charge</th>
      </tr></thead>
      <tbody>
        @foreach($data['jobs'] as $j)
        @php $stClass = match($j->status){'Completed'=>'st-completed','In Progress'=>'st-progress','Pending'=>'st-pending','Broken'=>'st-broken',default=>'st-not'}; @endphp
        <tr>
          <td><a href="{{ route('admin.invoices.show',$j) }}" class="mono" style="color:#696cff;text-decoration:none">{{ $j->order_no }}</a></td>
          <td>{{ $j->customer_name }}</td>
          <td>{{ $j->device_name }}</td>
          <td>{{ $j->device_fault ?: '—' }}</td>
          <td style="white-space:nowrap">{{ $j->date?->format('d M Y') }}</td>
          <td><span class="st-badge {{ $stClass }}" style="font-size:.68rem">{{ $j->status }}</span></td>
          <td>
            @if($j->need_assistant)
              <span style="background:#ede9fe;color:#7c3aed;border-radius:20px;padding:2px 8px;font-size:.68rem;font-weight:700">⚑ Yes</span>
            @else
              <span style="color:#ccc;font-size:.75rem">—</span>
            @endif
          </td>
          <td class="num">{{ number_format($j->rupees,2) }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endif
</div>
@endif
@endforeach

@if($unassignedJobs->isNotEmpty())
<div class="rpt-table-wrap">
  <div class="rpt-table-head"><h3><i class='bx bx-user-x' style="color:#aaa"></i> Unassigned Jobs &nbsp;<span style="font-size:.75rem;color:#aaa">{{ $unassignedJobs->count() }}</span></h3></div>
  <div style="overflow-x:auto">
    <table class="rpt-table">
      <thead><tr><th>Order No.</th><th>Customer</th><th>Device</th><th>Date</th><th>Status</th></tr></thead>
      <tbody>
        @foreach($unassignedJobs as $j)
        <tr>
          <td><a href="{{ route('admin.invoices.show',$j) }}" class="mono" style="color:#696cff;text-decoration:none">{{ $j->order_no }}</a></td>
          <td>{{ $j->customer_name }}</td>
          <td>{{ $j->device_name }}</td>
          <td>{{ $j->date?->format('d M Y') }}</td>
          <td><span class="st-badge st-pending">{{ $j->status }}</span></td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif

@if($staffData->every(fn($d)=>$d['total']===0) && $unassignedJobs->isEmpty())
  <div class="rpt-table-wrap"><div class="rpt-empty"><i class='bx bx-group'></i><p>No staff activity in this period</p></div></div>
@endif

@endif {{-- end report switch --}}

</div>{{-- .rpt-wrap --}}
@endsection

@push('scripts')
<script>
// Highlight active tab on click (instant feedback)
document.querySelectorAll('.rpt-tab').forEach(tab => {
  tab.addEventListener('click', function() {
    document.querySelectorAll('.rpt-tab').forEach(t => t.classList.remove('active'));
    this.classList.add('active');
  });
});
</script>
@endpush
