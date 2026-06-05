@extends('layouts.admin')
@section('title', 'Edit Survey – ' . $survey->survey_no)

@push('styles')
<style>
  .hero-bar { background:linear-gradient(135deg,#00cfe8,#0090a8); border-radius:16px; padding:1.25rem 1.75rem; color:#fff; margin-bottom:1.25rem; display:flex; align-items:center; gap:1rem; }
  .hero-bar .back-btn { width:38px; height:38px; border-radius:10px; background:rgba(255,255,255,.2); border:0; color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.2rem; flex-shrink:0; text-decoration:none; transition:background .15s; }
  .hero-bar .back-btn:hover { background:rgba(255,255,255,.32); color:#fff; }
  .hero-bar h4 { margin:0; font-size:1.2rem; font-weight:700; }
  .hero-bar p  { margin:0; opacity:.85; font-size:.85rem; }

  .survey-card { border-radius:14px; border:0; box-shadow:0 2px 12px rgba(0,200,232,.08); margin-bottom:1.25rem; }
  .survey-card .card-header { background:#f8f9fa; border-radius:14px 14px 0 0; padding:.85rem 1.25rem; font-weight:700; font-size:.875rem; display:flex; align-items:center; gap:.6rem; border-bottom:1px solid rgba(0,0,0,.06); }
  .sec-icon { width:26px; height:26px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:.75rem; font-weight:700; flex-shrink:0; }

  .pill-group { display:flex; flex-wrap:wrap; gap:.5rem; }
  .pill-label { cursor:pointer; }
  .pill-label input { display:none; }
  .pill-label span { display:inline-block; padding:.35rem .9rem; border-radius:50px; border:2px solid #d9dde1; font-size:.82rem; font-weight:500; color:#6c757d; transition:all .15s; }
  .pill-label input:checked + span { border-color:#00cfe8; background:#00cfe8; color:#fff; }
  .pill-label:hover span { border-color:#00cfe8; color:#00cfe8; }
  .pill-label.blue input:checked  + span { border-color:#696cff; background:#696cff; }
  .pill-label.green input:checked + span { border-color:#71dd37; background:#71dd37; color:#283b50; }
  .pill-label.red input:checked   + span { border-color:#ff3e1d; background:#ff3e1d; }

  .search-drop { position:absolute; z-index:100; width:100%; background:#fff; border:1px solid #e0e0e0; border-radius:10px; box-shadow:0 4px 16px rgba(0,0,0,.1); max-height:200px; overflow-y:auto; margin-top:3px; }
  .search-drop div { padding:.5rem .85rem; font-size:.875rem; cursor:pointer; border-bottom:1px solid #f4f4f4; }
  .search-drop div:last-child { border-bottom:0; }
  .search-drop div:hover { background:#eef0ff; }

  .section-nav { position:sticky; top:0; z-index:50; background:#fff; border-bottom:1px solid #e0e0e0; box-shadow:0 1px 6px rgba(0,0,0,.06); margin:0 -1.5rem 1.25rem; padding:.5rem 1.5rem; overflow-x:auto; display:flex; gap:.35rem; }
  .section-nav a { flex-shrink:0; text-decoration:none; font-size:.78rem; font-weight:600; color:#697a8d; padding:.3rem .7rem; border-radius:20px; background:#f3f4f6; transition:all .15s; }
  .section-nav a:hover { background:#00cfe8; color:#fff; }

  .cam-table-head { display:grid; grid-template-columns:2fr 1fr 1.2fr .8fr .6fr .6fr .5fr; gap:.5rem; padding:.4rem .2rem; font-size:.75rem; font-weight:700; text-transform:uppercase; color:#adb5bd; border-bottom:1px solid #e9ecef; margin-bottom:.5rem; }
  .cam-row { display:grid; grid-template-columns:2fr 1fr 1.2fr .8fr .6fr .6fr .5fr; gap:.5rem; align-items:center; padding:.3rem 0; border-bottom:1px solid #f3f4f6; }
  .cam-row:last-child { border-bottom:0; }

  @media(max-width:767px) {
    .cam-table-head { display:none; }
    .cam-row { grid-template-columns:1fr 1fr; gap:.4rem; padding:.5rem; border:1px solid #e9ecef; border-radius:8px; margin-bottom:.5rem; }
    .cam-row:last-child { border:1px solid #e9ecef; }
  }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  {{-- Hero Bar --}}
  <div class="hero-bar">
    <a href="{{ route('admin.cctv.surveys.show', $survey) }}" class="back-btn"><i class="bx bx-arrow-back"></i></a>
    <div>
      <h4>Edit Survey – {{ $survey->survey_no }}</h4>
      <p>{{ $survey->customer_name }} &bull; {{ $survey->survey_mode }}</p>
    </div>
  </div>

  @if($errors->any())
  <div class="alert alert-danger mb-3">
    <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
  </div>
  @endif

  <form method="POST" action="{{ route('admin.cctv.surveys.update', $survey) }}" enctype="multipart/form-data">
    @csrf @method('PUT')

    {{-- ── Survey Type & Mode pills ── --}}
    <div class="card survey-card">
      <div class="card-body">
        <div class="row g-4">
          <div class="col-md-6">
            <label class="form-label fw-semibold small text-uppercase text-muted mb-2">Survey Type</label>
            <div class="pill-group">
              @foreach(['New Site','Upgrading','Modification','Service'] as $t)
              <label class="pill-label blue">
                <input type="radio" name="survey_type" value="{{ $t }}" {{ old('survey_type',$survey->survey_type)===$t?'checked':'' }}>
                <span>{{ $t }}</span>
              </label>
              @endforeach
            </div>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold small text-uppercase text-muted mb-2">Survey Mode</label>
            <div class="pill-group" id="surveyModeGroup">
              @foreach(['Detailed','Simple'] as $m)
              <label class="pill-label">
                <input type="radio" name="survey_mode" value="{{ $m }}" {{ old('survey_mode',$survey->survey_mode)===$m?'checked':'' }}>
                <span style="padding:.45rem 1.25rem;font-size:.9rem;">{{ $m }}</span>
              </label>
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- ── Basic Info (always visible) ── --}}
    <div class="card survey-card" id="s0">
      <div class="card-header">
        <span class="sec-icon bg-label-primary">0</span> Basic Information
      </div>
      <div class="card-body row g-3">
        <div class="col-md-4">
          <label class="form-label fw-semibold">Mobile</label>
          <div class="position-relative">
            <input type="text" id="mobileSearch" name="mobile" autocomplete="off" placeholder="07X XXX XXXX"
              class="form-control" value="{{ old('mobile', $survey->mobile) }}">
            <div id="mobileDropdown" class="search-drop d-none"></div>
          </div>
        </div>
        <div class="col-md-8">
          <label class="form-label fw-semibold">Customer Name <span class="text-danger">*</span></label>
          <div class="position-relative">
            <input type="text" id="customerSearch" autocomplete="off" placeholder="Type name…"
              class="form-control" value="{{ old('customer_name', $survey->customer_name) }}">
            <input type="hidden" name="customer_name" id="customerNameHidden" value="{{ old('customer_name', $survey->customer_name) }}">
            <div id="customerDropdown" class="search-drop d-none"></div>
          </div>
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold">Survey Date</label>
          <input type="date" name="survey_date" value="{{ old('survey_date', $survey->survey_date ? \Carbon\Carbon::parse($survey->survey_date)->format('Y-m-d') : '') }}" class="form-control">
        </div>
        <div class="col-md-8">
          <label class="form-label fw-semibold">Technician</label>
          <div class="position-relative">
            <input type="text" id="techSearch" autocomplete="off" placeholder="Search technician…"
              class="form-control" value="{{ old('technician_name', $survey->technician?->employee_name ?? '') }}">
            <input type="hidden" name="technician_id" id="techIdHidden" value="{{ old('technician_id', $survey->technician_id) }}">
            <div id="techDropdown" class="search-drop d-none"></div>
          </div>
        </div>
      </div>
    </div>

    {{-- ════════════════════════════════════
         DETAILED SURVEY SECTIONS
    ════════════════════════════════════ --}}
    @php $isDetailed = old('survey_mode', $survey->survey_mode) !== 'Simple'; @endphp
    <div id="detailedSurvey" class="{{ $isDetailed ? '' : 'd-none' }}">

      <div class="section-nav">
        @foreach([['s1','Customer'],['s2','Site'],['s3','Purpose'],['s4','Cameras'],['s5','Network'],['s6','Power'],['s7','Install'],['s8','Materials'],['s10','Risks'],['s11','Notes']] as [$id,$lbl])
        <a href="#{{ $id }}">{{ $lbl }}</a>
        @endforeach
      </div>

      {{-- S1: Customer Details --}}
      <div class="card survey-card" id="s1">
        <div class="card-header"><span class="sec-icon bg-label-primary">1</span> Customer Details</div>
        <div class="card-body row g-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold">Contact Person</label>
            <input type="text" name="contact_person" value="{{ old('contact_person', $survey->contact_person) }}" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Alt. Mobile</label>
            <input type="text" name="alt_mobile" value="{{ old('alt_mobile', $survey->alt_mobile) }}" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Email</label>
            <input type="email" name="email" value="{{ old('email', $survey->email) }}" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">GPS Location</label>
            <div class="input-group">
              <input type="text" name="gps_location" id="gpsInput" value="{{ old('gps_location', $survey->gps_location) }}" class="form-control" placeholder="Latitude, Longitude">
              <button type="button" id="gpsBtn" class="btn btn-outline-secondary"><i class="bx bx-current-location" id="gpsIcon"></i> <span id="gpsBtnText">Fetch</span></button>
            </div>
            <div id="gpsStatus" class="form-text d-none"></div>
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold d-block mb-2">Customer Type</label>
            <div class="pill-group">
              @foreach(['House','Shop','Office','Factory','School','Hotel','Government','Other'] as $ct)
              <label class="pill-label">
                <input type="radio" name="customer_type" value="{{ $ct }}" {{ old('customer_type',$survey->customer_type)===$ct?'checked':'' }}>
                <span>{{ $ct }}</span>
              </label>
              @endforeach
            </div>
            <div id="customerTypeOtherWrap" class="{{ old('customer_type',$survey->customer_type)==='Other'?'':'d-none' }} mt-2">
              <input type="text" name="customer_type_other" value="{{ old('customer_type_other', $survey->customer_type_other) }}" class="form-control" placeholder="Specify type…">
            </div>
          </div>
        </div>
      </div>

      {{-- S2: Site --}}
      <div class="card survey-card" id="s2">
        <div class="card-header"><span class="sec-icon bg-label-success">2</span> Site Information</div>
        <div class="card-body row g-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold">Building Name / Address</label>
            <input type="text" name="building_name" value="{{ old('building_name', $survey->building_name) }}" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Building Type</label>
            <input type="text" name="building_type" value="{{ old('building_type', $survey->building_type) }}" class="form-control" placeholder="e.g. 2-storey, Villa…">
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Site Size</label>
            <input type="text" name="site_size" value="{{ old('site_size', $survey->site_size) }}" class="form-control" placeholder="e.g. 5000 sqft">
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Construction Status</label>
            <select name="construction_status" class="form-select">
              <option value="">— Select —</option>
              @foreach(['Existing','Under Construction','New Building'] as $cs)
              <option value="{{ $cs }}" {{ old('construction_status',$survey->construction_status)===$cs?'selected':'' }}>{{ $cs }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4 d-flex align-items-end pb-1">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="existing_security_system" id="existingSec" value="1"
                {{ old('existing_security_system', $survey->existing_security_system)?'checked':'' }}>
              <label class="form-check-label fw-semibold" for="existingSec">Existing security system?</label>
            </div>
          </div>
        </div>
      </div>

      {{-- S3: Purpose --}}
      <div class="card survey-card" id="s3">
        <div class="card-header"><span class="sec-icon bg-label-warning">3</span> Purpose / Requirements</div>
        <div class="card-body">
          @php
            $purposeOptions = ['Theft Prevention','Employee Monitoring','Perimeter Security','Visitor Tracking','Fire/Safety Monitoring','Remote Monitoring','Evidence Recording','Access Control Integration','Child/Elder Safety','General Surveillance'];
            $selectedPurposes = old('purposes', $survey->purposes ?? []);
          @endphp
          <div class="row g-2">
            @foreach($purposeOptions as $p)
            <div class="col-6 col-md-4">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="purposes[]" value="{{ $p }}" id="pur_{{ $loop->index }}"
                  {{ in_array($p,(array)$selectedPurposes)?'checked':'' }}>
                <label class="form-check-label" for="pur_{{ $loop->index }}">{{ $p }}</label>
              </div>
            </div>
            @endforeach
          </div>
        </div>
      </div>

      {{-- S4: Camera Locations --}}
      @php $camLocs = $survey->camera_locations ?? []; @endphp
      <div class="card survey-card" id="s4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <span class="d-flex align-items-center gap-2"><span class="sec-icon bg-label-info">4</span> Camera Locations</span>
          <button type="button" id="addCamBtn" class="btn btn-sm btn-primary d-flex align-items-center gap-1"><i class="bx bx-plus"></i> Add Row</button>
        </div>
        <div class="card-body p-0">
          <div class="px-3 pt-3">
            <div class="cam-table-head">
              <div>Location / Description</div><div>In/Out</div><div>Camera Type</div><div>MP</div>
              <div class="text-center">Night</div><div class="text-center">Audio</div><div></div>
            </div>
          </div>
          <div id="camRows" class="px-3 pb-3">
            @forelse($camLocs as $cam)
            <div class="cam-row">
              <input type="text" name="cam_location[]" value="{{ $cam['location'] ?? '' }}" class="form-control form-control-sm">
              <select name="cam_io[]" class="form-select form-select-sm">
                <option {{ ($cam['indoor_outdoor']??'')=='Indoor'?'selected':'' }}>Indoor</option>
                <option {{ ($cam['indoor_outdoor']??'')=='Outdoor'?'selected':'' }}>Outdoor</option>
              </select>
              <input type="text" name="cam_type[]" value="{{ $cam['camera_type'] ?? '' }}" placeholder="Dome/Bullet…" class="form-control form-control-sm">
              <input type="text" name="cam_mp[]" value="{{ $cam['mp'] ?? '' }}" placeholder="2MP" class="form-control form-control-sm">
              <div class="text-center"><input type="checkbox" name="cam_nv[]" value="1" class="form-check-input" {{ ($cam['night_vision']??false)?'checked':'' }}></div>
              <div class="text-center"><input type="checkbox" name="cam_audio[]" value="1" class="form-check-input" {{ ($cam['audio']??false)?'checked':'' }}></div>
              <div class="text-center"><button type="button" class="remove-cam-btn btn btn-sm btn-icon btn-outline-danger"><i class="bx bx-x"></i></button></div>
            </div>
            @empty
            <div class="cam-row">
              <input type="text" name="cam_location[]" placeholder="e.g. Front Gate" class="form-control form-control-sm">
              <select name="cam_io[]" class="form-select form-select-sm"><option>Indoor</option><option>Outdoor</option></select>
              <input type="text" name="cam_type[]" placeholder="Dome/Bullet…" class="form-control form-control-sm">
              <input type="text" name="cam_mp[]" placeholder="2MP" class="form-control form-control-sm">
              <div class="text-center"><input type="checkbox" name="cam_nv[]" value="1" class="form-check-input"></div>
              <div class="text-center"><input type="checkbox" name="cam_audio[]" value="1" class="form-check-input"></div>
              <div class="text-center"><button type="button" class="remove-cam-btn btn btn-sm btn-icon btn-outline-danger"><i class="bx bx-x"></i></button></div>
            </div>
            @endforelse
          </div>
        </div>
      </div>

      {{-- S5: Network --}}
      <div class="card survey-card" id="s5">
        <div class="card-header"><span class="sec-icon bg-label-info">5</span> Network / Connectivity</div>
        <div class="card-body row g-3">
          <div class="col-md-4">
            <label class="form-label fw-semibold">Internet Status</label>
            <select name="internet_status" id="internetStatusSel" class="form-select">
              <option value="">— Select —</option>
              <option value="Available" {{ old('internet_status',$survey->internet_status)==='Available'?'selected':'' }}>Available</option>
              <option value="Not Available" {{ old('internet_status',$survey->internet_status)==='Not Available'?'selected':'' }}>Not Available</option>
            </select>
          </div>
          <div class="col-md-4 {{ old('internet_status',$survey->internet_status)==='Available'?'':'d-none' }}" id="ispWrap">
            <label class="form-label fw-semibold">ISP</label>
            <select name="isp" id="ispSel" class="form-select">
              <option value="">— Select —</option>
              @foreach(['SLT','Dialog','Starlink','Other'] as $isp)
              <option value="{{ $isp }}" {{ old('isp',$survey->isp)===$isp?'selected':'' }}>{{ $isp }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4 {{ old('isp',$survey->isp)==='Other'?'':'d-none' }}" id="ispOtherWrap">
            <label class="form-label fw-semibold">ISP Name</label>
            <input type="text" name="isp_other" value="{{ old('isp_other', $survey->isp_other) }}" class="form-control">
          </div>
          <div class="col-12 d-flex gap-4 flex-wrap">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="wifi_coverage" id="wifiCov" value="1" {{ old('wifi_coverage',$survey->wifi_coverage)?'checked':'' }}>
              <label class="form-check-label" for="wifiCov">WiFi coverage at site?</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="lan_available" id="lanAvail" value="1" {{ old('lan_available',$survey->lan_available)?'checked':'' }}>
              <label class="form-check-label" for="lanAvail">LAN / Ethernet available?</label>
            </div>
          </div>
        </div>
      </div>

      {{-- S6: Power --}}
      <div class="card survey-card" id="s6">
        <div class="card-header"><span class="sec-icon bg-label-warning">6</span> Power Supply</div>
        <div class="card-body row g-3">
          <div class="col-md-4">
            <label class="form-label fw-semibold">Power Availability</label>
            <select name="power_availability" class="form-select">
              <option value="">— Select —</option>
              @foreach(['Stable','Moderate','Poor'] as $pa)
              <option value="{{ $pa }}" {{ old('power_availability',$survey->power_availability)===$pa?'selected':'' }}>{{ $pa }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-8 d-flex align-items-end gap-4 flex-wrap pb-1">
            @foreach([['ups_required','UPS Required?'],['electrical_work_required','Electrical Work Required?'],['voltage_issues','Voltage Issues Observed?']] as [$fn,$fl])
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="{{ $fn }}" id="{{ $fn }}" value="1"
                {{ old($fn, $survey->$fn)?'checked':'' }}>
              <label class="form-check-label" for="{{ $fn }}">{{ $fl }}</label>
            </div>
            @endforeach
          </div>
        </div>
      </div>

      {{-- S7: Installation --}}
      <div class="card survey-card" id="s7">
        <div class="card-header"><span class="sec-icon bg-label-danger">7</span> Installation Assessment</div>
        <div class="card-body row g-3">
          <div class="col-md-4">
            <label class="form-label fw-semibold">Cable Route</label>
            <select name="cable_route" class="form-select">
              <option value="">— Select —</option>
              @foreach(['Easy','Medium','Difficult'] as $cr)
              <option value="{{ $cr }}" {{ old('cable_route',$survey->cable_route)===$cr?'selected':'' }}>{{ $cr }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Ceiling Type</label>
            <select name="ceiling_type" class="form-select">
              <option value="">— Select —</option>
              @foreach(['Concrete','Gypsum','Metal','Wooden'] as $ct)
              <option value="{{ $ct }}" {{ old('ceiling_type',$survey->ceiling_type)===$ct?'selected':'' }}>{{ $ct }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Wall Type</label>
            <select name="wall_type" class="form-select">
              <option value="">— Select —</option>
              @foreach(['Brick','Concrete','Partition'] as $wt)
              <option value="{{ $wt }}" {{ old('wall_type',$survey->wall_type)===$wt?'selected':'' }}>{{ $wt }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-12 d-flex gap-4 flex-wrap">
            @foreach([['ladder_required','Ladder Required'],['scaffolding_required','Scaffolding Required']] as [$fn,$fl])
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="{{ $fn }}" id="{{ $fn }}" value="1"
                {{ old($fn, $survey->$fn)?'checked':'' }}>
              <label class="form-check-label" for="{{ $fn }}">{{ $fl }}</label>
            </div>
            @endforeach
          </div>
          <div class="col-md-8">
            <label class="form-label fw-semibold">Height Risk Level: <strong id="heightRiskVal">{{ old('height_risk',$survey->height_risk ?? 0) }}</strong> / 10</label>
            <input type="range" class="form-range" name="height_risk" id="heightRiskRange" min="0" max="10"
              value="{{ old('height_risk',$survey->height_risk ?? 0) }}">
            <div class="d-flex justify-content-between" style="font-size:.75rem;color:#adb5bd;margin-top:-4px;">
              <span>0 (Safe)</span><span>5 (Medium)</span><span>10 (Extreme)</span>
            </div>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Special Safety Equipment</label>
            <input type="text" name="special_safety_equipment" value="{{ old('special_safety_equipment', $survey->special_safety_equipment) }}" class="form-control">
          </div>
        </div>
      </div>

      {{-- S8: Materials --}}
      @php $accs = $survey->accessories ?? []; @endphp
      <div class="card survey-card" id="s8">
        <div class="card-header"><span class="sec-icon bg-label-success">8</span> Material Estimation</div>
        <div class="card-body">
          <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
              <label class="form-label fw-semibold">Total Cameras</label>
              <input type="number" name="cameras_qty" value="{{ old('cameras_qty',$survey->cameras_qty ?? 0) }}" min="0" class="form-control">
            </div>
            <div class="col-6 col-md-3">
              <label class="form-label fw-semibold">DVR Channels</label>
              <input type="number" name="dvr_channels" value="{{ old('dvr_channels',$survey->dvr_channels ?? 0) }}" min="0" class="form-control">
            </div>
            <div class="col-6 col-md-3">
              <label class="form-label fw-semibold">HDD Storage (days)</label>
              <input type="number" name="hdd_storage_days" value="{{ old('hdd_storage_days',$survey->hdd_storage_days ?? 30) }}" min="1" class="form-control">
            </div>
            <div class="col-6 col-md-3">
              <label class="form-label fw-semibold">Cable (meters)</label>
              <input type="number" name="cable_meters" value="{{ old('cable_meters',$survey->cable_meters ?? 0) }}" min="0" class="form-control">
            </div>
          </div>
          <div class="d-flex justify-content-between align-items-center mb-2">
            <p class="fw-bold small text-uppercase text-muted mb-0">Additional Accessories</p>
            <button type="button" id="addAccBtn" class="btn btn-sm btn-success d-flex align-items-center gap-1"><i class="bx bx-plus"></i> Add Item</button>
          </div>
          <div id="accRows">
            @foreach($accs as $acc)
            <div class="row g-2 mb-2 acc-row">
              <div class="col-8">
                <input type="text" name="acc_name[]" value="{{ $acc['name'] ?? '' }}" class="form-control form-control-sm" placeholder="Item name">
              </div>
              <div class="col-3">
                <input type="number" name="acc_qty[]" value="{{ $acc['qty'] ?? 1 }}" min="1" class="form-control form-control-sm" placeholder="Qty">
              </div>
              <div class="col-1">
                <button type="button" class="remove-acc-btn btn btn-sm btn-icon btn-outline-danger"><i class="bx bx-x"></i></button>
              </div>
            </div>
            @endforeach
          </div>
        </div>
      </div>

      {{-- S10: Risks --}}
      <div class="card survey-card" id="s10">
        <div class="card-header"><span class="sec-icon bg-label-danger">10</span> Risk Assessment</div>
        <div class="card-body">
          @php
            $riskOptions = ['High-rise installation','Confined space','Electrical hazard','Unstable structure','Aggressive animals','Flooding risk','Extreme heat','Poor lighting','Traffic exposure','Customer access restrictions','No signal area','Vandalism risk'];
            $selectedRisks = old('risks', $survey->risks ?? []);
          @endphp
          <div class="row g-2">
            @foreach($riskOptions as $r)
            <div class="col-6 col-md-4">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="risks[]" value="{{ $r }}" id="risk_{{ $loop->index }}"
                  {{ in_array($r,(array)$selectedRisks)?'checked':'' }}>
                <label class="form-check-label" for="risk_{{ $loop->index }}">{{ $r }}</label>
              </div>
            </div>
            @endforeach
          </div>
        </div>
      </div>

      {{-- S11: Notes --}}
      <div class="card survey-card" id="s11">
        <div class="card-header"><span class="sec-icon bg-label-secondary">11</span> Special Notes</div>
        <div class="card-body">
          <textarea name="special_notes" class="form-control" rows="4">{{ old('special_notes', $survey->special_notes) }}</textarea>
        </div>
      </div>

    </div>{{-- /detailedSurvey --}}

    {{-- ════════════════════════════════════
         SIMPLE SURVEY SECTIONS
    ════════════════════════════════════ --}}
    <div id="simpleSurvey" class="{{ $isDetailed ? 'd-none' : '' }}">

      {{-- Camera & DVR --}}
      <div class="card survey-card">
        <div class="card-header"><span class="sec-icon bg-primary bg-opacity-10 text-primary"><i class="bx bx-camera"></i></span> Camera & Recorder</div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-3 col-6">
              <label class="form-label fw-semibold small">No. of Cameras</label>
              <input type="number" name="simple_num_cameras" class="form-control" value="{{ old('simple_num_cameras', $survey->simple_num_cameras ?? 0) }}" min="0">
            </div>
            <div class="col-md-3 col-6">
              <label class="form-label fw-semibold small">DVR / NVR</label>
              <select name="simple_dvr_nvr" class="form-select">
                <option value="">-- Select --</option>
                @foreach(['DVR','NVR'] as $d)
                <option value="{{ $d }}" {{ old('simple_dvr_nvr',$survey->simple_dvr_nvr)===$d?'selected':'' }}>{{ $d }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3 col-6">
              <label class="form-label fw-semibold small">Channels</label>
              <select name="simple_dvr_channels" class="form-select">
                <option value="">-- Select --</option>
                @foreach([4,8,16,32,64] as $ch)
                <option value="{{ $ch }}" {{ old('simple_dvr_channels',$survey->simple_dvr_channels)==$ch?'selected':'' }}>{{ $ch }} CH</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3 col-6">
              <label class="form-label fw-semibold small">No. of Technicians</label>
              <input type="number" name="simple_num_technicians" class="form-control" value="{{ old('simple_num_technicians', $survey->simple_num_technicians ?? 1) }}" min="1">
            </div>
            <div class="col-md-3 col-6">
              <label class="form-label fw-semibold small">Estimated Days</label>
              <input type="number" name="simple_estimated_days" class="form-control" value="{{ old('simple_estimated_days', $survey->simple_estimated_days ?? 1) }}" min="1">
            </div>
          </div>
        </div>
      </div>

      {{-- Internet --}}
      <div class="card survey-card">
        <div class="card-header"><span class="sec-icon bg-info bg-opacity-10 text-info"><i class="bx bx-wifi"></i></span> Internet</div>
        <div class="card-body">
          <div class="row g-3 align-items-end">
            <div class="col-md-4">
              <label class="form-label fw-semibold small">Internet Available?</label>
              <div class="d-flex gap-3 mt-1">
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="simple_internet_available" id="siYes" value="1"
                    {{ old('simple_internet_available', $survey->simple_internet_available ? '1' : '0') == '1' ? 'checked' : '' }}>
                  <label class="form-check-label" for="siYes">Yes</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="simple_internet_available" id="siNo" value="0"
                    {{ old('simple_internet_available', $survey->simple_internet_available ? '1' : '0') == '0' ? 'checked' : '' }}>
                  <label class="form-check-label" for="siNo">No</label>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold small">ISP</label>
              <select name="simple_isp" class="form-select" id="simpleIspSelect">
                <option value="">-- Select ISP --</option>
                @foreach(['SLT','Dialog','Starlink','Other','None'] as $isp)
                <option value="{{ $isp }}" {{ old('simple_isp',$survey->simple_isp)===$isp?'selected':'' }}>{{ $isp }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>
      </div>

      {{-- Sliders --}}
      <div class="card survey-card">
        <div class="card-header"><span class="sec-icon bg-warning bg-opacity-10 text-warning"><i class="bx bx-slider"></i></span> Site Assessment</div>
        <div class="card-body">
          <div class="row g-4">
            <div class="col-md-6">
              <label class="form-label fw-semibold small d-flex justify-content-between">
                Cabling Easiness <span class="badge bg-primary" id="cablingVal">{{ old('simple_cabling_ease', $survey->simple_cabling_ease ?? 5) }}</span>
              </label>
              <input type="range" name="simple_cabling_ease" class="form-range" min="1" max="10" step="1"
                value="{{ old('simple_cabling_ease', $survey->simple_cabling_ease ?? 5) }}" id="cablingSlider">
              <div class="d-flex justify-content-between text-muted" style="font-size:.72rem;margin-top:2px;">
                <span>1 (Very Difficult)</span><span>10 (Very Easy)</span>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold small d-flex justify-content-between">
                Risk Level <span class="badge bg-danger" id="riskVal">{{ old('simple_risk_level', $survey->simple_risk_level ?? 5) }}</span>
              </label>
              <input type="range" name="simple_risk_level" class="form-range" min="1" max="10" step="1"
                value="{{ old('simple_risk_level', $survey->simple_risk_level ?? 5) }}" id="riskSlider">
              <div class="d-flex justify-content-between text-muted" style="font-size:.72rem;margin-top:2px;">
                <span>1 (Low Risk)</span><span>10 (High Risk)</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- GPS --}}
      <div class="card survey-card">
        <div class="card-header"><span class="sec-icon bg-success bg-opacity-10 text-success"><i class="bx bx-map-pin"></i></span> Location</div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label fw-semibold small">GPS Location</label>
              <div class="input-group">
                <input type="text" name="simple_gps_location" id="simpleGps" class="form-control"
                  placeholder="lat, lng" value="{{ old('simple_gps_location', $survey->simple_gps_location) }}">
                <button type="button" class="btn btn-outline-secondary" id="simpleGpsBtn">
                  <i class="bx bx-current-location me-1"></i>Fetch
                </button>
              </div>
              <div id="simpleGpsStatus" class="form-text text-muted" style="min-height:1.2em;"></div>
            </div>
          </div>
        </div>
      </div>

      {{-- Outcome --}}
      <div class="card survey-card">
        <div class="card-header"><span class="sec-icon bg-secondary bg-opacity-10 text-secondary"><i class="bx bx-info-circle"></i></span> Outcome</div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label fw-semibold small">Status</label>
              <select name="status" class="form-select" id="simpleStatusSelect">
                @foreach(['Scheduled','Completed','Need More Time','Cancelled'] as $s)
                <option value="{{ $s }}" {{ old('status',$survey->status)===$s?'selected':'' }}>{{ $s }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold small">Remark</label>
              <textarea name="simple_remark" class="form-control" rows="3">{{ old('simple_remark', $survey->simple_remark) }}</textarea>
            </div>
          </div>
        </div>
      </div>

    </div>{{-- /simpleSurvey --}}

    {{-- Status for detailed (outside simple block) --}}
    <div id="detailedStatus" class="{{ $isDetailed ? '' : 'd-none' }}">
      <div class="card survey-card">
        <div class="card-header"><span class="sec-icon bg-label-secondary"><i class="bx bx-check-circle"></i></span> Status</div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label fw-semibold">Survey Status</label>
              <select name="status" class="form-select">
                @foreach(['Scheduled','Completed','Need More Time','Cancelled'] as $s)
                <option value="{{ $s }}" {{ old('status',$survey->status)===$s?'selected':'' }}>{{ $s }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Submit --}}
    <div class="d-flex justify-content-end gap-2 pb-5">
      <a href="{{ route('admin.cctv.surveys.show', $survey) }}" class="btn btn-outline-secondary">Cancel</a>
      <button type="submit" class="btn btn-primary px-4">
        <i class="bx bx-save me-1"></i> Save Changes
      </button>
    </div>

  </form>
</div>
@endsection

@push('scripts')
@php
$leadsJson = $leads->map(function($l){ return ['id'=>$l->id,'name'=>$l->customer_name,'mobile'=>$l->mobile,'lead_id'=>$l->id]; })->values();
$techJson  = $employees->map(function($e){ return ['id'=>$e->id,'name'=>$e->employee_name]; })->values();
@endphp
<script>
const leadsData   = @json($leadsJson);
const custNameHid = document.getElementById('customerNameHidden');

function fillCustomer(lead) {
    const mobInput  = document.getElementById('mobileSearch');
    const nameInput = document.getElementById('customerSearch');
    if (mobInput && !mobInput.value) mobInput.value = lead.mobile || '';
    if (nameInput) nameInput.value = lead.name || '';
    custNameHid.value = lead.name || '';
}

function buildDrop(drop, hits, onPick) {
    drop.innerHTML = hits.map(l =>
        `<div data-id="${l.lead_id}" data-name="${l.name}" data-mobile="${l.mobile||''}">
            <span class="fw-semibold">${l.name}</span>
            <span class="text-muted small float-end">${l.mobile||''}</span>
         </div>`
    ).join('');
    drop.classList.remove('d-none');
    drop.querySelectorAll('[data-id]').forEach(el => el.addEventListener('click', function() {
        onPick(this);
        drop.classList.add('d-none');
    }));
}

// ── Mobile search ──────────────────────────────────────────────
const mobSearch = document.getElementById('mobileSearch');
const mobDrop   = document.getElementById('mobileDropdown');
mobSearch.addEventListener('input', function() {
    const q = this.value.trim();
    custNameHid.value = '';
    if (q.length < 2) { mobDrop.classList.add('d-none'); return; }
    const hits = leadsData.filter(l => l.mobile && l.mobile.includes(q)).slice(0,8);
    if (!hits.length) { mobDrop.classList.add('d-none'); return; }
    buildDrop(mobDrop, hits, function(el) {
        mobSearch.value = el.dataset.mobile;
        fillCustomer({ name: el.dataset.name, mobile: el.dataset.mobile, lead_id: el.dataset.id });
    });
});
document.addEventListener('click', e => {
    if (!mobSearch.contains(e.target) && !mobDrop.contains(e.target)) mobDrop.classList.add('d-none');
});

// ── Customer name search ───────────────────────────────────────
const custSearch = document.getElementById('customerSearch');
const custDrop   = document.getElementById('customerDropdown');
custSearch.addEventListener('input', function() {
    const q = this.value.trim().toLowerCase();
    custNameHid.value = this.value;
    if (q.length < 1) { custDrop.classList.add('d-none'); return; }
    const hits = leadsData.filter(l => l.name.toLowerCase().includes(q)).slice(0,8);
    if (!hits.length) { custDrop.classList.add('d-none'); return; }
    buildDrop(custDrop, hits, function(el) {
        custSearch.value = el.dataset.name;
        fillCustomer({ name: el.dataset.name, mobile: el.dataset.mobile, lead_id: el.dataset.id });
    });
});
document.addEventListener('click', e => {
    if (!custSearch.contains(e.target) && !custDrop.contains(e.target)) custDrop.classList.add('d-none');
});

// ── Technician live search ─────────────────────────────────────
const techData   = @json($techJson);
const techSearch = document.getElementById('techSearch');
const techIdHid  = document.getElementById('techIdHidden');
const techDrop   = document.getElementById('techDropdown');
techSearch.addEventListener('input', function() {
    const q = this.value.trim().toLowerCase();
    techIdHid.value = '';
    if (!q) { techDrop.classList.add('d-none'); return; }
    const hits = techData.filter(e => e.name.toLowerCase().includes(q)).slice(0,8);
    if (!hits.length) { techDrop.classList.add('d-none'); return; }
    techDrop.innerHTML = hits.map(e => `<div data-id="${e.id}" data-name="${e.name}">${e.name}</div>`).join('');
    techDrop.classList.remove('d-none');
    techDrop.querySelectorAll('[data-id]').forEach(el => {
        el.addEventListener('click', function() {
            techSearch.value = this.dataset.name;
            techIdHid.value  = this.dataset.id;
            techDrop.classList.add('d-none');
        });
    });
});
document.addEventListener('click', e => {
    if (!techSearch.contains(e.target) && !techDrop.contains(e.target)) techDrop.classList.add('d-none');
});

// ── Survey Mode toggle ─────────────────────────────────────────
document.querySelectorAll('input[name="survey_mode"]').forEach(inp => {
    inp.addEventListener('change', function() {
        const isSimple = this.value === 'Simple';
        document.getElementById('simpleSurvey').classList.toggle('d-none', !isSimple);
        document.getElementById('detailedSurvey').classList.toggle('d-none', isSimple);
        document.getElementById('detailedStatus').classList.toggle('d-none', isSimple);
    });
});

// ── Sliders ────────────────────────────────────────────────────
const cablingSlider = document.getElementById('cablingSlider');
const cablingVal    = document.getElementById('cablingVal');
if (cablingSlider) cablingSlider.addEventListener('input', () => cablingVal.textContent = cablingSlider.value);

const riskSlider = document.getElementById('riskSlider');
const riskVal    = document.getElementById('riskVal');
if (riskSlider) riskSlider.addEventListener('input', () => riskVal.textContent = riskSlider.value);

const heightRiskRange = document.getElementById('heightRiskRange');
const heightRiskVal   = document.getElementById('heightRiskVal');
if (heightRiskRange) heightRiskRange.addEventListener('input', () => heightRiskVal.textContent = heightRiskRange.value);

// ── Camera rows ────────────────────────────────────────────────
const camRowTemplate = `<div class="cam-row">
  <input type="text" name="cam_location[]" placeholder="e.g. Front Gate" class="form-control form-control-sm">
  <select name="cam_io[]" class="form-select form-select-sm"><option>Indoor</option><option>Outdoor</option></select>
  <input type="text" name="cam_type[]" placeholder="Dome/Bullet…" class="form-control form-control-sm">
  <input type="text" name="cam_mp[]" placeholder="2MP" class="form-control form-control-sm">
  <div class="text-center"><input type="checkbox" name="cam_nv[]" value="1" class="form-check-input" title="Night Vision"></div>
  <div class="text-center"><input type="checkbox" name="cam_audio[]" value="1" class="form-check-input" title="Audio"></div>
  <div class="text-center"><button type="button" class="remove-cam-btn btn btn-sm btn-icon btn-outline-danger"><i class="bx bx-x"></i></button></div>
</div>`;
document.getElementById('addCamBtn')?.addEventListener('click', () => {
    document.getElementById('camRows').insertAdjacentHTML('beforeend', camRowTemplate);
});
document.getElementById('camRows')?.addEventListener('click', e => {
    if (e.target.closest('.remove-cam-btn')) e.target.closest('.cam-row').remove();
});

// ── Accessories ────────────────────────────────────────────────
document.getElementById('addAccBtn')?.addEventListener('click', () => {
    document.getElementById('accRows').insertAdjacentHTML('beforeend', `
    <div class="row g-2 mb-2 acc-row">
        <div class="col-8"><input type="text" name="acc_name[]" class="form-control form-control-sm" placeholder="Item name"></div>
        <div class="col-3"><input type="number" name="acc_qty[]" value="1" min="1" class="form-control form-control-sm"></div>
        <div class="col-1"><button type="button" class="remove-acc-btn btn btn-sm btn-icon btn-outline-danger"><i class="bx bx-x"></i></button></div>
    </div>`);
});
document.getElementById('accRows')?.addEventListener('click', e => {
    if (e.target.closest('.remove-acc-btn')) e.target.closest('.acc-row').remove();
});

// ── Customer type "Other" ──────────────────────────────────────
document.querySelectorAll('input[name="customer_type"]').forEach(r => {
    r.addEventListener('change', function() {
        const w = document.getElementById('customerTypeOtherWrap');
        if (w) w.classList.toggle('d-none', this.value !== 'Other');
    });
});

// ── Internet status show/hide ISP ──────────────────────────────
const internetSel = document.getElementById('internetStatusSel');
if (internetSel) {
    internetSel.addEventListener('change', function() {
        document.getElementById('ispWrap')?.classList.toggle('d-none', this.value !== 'Available');
        if (this.value !== 'Available') document.getElementById('ispOtherWrap')?.classList.add('d-none');
    });
}
const ispSel = document.getElementById('ispSel');
if (ispSel) {
    ispSel.addEventListener('change', function() {
        document.getElementById('ispOtherWrap')?.classList.toggle('d-none', this.value !== 'Other');
    });
}

// ── GPS fetch (detailed) ───────────────────────────────────────
const gpsBtn = document.getElementById('gpsBtn');
if (gpsBtn) {
    gpsBtn.addEventListener('click', function() {
        const input  = document.getElementById('gpsInput');
        const status = document.getElementById('gpsStatus');
        if (!navigator.geolocation) { status.textContent = 'Geolocation not supported'; status.classList.remove('d-none'); return; }
        gpsBtn.disabled = true;
        document.getElementById('gpsBtnText').textContent = 'Fetching…';
        navigator.geolocation.getCurrentPosition(
            pos => {
                input.value = pos.coords.latitude.toFixed(6) + ', ' + pos.coords.longitude.toFixed(6);
                status.textContent = 'Location fetched!';
                status.className = 'form-text text-success';
                gpsBtn.disabled = false;
                document.getElementById('gpsBtnText').textContent = 'Fetch';
            },
            err => {
                status.textContent = 'Could not get location: ' + err.message;
                status.className = 'form-text text-danger';
                status.classList.remove('d-none');
                gpsBtn.disabled = false;
                document.getElementById('gpsBtnText').textContent = 'Fetch';
            }
        );
    });
}

// ── GPS fetch (simple) ─────────────────────────────────────────
const simpleGpsBtn = document.getElementById('simpleGpsBtn');
if (simpleGpsBtn) {
    simpleGpsBtn.addEventListener('click', function() {
        const input  = document.getElementById('simpleGps');
        const status = document.getElementById('simpleGpsStatus');
        if (!navigator.geolocation) { status.textContent = 'Not supported'; return; }
        simpleGpsBtn.disabled = true;
        simpleGpsBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Fetching…';
        navigator.geolocation.getCurrentPosition(
            pos => {
                input.value = pos.coords.latitude.toFixed(6) + ', ' + pos.coords.longitude.toFixed(6);
                status.textContent = 'Location fetched!';
                simpleGpsBtn.disabled = false;
                simpleGpsBtn.innerHTML = '<i class="bx bx-current-location me-1"></i>Fetch';
            },
            err => {
                status.textContent = 'Error: ' + err.message;
                simpleGpsBtn.disabled = false;
                simpleGpsBtn.innerHTML = '<i class="bx bx-current-location me-1"></i>Fetch';
            }
        );
    });
}

// ── Sync visible customer name input → hidden field on submit ──
document.querySelector('form').addEventListener('submit', function() {
    const nameInput = document.getElementById('customerSearch');
    if (nameInput && nameInput.value) custNameHid.value = nameInput.value;
});
</script>
@endpush
