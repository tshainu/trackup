@extends('layouts.admin')
@section('title', 'Dashboard')

@push('styles')
<style>
  .stat-card {
    border-radius: 16px;
    border: 2px solid transparent;
    background: #fff;
    transition: transform 0.18s, box-shadow 0.18s;
    cursor: default;
  }
  .stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 28px rgba(0,0,0,0.10);
  }
  .stat-card .card-body {
    padding: 1.4rem 1.5rem;
  }
  .stat-card .stat-icon {
    width: 60px !important;
    height: 60px !important;
    max-width: 60px !important;
    max-height: 60px !important;
    min-width: 60px;
    min-height: 60px;
    object-fit: contain;
    flex-shrink: 0;
    display: block;
  }
  .stat-card .stat-label {
    font-size: 0.82rem;
    color: #8a8d93;
    margin-bottom: 2px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  .stat-card .stat-value {
    font-size: 1.75rem;
    font-weight: 700;
    line-height: 1;
    margin: 0;
  }

  /* Coloured borders — forced over Sneat's card styles */
  .card.border-c1 { border: 2px solid #696cff !important; }
  .card.border-c2 { border: 2px solid #ffab00 !important; }
  .card.border-c3 { border: 2px solid #03c3ec !important; }
  .card.border-c4 { border: 2px solid #71dd37 !important; }
  .card.border-c5 { border: 2px solid #ff3e1d !important; }
  .card.border-c6 { border: 2px solid #00ab55 !important; }
  .card.border-c7 { border: 2px solid #8c57ff !important; }
  .card.border-c8 { border: 2px solid #ff7d00 !important; }

  .text-c1 { color: #696cff; }
  .text-c2 { color: #ffab00; }
  .text-c3 { color: #03c3ec; }
  .text-c4 { color: #71dd37; }
  .text-c5 { color: #ff3e1d; }
  .text-c6 { color: #00ab55; }
  .text-c7 { color: #8c57ff; }
  .text-c8 { color: #ff7d00; }

  /* Chart card */
  .chart-card {
    border-radius: 16px;
    border: 0;
    box-shadow: 0 2px 12px rgba(0,0,0,0.07);
  }
</style>
@endpush

@section('content')

{{-- ── Stat Cards ─────────────────────────────────────────────── --}}
<div class="row g-4 mb-4">

  {{-- Total Job Orders --}}
  <div class="col-6 col-xl-3">
    <div class="card stat-card border-c1 h-100">
      <div class="card-body d-flex align-items-center gap-3">
        <img src="{{ asset('assets/img/icons/job.gif') }}" class="stat-icon" width="60" height="60" alt="Total Jobs">
        <div>
          <p class="stat-label">Total Job Orders</p>
          <h4 class="stat-value text-c1">{{ $stats['total'] }}</h4>
        </div>
      </div>
    </div>
  </div>

  {{-- Pending --}}
  <div class="col-6 col-xl-3">
    <div class="card stat-card border-c2 h-100">
      <div class="card-body d-flex align-items-center gap-3">
        <img src="{{ asset('assets/img/icons/pending.gif') }}" class="stat-icon" width="60" height="60" alt="Pending">
        <div>
          <p class="stat-label">Pending</p>
          <h4 class="stat-value text-c2">{{ $stats['pending'] }}</h4>
        </div>
      </div>
    </div>
  </div>

  {{-- In Progress --}}
  <div class="col-6 col-xl-3">
    <div class="card stat-card border-c3 h-100">
      <div class="card-body d-flex align-items-center gap-3">
        <img src="{{ asset('assets/img/icons/in_progress.gif') }}" class="stat-icon" width="60" height="60" alt="In Progress">
        <div>
          <p class="stat-label">In Progress</p>
          <h4 class="stat-value text-c3">{{ $stats['in_progress'] }}</h4>
        </div>
      </div>
    </div>
  </div>

  {{-- Completed --}}
  <div class="col-6 col-xl-3">
    <div class="card stat-card border-c4 h-100">
      <div class="card-body d-flex align-items-center gap-3">
        <img src="{{ asset('assets/img/icons/completed.gif') }}" class="stat-icon" width="60" height="60" alt="Completed">
        <div>
          <p class="stat-label">Completed</p>
          <h4 class="stat-value text-c4">{{ $stats['completed'] }}</h4>
        </div>
      </div>
    </div>
  </div>

  {{-- Not Completed --}}
  <div class="col-6 col-xl-3">
    <div class="card stat-card border-c5 h-100">
      <div class="card-body d-flex align-items-center gap-3">
        <img src="{{ asset('assets/img/icons/not_completed.gif') }}" class="stat-icon" width="60" height="60" alt="Not Completed">
        <div>
          <p class="stat-label">Not Completed</p>
          <h4 class="stat-value text-c5">{{ $stats['not_completed'] }}</h4>
        </div>
      </div>
    </div>
  </div>

  {{-- Active Employees --}}
  <div class="col-6 col-xl-3">
    <div class="card stat-card border-c6 h-100">
      <div class="card-body d-flex align-items-center gap-3">
        <img src="{{ asset('assets/img/icons/employee.gif') }}" class="stat-icon" width="60" height="60" alt="Employees">
        <div>
          <p class="stat-label">Active Employees</p>
          <h4 class="stat-value text-c6">{{ $stats['employees'] }}</h4>
        </div>
      </div>
    </div>
  </div>

  {{-- Total Revenue --}}
  <div class="col-6 col-xl-3">
    <div class="card stat-card border-c7 h-100">
      <div class="card-body d-flex align-items-center gap-3">
        <img src="{{ asset('assets/img/icons/revenue.gif') }}" class="stat-icon" width="60" height="60" alt="Revenue">
        <div>
          <p class="stat-label">Total Revenue</p>
          <h4 class="stat-value text-c7" style="font-size:1.4rem;">Rs.{{ number_format($stats['revenue'], 0) }}</h4>
        </div>
      </div>
    </div>
  </div>

  {{-- Today's Jobs --}}
  <div class="col-6 col-xl-3">
    <div class="card stat-card border-c8 h-100">
      <div class="card-body d-flex align-items-center gap-3">
        <img src="{{ asset('assets/img/icons/today.gif') }}" class="stat-icon" width="60" height="60" alt="Today">
        <div>
          <p class="stat-label">Today's Orders</p>
          <h4 class="stat-value text-c8">{{ $stats['today'] }}</h4>
        </div>
      </div>
    </div>
  </div>

</div>

{{-- ── Line Chart ──────────────────────────────────────────────── --}}
<div class="card chart-card mb-4">
  <div class="card-header d-flex justify-content-between align-items-center bg-white border-0 pt-4 pb-0">
    <div>
      <h5 class="mb-1 fw-bold">Jobs Overview</h5>
      <small class="text-muted">Last 7 days — Total, Completed, Pending & Revenue</small>
    </div>
    <div class="d-flex gap-3 flex-wrap justify-content-end" style="font-size:0.8rem;">
      <span><span class="d-inline-block me-1" style="width:12px;height:12px;border-radius:50%;background:#696cff;"></span>Total</span>
      <span><span class="d-inline-block me-1" style="width:12px;height:12px;border-radius:50%;background:#71dd37;"></span>Completed</span>
      <span><span class="d-inline-block me-1" style="width:12px;height:12px;border-radius:50%;background:#ffab00;"></span>Pending</span>
      <span><span class="d-inline-block me-1" style="width:12px;height:12px;border-radius:50%;background:#8c57ff;"></span>Revenue (÷100)</span>
    </div>
  </div>
  <div class="card-body pt-3" style="height:280px; position:relative;">
    <canvas id="jobsLineChart"></canvas>
  </div>
</div>

{{-- ── Recent Job Orders Table ─────────────────────────────────── --}}
<div class="card" style="border-radius:16px; border:0; box-shadow:0 2px 12px rgba(0,0,0,0.07);">
  <div class="card-header d-flex justify-content-between align-items-center bg-white border-0 pt-4">
    <h5 class="mb-0 fw-bold"><i class='bx bx-list-ul me-1'></i> Recent Job Orders</h5>
    <a href="{{ route('admin.jobcards.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>Order No</th>
          <th>Customer</th>
          <th>Device</th>
          <th>Fault</th>
          <th>Date</th>
          <th>Amount</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        @foreach($recentJobs as $job)
        <tr>
          <td><span class="fw-semibold text-primary">{{ $job->order_no }}</span></td>
          <td>
            <div class="fw-semibold">{{ $job->customer_name }}</div>
            <small class="text-muted">{{ $job->phone_no }}</small>
          </td>
          <td>
            {{ $job->device_name }}<br>
            <small class="text-muted">{{ $job->device_brand }}</small>
          </td>
          <td><small>{{ Str::limit($job->device_fault, 25) }}</small></td>
          <td><small>{{ $job->date ? $job->date->format('d M Y') : '' }}</small></td>
          <td class="fw-semibold">Rs.{{ number_format($job->rupees, 0) }}</td>
          <td>
            @php
              $badges = [
                'Pending'       => 'bg-label-warning',
                'In Progress'   => 'bg-label-info',
                'Completed'     => 'bg-label-success',
                'Not Completed' => 'bg-label-danger',
              ];
            @endphp
            <span class="badge {{ $badges[$job->status] ?? 'bg-label-secondary' }}">{{ $job->status ?: 'Pending' }}</span>
          </td>
          <td>
            <a href="{{ route('admin.jobcards.show', $job) }}" class="btn btn-sm btn-icon btn-outline-primary me-1">
              <i class='bx bx-show'></i>
            </a>
            <a href="{{ route('admin.jobcards.edit', $job) }}" class="btn btn-sm btn-icon btn-outline-secondary">
              <i class='bx bx-edit'></i>
            </a>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
  // Build last-7-days labels
  const labels = [];
  for (let i = 6; i >= 0; i--) {
    const d = new Date();
    d.setDate(d.getDate() - i);
    labels.push(d.toLocaleDateString('en-GB', { day: 'numeric', month: 'short' }));
  }

  // Data from controller (passed as JSON)
  const chartData = @json($chartData ?? []);

  const totalData    = labels.map((_, i) => chartData[i]?.total    ?? 0);
  const completedData= labels.map((_, i) => chartData[i]?.completed?? 0);
  const pendingData  = labels.map((_, i) => chartData[i]?.pending  ?? 0);
  const revenueData  = labels.map((_, i) => (chartData[i]?.revenue ?? 0) / 100);

  const ctx = document.getElementById('jobsLineChart').getContext('2d');
  new Chart(ctx, {
    type: 'line',
    data: {
      labels,
      datasets: [
        {
          label: 'Total',
          data: totalData,
          borderColor: '#696cff',
          backgroundColor: 'rgba(105,108,255,0.10)',
          borderWidth: 2.5,
          pointBackgroundColor: '#696cff',
          pointRadius: 4,
          tension: 0.45,
          fill: true,
        },
        {
          label: 'Completed',
          data: completedData,
          borderColor: '#71dd37',
          backgroundColor: 'rgba(113,221,55,0.08)',
          borderWidth: 2.5,
          pointBackgroundColor: '#71dd37',
          pointRadius: 4,
          tension: 0.45,
          fill: false,
        },
        {
          label: 'Pending',
          data: pendingData,
          borderColor: '#ffab00',
          backgroundColor: 'rgba(255,171,0,0.08)',
          borderWidth: 2.5,
          pointBackgroundColor: '#ffab00',
          pointRadius: 4,
          tension: 0.45,
          fill: false,
        },
        {
          label: 'Revenue (÷100)',
          data: revenueData,
          borderColor: '#8c57ff',
          backgroundColor: 'rgba(140,87,255,0.07)',
          borderWidth: 2.5,
          pointBackgroundColor: '#8c57ff',
          pointRadius: 4,
          tension: 0.45,
          fill: false,
        },
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      interaction: { mode: 'index', intersect: false },
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: '#fff',
          borderColor: '#e0e0e0',
          borderWidth: 1,
          titleColor: '#444',
          bodyColor: '#555',
          padding: 10,
          callbacks: {
            label: function(ctx) {
              if (ctx.dataset.label === 'Revenue (÷100)') {
                return 'Revenue: Rs.' + (ctx.raw * 100).toLocaleString();
              }
              return ctx.dataset.label + ': ' + ctx.raw;
            }
          }
        }
      },
      scales: {
        x: {
          grid: { color: 'rgba(0,0,0,0.04)' },
          ticks: { color: '#8a8d93', font: { size: 11 } }
        },
        y: {
          beginAtZero: true,
          grid: { color: 'rgba(0,0,0,0.04)' },
          ticks: { color: '#8a8d93', font: { size: 11 }, precision: 0 }
        }
      }
    }
  });
})();
</script>
@endpush
