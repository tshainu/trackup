@extends('layouts.admin')
@section('title', 'Field Tickets')

@push('styles')
<style>
  .stat-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-bottom: 1.5rem;
  }
  @media (max-width: 991px) { .stat-grid { grid-template-columns: repeat(2, 1fr); } }
  @media (max-width: 575px)  { .stat-grid { grid-template-columns: repeat(2, 1fr); } }

  .stat-card {
    background: #fff;
    border-radius: 14px;
    padding: 16px 18px;
    display: flex;
    align-items: center;
    gap: 14px;
    box-shadow: 0 2px 12px rgba(0,0,0,.06);
    border-left: 4px solid transparent;
    transition: transform .15s, box-shadow .15s;
  }
  .stat-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,.1); }

  .stat-icon {
    width: 46px; height: 46px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.35rem;
    flex-shrink: 0;
  }
  .stat-num  { font-size: 1.65rem; font-weight: 800; line-height: 1.1; }
  .stat-lbl  { font-size: .72rem; font-weight: 600; color: #8592a3; text-transform: uppercase; letter-spacing: .04em; margin-top: 1px; }
  .stat-sub  { font-size: .7rem; color: #adb5bd; margin-top: 2px; }

  /* color variants */
  .sc-blue   { border-color: #696cff; }
  .sc-blue   .stat-icon { background: #eef0ff; color: #696cff; }
  .sc-blue   .stat-num  { color: #696cff; }

  .sc-orange { border-color: #fd7e14; }
  .sc-orange .stat-icon { background: #fff3e8; color: #fd7e14; }
  .sc-orange .stat-num  { color: #fd7e14; }

  .sc-purple { border-color: #8c57ff; }
  .sc-purple .stat-icon { background: #f3eeff; color: #8c57ff; }
  .sc-purple .stat-num  { color: #8c57ff; }

  .sc-green  { border-color: #28c76f; }
  .sc-green  .stat-icon { background: #e8faf0; color: #28c76f; }
  .sc-green  .stat-num  { color: #28c76f; }

  .sc-red    { border-color: #ea5455; }
  .sc-red    .stat-icon { background: #fdeaea; color: #ea5455; }
  .sc-red    .stat-num  { color: #ea5455; }

  .sc-teal   { border-color: #00cfe8; }
  .sc-teal   .stat-icon { background: #e0f9fc; color: #00a4b8; }
  .sc-teal   .stat-num  { color: #00a4b8; }

  .sc-dark   { border-color: #4b4b5a; }
  .sc-dark   .stat-icon { background: #f0f0f5; color: #4b4b5a; }
  .sc-dark   .stat-num  { color: #4b4b5a; }

  .sc-yellow { border-color: #ffab00; }
  .sc-yellow .stat-icon { background: #fff8e6; color: #cc8800; }
  .sc-yellow .stat-num  { color: #cc8800; }

  .fc-hero {
    background: linear-gradient(135deg, #696cff 0%, #8c57ff 100%);
    border-radius: 16px;
    padding: 1.5rem 2rem;
    color: #fff;
    margin-bottom: 1.5rem;
    position: relative;
    overflow: hidden;
  }
  .fc-hero::after {
    content: '\ec4e';
    font-family: 'boxicons';
    position: absolute;
    right: -10px; top: -20px;
    font-size: 9rem;
    opacity: .08;
    line-height: 1;
    pointer-events: none;
  }
  .fc-hero h4 { font-size: 1.4rem; font-weight: 700; margin-bottom: .25rem; }
  .fc-hero p  { opacity: .85; margin: 0; font-size: .9rem; }

  .stat-chip {
    background: rgba(255,255,255,.18);
    border-radius: 8px;
    padding: .35rem .85rem;
    font-size: .78rem;
    font-weight: 700;
    backdrop-filter: blur(4px);
    white-space: nowrap;
  }

  .fc-card {
    border-radius: 16px;
    border: 0;
    box-shadow: 0 2px 18px rgba(105,108,255,.08);
  }

  .nav-pills .nav-link {
    border-radius: 8px;
    font-size: .82rem;
    font-weight: 600;
    padding: .4rem .85rem;
    color: #697a8d;
    transition: all .15s;
  }
  .nav-pills .nav-link.active {
    background: linear-gradient(135deg, #696cff, #8c57ff);
    color: #fff;
    box-shadow: 0 4px 12px rgba(105,108,255,.35);
  }
  .nav-pills .nav-link:hover:not(.active) { background: #f0f0ff; color: #696cff; }

  .filter-strip {
    background: #f8f8fc;
    border-radius: 10px;
    padding: 12px 16px;
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
  }

  .table-hover tbody tr { transition: background .1s; }
  .table-hover tbody tr:hover { background: #f5f5ff; }

  .complaint-no {
    font-family: 'Courier New', monospace;
    font-size: .82rem;
    font-weight: 700;
    color: #696cff;
    letter-spacing: .03em;
  }

  .action-btn {
    width: 30px; height: 30px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
  }

  .priority-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 4px;
    flex-shrink: 0;
  }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  {{-- Hero header --}}
  <div class="fc-hero d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
      <h4><i class="bx bx-map-pin me-2"></i>Field Tickets</h4>
      <p>On-site repair &amp; service requests</p>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
      <span class="stat-chip">{{ $counts['all'] }} Total</span>
      <span class="stat-chip">{{ $counts['inprogress'] }} In Progress</span>
      @if($stats['overdue'] > 0)
      <span class="stat-chip" style="background:rgba(234,84,85,.3);">⚠ {{ $stats['overdue'] }} Overdue</span>
      @endif
      <a href="{{ route('admin.field-complaints.create') }}"
         class="btn btn-light fw-bold ms-2"
         style="border-radius:10px;color:#696cff;">
        <i class="bx bx-plus me-1"></i>New Ticket
      </a>
    </div>
  </div>

  @if(session('success'))
  <div class="alert alert-success alert-dismissible mb-4" role="alert">
    <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif

  {{-- ── Stat Grid ─────────────────────────────────────────────────────── --}}
  <div class="stat-grid">

    {{-- New Today --}}
    <div class="stat-card sc-blue">
      <div class="stat-icon"><i class="bx bx-plus-circle"></i></div>
      <div>
        <div class="stat-num">{{ $stats['new_today'] }}</div>
        <div class="stat-lbl">New Today</div>
        <div class="stat-sub">Received today</div>
      </div>
    </div>

    {{-- Pending / Awaiting --}}
    <div class="stat-card sc-orange">
      <div class="stat-icon"><i class="bx bx-time-five"></i></div>
      <div>
        <div class="stat-num">{{ $stats['pending'] }}</div>
        <div class="stat-lbl">Pending</div>
        <div class="stat-sub">Pending + Assigned</div>
      </div>
    </div>

    {{-- In Progress --}}
    <div class="stat-card sc-purple">
      <div class="stat-icon"><i class="bx bx-wrench"></i></div>
      <div>
        <div class="stat-num">{{ $stats['in_progress'] }}</div>
        <div class="stat-lbl">In Progress</div>
        <div class="stat-sub">Actively on-site</div>
      </div>
    </div>

    {{-- Overdue --}}
    <div class="stat-card sc-red">
      <div class="stat-icon"><i class="bx bx-error-circle"></i></div>
      <div>
        <div class="stat-num">{{ $stats['overdue'] }}</div>
        <div class="stat-lbl">Overdue</div>
        <div class="stat-sub">Missed schedule</div>
      </div>
    </div>

    {{-- Completed --}}
    <div class="stat-card sc-green">
      <div class="stat-icon"><i class="bx bx-check-circle"></i></div>
      <div>
        <div class="stat-num">{{ $stats['completed'] }}</div>
        <div class="stat-lbl">Done</div>
        <div class="stat-sub">Completed jobs</div>
      </div>
    </div>

    {{-- Billed --}}
    <div class="stat-card sc-teal">
      <div class="stat-icon"><i class="bx bx-receipt"></i></div>
      <div>
        <div class="stat-num">{{ $stats['billed'] }}</div>
        <div class="stat-lbl">Billed</div>
        <div class="stat-sub">Invoice issued</div>
      </div>
    </div>

    {{-- Revenue Collected --}}
    <div class="stat-card sc-dark">
      <div class="stat-icon"><i class="bx bx-money"></i></div>
      <div>
        <div class="stat-num">Rs.{{ number_format($stats['revenue'], 0) }}</div>
        <div class="stat-lbl">Collected</div>
        <div class="stat-sub">Total payments in</div>
      </div>
    </div>

    {{-- Outstanding --}}
    <div class="stat-card sc-yellow">
      <div class="stat-icon"><i class="bx bx-wallet-alt"></i></div>
      <div>
        <div class="stat-num">Rs.{{ number_format(max(0, $stats['outstanding']), 0) }}</div>
        <div class="stat-lbl">Outstanding</div>
        <div class="stat-sub">Balance to collect</div>
      </div>
    </div>

  </div>
  {{-- ── /Stat Grid ────────────────────────────────────────────────────── --}}

  <div class="card fc-card">

    {{-- Tabs + Search --}}
    <div class="card-header border-bottom pb-0 pt-3 px-4">
      @php
        $tabs = [
          'all'        => ['All',         $counts['all'],        'secondary'],
          'pending'    => ['Pending',     $counts['pending'],    'warning'],
          'assigned'   => ['Assigned',    $counts['assigned'],   'info'],
          'inprogress' => ['In Progress', $counts['inprogress'], 'primary'],
          'completed'  => ['Completed',   $counts['completed'],  'success'],
          'billed'     => ['Billed',      $counts['billed'],     'purple'],
        ];
      @endphp
      <ul class="nav nav-pills mb-3 gap-1 flex-wrap">
        @foreach($tabs as $key => [$label, $count, $color])
        <li class="nav-item">
          <a class="nav-link {{ $tab === $key ? 'active' : '' }}"
             href="{{ request()->fullUrlWithQuery(['tab'=>$key,'page'=>1]) }}">
            {{ $label }}
            <span class="badge ms-1 {{ $tab === $key ? 'bg-white text-dark' : 'bg-label-'.$color }}">{{ $count }}</span>
          </a>
        </li>
        @endforeach
      </ul>

      <div class="filter-strip mb-3">
        <form method="GET" class="d-flex align-items-center gap-2 flex-grow-1 flex-wrap">
          <input type="hidden" name="tab" value="{{ $tab }}">
          <div class="input-group" style="max-width:340px;">
            <span class="input-group-text bg-white border-end-0"><i class="bx bx-search text-muted"></i></span>
            <input type="text" name="q" value="{{ $search }}"
                   placeholder="Ticket#, name, phone…"
                   class="form-control border-start-0 ps-0" style="box-shadow:none;">
          </div>
          <button class="btn btn-primary btn-sm px-3">Search</button>
          @if($search)
          <a href="{{ route('admin.field-complaints.index',['tab'=>$tab]) }}"
             class="btn btn-sm btn-outline-secondary">Clear</a>
          @endif
        </form>
        @if($search)
        <span class="text-muted small">Results for "<strong>{{ $search }}</strong>"</span>
        @endif
      </div>
    </div>

    {{-- Table --}}
    <div class="card-datatable table-responsive">
      @if($complaints->isEmpty())
      <div class="text-center py-5 text-muted">
        <div style="width:64px;height:64px;border-radius:50%;background:#f0f0ff;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
          <i class="bx bx-clipboard" style="font-size:1.8rem;color:#c4c6ff;"></i>
        </div>
        <div class="fw-semibold mb-1">No complaints found</div>
        <div class="small">
          @if($search)
          Try a different search term
          @else
          No {{ $tab !== 'all' ? $tab : '' }} complaints yet
          @endif
        </div>
      </div>
      @else
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th class="ps-4">Ticket #</th>
            <th>Customer</th>
            <th>Service</th>
            <th>Assigned</th>
            <th>Status</th>
            <th>Priority</th>
            <th>Scheduled</th>
            <th class="text-end pe-4">Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($complaints as $fc)
          @php
            $statusBadge = [
              'Pending'    =>'warning','Assigned'=>'info','In Progress'=>'primary',
              'Completed'  =>'success','Billed'=>'purple','Cancelled'=>'danger',
            ][$fc->status] ?? 'secondary';
            $priBadge = ['Low'=>'secondary','Normal'=>'info','High'=>'warning','Urgent'=>'danger'][$fc->priority] ?? 'secondary';
            $priDot   = ['Low'=>'#8a8d93','Normal'=>'#03c3ec','High'=>'#ffab00','Urgent'=>'#ff3e1d'][$fc->priority] ?? '#8a8d93';
            $isOverdue = $fc->scheduled_date
                && $fc->scheduled_date->lt(now()->startOfDay())
                && !in_array($fc->status, ['Completed','Billed','Cancelled']);
          @endphp
          <tr style="{{ $isOverdue ? 'background:#fff5f5;' : '' }}">
            <td class="ps-4">
              <a href="{{ route('admin.field-complaints.show', $fc) }}" class="complaint-no d-block">
                {{ $fc->complaint_no }}
              </a>
              <div class="text-muted" style="font-size:.72rem;">{{ $fc->created_at->format('d M Y') }}</div>
            </td>
            <td>
              <div class="fw-semibold" style="font-size:.875rem;">{{ $fc->customer_name }}</div>
              <div class="text-muted small">{{ $fc->phone_no }}</div>
              @if($fc->gps_lat && $fc->gps_lng)
              <a href="{{ $fc->googleMapsUrl() }}" target="_blank"
                 class="small text-success d-inline-flex align-items-center gap-1 mt-1">
                <i class="bx bxs-map-pin"></i> GPS
              </a>
              @endif
            </td>
            <td>
              <span class="small {{ $fc->service_type_name ? 'fw-semibold' : 'text-muted' }}">
                {{ $fc->service_type_name ?: '—' }}
              </span>
            </td>
            <td>
              <span class="small {{ $fc->assignedEmployee ? '' : 'text-muted' }}">
                {{ $fc->assignedEmployee?->employee_name ?? '—' }}
              </span>
            </td>
            <td><span class="badge bg-label-{{ $statusBadge }}">{{ $fc->status }}</span></td>
            <td>
              <span class="d-inline-flex align-items-center small fw-semibold">
                <span class="priority-dot" style="background:{{ $priDot }};"></span>
                {{ $fc->priority }}
              </span>
            </td>
            <td class="small {{ $fc->scheduled_date ? '' : 'text-muted' }}">
              {{ $fc->scheduled_date?->format('d M Y') ?? '—' }}
              @if($isOverdue)
              <span class="badge bg-label-danger ms-1" style="font-size:.65rem;">Overdue</span>
              @endif
            </td>
            <td class="text-end pe-4">
              <a href="{{ route('admin.field-complaints.show', $fc) }}"
                 class="btn btn-sm btn-icon btn-outline-primary action-btn" title="View">
                <i class="bx bx-show"></i>
              </a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>

      @if($complaints->hasPages())
      <div class="px-4 py-3 border-top">{{ $complaints->links() }}</div>
      @endif
      @endif
    </div>
  </div>
</div>
@endsection
