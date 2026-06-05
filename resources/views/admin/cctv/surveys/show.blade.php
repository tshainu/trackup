@extends('layouts.admin')
@section('title', 'Survey – ' . $survey->survey_no)

@push('styles')
<style>
  .hero-bar { background:linear-gradient(135deg,#00cfe8,#0090a8); border-radius:16px; padding:1.25rem 1.75rem; color:#fff; margin-bottom:1.5rem; display:flex; align-items:center; gap:1rem; flex-wrap:wrap; }
  .hero-bar .back-btn { width:38px; height:38px; border-radius:10px; background:rgba(255,255,255,.2); border:0; color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.2rem; text-decoration:none; transition:background .15s; }
  .hero-bar .back-btn:hover { background:rgba(255,255,255,.32); color:#fff; }
  .hero-bar h4 { margin:0; font-size:1.2rem; font-weight:700; }
  .section-card { border-radius:14px; border:0; box-shadow:0 2px 12px rgba(105,108,255,.08); margin-bottom:1.25rem; }
  .section-card .card-header { background:#f8f9fa; border-radius:14px 14px 0 0; padding:.85rem 1.25rem; font-weight:700; font-size:.875rem; display:flex; align-items:center; gap:.5rem; border-bottom:1px solid rgba(0,0,0,.06); }
  .sec-icon { width:28px; height:28px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:.9rem; flex-shrink:0; }
  .info-label { font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#a1acb8; margin-bottom:.25rem; }
  .info-value { font-size:.92rem; font-weight:500; color:#32325d; }
  .range-bar-wrap { position:relative; height:8px; background:#e9ecef; border-radius:10px; overflow:visible; }
  .range-bar-fill { height:8px; border-radius:10px; transition:width .3s; }
  .range-bar-label { position:absolute; right:0; top:-18px; font-size:.72rem; font-weight:700; }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  @php
    $sc = match(strtolower($survey->status)) {
        'completed'     => 'success',
        'need more time'=> 'warning',
        'cancelled'     => 'danger',
        default         => 'secondary'
    };
    $isSimple = $survey->survey_mode === 'Simple';
  @endphp

  {{-- Header --}}
  <div class="hero-bar">
    <a href="{{ route('admin.cctv.surveys.index') }}" class="back-btn"><i class="bx bx-arrow-back"></i></a>
    <div class="flex-grow-1">
      <h4>{{ $survey->survey_no }} — {{ $survey->customer_name }}</h4>
      <div style="opacity:.85;font-size:.85rem;">
        <span class="badge bg-label-{{ $sc }}">{{ $survey->status }}</span>
        <span class="badge bg-light text-dark ms-1">{{ $survey->survey_mode ?? 'Detailed' }}</span>
        <span class="badge bg-light text-dark ms-1">{{ $survey->survey_type ?? '' }}</span>
        @if($survey->survey_date)
          <span class="ms-2">{{ \Carbon\Carbon::parse($survey->survey_date)->format('d M Y') }}</span>
        @endif
      </div>
    </div>
    <a href="{{ route('admin.cctv.surveys.edit', $survey) }}" class="btn btn-light btn-sm">
      <i class="bx bx-edit me-1"></i> Edit
    </a>
  </div>

  @if(session('success'))
  <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
    <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif

  <div class="row g-3">
    {{-- LEFT COLUMN --}}
    <div class="col-lg-8">

      {{-- Customer Info --}}
      <div class="card section-card">
        <div class="card-header">
          <span class="sec-icon bg-primary bg-opacity-10 text-primary"><i class="bx bx-user"></i></span>
          Customer
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-sm-6"><div class="info-label">Name</div><div class="info-value">{{ $survey->customer_name }}</div></div>
            <div class="col-sm-6"><div class="info-label">Mobile</div><div class="info-value font-monospace">{{ $survey->mobile ?: '—' }}</div></div>
            @if(!$isSimple)
            <div class="col-sm-6"><div class="info-label">Contact Person</div><div class="info-value">{{ $survey->contact_person ?: '—' }}</div></div>
            <div class="col-sm-6"><div class="info-label">Alt Mobile</div><div class="info-value">{{ $survey->alt_mobile ?: '—' }}</div></div>
            <div class="col-sm-6"><div class="info-label">Email</div><div class="info-value">{{ $survey->email ?: '—' }}</div></div>
            <div class="col-sm-6"><div class="info-label">Customer Type</div><div class="info-value">{{ $survey->customer_type ?: '—' }}</div></div>
            @endif
            <div class="col-sm-6">
              <div class="info-label">GPS Location</div>
              @php $gps = $isSimple ? $survey->simple_gps_location : $survey->gps_location; @endphp
              @if($gps)
                @php [$lat,$lng] = array_map('trim', explode(',', $gps . ',')) @endphp
                <div class="info-value">
                  {{ $gps }}
                  <a href="https://maps.google.com/?q={{ urlencode($gps) }}" target="_blank" class="ms-1 text-primary small">↗ Map</a>
                </div>
              @else
                <div class="info-value">—</div>
              @endif
            </div>
          </div>
        </div>
      </div>

      @if($isSimple)
      {{-- ── SIMPLE SURVEY FINDINGS ── --}}
      <div class="card section-card">
        <div class="card-header">
          <span class="sec-icon bg-info bg-opacity-10 text-info"><i class="bx bx-camera"></i></span>
          Camera & Recorder
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-6 col-md-3">
              <div class="info-label">No. of Cameras</div>
              <div class="info-value fw-bold text-primary" style="font-size:1.4rem;">{{ $survey->simple_num_cameras ?? 0 }}</div>
            </div>
            <div class="col-6 col-md-3">
              <div class="info-label">DVR / NVR</div>
              <div class="info-value">{{ $survey->simple_dvr_nvr ?: '—' }}</div>
            </div>
            <div class="col-6 col-md-3">
              <div class="info-label">Channels</div>
              <div class="info-value">{{ $survey->simple_dvr_channels ? $survey->simple_dvr_channels.' CH' : '—' }}</div>
            </div>
            <div class="col-6 col-md-3">
              <div class="info-label">Internet</div>
              <div class="info-value">
                @if($survey->simple_internet_available)
                  <span class="badge bg-label-success">Available</span>
                  @if($survey->simple_isp) <span class="ms-1 text-muted small">{{ $survey->simple_isp }}</span> @endif
                @else
                  <span class="badge bg-label-secondary">Not Available</span>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card section-card">
        <div class="card-header">
          <span class="sec-icon bg-warning bg-opacity-10 text-warning"><i class="bx bx-slider"></i></span>
          Site Assessment
        </div>
        <div class="card-body">
          <div class="row g-4">
            <div class="col-md-6">
              <div class="d-flex justify-content-between mb-1">
                <span class="info-label mb-0">Cabling Easiness</span>
                <strong class="text-primary">{{ $survey->simple_cabling_ease ?? 5 }} / 10</strong>
              </div>
              <div class="range-bar-wrap">
                <div class="range-bar-fill bg-primary" style="width:{{ (($survey->simple_cabling_ease ?? 5) / 10) * 100 }}%"></div>
              </div>
              <div class="d-flex justify-content-between text-muted mt-1" style="font-size:.7rem;">
                <span>Very Difficult</span><span>Very Easy</span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="d-flex justify-content-between mb-1">
                <span class="info-label mb-0">Risk Level</span>
                @php $rl = $survey->simple_risk_level ?? 5; $rc = $rl >= 8 ? 'danger' : ($rl >= 5 ? 'warning' : 'success'); @endphp
                <strong class="text-{{ $rc }}">{{ $rl }} / 10</strong>
              </div>
              <div class="range-bar-wrap">
                <div class="range-bar-fill bg-{{ $rc }}" style="width:{{ ($rl / 10) * 100 }}%"></div>
              </div>
              <div class="d-flex justify-content-between text-muted mt-1" style="font-size:.7rem;">
                <span>Low Risk</span><span>High Risk</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card section-card">
        <div class="card-header">
          <span class="sec-icon bg-success bg-opacity-10 text-success"><i class="bx bx-time"></i></span>
          Work Estimation
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-6 col-md-4">
              <div class="info-label">No. of Technicians</div>
              <div class="info-value">{{ $survey->simple_num_technicians ?? 1 }}</div>
            </div>
            <div class="col-6 col-md-4">
              <div class="info-label">Estimated Days</div>
              <div class="info-value">{{ $survey->simple_estimated_days ?? 1 }} day(s)</div>
            </div>
          </div>
        </div>
      </div>

      @if($survey->simple_remark)
      <div class="card section-card">
        <div class="card-header">
          <span class="sec-icon bg-secondary bg-opacity-10 text-secondary"><i class="bx bx-note"></i></span>
          Remark
        </div>
        <div class="card-body">
          <p class="mb-0" style="white-space:pre-line;">{{ $survey->simple_remark }}</p>
        </div>
      </div>
      @endif

      @else
      {{-- ── DETAILED SURVEY FINDINGS ── --}}
      @if($survey->purposes && count($survey->purposes))
      <div class="card section-card">
        <div class="card-header">
          <span class="sec-icon bg-info bg-opacity-10 text-info"><i class="bx bx-target-lock"></i></span>
          Purposes
        </div>
        <div class="card-body d-flex flex-wrap gap-2">
          @foreach($survey->purposes as $p)
            <span class="badge bg-label-info">{{ $p }}</span>
          @endforeach
        </div>
      </div>
      @endif

      @if($survey->camera_locations && count($survey->camera_locations))
      <div class="card section-card">
        <div class="card-header">
          <span class="sec-icon bg-primary bg-opacity-10 text-primary"><i class="bx bx-camera"></i></span>
          Camera Locations ({{ count($survey->camera_locations) }})
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
              <thead class="table-light"><tr>
                <th>Location</th><th>In/Out</th><th>Type</th><th>MP</th><th>Night</th><th>Audio</th>
              </tr></thead>
              <tbody>
                @foreach($survey->camera_locations as $cam)
                <tr>
                  <td>{{ $cam['location'] }}</td>
                  <td><span class="badge bg-label-{{ $cam['indoor_outdoor']==='Indoor'?'primary':'warning' }}">{{ $cam['indoor_outdoor'] }}</span></td>
                  <td>{{ $cam['camera_type'] ?: '—' }}</td>
                  <td>{{ $cam['mp'] ?: '—' }}</td>
                  <td>{{ $cam['night_vision'] ? '✓' : '—' }}</td>
                  <td>{{ $cam['audio'] ? '✓' : '—' }}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
      @endif

      <div class="card section-card">
        <div class="card-header">
          <span class="sec-icon bg-warning bg-opacity-10 text-warning"><i class="bx bx-wifi"></i></span>
          Network & Power
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-sm-4"><div class="info-label">Internet</div><div class="info-value">{{ $survey->internet_status ?: '—' }}</div></div>
            <div class="col-sm-4"><div class="info-label">ISP</div><div class="info-value">{{ $survey->isp ?: '—' }}{{ $survey->isp==='Other' ? ' – '.$survey->isp_other : '' }}</div></div>
            <div class="col-sm-4"><div class="info-label">Wi-Fi Coverage</div><div class="info-value">{{ $survey->wifi_coverage ? 'Yes' : 'No' }}</div></div>
            <div class="col-sm-4"><div class="info-label">Power</div><div class="info-value">{{ $survey->power_availability ?: '—' }}</div></div>
            <div class="col-sm-4"><div class="info-label">UPS Required</div><div class="info-value">{{ $survey->ups_required ? 'Yes' : 'No' }}</div></div>
            <div class="col-sm-4"><div class="info-label">Electrical Work</div><div class="info-value">{{ $survey->electrical_work_required ? 'Yes' : 'No' }}</div></div>
          </div>
        </div>
      </div>

      <div class="card section-card">
        <div class="card-header">
          <span class="sec-icon bg-danger bg-opacity-10 text-danger"><i class="bx bx-hard-hat"></i></span>
          Installation
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-sm-4"><div class="info-label">Cable Route</div><div class="info-value">{{ $survey->cable_route ?: '—' }}</div></div>
            <div class="col-sm-4"><div class="info-label">Ceiling Type</div><div class="info-value">{{ $survey->ceiling_type ?: '—' }}</div></div>
            <div class="col-sm-4"><div class="info-label">Wall Type</div><div class="info-value">{{ $survey->wall_type ?: '—' }}</div></div>
            <div class="col-sm-4"><div class="info-label">Height Risk</div>
              <div class="info-value">{{ $survey->height_risk ?? 0 }}/10</div>
            </div>
            <div class="col-sm-4"><div class="info-label">Ladder</div><div class="info-value">{{ $survey->ladder_required ? 'Required' : 'No' }}</div></div>
            <div class="col-sm-4"><div class="info-label">Scaffolding</div><div class="info-value">{{ $survey->scaffolding_required ? 'Required' : 'No' }}</div></div>
          </div>
        </div>
      </div>

      <div class="card section-card">
        <div class="card-header">
          <span class="sec-icon bg-success bg-opacity-10 text-success"><i class="bx bx-package"></i></span>
          Material Estimation
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-sm-3"><div class="info-label">Cameras</div><div class="info-value">{{ $survey->cameras_qty ?? 0 }}</div></div>
            <div class="col-sm-3"><div class="info-label">DVR Channels</div><div class="info-value">{{ $survey->dvr_channels ?? 0 }}</div></div>
            <div class="col-sm-3"><div class="info-label">Cable (m)</div><div class="info-value">{{ $survey->cable_meters ?? 0 }}</div></div>
            <div class="col-sm-3"><div class="info-label">Storage</div><div class="info-value">{{ $survey->hdd_storage_days ?? 30 }} days</div></div>
          </div>
          @if($survey->accessories && count($survey->accessories))
          <hr>
          <div class="info-label mb-2">Accessories</div>
          <div class="d-flex flex-wrap gap-2">
            @foreach($survey->accessories as $acc)
              <span class="badge bg-label-secondary">{{ $acc['name'] }} × {{ $acc['qty'] }}</span>
            @endforeach
          </div>
          @endif
        </div>
      </div>

      @if($survey->special_notes)
      <div class="card section-card">
        <div class="card-header">
          <span class="sec-icon bg-secondary bg-opacity-10 text-secondary"><i class="bx bx-note"></i></span>
          Notes
        </div>
        <div class="card-body">
          <p class="mb-0" style="white-space:pre-line;">{{ $survey->special_notes }}</p>
        </div>
      </div>
      @endif
      @endif {{-- /detailed --}}

    </div>{{-- /col-lg-8 --}}

    {{-- RIGHT COLUMN --}}
    <div class="col-lg-4">
      <div class="card section-card">
        <div class="card-header">
          <span class="sec-icon bg-info bg-opacity-10 text-info"><i class="bx bx-info-circle"></i></span>
          Quick Info
        </div>
        <div class="card-body">
          <div class="mb-3">
            <div class="info-label">Survey No</div>
            <div class="fw-bold font-monospace text-primary" style="font-size:1rem;">{{ $survey->survey_no }}</div>
          </div>
          <div class="mb-3">
            <div class="info-label">Status</div>
            <span class="badge bg-label-{{ $sc }}">{{ $survey->status }}</span>
          </div>
          <div class="mb-3">
            <div class="info-label">Mode</div>
            <span class="badge bg-label-secondary">{{ $survey->survey_mode ?? 'Detailed' }}</span>
            <span class="badge bg-label-secondary ms-1">{{ $survey->survey_type ?? '' }}</span>
          </div>
          <div class="mb-3">
            <div class="info-label">Survey Date</div>
            <div class="info-value">{{ $survey->survey_date ? \Carbon\Carbon::parse($survey->survey_date)->format('d M Y') : '—' }}</div>
          </div>
          <div class="mb-3">
            <div class="info-label">Surveyed By</div>
            <div class="info-value">{{ $survey->technician?->employee_name ?? '—' }}</div>
          </div>
          <div class="mb-3">
            <div class="info-label">Created</div>
            <div class="info-value">{{ $survey->created_at->format('d M Y, h:i A') }}</div>
          </div>
          <hr>
          <div class="d-grid gap-2">
            <a href="{{ route('admin.cctv.surveys.edit', $survey) }}" class="btn btn-primary btn-sm">
              <i class="bx bx-edit me-1"></i> Edit Survey
            </a>
          </div>
        </div>
      </div>

      @if($isSimple && $survey->simple_internet_available && $survey->simple_isp)
      <div class="card section-card">
        <div class="card-header">
          <span class="sec-icon bg-info bg-opacity-10 text-info"><i class="bx bx-wifi"></i></span>
          Internet
        </div>
        <div class="card-body">
          <div class="mb-2"><div class="info-label">ISP</div><div class="info-value">{{ $survey->simple_isp }}</div></div>
        </div>
      </div>
      @endif
    </div>{{-- /col-lg-4 --}}

  </div>
</div>
@endsection
