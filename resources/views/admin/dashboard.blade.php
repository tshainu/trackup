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

  /* Gradient borders using pseudo-element trick */
  .stat-card {
    position: relative;
    border: none !important;
    background: #fff;
    z-index: 0;
  }
  .stat-card::before {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: 16px;
    padding: 2.5px;
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    z-index: -1;
  }
  .card.border-c1::before { background: linear-gradient(135deg, #696cff, #a78bfa); }
  .card.border-c2::before { background: linear-gradient(135deg, #ffab00, #ff6f00); }
  .card.border-c3::before { background: linear-gradient(135deg, #03c3ec, #0ea5e9); }
  .card.border-c4::before { background: linear-gradient(135deg, #71dd37, #16a34a); }
  .card.border-c5::before { background: linear-gradient(135deg, #ff3e1d, #ff6b6b); }
  .card.border-c6::before { background: linear-gradient(135deg, #00ab55, #34d399); }
  .card.border-c7::before { background: linear-gradient(135deg, #8c57ff, #c084fc); }
  .card.border-c8::before { background: linear-gradient(135deg, #ff7d00, #fbbf24); }

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

{{-- ── Today's Delivery List ────────────────────────────────────── --}}
<div class="card mt-4" style="border-radius:16px; border:0; box-shadow:0 2px 12px rgba(0,0,0,0.07);">
  <div class="card-header d-flex justify-content-between align-items-center bg-white border-0 pt-4">
    <div>
      <h5 class="mb-0 fw-bold">
        <i class='bx bx-package me-1' style="color:#03c3ec;"></i>
        Today's Delivery List
      </h5>
      <small class="text-muted">{{ now()->format('l, d M Y') }}</small>
    </div>
    <span class="badge bg-label-info fs-6">{{ $todayDeliveries->count() }} orders</span>
  </div>

  @if($todayDeliveries->isEmpty())
    <div class="card-body text-center py-5">
      <i class='bx bx-inbox' style="font-size:3rem; color:#c8c9ca;"></i>
      <p class="text-muted mt-2 mb-0">No deliveries scheduled for today.</p>
    </div>
  @else
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Order No</th>
          <th>Customer</th>
          <th>Phone</th>
          <th>Device</th>
          <th>Amount</th>
          <th>Assigned To</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        @foreach($todayDeliveries as $i => $job)
        @php
          $badges = [
            'Pending'       => ['class' => 'bg-label-warning',  'icon' => 'bx-time-five',      'dot' => '#ffab00'],
            'In Progress'   => ['class' => 'bg-label-info',     'icon' => 'bx-loader-alt',     'dot' => '#03c3ec'],
            'Completed'     => ['class' => 'bg-label-success',  'icon' => 'bx-check-circle',   'dot' => '#71dd37'],
            'Not Completed' => ['class' => 'bg-label-danger',   'icon' => 'bx-x-circle',       'dot' => '#ff3e1d'],
          ];
          $b = $badges[$job->status] ?? ['class' => 'bg-label-secondary', 'icon' => 'bx-circle', 'dot' => '#aaa'];
        @endphp
        <tr>
          <td class="text-muted small">{{ $i + 1 }}</td>
          <td><span class="fw-semibold text-primary">{{ $job->order_no }}</span></td>
          <td>
            <div class="fw-semibold">{{ $job->customer_name }}</div>
            @if($job->customer_address)
              <small class="text-muted"><i class='bx bx-map-pin'></i> {{ Str::limit($job->customer_address, 30) }}</small>
            @endif
          </td>
          <td><small>{{ $job->phone_no }}</small></td>
          <td>
            <span class="fw-medium">{{ $job->device_name }}</span><br>
            <small class="text-muted">{{ $job->device_brand }}</small>
          </td>
          <td class="fw-semibold">Rs.{{ number_format($job->rupees, 0) }}</td>
          <td>
            @if($job->employee)
              <span class="badge bg-label-secondary">{{ $job->employee->name }}</span>
            @else
              <span class="text-muted small">—</span>
            @endif
          </td>
          <td>
            <span class="badge {{ $b['class'] }} d-inline-flex align-items-center gap-1">
              <i class='bx {{ $b['icon'] }}'></i>
              {{ $job->status ?: 'Pending' }}
            </span>
          </td>
          <td>
            <a href="{{ route('admin.jobcards.show', $job) }}" class="btn btn-sm btn-icon btn-outline-primary me-1" title="View">
              <i class='bx bx-show'></i>
            </a>
            <a href="{{ route('admin.jobcards.edit', $job) }}" class="btn btn-sm btn-icon btn-outline-secondary" title="Edit">
              <i class='bx bx-edit'></i>
            </a>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endif
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
