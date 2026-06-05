@extends('layouts.admin')
@section('title', 'CCTV Dashboard')

@push('styles')
<style>
.cctv-hero {
  background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
  border-radius: 16px; padding: 1.5rem 2rem; color: #fff;
  margin-bottom: 1.5rem; position: relative; overflow: hidden;
}
.cctv-hero::after {
  content: '\ec56'; font-family: 'boxicons';
  position: absolute; right: -10px; top: -20px;
  font-size: 9rem; opacity: .06; line-height: 1; pointer-events: none;
}
.stat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 1.5rem; }
@media (max-width: 991px) { .stat-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 575px) { .stat-grid { grid-template-columns: repeat(2, 1fr); } }
.stat-card {
  background: #fff; border-radius: 14px; padding: 16px 18px;
  display: flex; align-items: center; gap: 14px;
  box-shadow: 0 2px 12px rgba(0,0,0,.06); border-left: 4px solid transparent;
  transition: transform .15s, box-shadow .15s;
}
.stat-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,.1); }
.stat-icon { width: 46px; height: 46px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; }
.stat-num  { font-size: 1.65rem; font-weight: 800; line-height: 1.1; }
.stat-lbl  { font-size: .72rem; font-weight: 600; color: #8592a3; text-transform: uppercase; letter-spacing: .04em; margin-top: 1px; }
.sc-blue   { border-color: #696cff; } .sc-blue   .stat-icon { background: #eef0ff; color: #696cff; } .sc-blue   .stat-num { color: #696cff; }
.sc-orange { border-color: #fd7e14; } .sc-orange .stat-icon { background: #fff3e8; color: #fd7e14; } .sc-orange .stat-num { color: #fd7e14; }
.sc-green  { border-color: #28c76f; } .sc-green  .stat-icon { background: #e8faf0; color: #28c76f; } .sc-green  .stat-num { color: #28c76f; }
.sc-red    { border-color: #ea5455; } .sc-red    .stat-icon { background: #fdeaea; color: #ea5455; } .sc-red    .stat-num { color: #ea5455; }
.sc-purple { border-color: #8c57ff; } .sc-purple .stat-icon { background: #f3eeff; color: #8c57ff; } .sc-purple .stat-num { color: #8c57ff; }
.sc-teal   { border-color: #00cfe8; } .sc-teal   .stat-icon { background: #e0f9fc; color: #00a4b8; } .sc-teal   .stat-num { color: #00a4b8; }
.sc-yellow { border-color: #ffab00; } .sc-yellow .stat-icon { background: #fff8e6; color: #cc8800; } .sc-yellow .stat-num { color: #cc8800; }
.sc-dark   { border-color: #4b4b5a; } .sc-dark   .stat-icon { background: #f0f0f5; color: #4b4b5a; } .sc-dark   .stat-num { color: #4b4b5a; }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  {{-- Hero --}}
  <div class="cctv-hero">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
      <div>
        <h4 class="mb-1"><i class="bx bx-cctv me-2"></i>CCTV Operations Dashboard</h4>
        <p class="mb-0 opacity-75">Complete lifecycle management — from lead to maintenance</p>
      </div>
      <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('admin.cctv.leads.create') }}" class="btn btn-sm" style="background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.3);">
          <i class="bx bx-plus me-1"></i> New Lead
        </a>
        <a href="{{ route('admin.cctv.service-tickets.create') }}" class="btn btn-sm" style="background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.3);">
          <i class="bx bx-support me-1"></i> New Ticket
        </a>
      </div>
    </div>
  </div>

  {{-- Stats Grid --}}
  <div class="stat-grid">
    <a href="{{ route('admin.cctv.leads.index') }}" class="stat-card sc-blue text-decoration-none">
      <div class="stat-icon"><i class="bx bx-user-plus"></i></div>
      <div><div class="stat-num">{{ $stats['leads_new'] }}</div><div class="stat-lbl">New Leads</div></div>
    </a>
    <a href="{{ route('admin.cctv.projects.index') }}" class="stat-card sc-orange text-decoration-none">
      <div class="stat-icon"><i class="bx bx-hard-hat"></i></div>
      <div><div class="stat-num">{{ $stats['projects_active'] }}</div><div class="stat-lbl">Active Projects</div></div>
    </a>
    <a href="{{ route('admin.cctv.service-tickets.index') }}" class="stat-card sc-red text-decoration-none">
      <div class="stat-icon"><i class="bx bx-support"></i></div>
      <div><div class="stat-num">{{ $stats['tickets_open'] }}</div><div class="stat-lbl">Open Tickets</div></div>
    </a>
    <a href="{{ route('admin.cctv.amc.index') }}" class="stat-card sc-green text-decoration-none">
      <div class="stat-icon"><i class="bx bx-refresh"></i></div>
      <div><div class="stat-num">{{ $stats['amc_active'] }}</div><div class="stat-lbl">Active AMC</div></div>
    </a>
    <a href="{{ route('admin.cctv.repairs.index') }}" class="stat-card sc-purple text-decoration-none">
      <div class="stat-icon"><i class="bx bx-wrench"></i></div>
      <div><div class="stat-num">{{ $stats['repairs_pending'] }}</div><div class="stat-lbl">Repairs Pending</div></div>
    </a>
    <a href="{{ route('admin.cctv.assets.index') }}" class="stat-card sc-teal text-decoration-none">
      <div class="stat-icon"><i class="bx bx-server"></i></div>
      <div><div class="stat-num">{{ $stats['assets_total'] }}</div><div class="stat-lbl">Active Assets</div></div>
    </a>
    <a href="{{ route('admin.cctv.amc.index', ['tab'=>'active']) }}" class="stat-card sc-yellow text-decoration-none">
      <div class="stat-icon"><i class="bx bx-alarm"></i></div>
      <div><div class="stat-num">{{ $stats['amc_renewal_due'] }}</div><div class="stat-lbl">AMC Renewal Due</div></div>
    </a>
    <a href="{{ route('admin.cctv.inventory.index') }}" class="stat-card sc-dark text-decoration-none">
      <div class="stat-icon"><i class="bx bx-package"></i></div>
      <div><div class="stat-num">{{ $stats['low_stock'] }}</div><div class="stat-lbl">Low Stock Items</div></div>
    </a>
  </div>


  {{-- ── Line Charts ────────────────────────────────────────────── --}}
  <div class="row g-4 mb-4">
    {{-- Leads Chart --}}
    <div class="col-md-6">
      <div class="card" style="border-radius:14px;border:0;box-shadow:0 2px 12px rgba(0,0,0,.06);">
        <div class="card-header d-flex justify-content-between align-items-center bg-white border-0 pt-3 pb-0">
          <div>
            <h6 class="mb-0 fw-bold"><i class="bx bx-trending-up me-1 text-primary"></i>New Leads</h6>
            <small class="text-muted">Last 7 days</small>
          </div>
          <span><span class="d-inline-block me-1" style="width:10px;height:10px;border-radius:50%;background:#696cff;"></span><small>Leads</small></span>
        </div>
        <div class="card-body pt-2" style="height:220px;position:relative;">
          <canvas id="leadsLineChart"></canvas>
        </div>
      </div>
    </div>
    {{-- Tickets Chart --}}
    <div class="col-md-6">
      <div class="card" style="border-radius:14px;border:0;box-shadow:0 2px 12px rgba(0,0,0,.06);">
        <div class="card-header d-flex justify-content-between align-items-center bg-white border-0 pt-3 pb-0">
          <div>
            <h6 class="mb-0 fw-bold"><i class="bx bx-support me-1 text-danger"></i>Service Tickets</h6>
            <small class="text-muted">Last 7 days</small>
          </div>
          <span><span class="d-inline-block me-1" style="width:10px;height:10px;border-radius:50%;background:#ea5455;"></span><small>Tickets</small></span>
        </div>
        <div class="card-body pt-2" style="height:220px;position:relative;">
          <canvas id="ticketsLineChart"></canvas>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    {{-- Recent Leads --}}
    <div class="col-md-6">
      <div class="card h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h6 class="mb-0"><i class="bx bx-user-plus me-2 text-primary"></i>Recent Leads</h6>
          <a href="{{ route('admin.cctv.leads.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
        </div>
        <div class="card-body p-0">
          @forelse($recentLeads as $lead)
          <div class="d-flex align-items-center gap-3 px-3 py-2 border-bottom">
            <div class="flex-grow-1">
              <div class="fw-semibold" style="font-size:.9rem;">{{ $lead->customer_name }}</div>
              <div style="font-size:.78rem;color:#8592a3;">{{ $lead->lead_no }} · {{ $lead->mobile }}</div>
            </div>
            <div>
              @php
                $sc = ['New Lead'=>'bg-label-primary','Survey Scheduled'=>'bg-label-warning','Survey Completed'=>'bg-label-info','Estimation Sent'=>'bg-label-purple','Approved'=>'bg-label-success','Lost'=>'bg-label-danger'];
              @endphp
              <span class="badge {{ $sc[$lead->status] ?? 'bg-label-secondary' }}" style="font-size:.7rem;">{{ $lead->status }}</span>
            </div>
          </div>
          @empty
          <div class="text-center text-muted p-4">No leads yet. <a href="{{ route('admin.cctv.leads.create') }}">Add one</a></div>
          @endforelse
        </div>
      </div>
    </div>

    {{-- Recent Tickets --}}
    <div class="col-md-6">
      <div class="card h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h6 class="mb-0"><i class="bx bx-support me-2 text-danger"></i>Recent Service Tickets</h6>
          <a href="{{ route('admin.cctv.service-tickets.index') }}" class="btn btn-sm btn-outline-danger">View All</a>
        </div>
        <div class="card-body p-0">
          @forelse($recentTickets as $ticket)
          <div class="d-flex align-items-center gap-3 px-3 py-2 border-bottom">
            <div class="flex-grow-1">
              <div class="fw-semibold" style="font-size:.9rem;">{{ $ticket->customer_name }}</div>
              <div style="font-size:.78rem;color:#8592a3;">{{ $ticket->ticket_no }} · {{ $ticket->ticket_type }}</div>
            </div>
            <div>
              @php
                $sc2 = ['Open'=>'bg-label-danger','Assigned'=>'bg-label-warning','In Progress'=>'bg-label-info','Waiting Parts'=>'bg-label-orange','Completed'=>'bg-label-success','Closed'=>'bg-label-secondary'];
              @endphp
              <span class="badge {{ $sc2[$ticket->status] ?? 'bg-label-secondary' }}" style="font-size:.7rem;">{{ $ticket->status }}</span>
            </div>
          </div>
          @empty
          <div class="text-center text-muted p-4">No tickets yet.</div>
          @endforelse
        </div>
      </div>
    </div>

    {{-- Active Projects --}}
    <div class="col-md-6">
      <div class="card h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h6 class="mb-0"><i class="bx bx-hard-hat me-2 text-warning"></i>Recent Projects</h6>
          <a href="{{ route('admin.cctv.projects.index') }}" class="btn btn-sm btn-outline-warning">View All</a>
        </div>
        <div class="card-body p-0">
          @forelse($recentProjects as $proj)
          <div class="d-flex align-items-center gap-3 px-3 py-2 border-bottom">
            <div class="flex-grow-1">
              <div class="fw-semibold" style="font-size:.9rem;">{{ $proj->customer_name }}</div>
              <div style="font-size:.78rem;color:#8592a3;">{{ $proj->project_no }} · {{ $proj->address }}</div>
            </div>
            <div>
              <span class="badge bg-label-info" style="font-size:.7rem;">{{ $proj->stage }}</span>
            </div>
          </div>
          @empty
          <div class="text-center text-muted p-4">No projects yet.</div>
          @endforelse
        </div>
      </div>
    </div>

    {{-- AMC Renewal Due --}}
    <div class="col-md-6">
      <div class="card h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h6 class="mb-0"><i class="bx bx-alarm me-2 text-warning"></i>AMC Renewal Due (60 days)</h6>
          <a href="{{ route('admin.cctv.amc.index') }}" class="btn btn-sm btn-outline-secondary">View All</a>
        </div>
        <div class="card-body p-0">
          @forelse($upcomingAmc as $amc)
          <div class="d-flex align-items-center gap-3 px-3 py-2 border-bottom">
            <div class="flex-grow-1">
              <div class="fw-semibold" style="font-size:.9rem;">{{ $amc->customer_name }}</div>
              <div style="font-size:.78rem;color:#8592a3;">{{ $amc->amc_no }} · Expires {{ $amc->end_date?->format('d M Y') }}</div>
            </div>
            <div>
              @php $days = $amc->daysToExpiry(); @endphp
              <span class="badge {{ $days <= 7 ? 'bg-danger' : ($days <= 30 ? 'bg-warning' : 'bg-label-secondary') }}" style="font-size:.7rem;">
                {{ $days > 0 ? "{$days}d left" : 'Expired' }}
              </span>
            </div>
          </div>
          @empty
          <div class="text-center text-muted p-4">No renewals due soon.</div>
          @endforelse
        </div>
      </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function() {
  const labels = [];
  for (let i = 6; i >= 0; i--) {
    const d = new Date();
    d.setDate(d.getDate() - i);
    labels.push(d.toLocaleDateString('en-GB', { day: 'numeric', month: 'short' }));
  }

  const leadsData   = @json($leadsChart ?? []);
  const ticketsData = @json($ticketsChart ?? []);

  function makeChart(id, data, color) {
    const ctx = document.getElementById(id).getContext('2d');
    new Chart(ctx, {
      type: 'line',
      data: {
        labels,
        datasets: [{
          data,
          borderColor: color,
          backgroundColor: color + '22',
          borderWidth: 2.5,
          pointBackgroundColor: color,
          pointRadius: 4,
          tension: 0.45,
          fill: true,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: '#fff',
            borderColor: '#e0e0e0',
            borderWidth: 1,
            titleColor: '#444',
            bodyColor: '#555',
            padding: 10,
          }
        },
        scales: {
          x: { grid: { display: false }, ticks: { font: { size: 11 } } },
          y: {
            beginAtZero: true,
            ticks: { stepSize: 1, font: { size: 11 } },
            grid: { color: 'rgba(0,0,0,.05)' }
          }
        }
      }
    });
  }

  makeChart('leadsLineChart',   leadsData,   '#696cff');
  makeChart('ticketsLineChart', ticketsData, '#ea5455');
})();
</script>
@endpush
