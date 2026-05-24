@extends('layouts.admin')
@section('title', 'Field Complaints')

@push('styles')
<style>
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
      <h4><i class="bx bx-map-pin me-2"></i>Field Complaints</h4>
      <p>On-site repair &amp; service requests</p>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
      <span class="stat-chip">{{ $counts['all'] }} Total</span>
      <span class="stat-chip">{{ $counts['pending'] }} Pending</span>
      <span class="stat-chip">{{ $counts['inprogress'] }} In Progress</span>
      <a href="{{ route('admin.field-complaints.create') }}"
         class="btn btn-light fw-bold ms-2"
         style="border-radius:10px;color:#696cff;">
        <i class="bx bx-plus me-1"></i>New Complaint
      </a>
    </div>
  </div>

  @if(session('success'))
  <div class="alert alert-success alert-dismissible mb-4" role="alert">
    <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif

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
                   placeholder="Complaint#, name, phone…"
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
            <th class="ps-4">Complaint #</th>
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
          @endphp
          <tr>
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
