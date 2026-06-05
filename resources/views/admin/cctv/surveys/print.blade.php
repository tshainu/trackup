<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Survey Report – {{ $survey->survey_no }}</title>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: Arial, Helvetica, sans-serif;
      font-size: 12px;
      color: #1a1a2e;
      background: #f5f5f5;
    }

    /* ── Toolbar (no-print) ── */
    .toolbar {
      position: fixed; top: 0; left: 0; right: 0; z-index: 100;
      background: #1a1a2e; color: #fff;
      display: flex; align-items: center; gap: 10px;
      padding: 10px 20px;
      box-shadow: 0 2px 8px rgba(0,0,0,.3);
    }
    .toolbar .tbar-title { flex: 1; font-size: 14px; font-weight: 700; }
    .toolbar .tbar-sub { font-size: 11px; opacity: .7; }
    .toolbar button, .toolbar a {
      display: inline-flex; align-items: center; gap: 6px;
      padding: 7px 16px; border-radius: 6px; border: none;
      font-size: 12px; font-weight: 600; cursor: pointer;
      text-decoration: none;
    }
    .btn-print  { background: #696cff; color: #fff; }
    .btn-share  { background: #25d366; color: #fff; }
    .btn-copy   { background: #555;    color: #fff; }
    .btn-back   { background: rgba(255,255,255,.15); color: #fff; border: 1px solid rgba(255,255,255,.25); }
    .btn-print:hover { background: #5a5de8; }
    .btn-share:hover { background: #1fbb58; }
    .btn-copy:hover  { background: #3a3a3a; }

    /* ── Page (A4) ── */
    .page {
      max-width: 794px;
      margin: 70px auto 40px;
      background: #fff;
      box-shadow: 0 4px 24px rgba(0,0,0,.12);
    }

    /* ── Document Header ── */
    .doc-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      padding: 28px 32px 20px;
      border-bottom: 3px solid #696cff;
      background: linear-gradient(135deg, #fafafe, #fff);
    }
    .brand-name  { font-size: 20px; font-weight: 800; color: #696cff; letter-spacing: -.3px; }
    .brand-sub   { font-size: 10px; color: #888; text-transform: uppercase; letter-spacing: .08em; margin-top: 2px; }
    .brand-info  { font-size: 10.5px; color: #555; margin-top: 8px; line-height: 1.7; }
    .doc-meta    { text-align: right; }
    .doc-type    { font-size: 18px; font-weight: 800; color: #1a1a2e; text-transform: uppercase; letter-spacing: 1px; }
    .doc-no      { font-size: 13px; font-weight: 700; color: #696cff; margin-top: 4px; }
    .doc-date    { font-size: 10.5px; color: #888; margin-top: 3px; }
    .doc-badge   { display: inline-block; margin-top: 6px; padding: 3px 10px; border-radius: 20px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; }
    .badge-completed    { background: #d1fae5; color: #065f46; }
    .badge-scheduled    { background: #dbeafe; color: #1d4ed8; }
    .badge-cancelled    { background: #fee2e2; color: #991b1b; }
    .badge-needmoretime { background: #fef3c7; color: #92400e; }

    /* ── Section layout ── */
    .body { padding: 24px 32px; }

    .section { margin-bottom: 20px; }
    .section-title {
      font-size: 10px; font-weight: 800; text-transform: uppercase;
      letter-spacing: .08em; color: #696cff;
      border-bottom: 1.5px solid #e8e8ff;
      padding-bottom: 5px; margin-bottom: 12px;
      display: flex; align-items: center; gap: 6px;
    }
    .section-title::before {
      content: ''; display: inline-block;
      width: 3px; height: 14px; background: #696cff; border-radius: 2px;
    }

    /* ── Grid rows ── */
    .grid { display: flex; flex-wrap: wrap; gap: 0; }
    .cell { flex: 0 0 25%; padding: 6px 10px 6px 0; }
    .cell.half { flex: 0 0 50%; }
    .cell.full { flex: 0 0 100%; }
    .cell.third { flex: 0 0 33.333%; }
    .lbl { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #aaa; margin-bottom: 2px; }
    .val { font-size: 12px; font-weight: 500; color: #1a1a2e; }
    .val.mono { font-family: monospace; }
    .val.big  { font-size: 20px; font-weight: 800; color: #696cff; }

    /* ── Progress bar ── */
    .bar-wrap { height: 8px; background: #e9ecef; border-radius: 10px; margin-top: 4px; overflow: hidden; }
    .bar-fill  { height: 8px; border-radius: 10px; }
    .bar-primary { background: #696cff; }
    .bar-success { background: #28a745; }
    .bar-warning { background: #ffc107; }
    .bar-danger  { background: #dc3545; }
    .bar-labels  { display: flex; justify-content: space-between; font-size: 9px; color: #aaa; margin-top: 2px; }

    /* ── Camera table ── */
    table { width: 100%; border-collapse: collapse; font-size: 11px; }
    thead tr { background: #696cff; color: #fff; }
    thead th { padding: 7px 10px; font-size: 10px; font-weight: 700; text-align: left; text-transform: uppercase; letter-spacing: .04em; }
    tbody tr { border-bottom: 1px solid #f0f0f0; }
    tbody tr:nth-child(even) { background: #fafafe; }
    tbody td { padding: 6px 10px; }

    /* ── Tags / badges ── */
    .tag { display: inline-block; background: #eef0ff; color: #696cff; border: 1px solid #d0d3ff; border-radius: 4px; padding: 2px 8px; font-size: 10px; font-weight: 600; margin: 2px 2px 2px 0; }
    .tag.gray { background: #f3f4f6; color: #555; border-color: #ddd; }
    .tag.green { background: #d1fae5; color: #065f46; border-color: #a7f3d0; }
    .tag.orange { background: #fef3c7; color: #92400e; border-color: #fde68a; }

    /* ── Yes/No indicators ── */
    .yn-yes { color: #059669; font-weight: 700; }
    .yn-no  { color: #aaa; }

    /* ── Signature row ── */
    .sig-row { display: flex; gap: 32px; margin-top: 32px; padding-top: 20px; border-top: 1px solid #e9ecef; }
    .sig-box { flex: 1; }
    .sig-line { border-top: 1px solid #333; margin-top: 36px; margin-bottom: 4px; }
    .sig-name { font-size: 10px; color: #555; }

    /* ── Footer ── */
    .doc-footer {
      text-align: center; font-size: 10px; color: #bbb;
      padding: 14px 32px;
      border-top: 1px solid #f0f0f0;
    }

    /* ── Print styles ── */
    @media print {
      body { background: #fff; font-size: 11px; }
      .toolbar { display: none; }
      .page { margin: 0; box-shadow: none; max-width: 100%; }
      .body { padding: 18px 24px; }
      .doc-header { padding: 20px 24px 16px; }
      a { color: inherit; text-decoration: none; }
      @page { size: A4; margin: 12mm 10mm; }
    }
  </style>
</head>
<body>

@php
  $isSimple = $survey->survey_mode === 'Simple';
  $gps      = $isSimple ? $survey->simple_gps_location : $survey->gps_location;
  $statusSlug = strtolower(str_replace(' ', '', $survey->status));
  $badgeClass = match($statusSlug) {
    'completed'    => 'badge-completed',
    'cancelled'    => 'badge-cancelled',
    'needmoretime' => 'badge-needmoretime',
    default        => 'badge-scheduled',
  };
  // Share URL
  $printUrl = request()->url();
@endphp

{{-- ── TOOLBAR (screen only) ── --}}
<div class="toolbar no-print">
  <div>
    <div class="tbar-title">{{ $survey->survey_no }}</div>
    <div class="tbar-sub">{{ $survey->customer_name }} &middot; {{ $survey->survey_mode }} Survey</div>
  </div>
  <a href="{{ route('admin.cctv.surveys.show', $survey) }}" class="btn-back">← Back</a>
  <button class="btn-copy" onclick="copyLink()">🔗 Copy Link</button>
  <a class="btn-share" id="waShareBtn"
     href="https://wa.me/?text={{ urlencode('CCTV Survey Report: '.$survey->survey_no.' – '.$survey->customer_name.'. View: '.$printUrl) }}"
     target="_blank">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.554 4.117 1.528 5.849L.057 23.429a.5.5 0 00.614.614l5.58-1.471A11.95 11.95 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.797 9.797 0 01-5.003-1.376l-.359-.213-3.712.979.994-3.618-.233-.372A9.808 9.808 0 012.182 12C2.182 6.57 6.57 2.182 12 2.182S21.818 6.57 21.818 12 17.43 21.818 12 21.818z"/></svg>
    WhatsApp
  </a>
  <button class="btn-print" onclick="window.print()">🖨 Print / PDF</button>
</div>

{{-- ── PAGE ── --}}
<div class="page">

  {{-- HEADER --}}
  <div class="doc-header">
    <div>
      <div class="brand-name">{{ $store?->store_name ?? config('app.name') }}</div>
      <div class="brand-sub">CCTV Security Solutions</div>
      <div class="brand-info">
        @if($store?->phone_no1) {{ $store->phone_no1 }}<br>@endif
        @if($store?->phone_no2) {{ $store->phone_no2 }}<br>@endif
        @if($store?->store_address) {{ $store->store_address }}@endif
      </div>
    </div>
    <div class="doc-meta">
      <div class="doc-type">Survey Report</div>
      <div class="doc-no">{{ $survey->survey_no }}</div>
      <div class="doc-date">
        Date: {{ $survey->survey_date ? \Carbon\Carbon::parse($survey->survey_date)->format('d M Y') : \Carbon\Carbon::parse($survey->created_at)->format('d M Y') }}
      </div>
      <div>
        <span class="doc-badge {{ $badgeClass }}">{{ $survey->status }}</span>
        <span class="doc-badge" style="background:#eef0ff;color:#696cff;">{{ $survey->survey_mode }}</span>
        <span class="doc-badge" style="background:#f3f4f6;color:#555;">{{ $survey->survey_type }}</span>
      </div>
    </div>
  </div>

  <div class="body">

    {{-- ── CUSTOMER INFO (both modes) ── --}}
    <div class="section">
      <div class="section-title">Customer Information</div>
      <div class="grid">
        <div class="cell half">
          <div class="lbl">Customer Name</div>
          <div class="val" style="font-weight:700;font-size:13px;">{{ $survey->customer_name }}</div>
        </div>
        <div class="cell half">
          <div class="lbl">Mobile</div>
          <div class="val mono">{{ $survey->mobile ?: '—' }}</div>
        </div>
        @if(!$isSimple)
        <div class="cell half">
          <div class="lbl">Contact Person</div>
          <div class="val">{{ $survey->contact_person ?: '—' }}</div>
        </div>
        <div class="cell half">
          <div class="lbl">Alt Mobile</div>
          <div class="val mono">{{ $survey->alt_mobile ?: '—' }}</div>
        </div>
        <div class="cell half">
          <div class="lbl">Email</div>
          <div class="val">{{ $survey->email ?: '—' }}</div>
        </div>
        <div class="cell half">
          <div class="lbl">Customer Type</div>
          <div class="val">{{ $survey->customer_type ?: '—' }}</div>
        </div>
        @endif
        @if($gps)
        <div class="cell full">
          <div class="lbl">GPS Location</div>
          <div class="val mono">{{ $gps }}</div>
        </div>
        @endif
      </div>
    </div>

    {{-- ── SURVEY INFO ── --}}
    <div class="section">
      <div class="section-title">Survey Details</div>
      <div class="grid">
        <div class="cell third">
          <div class="lbl">Survey Date</div>
          <div class="val">{{ $survey->survey_date ? \Carbon\Carbon::parse($survey->survey_date)->format('d M Y') : '—' }}</div>
        </div>
        <div class="cell third">
          <div class="lbl">Surveyed By</div>
          <div class="val">{{ $survey->technician?->employee_name ?? '—' }}</div>
        </div>
        <div class="cell third">
          <div class="lbl">Survey Type</div>
          <div class="val">{{ $survey->survey_type ?? '—' }}</div>
        </div>
      </div>
    </div>

    @if($isSimple)
    {{-- ══════════════════ SIMPLE SURVEY ══════════════════ --}}

    {{-- Camera & Recorder --}}
    <div class="section">
      <div class="section-title">Camera &amp; Recorder</div>
      <div class="grid">
        <div class="cell">
          <div class="lbl">No. of Cameras</div>
          <div class="val big">{{ $survey->simple_num_cameras ?? 0 }}</div>
        </div>
        <div class="cell">
          <div class="lbl">DVR / NVR</div>
          <div class="val">{{ $survey->simple_dvr_nvr ?: '—' }}</div>
        </div>
        <div class="cell">
          <div class="lbl">Channels</div>
          <div class="val">{{ $survey->simple_dvr_channels ? $survey->simple_dvr_channels.' CH' : '—' }}</div>
        </div>
        <div class="cell">
          <div class="lbl">Internet</div>
          <div class="val">
            @if($survey->simple_internet_available)
              <span class="yn-yes">✓ Available</span>
              @if($survey->simple_isp)
                <br><span style="font-size:11px;color:#555;">{{ $survey->simple_isp }}</span>
              @endif
            @else
              <span class="yn-no">✗ Not Available</span>
            @endif
          </div>
        </div>
      </div>
    </div>

    {{-- Site Assessment --}}
    <div class="section">
      <div class="section-title">Site Assessment</div>
      <div class="grid">
        <div class="cell half" style="padding-right:24px;">
          <div class="lbl">Cabling Easiness &nbsp; <strong>{{ $survey->simple_cabling_ease ?? 5 }} / 10</strong></div>
          <div class="bar-wrap">
            <div class="bar-fill bar-primary" style="width:{{ (($survey->simple_cabling_ease ?? 5)/10)*100 }}%"></div>
          </div>
          <div class="bar-labels"><span>Very Difficult</span><span>Very Easy</span></div>
        </div>
        @php $rl = $survey->simple_risk_level ?? 5; $rc = $rl >= 8 ? 'bar-danger' : ($rl >= 5 ? 'bar-warning' : 'bar-success'); @endphp
        <div class="cell half">
          <div class="lbl">Risk Level &nbsp; <strong>{{ $rl }} / 10</strong></div>
          <div class="bar-wrap">
            <div class="bar-fill {{ $rc }}" style="width:{{ ($rl/10)*100 }}%"></div>
          </div>
          <div class="bar-labels"><span>Low Risk</span><span>High Risk</span></div>
        </div>
      </div>
    </div>

    {{-- Work Estimation --}}
    <div class="section">
      <div class="section-title">Work Estimation</div>
      <div class="grid">
        <div class="cell half">
          <div class="lbl">No. of Technicians Required</div>
          <div class="val">{{ $survey->simple_num_technicians ?? 1 }}</div>
        </div>
        <div class="cell half">
          <div class="lbl">Estimated Days to Complete</div>
          <div class="val">{{ $survey->simple_estimated_days ?? 1 }} day(s)</div>
        </div>
      </div>
    </div>

    @if($survey->simple_remark)
    <div class="section">
      <div class="section-title">Remarks</div>
      <div style="font-size:12px;color:#333;line-height:1.7;white-space:pre-line;background:#fafafe;border-left:3px solid #696cff;padding:10px 14px;border-radius:0 6px 6px 0;">{{ $survey->simple_remark }}</div>
    </div>
    @endif

    @else
    {{-- ══════════════════ DETAILED SURVEY ══════════════════ --}}

    {{-- Site Info --}}
    <div class="section">
      <div class="section-title">Site Information</div>
      <div class="grid">
        <div class="cell half">
          <div class="lbl">Building Name</div>
          <div class="val">{{ $survey->building_name ?: '—' }}</div>
        </div>
        <div class="cell half">
          <div class="lbl">Building Type</div>
          <div class="val">{{ $survey->building_type ?: '—' }}</div>
        </div>
        <div class="cell third">
          <div class="lbl">Site Size</div>
          <div class="val">{{ $survey->site_size ?: '—' }}</div>
        </div>
        <div class="cell third">
          <div class="lbl">Construction Status</div>
          <div class="val">{{ $survey->construction_status ?: '—' }}</div>
        </div>
        <div class="cell third">
          <div class="lbl">Existing Security</div>
          <div class="val">{{ $survey->existing_security_system ? 'Yes' : 'No' }}</div>
        </div>
        <div class="cell third">
          <div class="lbl">Floors</div>
          <div class="val">{{ $survey->num_floors ?? 1 }}</div>
        </div>
        <div class="cell third">
          <div class="lbl">Existing CCTV</div>
          <div class="val">{{ $survey->existing_cctv ? 'Yes' : 'No' }}</div>
        </div>
      </div>
    </div>

    {{-- Purposes --}}
    @if($survey->purposes && count($survey->purposes))
    <div class="section">
      <div class="section-title">Purposes / Requirements</div>
      <div style="padding:4px 0;">
        @foreach($survey->purposes as $p)
          <span class="tag">{{ $p }}</span>
        @endforeach
      </div>
    </div>
    @endif

    {{-- Camera Locations --}}
    @if($survey->camera_locations && count($survey->camera_locations))
    <div class="section">
      <div class="section-title">Camera Locations ({{ count($survey->camera_locations) }} cameras)</div>
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Location</th>
            <th>Indoor / Outdoor</th>
            <th>Type</th>
            <th>MP</th>
            <th>Night Vision</th>
            <th>Audio</th>
          </tr>
        </thead>
        <tbody>
          @foreach($survey->camera_locations as $i => $cam)
          <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $cam['location'] }}</td>
            <td>{{ $cam['indoor_outdoor'] ?? '—' }}</td>
            <td>{{ $cam['camera_type'] ?: '—' }}</td>
            <td>{{ $cam['mp'] ?: '—' }}</td>
            <td>{{ ($cam['night_vision'] ?? false) ? '✓' : '—' }}</td>
            <td>{{ ($cam['audio'] ?? false) ? '✓' : '—' }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @endif

    {{-- Network --}}
    <div class="section">
      <div class="section-title">Network</div>
      <div class="grid">
        <div class="cell third">
          <div class="lbl">Internet Status</div>
          <div class="val">{{ $survey->internet_status ?: '—' }}</div>
        </div>
        <div class="cell third">
          <div class="lbl">ISP</div>
          <div class="val">{{ $survey->isp ?: '—' }}{{ $survey->isp === 'Other' ? ' – '.$survey->isp_other : '' }}</div>
        </div>
        <div class="cell third">
          <div class="lbl">Wi-Fi Coverage</div>
          <div class="val {{ $survey->wifi_coverage ? 'yn-yes' : 'yn-no' }}">{{ $survey->wifi_coverage ? '✓ Yes' : '✗ No' }}</div>
        </div>
        <div class="cell third">
          <div class="lbl">LAN Available</div>
          <div class="val {{ $survey->lan_available ? 'yn-yes' : 'yn-no' }}">{{ $survey->lan_available ? '✓ Yes' : '✗ No' }}</div>
        </div>
      </div>
    </div>

    {{-- Power --}}
    <div class="section">
      <div class="section-title">Power</div>
      <div class="grid">
        <div class="cell third">
          <div class="lbl">Power Availability</div>
          <div class="val">{{ $survey->power_availability ?: '—' }}</div>
        </div>
        <div class="cell third">
          <div class="lbl">UPS Required</div>
          <div class="val {{ $survey->ups_required ? 'yn-yes' : 'yn-no' }}">{{ $survey->ups_required ? '✓ Yes' : '✗ No' }}</div>
        </div>
        <div class="cell third">
          <div class="lbl">Electrical Work</div>
          <div class="val {{ $survey->electrical_work_required ? 'yn-yes' : 'yn-no' }}">{{ $survey->electrical_work_required ? '✓ Required' : '✗ No' }}</div>
        </div>
        <div class="cell third">
          <div class="lbl">Voltage Issues</div>
          <div class="val {{ $survey->voltage_issues ? 'yn-yes' : 'yn-no' }}">{{ $survey->voltage_issues ? '⚠ Yes' : '✗ No' }}</div>
        </div>
      </div>
    </div>

    {{-- Installation --}}
    <div class="section">
      <div class="section-title">Installation</div>
      <div class="grid">
        <div class="cell third">
          <div class="lbl">Cable Route</div>
          <div class="val">{{ $survey->cable_route ?: '—' }}</div>
        </div>
        <div class="cell third">
          <div class="lbl">Ceiling Type</div>
          <div class="val">{{ $survey->ceiling_type ?: '—' }}</div>
        </div>
        <div class="cell third">
          <div class="lbl">Wall Type</div>
          <div class="val">{{ $survey->wall_type ?: '—' }}</div>
        </div>
        <div class="cell third">
          <div class="lbl">Ladder Required</div>
          <div class="val {{ $survey->ladder_required ? 'yn-yes' : 'yn-no' }}">{{ $survey->ladder_required ? '✓ Yes' : '✗ No' }}</div>
        </div>
        <div class="cell third">
          <div class="lbl">Scaffolding</div>
          <div class="val {{ $survey->scaffolding_required ? 'yn-yes' : 'yn-no' }}">{{ $survey->scaffolding_required ? '✓ Required' : '✗ No' }}</div>
        </div>
        <div class="cell third">
          <div class="lbl">Height Risk</div>
          @php $hr = $survey->height_risk ?? 0; $hrc = $hr >= 8 ? 'bar-danger' : ($hr >= 5 ? 'bar-warning' : 'bar-success'); @endphp
          <div class="val">{{ $hr }} / 10</div>
          <div class="bar-wrap" style="margin-top:4px;">
            <div class="bar-fill {{ $hrc }}" style="width:{{ ($hr/10)*100 }}%"></div>
          </div>
        </div>
        @if($survey->special_safety_equipment)
        <div class="cell full">
          <div class="lbl">Special Safety Equipment</div>
          <div class="val">{{ $survey->special_safety_equipment }}</div>
        </div>
        @endif
      </div>
    </div>

    {{-- Material Estimation --}}
    <div class="section">
      <div class="section-title">Material Estimation</div>
      <div class="grid">
        <div class="cell">
          <div class="lbl">Cameras</div>
          <div class="val big">{{ $survey->cameras_qty ?? 0 }}</div>
        </div>
        <div class="cell">
          <div class="lbl">DVR Channels</div>
          <div class="val big">{{ $survey->dvr_channels ?? 0 }}</div>
        </div>
        <div class="cell">
          <div class="lbl">Cable (m)</div>
          <div class="val big">{{ $survey->cable_meters ?? 0 }}</div>
        </div>
        <div class="cell">
          <div class="lbl">Storage</div>
          <div class="val">{{ $survey->hdd_storage_days ?? 30 }} days</div>
        </div>
      </div>
      @if($survey->accessories && count($survey->accessories))
      <div style="margin-top:10px;">
        <div class="lbl" style="margin-bottom:5px;">Accessories</div>
        @foreach($survey->accessories as $acc)
          <span class="tag gray">{{ $acc['name'] }} × {{ $acc['qty'] }}</span>
        @endforeach
      </div>
      @endif
    </div>

    {{-- Risks --}}
    @if($survey->risks && count($survey->risks))
    <div class="section">
      <div class="section-title">Identified Risks</div>
      <div style="padding:4px 0;">
        @foreach($survey->risks as $r)
          <span class="tag orange">⚠ {{ $r }}</span>
        @endforeach
      </div>
    </div>
    @endif

    {{-- Notes --}}
    @if($survey->special_notes)
    <div class="section">
      <div class="section-title">Special Notes</div>
      <div style="font-size:12px;color:#333;line-height:1.7;white-space:pre-line;background:#fafafe;border-left:3px solid #696cff;padding:10px 14px;border-radius:0 6px 6px 0;">{{ $survey->special_notes }}</div>
    </div>
    @endif

    @endif {{-- /simple vs detailed --}}

    {{-- ── SIGNATURE SECTION ── --}}
    <div class="sig-row">
      <div class="sig-box">
        <div class="sig-line"></div>
        <div class="sig-name">Surveyed By: {{ $survey->technician?->employee_name ?? '____________________' }}</div>
      </div>
      <div class="sig-box">
        <div class="sig-line"></div>
        <div class="sig-name">Customer Acknowledgement</div>
      </div>
      <div class="sig-box">
        <div class="sig-line"></div>
        <div class="sig-name">Authorized By</div>
      </div>
    </div>

  </div>{{-- /body --}}

  {{-- FOOTER --}}
  <div class="doc-footer">
    {{ $store?->store_name ?? config('app.name') }} &nbsp;·&nbsp;
    Generated {{ now()->format('d M Y, h:i A') }} &nbsp;·&nbsp;
    {{ $survey->survey_no }}
  </div>

</div>{{-- /page --}}

<script>
function copyLink() {
  navigator.clipboard.writeText(window.location.href).then(() => {
    const btn = document.querySelector('.btn-copy');
    btn.textContent = '✓ Copied!';
    btn.style.background = '#059669';
    setTimeout(() => { btn.textContent = '🔗 Copy Link'; btn.style.background = '#555'; }, 2000);
  });
}
</script>
</body>
</html>
