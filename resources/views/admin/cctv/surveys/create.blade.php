@extends('layouts.admin')
@section('title', 'New Survey')

@push('styles')
<style>
  .hero-bar { background:linear-gradient(135deg,#696cff,#8c57ff); border-radius:16px; padding:1.25rem 1.75rem; color:#fff; margin-bottom:1.25rem; display:flex; align-items:center; gap:1rem; }
  .hero-bar .back-btn { width:38px; height:38px; border-radius:10px; background:rgba(255,255,255,.2); border:0; color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.2rem; flex-shrink:0; text-decoration:none; transition:background .15s; }
  .hero-bar .back-btn:hover { background:rgba(255,255,255,.32); color:#fff; }
  .hero-bar h4 { margin:0; font-size:1.2rem; font-weight:700; }
  .hero-bar p  { margin:0; opacity:.85; font-size:.85rem; }

  /* Cards */
  .survey-card { border-radius:14px; border:0; box-shadow:0 2px 12px rgba(105,108,255,.08); margin-bottom:1.25rem; }
  .survey-card .card-header { background:#f8f9fa; border-radius:14px 14px 0 0; padding:.85rem 1.25rem; font-weight:700; font-size:.875rem; display:flex; align-items:center; gap:.6rem; border-bottom:1px solid rgba(0,0,0,.06); }
  .sec-icon { width:26px; height:26px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:.75rem; font-weight:700; flex-shrink:0; }

  /* Pill radio selectors */
  .pill-group { display:flex; flex-wrap:wrap; gap:.5rem; }
  .pill-label { cursor:pointer; }
  .pill-label input { display:none; }
  .pill-label span { display:inline-block; padding:.35rem .9rem; border-radius:50px; border:2px solid #d9dde1; font-size:.82rem; font-weight:500; color:#6c757d; transition:all .15s; }
  .pill-label input:checked + span { border-color:#696cff; background:#696cff; color:#fff; }
  .pill-label:hover span { border-color:#696cff; color:#696cff; }

  .pill-label.blue input:checked + span  { border-color:#696cff; background:#696cff; }
  .pill-label.green input:checked + span { border-color:#71dd37; background:#71dd37; color:#283b50; }
  .pill-label.red input:checked + span   { border-color:#ff3e1d; background:#ff3e1d; }

  /* Sticky section nav */
  .section-nav { position:sticky; top:0; z-index:50; background:#fff; border-bottom:1px solid #e0e0e0; box-shadow:0 1px 6px rgba(0,0,0,.06); margin:0 -1.5rem 1.25rem; padding:.5rem 1.5rem; overflow-x:auto; display:flex; gap:.35rem; }
  .section-nav a { white-space:nowrap; padding:.3rem .75rem; border-radius:50px; font-size:.78rem; font-weight:600; color:#6c757d; text-decoration:none; transition:all .15s; }
  .section-nav a:hover { background:#eef0ff; color:#696cff; }

  /* Camera repeater table */
  .cam-table-head { display:grid; grid-template-columns:2fr 1fr 1fr .7fr .5fr .5fr .4fr; gap:.5rem; font-size:.72rem; font-weight:700; text-transform:uppercase; color:#adb5bd; padding:0 .5rem .3rem; }
  .cam-row { display:grid; grid-template-columns:2fr 1fr 1fr .7fr .5fr .5fr .4fr; gap:.5rem; align-items:center; background:#f8f9fa; border-radius:8px; padding:.4rem .5rem; margin-bottom:.4rem; }
  @media(max-width:640px) {
    .cam-table-head { display:none; }
    .cam-row { grid-template-columns:1fr 1fr; }
  }

  /* Acc row */
  .acc-row { display:flex; gap:.5rem; align-items:center; margin-bottom:.4rem; }

  /* Photo drop zone */
  .photo-drop { border:2px dashed #d9dde1; border-radius:12px; padding:2rem; text-align:center; cursor:pointer; transition:border-color .15s; }
  .photo-drop:hover { border-color:#696cff; }

  /* Live search dropdown */
  .search-drop { position:absolute; z-index:100; width:100%; background:#fff; border:1px solid #e0e0e0; border-radius:10px; box-shadow:0 4px 16px rgba(0,0,0,.1); max-height:200px; overflow-y:auto; margin-top:3px; }
  .search-drop div { padding:.5rem .85rem; font-size:.875rem; cursor:pointer; border-bottom:1px solid #f4f4f4; }
  .search-drop div:last-child { border-bottom:0; }
  .search-drop div:hover { background:#eef0ff; }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  {{-- Header --}}
  <div class="hero-bar">
    <a href="{{ route('admin.cctv.surveys.index') }}" class="back-btn"><i class="bx bx-arrow-back"></i></a>
    <div>
      <h4>New CCTV Survey</h4>
      <p>Complete a detailed site assessment</p>
    </div>
  </div>

  @if($errors->any())
  <div class="alert alert-danger mb-3">
    <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
  </div>
  @endif

  <form method="POST" action="{{ route('admin.cctv.surveys.store') }}" enctype="multipart/form-data" id="surveyForm">
  @csrf

  {{-- ── Survey Type + Mode ── --}}
  <div class="card survey-card">
    <div class="card-body">
      <div class="row g-4">
        <div class="col-md-6">
          <label class="form-label fw-semibold small text-uppercase text-muted mb-2">Survey Type</label>
          <div class="pill-group" id="surveyTypeGroup">
            @foreach(['New Site','Upgrading','Modification','Service'] as $t)
            <label class="pill-label blue">
              <input type="radio" name="survey_type" value="{{ $t }}" {{ old('survey_type','New Site')===$t?'checked':'' }}>
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
              <input type="radio" name="survey_mode" value="{{ $m }}" {{ old('survey_mode','Detailed')===$m?'checked':'' }}>
              <span style="padding:.45rem 1.25rem;font-size:.9rem;">{{ $m }}</span>
            </label>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- ── DETAILED SURVEY ── --}}
  <div id="detailedSurvey" class="{{ old('survey_mode','Detailed')==='Simple'?'d-none':'' }}">

    {{-- Sticky nav --}}
    <div class="section-nav">
      @foreach([['s0','Basic'],['s1','Customer'],['s2','Site'],['s3','Purpose'],['s4','Cameras'],['s5','Network'],['s6','Power'],['s7','Install'],['s8','Materials'],['s9','Photos'],['s10','Risks'],['s11','Notes']] as [$id,$lbl])
      <a href="#{{ $id }}">{{ $lbl }}</a>
      @endforeach
    </div>

    {{-- ── S0: Basic ── --}}
    <div class="card survey-card" id="s0">
      <div class="card-header">
        <span class="sec-icon bg-label-primary">0</span> Basic Information
      </div>
      <div class="card-body row g-3">
        {{-- Mobile first — live search triggers on it --}}
        <div class="col-md-4">
          <label class="form-label fw-semibold">Mobile <span class="text-danger">*</span></label>
          <div class="position-relative">
            <input type="text" id="mobileSearch" name="mobile" autocomplete="off" placeholder="07X XXX XXXX"
              class="form-control" value="{{ old('mobile', $lead?->mobile ?? '') }}">
            <div id="mobileDropdown" class="search-drop d-none"></div>
          </div>
        </div>
        {{-- Customer name fills automatically after picking from dropdown --}}
        <div class="col-md-8">
          <label class="form-label fw-semibold">Customer Name <span class="text-danger">*</span></label>
          <div class="position-relative">
            <input type="text" id="customerSearch" autocomplete="off" placeholder="Auto-filled or type name…"
              class="form-control" value="{{ old('customer_name', $lead?->customer_name ?? '') }}">
            <input type="hidden" name="customer_name" id="customerNameHidden" value="{{ old('customer_name', $lead?->customer_name ?? '') }}">
            <input type="hidden" name="customer_id"   id="customerIdHidden">
            <input type="hidden" name="lead_id"       id="leadIdHidden" value="{{ old('lead_id', $lead?->id ?? '') }}">
            <div id="customerDropdown" class="search-drop d-none"></div>
          </div>
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold">Survey Date</label>
          <input type="date" name="survey_date" value="{{ old('survey_date', now()->toDateString()) }}" class="form-control">
        </div>
        <div class="col-md-8">
          <label class="form-label fw-semibold">Technician</label>
          <div class="position-relative">
            <input type="text" id="techSearch" autocomplete="off" placeholder="Search technician…"
              class="form-control" value="{{ old('technician_name') }}">
            <input type="hidden" name="technician_id" id="techIdHidden" value="{{ old('technician_id') }}">
            <div id="techDropdown" class="search-drop d-none"></div>
          </div>
        </div>
      </div>
    </div>

    {{-- ── S1: Customer Details ── --}}
    <div class="card survey-card" id="s1">
      <div class="card-header">
        <span class="sec-icon bg-label-primary">1</span> Customer Details
      </div>
      <div class="card-body row g-3">
        <div class="col-md-6">
          <label class="form-label fw-semibold">Contact Person</label>
          <input type="text" name="contact_person" value="{{ old('contact_person') }}" class="form-control" placeholder="Who to contact on-site">
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">Alt. Mobile</label>
          <input type="text" name="alt_mobile" value="{{ old('alt_mobile') }}" class="form-control" placeholder="Alternative number">
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">Email</label>
          <input type="email" name="email" value="{{ old('email') }}" class="form-control">
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">GPS Location</label>
          <div class="input-group">
            <input type="text" name="gps_location" id="gpsInput" value="{{ old('gps_location') }}" class="form-control" placeholder="Latitude, Longitude">
            <button type="button" id="gpsBtn" class="btn btn-outline-secondary d-flex align-items-center gap-1" title="Auto-fetch location">
              <i class="bx bx-current-location" id="gpsIcon"></i>
              <span id="gpsBtnText">Fetch</span>
            </button>
          </div>
          <div id="gpsStatus" class="form-text d-none"></div>
        </div>
        <div class="col-12">
          <label class="form-label fw-semibold d-block mb-2">Customer Type</label>
          <div class="pill-group" id="customerTypeGroup">
            @foreach(['House','Shop','Office','Factory','School','Hotel','Government','Other'] as $ct)
            <label class="pill-label">
              <input type="radio" name="customer_type" value="{{ $ct }}" {{ old('customer_type')===$ct?'checked':'' }}>
              <span>{{ $ct }}</span>
            </label>
            @endforeach
          </div>
          <div id="customerTypeOtherWrap" class="{{ old('customer_type')==='Other'?'':'d-none' }} mt-2">
            <input type="text" name="customer_type_other" value="{{ old('customer_type_other') }}" class="form-control" placeholder="Specify type…">
          </div>
        </div>
      </div>
    </div>

    {{-- ── S2: Site ── --}}
    <div class="card survey-card" id="s2">
      <div class="card-header">
        <span class="sec-icon bg-label-success">2</span> Site Information
      </div>
      <div class="card-body row g-3">
        <div class="col-md-6">
          <label class="form-label fw-semibold">Building Name / Address</label>
          <input type="text" name="building_name" value="{{ old('building_name', $lead?->site_address ?? '') }}" class="form-control">
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">Building Type</label>
          <input type="text" name="building_type" value="{{ old('building_type') }}" class="form-control" placeholder="e.g. 2-storey, Villa…">
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold">Site Size</label>
          <input type="text" name="site_size" value="{{ old('site_size') }}" class="form-control" placeholder="e.g. 5000 sqft">
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold">Construction Status</label>
          <select name="construction_status" class="form-select">
            <option value="">— Select —</option>
            @foreach(['Existing','Under Construction','New Building'] as $cs)
            <option value="{{ $cs }}" {{ old('construction_status')===$cs?'selected':'' }}>{{ $cs }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-4 d-flex align-items-end pb-1">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="existing_security_system" id="existingSec" value="1" {{ old('existing_security_system')?'checked':'' }}>
            <label class="form-check-label fw-semibold" for="existingSec">Existing security system?</label>
          </div>
        </div>
      </div>
    </div>

    {{-- ── S3: Purpose ── --}}
    <div class="card survey-card" id="s3">
      <div class="card-header">
        <span class="sec-icon bg-label-warning">3</span> Purpose / Requirements
      </div>
      <div class="card-body">
        @php
        $purposeOptions = ['Theft Prevention','Employee Monitoring','Perimeter Security','Visitor Tracking','Fire/Safety Monitoring','Remote Monitoring','Evidence Recording','Access Control Integration','Child/Elder Safety','General Surveillance'];
        $selectedPurposes = old('purposes', []);
        @endphp
        <div class="row g-2">
          @foreach($purposeOptions as $p)
          <div class="col-6 col-md-4">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="purposes[]" value="{{ $p }}" id="pur_{{ $loop->index }}" {{ in_array($p,$selectedPurposes)?'checked':'' }}>
              <label class="form-check-label" for="pur_{{ $loop->index }}">{{ $p }}</label>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>

    {{-- ── S4: Camera Locations ── --}}
    <div class="card survey-card" id="s4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <span class="d-flex align-items-center gap-2"><span class="sec-icon bg-label-info">4</span> Camera Locations</span>
        <button type="button" id="addCamBtn" class="btn btn-sm btn-primary d-flex align-items-center gap-1">
          <i class="bx bx-plus"></i> Add Row
        </button>
      </div>
      <div class="card-body p-0">
        <div class="px-3 pt-3">
          <div class="cam-table-head">
            <div>Location / Description</div>
            <div>In/Out</div>
            <div>Camera Type</div>
            <div>MP</div>
            <div class="text-center">Night</div>
            <div class="text-center">Audio</div>
            <div></div>
          </div>
        </div>
        <div id="camRows" class="px-3 pb-3">
          <div class="cam-row">
            <input type="text" name="cam_location[]" placeholder="e.g. Front Gate" class="form-control form-control-sm">
            <select name="cam_io[]" class="form-select form-select-sm">
              <option>Indoor</option><option>Outdoor</option>
            </select>
            <input type="text" name="cam_type[]" placeholder="Dome/Bullet…" class="form-control form-control-sm">
            <input type="text" name="cam_mp[]" placeholder="2MP" class="form-control form-control-sm">
            <div class="text-center"><input type="checkbox" name="cam_nv[]" value="1" class="form-check-input" title="Night Vision"></div>
            <div class="text-center"><input type="checkbox" name="cam_audio[]" value="1" class="form-check-input" title="Audio"></div>
            <div class="text-center">
              <button type="button" class="remove-cam-btn btn btn-sm btn-icon btn-outline-danger" title="Remove">
                <i class="bx bx-x"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- ── S5: Network ── --}}
    <div class="card survey-card" id="s5">
      <div class="card-header">
        <span class="sec-icon bg-label-info">5</span> Network / Connectivity
      </div>
      <div class="card-body row g-3">
        <div class="col-md-4">
          <label class="form-label fw-semibold">Internet Status</label>
          <select name="internet_status" id="internetStatusSel" class="form-select">
            <option value="">— Select —</option>
            <option value="Available"     {{ old('internet_status')==='Available'?'selected':'' }}>Available</option>
            <option value="Not Available" {{ old('internet_status')==='Not Available'?'selected':'' }}>Not Available</option>
          </select>
        </div>
        <div class="col-md-4 {{ old('internet_status')==='Available'?'':'d-none' }}" id="ispWrap">
          <label class="form-label fw-semibold">ISP</label>
          <select name="isp" id="ispSel" class="form-select">
            <option value="">— Select —</option>
            @foreach(['SLT','Dialog','Starlink','Other'] as $isp)
            <option value="{{ $isp }}" {{ old('isp')===$isp?'selected':'' }}>{{ $isp }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-4 {{ old('isp')==='Other'?'':'d-none' }}" id="ispOtherWrap">
          <label class="form-label fw-semibold">ISP Name</label>
          <input type="text" name="isp_other" value="{{ old('isp_other') }}" class="form-control" placeholder="ISP name…">
        </div>
        <div class="col-12 d-flex gap-4 flex-wrap">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="wifi_coverage" id="wifiCov" value="1" {{ old('wifi_coverage')?'checked':'' }}>
            <label class="form-check-label" for="wifiCov">WiFi coverage at site?</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="lan_available" id="lanAvail" value="1" {{ old('lan_available')?'checked':'' }}>
            <label class="form-check-label" for="lanAvail">LAN / Ethernet available?</label>
          </div>
        </div>
      </div>
    </div>

    {{-- ── S6: Power ── --}}
    <div class="card survey-card" id="s6">
      <div class="card-header">
        <span class="sec-icon bg-label-warning">6</span> Power Supply
      </div>
      <div class="card-body row g-3">
        <div class="col-md-4">
          <label class="form-label fw-semibold">Power Availability</label>
          <select name="power_availability" class="form-select">
            <option value="">— Select —</option>
            @foreach(['Stable','Moderate','Poor'] as $pa)
            <option value="{{ $pa }}" {{ old('power_availability')===$pa?'selected':'' }}>{{ $pa }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-8 d-flex align-items-end gap-4 flex-wrap pb-1">
          @foreach([['ups_required','UPS Required?'],['electrical_work_required','Electrical Work Required?'],['voltage_issues','Voltage Issues Observed?']] as [$fn,$fl])
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="{{ $fn }}" id="{{ $fn }}" value="1" {{ old($fn)?'checked':'' }}>
            <label class="form-check-label" for="{{ $fn }}">{{ $fl }}</label>
          </div>
          @endforeach
        </div>
      </div>
    </div>

    {{-- ── S7: Installation ── --}}
    <div class="card survey-card" id="s7">
      <div class="card-header">
        <span class="sec-icon bg-label-danger">7</span> Installation Assessment
      </div>
      <div class="card-body row g-3">
        <div class="col-md-4">
          <label class="form-label fw-semibold">Cable Route</label>
          <select name="cable_route" class="form-select">
            <option value="">— Select —</option>
            @foreach(['Easy','Medium','Difficult'] as $cr)
            <option value="{{ $cr }}" {{ old('cable_route')===$cr?'selected':'' }}>{{ $cr }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold">Ceiling Type</label>
          <select name="ceiling_type" class="form-select">
            <option value="">— Select —</option>
            @foreach(['Concrete','Gypsum','Metal','Wooden'] as $ct)
            <option value="{{ $ct }}" {{ old('ceiling_type')===$ct?'selected':'' }}>{{ $ct }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold">Wall Type</label>
          <select name="wall_type" class="form-select">
            <option value="">— Select —</option>
            @foreach(['Brick','Concrete','Partition'] as $wt)
            <option value="{{ $wt }}" {{ old('wall_type')===$wt?'selected':'' }}>{{ $wt }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-12 d-flex gap-4 flex-wrap">
          @foreach([['ladder_required','Ladder Required'],['scaffolding_required','Scaffolding Required']] as [$fn,$fl])
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="{{ $fn }}" id="{{ $fn }}" value="1" {{ old($fn)?'checked':'' }}>
            <label class="form-check-label" for="{{ $fn }}">{{ $fl }}</label>
          </div>
          @endforeach
        </div>
        <div class="col-md-8">
          <label class="form-label fw-semibold">Height Risk Level: <strong id="heightRiskVal">{{ old('height_risk',0) }}</strong> / 10</label>
          <input type="range" class="form-range" name="height_risk" id="heightRiskRange" min="0" max="10" value="{{ old('height_risk',0) }}">
          <div class="d-flex justify-content-between" style="font-size:.75rem;color:#adb5bd;margin-top:-4px;">
            <span>0 (Safe)</span><span>5 (Medium)</span><span>10 (Extreme)</span>
          </div>
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold">Special Safety Equipment</label>
          <input type="text" name="special_safety_equipment" value="{{ old('special_safety_equipment') }}" class="form-control" placeholder="e.g. Safety harness…">
        </div>
      </div>
    </div>

    {{-- ── S8: Materials ── --}}
    <div class="card survey-card" id="s8">
      <div class="card-header">
        <span class="sec-icon bg-label-success">8</span> Material Estimation
      </div>
      <div class="card-body">
        <div class="row g-3 mb-4">
          <div class="col-6 col-md-3">
            <label class="form-label fw-semibold">Total Cameras</label>
            <input type="number" name="cameras_qty" id="camerasQty" value="{{ old('cameras_qty',0) }}" min="0" class="form-control">
          </div>
          <div class="col-6 col-md-3">
            <label class="form-label fw-semibold">DVR Channels</label>
            <input type="number" name="dvr_channels" value="{{ old('dvr_channels',0) }}" min="0" class="form-control">
          </div>
          <div class="col-6 col-md-3">
            <label class="form-label fw-semibold">HDD Storage (days)</label>
            <input type="number" name="hdd_storage_days" value="{{ old('hdd_storage_days',30) }}" min="1" class="form-control">
          </div>
          <div class="col-6 col-md-3">
            <label class="form-label fw-semibold">Cable (meters)</label>
            <input type="number" name="cable_meters" value="{{ old('cable_meters',0) }}" min="0" class="form-control">
          </div>
        </div>

        {{-- Accessories --}}
        <div class="d-flex justify-content-between align-items-center mb-2">
          <p class="fw-bold small text-uppercase text-muted mb-0">Additional Accessories</p>
          <button type="button" id="addAccBtn" class="btn btn-sm btn-success d-flex align-items-center gap-1">
            <i class="bx bx-plus"></i> Add Item
          </button>
        </div>
        <div id="accRows"></div>
      </div>
    </div>

    {{-- ── S9: Photos ── --}}
    <div class="card survey-card" id="s9">
      <div class="card-header">
        <span class="sec-icon bg-label-danger">9</span> Site Photos
      </div>
      <div class="card-body">
        <div class="photo-drop" id="photoDropZone">
          <i class="bx bx-image-add" style="font-size:2.5rem;color:#d9dde1;"></i>
          <p class="mb-1 mt-2 fw-semibold text-muted">Tap to add photos or drag & drop</p>
          <p class="small text-muted mb-0">JPG, PNG, HEIC — multiple allowed</p>
          <input type="file" name="site_photos[]" id="sitePhotosInput" multiple accept="image/*" class="d-none">
        </div>
        <div id="photoPreview" class="d-flex flex-wrap gap-2 mt-3"></div>
      </div>
    </div>

    {{-- ── S10: Risks ── --}}
    <div class="card survey-card" id="s10">
      <div class="card-header">
        <span class="sec-icon bg-label-danger">10</span> Risk Assessment
      </div>
      <div class="card-body">
        @php
        $riskOptions = ['High-rise installation','Confined space','Electrical hazard','Unstable structure','Aggressive animals','Flooding risk','Extreme heat','Poor lighting','Traffic exposure','Customer access restrictions','No signal area','Vandalism risk'];
        $selectedRisks = old('risks', []);
        @endphp
        <div class="row g-2">
          @foreach($riskOptions as $r)
          <div class="col-6 col-md-4">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="risks[]" value="{{ $r }}" id="risk_{{ $loop->index }}" {{ in_array($r,$selectedRisks)?'checked':'' }}>
              <label class="form-check-label" for="risk_{{ $loop->index }}">{{ $r }}</label>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>

    {{-- ── S11: Notes ── --}}
    <div class="card survey-card" id="s11">
      <div class="card-header">
        <span class="sec-icon bg-label-secondary">11</span> Special Notes
      </div>
      <div class="card-body">
        <textarea name="special_notes" class="form-control" rows="4" placeholder="Any additional observations, customer requests, or instructions…">{{ old('special_notes') }}</textarea>
      </div>
    </div>

  </div>{{-- /detailedSurvey --}}

  {{-- ══════════════════════════════════════════════════════════
       SIMPLE SURVEY
  ══════════════════════════════════════════════════════════ --}}
  <div id="simpleSurvey" class="{{ old('survey_mode','Detailed')==='Simple'?'':'d-none' }}">

    {{-- Row 1: Camera & DVR --}}
    <div class="card survey-card">
      <div class="card-header">
        <span class="sec-icon bg-primary bg-opacity-10 text-primary"><i class="bx bx-camera"></i></span>
        Camera & Recorder
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-3 col-6">
            <label class="form-label fw-semibold small">No. of Cameras</label>
            <input type="number" name="simple_num_cameras" class="form-control"
                   value="{{ old('simple_num_cameras', 0) }}" min="0" max="999">
          </div>
          <div class="col-md-3 col-6">
            <label class="form-label fw-semibold small">DVR / NVR</label>
            <select name="simple_dvr_nvr" class="form-select">
              <option value="">-- Select --</option>
              @foreach(['DVR','NVR'] as $d)
              <option value="{{ $d }}" {{ old('simple_dvr_nvr')===$d?'selected':'' }}>{{ $d }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3 col-6">
            <label class="form-label fw-semibold small">Channels</label>
            <select name="simple_dvr_channels" class="form-select">
              <option value="">-- Select --</option>
              @foreach([4,8,16,32,64] as $ch)
              <option value="{{ $ch }}" {{ old('simple_dvr_channels')==$ch?'selected':'' }}>{{ $ch }} CH</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3 col-6">
            <label class="form-label fw-semibold small">No. of Technicians</label>
            <input type="number" name="simple_num_technicians" class="form-control"
                   value="{{ old('simple_num_technicians', 1) }}" min="1" max="20">
          </div>
          <div class="col-md-3 col-6">
            <label class="form-label fw-semibold small">Estimated Days</label>
            <input type="number" name="simple_estimated_days" class="form-control"
                   value="{{ old('simple_estimated_days', 1) }}" min="1" max="365">
          </div>
        </div>
      </div>
    </div>

    {{-- Row 2: Internet --}}
    <div class="card survey-card">
      <div class="card-header">
        <span class="sec-icon bg-info bg-opacity-10 text-info"><i class="bx bx-wifi"></i></span>
        Internet
      </div>
      <div class="card-body">
        <div class="row g-3 align-items-end">
          <div class="col-md-4 col-12">
            <label class="form-label fw-semibold small">Internet Available?</label>
            <div class="d-flex gap-3 mt-1">
              <div class="form-check">
                <input class="form-check-input" type="radio" name="simple_internet_available"
                       id="siYes" value="1" {{ old('simple_internet_available')=='1'?'checked':'' }}>
                <label class="form-check-label" for="siYes">Yes</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="simple_internet_available"
                       id="siNo" value="0" {{ old('simple_internet_available','0')=='0'?'checked':'' }}>
                <label class="form-check-label" for="siNo">No</label>
              </div>
            </div>
          </div>
          <div class="col-md-4 col-12">
            <label class="form-label fw-semibold small">ISP</label>
            <select name="simple_isp" class="form-select" id="simpleIspSelect">
              <option value="">-- Select ISP --</option>
              @foreach(['SLT','Dialog','Starlink','Other','None'] as $isp)
              <option value="{{ $isp }}" {{ old('simple_isp')===$isp?'selected':'' }}>{{ $isp }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>
    </div>

    {{-- Row 3: Sliders --}}
    <div class="card survey-card">
      <div class="card-header">
        <span class="sec-icon bg-warning bg-opacity-10 text-warning"><i class="bx bx-slider"></i></span>
        Site Assessment
      </div>
      <div class="card-body">
        <div class="row g-4">
          <div class="col-md-6">
            <label class="form-label fw-semibold small d-flex justify-content-between">
              Cabling Easiness
              <span class="badge bg-primary" id="cablingVal">{{ old('simple_cabling_ease', 5) }}</span>
            </label>
            <input type="range" name="simple_cabling_ease" class="form-range" min="1" max="10" step="1"
                   value="{{ old('simple_cabling_ease', 5) }}" id="cablingSlider">
            <div class="d-flex justify-content-between text-muted" style="font-size:.72rem;margin-top:2px;">
              <span>1 (Very Difficult)</span><span>10 (Very Easy)</span>
            </div>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold small d-flex justify-content-between">
              Risk Level
              <span class="badge bg-danger" id="riskVal">{{ old('simple_risk_level', 5) }}</span>
            </label>
            <input type="range" name="simple_risk_level" class="form-range" min="1" max="10" step="1"
                   value="{{ old('simple_risk_level', 5) }}" id="riskSlider">
            <div class="d-flex justify-content-between text-muted" style="font-size:.72rem;margin-top:2px;">
              <span>1 (Low Risk)</span><span>10 (High Risk)</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Row 4: GPS --}}
    <div class="card survey-card">
      <div class="card-header">
        <span class="sec-icon bg-success bg-opacity-10 text-success"><i class="bx bx-map-pin"></i></span>
        Location
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label fw-semibold small">GPS Location</label>
            <div class="input-group">
              <input type="text" name="simple_gps_location" id="simpleGps" class="form-control"
                     placeholder="lat, lng — or tap Fetch"
                     value="{{ old('simple_gps_location') }}">
              <button type="button" class="btn btn-outline-secondary" id="simpleGpsBtn">
                <i class="bx bx-current-location me-1"></i>Fetch
              </button>
            </div>
            <div id="simpleGpsStatus" class="form-text text-muted" style="min-height:1.2em;"></div>
          </div>
        </div>
      </div>
    </div>

    {{-- Row 5: Status, Remark, Survey By --}}
    <div class="card survey-card">
      <div class="card-header">
        <span class="sec-icon bg-secondary bg-opacity-10 text-secondary"><i class="bx bx-info-circle"></i></span>
        Outcome
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label fw-semibold small">Status</label>
            <select name="status" class="form-select" id="simpleStatusSelect">
              @foreach(['Scheduled','Completed','Need More Time','Cancelled'] as $s)
              <option value="{{ $s }}" {{ old('status','Scheduled')===$s?'selected':'' }}>{{ $s }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold small">Survey By</label>
            <select name="technician_id" class="form-select" id="simpleTechSelect">
              <option value="">-- Select Technician --</option>
              @foreach($employees as $emp)
              <option value="{{ $emp->id }}" {{ old('technician_id')==$emp->id?'selected':'' }}>
                {{ $emp->employee_name }}
              </option>
              @endforeach
            </select>
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold small">Remark</label>
            <textarea name="simple_remark" class="form-control" rows="3"
                      placeholder="Any notes, observations or follow-up actions…">{{ old('simple_remark') }}</textarea>
          </div>
        </div>
      </div>
    </div>

  </div>{{-- /simpleSurvey --}}

  {{-- Submit --}}
  <div class="d-flex justify-content-end gap-2 pb-5">
    <a href="{{ route('admin.cctv.surveys.index') }}" class="btn btn-outline-secondary">Cancel</a>
    <button type="submit" class="btn btn-primary px-4">
      <i class="bx bx-save me-1"></i> Save Survey
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
// ── Live search helpers ──────────────────────────────────────────
const leadsData   = @json($leadsJson);
const custNameHid = document.getElementById('customerNameHidden');
const custIdHid   = document.getElementById('customerIdHidden');
const leadIdHid   = document.getElementById('leadIdHidden');

function fillCustomer(lead) {
    const mobInput  = document.getElementById('mobileSearch');
    const nameInput = document.getElementById('customerSearch');
    if (mobInput  && !mobInput.value)  mobInput.value  = lead.mobile || '';
    if (nameInput) nameInput.value = lead.name || '';
    custNameHid.value = lead.name  || '';
    leadIdHid.value   = lead.lead_id || '';
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

// ── Mobile search (primary) ──────────────────────────────────────
const mobSearch = document.getElementById('mobileSearch');
const mobDrop   = document.getElementById('mobileDropdown');

mobSearch.addEventListener('input', function() {
    const q = this.value.trim();
    leadIdHid.value = '';
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

// ── Customer name search (secondary) ─────────────────────────────
const custSearch = document.getElementById('customerSearch');
const custDrop   = document.getElementById('customerDropdown');

custSearch.addEventListener('input', function() {
    const q = this.value.trim().toLowerCase();
    custNameHid.value = this.value;
    leadIdHid.value   = '';
    if (q.length < 1) { custDrop.classList.add('d-none'); return; }
    const hits = leadsData.filter(l => l.name.toLowerCase().includes(q)).slice(0,8);
    if (!hits.length) { custDrop.classList.add('d-none'); return; }
    buildDrop(custDrop, hits, function(el) {
        custSearch.value  = el.dataset.name;
        fillCustomer({ name: el.dataset.name, mobile: el.dataset.mobile, lead_id: el.dataset.id });
    });
});
document.addEventListener('click', e => {
    if (!custSearch.contains(e.target) && !custDrop.contains(e.target)) custDrop.classList.add('d-none');
});

// ── Technician live search ───────────────────────────────────────
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

// ── Survey Mode toggle ───────────────────────────────────────────
document.querySelectorAll('input[name="survey_mode"]').forEach(inp => {
    inp.addEventListener('change', function() {
        document.getElementById('detailedSurvey').classList.toggle('d-none', this.value !== 'Detailed');
        document.getElementById('simpleSurvey').classList.toggle('d-none', this.value !== 'Simple');
    });
});

// ── Customer Type "Other" reveal ─────────────────────────────────
document.querySelectorAll('input[name="customer_type"]').forEach(inp => {
    inp.addEventListener('change', function() {
        document.getElementById('customerTypeOtherWrap').classList.toggle('d-none', this.value !== 'Other');
    });
});

// ── Internet / ISP conditional ───────────────────────────────────
document.getElementById('internetStatusSel').addEventListener('change', function() {
    document.getElementById('ispWrap').classList.toggle('d-none', this.value !== 'Available');
    if (this.value !== 'Available') document.getElementById('ispOtherWrap').classList.add('d-none');
});
document.getElementById('ispSel').addEventListener('change', function() {
    document.getElementById('ispOtherWrap').classList.toggle('d-none', this.value !== 'Other');
});

// ── Height Risk slider ───────────────────────────────────────────
document.getElementById('heightRiskRange').addEventListener('input', function() {
    document.getElementById('heightRiskVal').textContent = this.value;
});

// ── Camera rows repeater ─────────────────────────────────────────
function makeCamRow() {
    const d = document.createElement('div');
    d.className = 'cam-row';
    d.innerHTML = `
        <input type="text" name="cam_location[]" placeholder="e.g. Back door" class="form-control form-control-sm">
        <select name="cam_io[]" class="form-select form-select-sm"><option>Indoor</option><option>Outdoor</option></select>
        <input type="text" name="cam_type[]" placeholder="Dome/Bullet…" class="form-control form-control-sm">
        <input type="text" name="cam_mp[]" placeholder="2MP" class="form-control form-control-sm">
        <div class="text-center"><input type="checkbox" name="cam_nv[]" value="1" class="form-check-input" title="Night Vision"></div>
        <div class="text-center"><input type="checkbox" name="cam_audio[]" value="1" class="form-check-input" title="Audio"></div>
        <div class="text-center">
            <button type="button" class="remove-cam-btn btn btn-sm btn-icon btn-outline-danger"><i class="bx bx-x"></i></button>
        </div>`;
    bindRemoveCam(d.querySelector('.remove-cam-btn'));
    return d;
}
function bindRemoveCam(btn) {
    btn.addEventListener('click', function() {
        if (document.querySelectorAll('.cam-row').length > 1) this.closest('.cam-row').remove();
    });
}
document.getElementById('addCamBtn').addEventListener('click', () => {
    const r = makeCamRow();
    document.getElementById('camRows').appendChild(r);
    r.querySelector('input[type="text"]').focus();
});
document.querySelectorAll('.remove-cam-btn').forEach(bindRemoveCam);

// ── Accessories repeater ─────────────────────────────────────────
function makeAccRow() {
    const d = document.createElement('div');
    d.className = 'acc-row';
    d.innerHTML = `
        <input type="text" name="acc_name[]" placeholder="Item name (e.g. BNC Connector)" class="form-control form-control-sm flex-grow-1" required>
        <input type="number" name="acc_qty[]" placeholder="Qty" min="1" value="1" class="form-control form-control-sm" style="width:80px">
        <button type="button" class="remove-acc-btn btn btn-sm btn-icon btn-outline-danger flex-shrink-0"><i class="bx bx-x"></i></button>`;
    d.querySelector('.remove-acc-btn').addEventListener('click', () => d.remove());
    return d;
}
document.getElementById('addAccBtn').addEventListener('click', () => {
    const r = makeAccRow();
    document.getElementById('accRows').appendChild(r);
    r.querySelector('input[type="text"]').focus();
});

// ── Photo upload preview ─────────────────────────────────────────
const photoInput    = document.getElementById('sitePhotosInput');
const photoPreview  = document.getElementById('photoPreview');
const photoDropZone = document.getElementById('photoDropZone');

photoDropZone.addEventListener('click', () => photoInput.click());
photoDropZone.addEventListener('dragover', e => { e.preventDefault(); photoDropZone.style.borderColor='#696cff'; });
photoDropZone.addEventListener('dragleave', () => photoDropZone.style.borderColor='');
photoDropZone.addEventListener('drop', e => {
    e.preventDefault();
    photoDropZone.style.borderColor = '';
    handleFiles(e.dataTransfer.files);
});
photoInput.addEventListener('change', () => handleFiles(photoInput.files));

function handleFiles(files) {
    Array.from(files).forEach(file => {
        const reader = new FileReader();
        reader.onload = e => {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.cssText = 'width:80px;height:80px;object-fit:cover;border-radius:8px;border:2px solid #e0e0e0;';
            photoPreview.appendChild(img);
        };
        reader.readAsDataURL(file);
    });
}

// ── GPS auto-fetch ───────────────────────────────────────────────
document.getElementById('gpsBtn').addEventListener('click', function() {
    const btn      = this;
    const icon     = document.getElementById('gpsIcon');
    const txt      = document.getElementById('gpsBtnText');
    const status   = document.getElementById('gpsStatus');
    const input    = document.getElementById('gpsInput');

    if (!navigator.geolocation) {
        status.textContent = 'Geolocation not supported by this browser.';
        status.className   = 'form-text text-danger';
        status.classList.remove('d-none');
        return;
    }

    // Loading state
    btn.disabled   = true;
    icon.className = 'bx bx-loader-alt bx-spin';
    txt.textContent = 'Fetching…';
    status.classList.add('d-none');

    navigator.geolocation.getCurrentPosition(
        function(pos) {
            const lat = pos.coords.latitude.toFixed(6);
            const lng = pos.coords.longitude.toFixed(6);
            input.value       = lat + ', ' + lng;
            icon.className    = 'bx bx-check';
            txt.textContent   = 'Got it';
            btn.disabled      = false;
            btn.classList.replace('btn-outline-secondary', 'btn-outline-success');
            status.innerHTML  = `<a href="https://maps.google.com/?q=${lat},${lng}" target="_blank" class="text-primary">View on Google Maps ↗</a>`;
            status.className  = 'form-text';
            status.classList.remove('d-none');
            // Reset button after 3s
            setTimeout(() => {
                icon.className  = 'bx bx-current-location';
                txt.textContent = 'Fetch';
                btn.classList.replace('btn-outline-success', 'btn-outline-secondary');
            }, 3000);
        },
        function(err) {
            icon.className  = 'bx bx-x';
            txt.textContent = 'Failed';
            btn.disabled    = false;
            const msgs = { 1:'Permission denied.', 2:'Position unavailable.', 3:'Request timed out.' };
            status.textContent = msgs[err.code] || 'Could not get location.';
            status.className   = 'form-text text-danger';
            status.classList.remove('d-none');
            setTimeout(() => {
                icon.className  = 'bx bx-current-location';
                txt.textContent = 'Fetch';
            }, 2000);
        },
        { enableHighAccuracy: true, timeout: 10000 }
    );
});

// ── Simple survey: slider live labels ──────────────────────────
const cablingSlider = document.getElementById('cablingSlider');
const riskSlider    = document.getElementById('riskSlider');
if (cablingSlider) {
    cablingSlider.addEventListener('input', () => {
        document.getElementById('cablingVal').textContent = cablingSlider.value;
    });
}
if (riskSlider) {
    riskSlider.addEventListener('input', () => {
        document.getElementById('riskVal').textContent = riskSlider.value;
    });
}

// ── Simple survey: GPS fetch ────────────────────────────────────
const simpleGpsBtn = document.getElementById('simpleGpsBtn');
if (simpleGpsBtn) {
    simpleGpsBtn.addEventListener('click', function() {
        const input  = document.getElementById('simpleGps');
        const status = document.getElementById('simpleGpsStatus');
        if (!navigator.geolocation) {
            status.textContent = 'Geolocation not supported.';
            return;
        }
        simpleGpsBtn.disabled = true;
        simpleGpsBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Fetching…';
        status.textContent = '';
        navigator.geolocation.getCurrentPosition(
            pos => {
                const lat = pos.coords.latitude.toFixed(6);
                const lng = pos.coords.longitude.toFixed(6);
                input.value = lat + ', ' + lng;
                status.innerHTML = `<a href="https://maps.google.com/?q=${lat},${lng}" target="_blank" class="text-primary">View on Google Maps ↗</a>`;
                simpleGpsBtn.disabled = false;
                simpleGpsBtn.innerHTML = '<i class="bx bx-current-location me-1"></i>Fetch';
            },
            err => {
                const msgs = {1:'Permission denied.',2:'Position unavailable.',3:'Timed out.'};
                status.textContent = msgs[err.code] || 'Failed.';
                status.className = 'form-text text-danger';
                simpleGpsBtn.disabled = false;
                simpleGpsBtn.innerHTML = '<i class="bx bx-current-location me-1"></i>Fetch';
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    });
}
</script>
@endpush
