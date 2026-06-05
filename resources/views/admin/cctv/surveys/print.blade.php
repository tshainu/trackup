<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Survey Report – {{ $survey->survey_no }}</title>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Segoe UI', Arial, Helvetica, sans-serif;
      font-size: 11.5px;
      color: #1e1e2d;
      background: #e8eaf0;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }

    /* ════════════════════════════════
       TOOLBAR (screen only)
    ════════════════════════════════ */
    .toolbar {
      position: fixed; top: 0; left: 0; right: 0; z-index: 200;
      background: #1e1e2d;
      display: flex; align-items: center; gap: 8px;
      padding: 10px 24px;
      box-shadow: 0 2px 12px rgba(0,0,0,.35);
    }
    .tbar-logo { font-size: 15px; font-weight: 800; color: #696cff; letter-spacing: -.3px; }
    .tbar-divider { width: 1px; height: 24px; background: rgba(255,255,255,.15); margin: 0 4px; }
    .tbar-title { font-size: 13px; font-weight: 600; color: #fff; }
    .tbar-sub { font-size: 10.5px; color: rgba(255,255,255,.5); margin-left: 6px; }
    .tbar-spacer { flex: 1; }
    .tbtn {
      display: inline-flex; align-items: center; gap: 5px;
      padding: 7px 15px; border-radius: 7px; border: none;
      font-size: 11.5px; font-weight: 600; cursor: pointer;
      text-decoration: none; transition: all .15s;
    }
    .tbtn-print  { background: #696cff; color: #fff; }
    .tbtn-wa     { background: #25d366; color: #fff; }
    .tbtn-copy   { background: rgba(255,255,255,.12); color: #fff; border: 1px solid rgba(255,255,255,.2); }
    .tbtn-back   { background: transparent; color: rgba(255,255,255,.65); border: 1px solid rgba(255,255,255,.15); }
    .tbtn:hover  { opacity: .85; transform: translateY(-1px); }

    /* ════════════════════════════════
       PAGE WRAPPER
    ════════════════════════════════ */
    .page-wrap {
      max-width: 860px;
      margin: 80px auto 60px;
      background: #fff;
      box-shadow: 0 8px 40px rgba(0,0,0,.14);
      border-radius: 4px;
      overflow: hidden;
    }

    /* ════════════════════════════════
       DOCUMENT HEADER
    ════════════════════════════════ */
    .doc-header {
      background: linear-gradient(135deg, #1e1e2d 0%, #2d2b55 60%, #3a3575 100%);
      padding: 0;
      position: relative;
      overflow: hidden;
    }
    .doc-header::before {
      content: '';
      position: absolute; top: -60px; right: -60px;
      width: 240px; height: 240px;
      background: radial-gradient(circle, rgba(105,108,255,.25) 0%, transparent 70%);
      border-radius: 50%;
    }
    .doc-header::after {
      content: '';
      position: absolute; bottom: -40px; left: 30%;
      width: 160px; height: 160px;
      background: radial-gradient(circle, rgba(105,108,255,.12) 0%, transparent 70%);
      border-radius: 50%;
    }
    .header-inner {
      position: relative; z-index: 1;
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      padding: 28px 36px 24px;
    }
    .brand-block {}
    .brand-logo-row {
      display: flex; align-items: center; gap: 10px; margin-bottom: 6px;
    }
    .brand-icon {
      width: 36px; height: 36px;
      background: #696cff;
      border-radius: 8px;
      display: flex; align-items: center; justify-content: center;
      font-size: 18px; font-weight: 900; color: #fff;
      flex-shrink: 0;
    }
    .brand-name   { font-size: 20px; font-weight: 800; color: #fff; letter-spacing: -.2px; }
    .brand-tagline { font-size: 10px; color: rgba(255,255,255,.5); text-transform: uppercase; letter-spacing: .1em; }
    .brand-contact { margin-top: 10px; font-size: 10.5px; color: rgba(255,255,255,.65); line-height: 1.8; }
    .brand-contact strong { color: rgba(255,255,255,.9); }

    .doc-title-block { text-align: right; }
    .doc-type-label {
      display: inline-block;
      background: rgba(255,255,255,.1);
      border: 1px solid rgba(255,255,255,.2);
      color: rgba(255,255,255,.7);
      font-size: 9.5px; font-weight: 700;
      text-transform: uppercase; letter-spacing: .12em;
      padding: 3px 10px; border-radius: 20px;
      margin-bottom: 6px;
    }
    .doc-type-main {
      font-size: 26px; font-weight: 900; color: #fff;
      letter-spacing: -.5px; line-height: 1;
    }
    .doc-type-sub { font-size: 12px; color: rgba(255,255,255,.5); margin-top: 2px; }
    .doc-no-box {
      margin-top: 10px;
      display: inline-block;
      background: rgba(105,108,255,.3);
      border: 1px solid rgba(105,108,255,.5);
      border-radius: 6px;
      padding: 5px 14px;
      font-size: 14px; font-weight: 700; color: #fff;
      letter-spacing: .04em;
    }
    .doc-date-row { margin-top: 6px; font-size: 10.5px; color: rgba(255,255,255,.5); }

    /* Header accent bar */
    .header-accent {
      height: 4px;
      background: linear-gradient(90deg, #696cff 0%, #8b8fff 50%, #b8baff 100%);
    }

    /* ════════════════════════════════
       STATUS BAR (pill badges row)
    ════════════════════════════════ */
    .status-bar {
      display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
      padding: 10px 36px;
      background: #f8f8ff;
      border-bottom: 1px solid #ebebff;
    }
    .pill {
      display: inline-flex; align-items: center; gap: 5px;
      padding: 4px 12px; border-radius: 20px;
      font-size: 10px; font-weight: 700;
      text-transform: uppercase; letter-spacing: .06em;
    }
    .pill-dot { width: 6px; height: 6px; border-radius: 50%; display: inline-block; }
    .pill-completed  { background: #d1fae5; color: #065f46; }
    .pill-completed .pill-dot  { background: #059669; }
    .pill-scheduled  { background: #dbeafe; color: #1d4ed8; }
    .pill-scheduled .pill-dot  { background: #3b82f6; }
    .pill-cancelled  { background: #fee2e2; color: #991b1b; }
    .pill-cancelled .pill-dot  { background: #ef4444; }
    .pill-needmoretime { background: #fef3c7; color: #92400e; }
    .pill-needmoretime .pill-dot { background: #f59e0b; }
    .pill-mode    { background: #eef0ff; color: #696cff; }
    .pill-mode .pill-dot { background: #696cff; }
    .pill-type    { background: #f3f4f6; color: #374151; }
    .pill-type .pill-dot { background: #9ca3af; }
    .status-bar-spacer { flex: 1; }
    .status-bar-ref { font-size: 10px; color: #aaa; }

    /* ════════════════════════════════
       SUMMARY STAT CARDS
    ════════════════════════════════ */
    .stat-row {
      display: flex; gap: 0;
      border-bottom: 2px solid #ebebff;
    }
    .stat-card {
      flex: 1;
      padding: 14px 18px;
      border-right: 1px solid #ebebff;
      position: relative;
    }
    .stat-card:last-child { border-right: none; }
    .stat-card::before {
      content: '';
      position: absolute; top: 0; left: 0; right: 0;
      height: 3px;
    }
    .stat-card.purple::before { background: #696cff; }
    .stat-card.blue::before   { background: #3b82f6; }
    .stat-card.green::before  { background: #10b981; }
    .stat-card.orange::before { background: #f59e0b; }
    .stat-card.red::before    { background: #ef4444; }
    .stat-num  { font-size: 26px; font-weight: 900; color: #1e1e2d; line-height: 1; }
    .stat-num.purple { color: #696cff; }
    .stat-num.blue   { color: #3b82f6; }
    .stat-num.green  { color: #10b981; }
    .stat-num.orange { color: #f59e0b; }
    .stat-num.red    { color: #ef4444; }
    .stat-lbl  { font-size: 9.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; color: #aaa; margin-top: 3px; }
    .stat-sub  { font-size: 10px; color: #888; margin-top: 2px; }

    /* ════════════════════════════════
       BODY
    ════════════════════════════════ */
    .doc-body { padding: 28px 36px; }

    /* ════════════════════════════════
       SECTION
    ════════════════════════════════ */
    .section { margin-bottom: 24px; }
    .section-header {
      display: flex; align-items: center; gap: 10px;
      margin-bottom: 14px;
      padding-bottom: 8px;
      border-bottom: 1.5px solid #f0f0f0;
    }
    .section-icon {
      width: 26px; height: 26px;
      background: #eef0ff; border-radius: 6px;
      display: flex; align-items: center; justify-content: center;
      font-size: 13px; flex-shrink: 0;
    }
    .section-title {
      font-size: 11px; font-weight: 800;
      text-transform: uppercase; letter-spacing: .08em;
      color: #1e1e2d;
    }
    .section-line {
      flex: 1; height: 1px; background: #f0f0f0;
    }
    .section-count {
      font-size: 10px; font-weight: 600; color: #696cff;
      background: #eef0ff; padding: 2px 8px; border-radius: 10px;
    }

    /* ════════════════════════════════
       GRID / FIELDS
    ════════════════════════════════ */
    .fields { display: flex; flex-wrap: wrap; gap: 0; }
    .field { padding: 6px 12px 8px 0; }
    .field.w25  { flex: 0 0 25%; }
    .field.w33  { flex: 0 0 33.333%; }
    .field.w50  { flex: 0 0 50%; }
    .field.w66  { flex: 0 0 66.666%; }
    .field.w75  { flex: 0 0 75%; }
    .field.w100 { flex: 0 0 100%; }
    .flbl {
      font-size: 9px; font-weight: 700;
      text-transform: uppercase; letter-spacing: .07em;
      color: #b0b0c3; margin-bottom: 3px;
    }
    .fval {
      font-size: 12px; font-weight: 500; color: #1e1e2d;
      line-height: 1.4;
    }
    .fval.bold   { font-weight: 700; font-size: 13px; }
    .fval.mono   { font-family: 'Courier New', monospace; }
    .fval.muted  { color: #bbb; font-style: italic; }
    .fval.large  { font-size: 22px; font-weight: 900; color: #696cff; line-height: 1; }
    .fval.green  { color: #059669; font-weight: 700; }
    .fval.red    { color: #ef4444; font-weight: 600; }
    .fval.gray   { color: #9ca3af; }

    /* ════════════════════════════════
       INLINE KEY-VALUE COMPACT TABLE
    ════════════════════════════════ */
    .kv-table { width: 100%; border-collapse: collapse; }
    .kv-table td { padding: 5px 8px; vertical-align: top; }
    .kv-table tr:nth-child(even) td { background: #fafafe; }
    .kv-table .kv-k { font-size: 10px; font-weight: 700; color: #888; width: 38%; white-space: nowrap; }
    .kv-table .kv-v { font-size: 11.5px; color: #1e1e2d; }

    /* ════════════════════════════════
       PROGRESS BAR
    ════════════════════════════════ */
    .prog-wrap  { height: 7px; background: #f0f0f5; border-radius: 10px; overflow: hidden; margin: 4px 0 2px; }
    .prog-fill  { height: 100%; border-radius: 10px; }
    .prog-lbl   { display: flex; justify-content: space-between; font-size: 9px; color: #bbb; }

    /* ════════════════════════════════
       TABLE (camera locations)
    ════════════════════════════════ */
    .data-table { width: 100%; border-collapse: collapse; font-size: 11px; }
    .data-table thead tr {
      background: linear-gradient(135deg, #1e1e2d, #2d2b55);
      color: #fff;
    }
    .data-table thead th {
      padding: 8px 10px;
      font-size: 9.5px; font-weight: 700;
      text-transform: uppercase; letter-spacing: .06em;
      text-align: left;
    }
    .data-table tbody tr { border-bottom: 1px solid #f0f0f5; }
    .data-table tbody tr:last-child { border-bottom: none; }
    .data-table tbody tr:nth-child(even) { background: #fafafe; }
    .data-table tbody td { padding: 7px 10px; }
    .data-table .col-num {
      width: 32px; text-align: center;
      font-weight: 700; color: #696cff;
    }
    .data-table .col-check {
      text-align: center;
      font-size: 13px;
    }
    .check-yes { color: #059669; }
    .check-no  { color: #e5e7eb; }

    /* ════════════════════════════════
       TAGS
    ════════════════════════════════ */
    .tags-wrap { display: flex; flex-wrap: wrap; gap: 5px; }
    .tag {
      display: inline-flex; align-items: center; gap: 4px;
      padding: 3px 10px; border-radius: 5px;
      font-size: 10.5px; font-weight: 600;
    }
    .tag-purple { background: #eef0ff; color: #696cff; border: 1px solid #d0d3ff; }
    .tag-gray   { background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; }
    .tag-green  { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
    .tag-orange { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .tag-red    { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

    /* ════════════════════════════════
       TWO-COLUMN SECTION LAYOUT
    ════════════════════════════════ */
    .two-col { display: flex; gap: 20px; margin-bottom: 24px; }
    .two-col > .col { flex: 1; }
    .two-col > .col-narrow { flex: 0 0 42%; }

    /* ════════════════════════════════
       NOTES / REMARKS BOX
    ════════════════════════════════ */
    .notes-box {
      background: #fafafe;
      border-left: 3px solid #696cff;
      border-radius: 0 6px 6px 0;
      padding: 12px 16px;
      font-size: 12px; color: #333;
      line-height: 1.75; white-space: pre-line;
    }

    /* ════════════════════════════════
       RISK ALERT BOX
    ════════════════════════════════ */
    .risk-box {
      background: #fffbeb;
      border: 1px solid #fde68a;
      border-left: 4px solid #f59e0b;
      border-radius: 0 6px 6px 0;
      padding: 10px 14px;
    }
    .risk-title { font-size: 10px; font-weight: 800; color: #92400e; text-transform: uppercase; letter-spacing: .07em; margin-bottom: 6px; }

    /* ════════════════════════════════
       PHOTO GRID
    ════════════════════════════════ */
    .photo-grid { display: flex; flex-wrap: wrap; gap: 8px; }
    .photo-grid img {
      width: calc(25% - 6px); max-width: 160px;
      aspect-ratio: 4/3; object-fit: cover;
      border-radius: 5px; border: 1px solid #e5e7eb;
    }

    /* ════════════════════════════════
       SIGNATURE SECTION
    ════════════════════════════════ */
    .sig-section {
      display: flex; gap: 0;
      border-top: 2px solid #f0f0f0;
      margin-top: 32px;
    }
    .sig-cell {
      flex: 1; padding: 24px 20px 16px;
      border-right: 1px solid #f0f0f0;
      text-align: center;
    }
    .sig-cell:last-child { border-right: none; }
    .sig-line-box {
      height: 42px;
      border-bottom: 1.5px solid #333;
      margin-bottom: 8px;
    }
    .sig-label { font-size: 10px; font-weight: 700; color: #888; text-transform: uppercase; letter-spacing: .07em; }
    .sig-name  { font-size: 11px; color: #555; margin-top: 2px; }

    /* ════════════════════════════════
       FOOTER
    ════════════════════════════════ */
    .doc-footer {
      background: #1e1e2d;
      padding: 12px 36px;
      display: flex; align-items: center; justify-content: space-between;
    }
    .footer-left  { font-size: 10px; color: rgba(255,255,255,.4); }
    .footer-mid   { font-size: 10px; color: rgba(255,255,255,.6); font-weight: 600; text-align: center; }
    .footer-right { font-size: 10px; color: rgba(255,255,255,.4); text-align: right; }
    .footer-brand { font-size: 11px; font-weight: 800; color: #696cff; }

    /* ════════════════════════════════
       WATERMARK (print bg)
    ════════════════════════════════ */
    .watermark {
      position: absolute; top: 50%; left: 50%;
      transform: translate(-50%,-50%) rotate(-35deg);
      font-size: 80px; font-weight: 900;
      color: rgba(105,108,255,.04);
      white-space: nowrap; pointer-events: none;
      letter-spacing: 8px; text-transform: uppercase;
      z-index: 0;
    }

    /* ════════════════════════════════
       DIVIDER
    ════════════════════════════════ */
    .divider { height: 1px; background: #f0f0f0; margin: 0 0 20px; }

    /* ════════════════════════════════
       PRINT OVERRIDES
    ════════════════════════════════ */
    @media print {
      body { background: #fff; }
      .toolbar { display: none !important; }
      .page-wrap { margin: 0; box-shadow: none; border-radius: 0; max-width: 100%; }
      .doc-body { padding: 20px 28px; }
      .doc-header { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
      .doc-footer { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
      .stat-row  { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
      .data-table thead tr { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
      a { color: inherit !important; text-decoration: none !important; }
      .section { page-break-inside: avoid; }
      @page { size: A4; margin: 10mm; }
    }
  </style>
</head>
<body>

@php
  $isSimple   = $survey->survey_mode === 'Simple';
  $gps        = $isSimple ? $survey->simple_gps_location : $survey->gps_location;
  $statusSlug = strtolower(str_replace(' ', '', $survey->status ?? 'scheduled'));
  $pillClass  = match($statusSlug) {
    'completed'    => 'pill-completed',
    'cancelled'    => 'pill-cancelled',
    'needmoretime' => 'pill-needmoretime',
    default        => 'pill-scheduled',
  };
  $printUrl = request()->url();

  // Stats for detailed
  $camCount  = $isSimple ? ($survey->simple_num_cameras ?? 0) : ($survey->cameras_qty ?? 0);
  $dvrCh     = $isSimple ? ($survey->simple_dvr_channels ?? 0) : ($survey->dvr_channels ?? 0);
  $cableM    = $isSimple ? '—' : ($survey->cable_meters ?? 0);
  $hddDays   = $isSimple ? '—' : ($survey->hdd_storage_days ?? 30);
  $locCount  = (!$isSimple && $survey->camera_locations) ? count($survey->camera_locations) : 0;
@endphp

{{-- ══ TOOLBAR ══ --}}
<div class="toolbar">
  <div class="tbar-logo">{{ $store?->store_name ?? config('app.name') }}</div>
  <div class="tbar-divider"></div>
  <span class="tbar-title">{{ $survey->survey_no }}</span>
  <span class="tbar-sub">{{ $survey->customer_name }} &middot; {{ $survey->survey_mode }} Survey</span>
  <div class="tbar-spacer"></div>
  <a href="{{ route('admin.cctv.surveys.show', $survey) }}" class="tbtn tbtn-back">&#8592; Back</a>
  <button class="tbtn tbtn-copy" onclick="copyLink()">&#128279; Copy Link</button>
  <a class="tbtn tbtn-wa"
     href="https://wa.me/?text={{ urlencode('CCTV Survey Report: '.$survey->survey_no.' – '.$survey->customer_name.'. View: '.$printUrl) }}"
     target="_blank">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.554 4.117 1.528 5.849L.057 23.429a.5.5 0 00.614.614l5.58-1.471A11.95 11.95 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.797 9.797 0 01-5.003-1.376l-.359-.213-3.712.979.994-3.618-.233-.372A9.808 9.808 0 012.182 12C2.182 6.57 6.57 2.182 12 2.182S21.818 6.57 21.818 12 17.43 21.818 12 21.818z"/></svg>
    WhatsApp
  </a>
  <button class="tbtn tbtn-print" onclick="window.print()">&#128438; Print / PDF</button>
</div>

{{-- ══ PAGE ══ --}}
<div class="page-wrap">

  {{-- ══ HEADER ══ --}}
  <div class="doc-header">
    <div class="header-inner">
      <div class="brand-block">
        <div class="brand-logo-row">
          @if($store?->logo)
            <img src="{{ asset('storage/'.$store->logo) }}" alt="Logo"
                 style="height:36px;width:auto;border-radius:6px;object-fit:contain;background:#fff;padding:2px;">
          @else
            <div class="brand-icon">{{ strtoupper(substr($store?->store_name ?? 'T', 0, 1)) }}</div>
          @endif
          <div>
            <div class="brand-name">{{ $store?->store_name ?? config('app.name') }}</div>
            <div class="brand-tagline">CCTV Security Solutions</div>
          </div>
        </div>
        <div class="brand-contact">
          @if($store?->phone_no1) <strong>Tel:</strong> {{ $store->phone_no1 }}<br>@endif
          @if($store?->phone_no2) <strong>Alt:</strong> {{ $store->phone_no2 }}<br>@endif
          @if($store?->store_address) <strong>Addr:</strong> {{ $store->store_address }}@endif
        </div>
      </div>

      <div class="doc-title-block">
        <div class="doc-type-label">Official Document</div>
        <div class="doc-type-main">Survey</div>
        <div class="doc-type-sub">Site Assessment Report</div>
        <div class="doc-no-box">{{ $survey->survey_no }}</div>
        <div class="doc-date-row">
          Dated: {{ $survey->survey_date
            ? \Carbon\Carbon::parse($survey->survey_date)->format('d M Y')
            : \Carbon\Carbon::parse($survey->created_at)->format('d M Y') }}
        </div>
      </div>
    </div>
    <div class="header-accent"></div>
  </div>

  {{-- ══ STATUS BAR ══ --}}
  <div class="status-bar">
    <span class="pill {{ $pillClass }}">
      <span class="pill-dot"></span>{{ $survey->status ?? 'Scheduled' }}
    </span>
    <span class="pill pill-mode">
      <span class="pill-dot"></span>{{ $survey->survey_mode }} Survey
    </span>
    @if($survey->survey_type)
    <span class="pill pill-type">
      <span class="pill-dot"></span>{{ $survey->survey_type }}
    </span>
    @endif
    <div class="status-bar-spacer"></div>
    <span class="status-bar-ref">
      Ref: {{ $survey->survey_no }} &nbsp;|&nbsp;
      Generated {{ now()->format('d M Y') }}
    </span>
  </div>

  {{-- ══ SUMMARY STAT CARDS ══ --}}
  <div class="stat-row">
    <div class="stat-card purple">
      <div class="stat-num purple">{{ $camCount }}</div>
      <div class="stat-lbl">Cameras</div>
      @if(!$isSimple && $locCount) <div class="stat-sub">{{ $locCount }} location(s) mapped</div> @endif
    </div>
    <div class="stat-card blue">
      <div class="stat-num blue">{{ $dvrCh }}</div>
      <div class="stat-lbl">DVR Channels</div>
      @if(!$isSimple && $survey->dvr_channels) <div class="stat-sub">Required capacity</div> @endif
    </div>
    @if(!$isSimple)
    <div class="stat-card green">
      <div class="stat-num green">{{ $cableM }}<span style="font-size:14px;font-weight:600"> m</span></div>
      <div class="stat-lbl">Cable Length</div>
    </div>
    <div class="stat-card orange">
      <div class="stat-num orange">{{ $hddDays }}</div>
      <div class="stat-lbl">Storage Days</div>
      <div class="stat-sub">HDD retention</div>
    </div>
    @php
      $rl = $isSimple ? ($survey->simple_risk_level ?? 0) : ($survey->height_risk ?? 0);
      $rlColor = $rl >= 8 ? 'red' : ($rl >= 5 ? 'orange' : 'green');
    @endphp
    <div class="stat-card {{ $rlColor }}">
      <div class="stat-num {{ $rlColor }}">{{ $rl }}<span style="font-size:14px;font-weight:600">/10</span></div>
      <div class="stat-lbl">{{ $isSimple ? 'Risk Level' : 'Height Risk' }}</div>
    </div>
    @else
    @php
      $ce = $survey->simple_cabling_ease ?? 5;
      $rl = $survey->simple_risk_level ?? 5;
    @endphp
    <div class="stat-card green">
      <div class="stat-num green">{{ $ce }}<span style="font-size:14px;font-weight:600">/10</span></div>
      <div class="stat-lbl">Cabling Ease</div>
    </div>
    <div class="stat-card {{ $rl >= 8 ? 'red' : ($rl >= 5 ? 'orange' : 'green') }}">
      <div class="stat-num {{ $rl >= 8 ? 'red' : ($rl >= 5 ? 'orange' : 'green') }}">{{ $rl }}<span style="font-size:14px;font-weight:600">/10</span></div>
      <div class="stat-lbl">Risk Level</div>
    </div>
    <div class="stat-card blue">
      <div class="stat-num blue">{{ $survey->simple_num_technicians ?? 1 }}</div>
      <div class="stat-lbl">Technicians</div>
      <div class="stat-sub">{{ $survey->simple_estimated_days ?? 1 }} day(s) est.</div>
    </div>
    @endif
  </div>

  {{-- ══ BODY ══ --}}
  <div class="doc-body" style="position:relative;">
    <div class="watermark">{{ $store?->store_name ?? 'TRACKUP' }}</div>

    {{-- ── CUSTOMER INFORMATION ── --}}
    <div class="section">
      <div class="section-header">
        <div class="section-icon">👤</div>
        <div class="section-title">Customer Information</div>
        <div class="section-line"></div>
      </div>
      <div class="fields">
        <div class="field w50">
          <div class="flbl">Customer Name</div>
          <div class="fval bold">{{ $survey->customer_name ?: '—' }}</div>
        </div>
        <div class="field w50">
          <div class="flbl">Mobile</div>
          <div class="fval mono">{{ $survey->mobile ?: '—' }}</div>
        </div>
        @if(!$isSimple)
        <div class="field w50">
          <div class="flbl">Contact Person</div>
          <div class="fval">{{ $survey->contact_person ?: '—' }}</div>
        </div>
        <div class="field w50">
          <div class="flbl">Alternate Mobile</div>
          <div class="fval mono">{{ $survey->alt_mobile ?: '—' }}</div>
        </div>
        <div class="field w50">
          <div class="flbl">Email Address</div>
          <div class="fval">{{ $survey->email ?: '—' }}</div>
        </div>
        <div class="field w50">
          <div class="flbl">Customer Type</div>
          <div class="fval">
            {{ $survey->customer_type ?: '—' }}
            @if($survey->customer_type === 'Other' && $survey->customer_type_other)
              – {{ $survey->customer_type_other }}
            @endif
          </div>
        </div>
        @endif
        @if($gps)
        <div class="field w100">
          <div class="flbl">GPS Location</div>
          <div class="fval mono" style="font-size:11px;">{{ $gps }}</div>
        </div>
        @endif
      </div>
    </div>

    {{-- ── SURVEY DETAILS ── --}}
    <div class="section">
      <div class="section-header">
        <div class="section-icon">📋</div>
        <div class="section-title">Survey Details</div>
        <div class="section-line"></div>
      </div>
      <div class="fields">
        <div class="field w33">
          <div class="flbl">Survey Date</div>
          <div class="fval">{{ $survey->survey_date ? \Carbon\Carbon::parse($survey->survey_date)->format('d M Y') : '—' }}</div>
        </div>
        <div class="field w33">
          <div class="flbl">Surveyed By</div>
          <div class="fval">{{ $survey->technician?->employee_name ?? '—' }}</div>
        </div>
        <div class="field w33">
          <div class="flbl">Survey Type</div>
          <div class="fval">{{ $survey->survey_type ?? '—' }}</div>
        </div>
        <div class="field w33">
          <div class="flbl">Survey Mode</div>
          <div class="fval" style="color:#696cff;font-weight:700;">{{ $survey->survey_mode }}</div>
        </div>
        <div class="field w33">
          <div class="flbl">Status</div>
          <div class="fval">{{ $survey->status ?? '—' }}</div>
        </div>
      </div>
    </div>

    @if($isSimple)
    {{-- ══════════════════ SIMPLE SURVEY SECTIONS ══════════════════ --}}

    <div class="two-col">
      {{-- Camera & Recorder --}}
      <div class="col">
        <div class="section" style="margin-bottom:0;">
          <div class="section-header">
            <div class="section-icon">📷</div>
            <div class="section-title">Camera &amp; Recorder</div>
            <div class="section-line"></div>
          </div>
          <div class="fields">
            <div class="field w50">
              <div class="flbl">No. of Cameras</div>
              <div class="fval large">{{ $survey->simple_num_cameras ?? 0 }}</div>
            </div>
            <div class="field w50">
              <div class="flbl">DVR / NVR</div>
              <div class="fval">{{ $survey->simple_dvr_nvr ?: '—' }}</div>
            </div>
            <div class="field w50">
              <div class="flbl">Channels</div>
              <div class="fval">{{ $survey->simple_dvr_channels ? $survey->simple_dvr_channels.' CH' : '—' }}</div>
            </div>
            <div class="field w50">
              <div class="flbl">Internet</div>
              @if($survey->simple_internet_available)
                <div class="fval green">✓ Available{{ $survey->simple_isp ? ' ('.$survey->simple_isp.')' : '' }}</div>
              @else
                <div class="fval gray">✗ Not Available</div>
              @endif
            </div>
          </div>
        </div>
      </div>

      {{-- Work Estimation --}}
      <div class="col-narrow">
        <div class="section" style="margin-bottom:0;">
          <div class="section-header">
            <div class="section-icon">🔧</div>
            <div class="section-title">Work Estimation</div>
            <div class="section-line"></div>
          </div>
          <div class="fields">
            <div class="field w100">
              <div class="flbl">Technicians Required</div>
              <div class="fval large">{{ $survey->simple_num_technicians ?? 1 }}</div>
            </div>
            <div class="field w100">
              <div class="flbl">Estimated Duration</div>
              <div class="fval bold">{{ $survey->simple_estimated_days ?? 1 }} day(s)</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Site Assessment progress bars --}}
    <div class="section" style="margin-top:20px;">
      <div class="section-header">
        <div class="section-icon">📊</div>
        <div class="section-title">Site Assessment</div>
        <div class="section-line"></div>
      </div>
      <div class="fields">
        @php $ce = $survey->simple_cabling_ease ?? 5; @endphp
        <div class="field w50" style="padding-right:24px;">
          <div class="flbl">Cabling Easiness &nbsp;
            <strong style="color:#696cff;">{{ $ce }} / 10</strong>
          </div>
          <div class="prog-wrap">
            <div class="prog-fill" style="width:{{ ($ce/10)*100 }}%;background:#696cff;"></div>
          </div>
          <div class="prog-lbl"><span>Very Difficult</span><span>Very Easy</span></div>
        </div>
        @php
          $rl = $survey->simple_risk_level ?? 5;
          $rc = $rl >= 8 ? '#ef4444' : ($rl >= 5 ? '#f59e0b' : '#10b981');
        @endphp
        <div class="field w50">
          <div class="flbl">Risk Level &nbsp;
            <strong style="color:{{ $rc }};">{{ $rl }} / 10</strong>
          </div>
          <div class="prog-wrap">
            <div class="prog-fill" style="width:{{ ($rl/10)*100 }}%;background:{{ $rc }};"></div>
          </div>
          <div class="prog-lbl"><span>Low Risk</span><span>High Risk</span></div>
        </div>
      </div>
    </div>

    @if($survey->simple_remark)
    <div class="section">
      <div class="section-header">
        <div class="section-icon">📝</div>
        <div class="section-title">Remarks</div>
        <div class="section-line"></div>
      </div>
      <div class="notes-box">{{ $survey->simple_remark }}</div>
    </div>
    @endif

    @else
    {{-- ══════════════════ DETAILED SURVEY SECTIONS ══════════════════ --}}

    <div class="two-col">
      {{-- Site Information --}}
      <div class="col">
        <div class="section-header">
          <div class="section-icon">🏢</div>
          <div class="section-title">Site Information</div>
          <div class="section-line"></div>
        </div>
        <table class="kv-table">
          <tr><td class="kv-k">Building Name</td><td class="kv-v">{{ $survey->building_name ?: '—' }}</td></tr>
          <tr><td class="kv-k">Building Type</td><td class="kv-v">{{ $survey->building_type ?: '—' }}</td></tr>
          <tr><td class="kv-k">Site Size</td><td class="kv-v">{{ $survey->site_size ?: '—' }}</td></tr>
          <tr><td class="kv-k">No. of Floors</td><td class="kv-v">{{ $survey->num_floors ?? 1 }}</td></tr>
          <tr><td class="kv-k">Construction</td><td class="kv-v">{{ $survey->construction_status ?: '—' }}</td></tr>
          <tr><td class="kv-k">Existing CCTV</td>
              <td class="kv-v {{ $survey->existing_cctv ? 'fval green' : '' }}">
                {{ $survey->existing_cctv ? '✓ Yes' : '✗ No' }}
              </td>
          </tr>
          <tr><td class="kv-k">Existing Security</td>
              <td class="kv-v {{ $survey->existing_security_system ? 'fval green' : '' }}">
                {{ $survey->existing_security_system ? '✓ Yes' : '✗ No' }}
              </td>
          </tr>
        </table>
      </div>

      {{-- Network & Power --}}
      <div class="col">
        <div class="section-header">
          <div class="section-icon">🌐</div>
          <div class="section-title">Network &amp; Power</div>
          <div class="section-line"></div>
        </div>
        <table class="kv-table">
          <tr><td class="kv-k">Internet Status</td><td class="kv-v">{{ $survey->internet_status ?: '—' }}</td></tr>
          <tr><td class="kv-k">ISP</td>
              <td class="kv-v">{{ $survey->isp ?: '—' }}{{ $survey->isp === 'Other' && $survey->isp_other ? ' – '.$survey->isp_other : '' }}</td>
          </tr>
          <tr><td class="kv-k">Wi-Fi Coverage</td>
              <td class="kv-v {{ $survey->wifi_coverage ? 'fval green' : 'fval red' }}">
                {{ $survey->wifi_coverage ? '✓ Yes' : '✗ No' }}
              </td>
          </tr>
          <tr><td class="kv-k">LAN Available</td>
              <td class="kv-v {{ $survey->lan_available ? 'fval green' : 'fval red' }}">
                {{ $survey->lan_available ? '✓ Yes' : '✗ No' }}
              </td>
          </tr>
          <tr><td class="kv-k">Power</td><td class="kv-v">{{ $survey->power_availability ?: '—' }}</td></tr>
          <tr><td class="kv-k">UPS Required</td>
              <td class="kv-v {{ $survey->ups_required ? 'fval green' : '' }}">
                {{ $survey->ups_required ? '✓ Yes' : '—' }}
              </td>
          </tr>
          <tr><td class="kv-k">Electrical Work</td>
              <td class="kv-v {{ $survey->electrical_work_required ? 'fval orange' : '' }}"
                  style="{{ $survey->electrical_work_required ? 'color:#f59e0b;' : '' }}">
                {{ $survey->electrical_work_required ? '⚠ Required' : '—' }}
              </td>
          </tr>
          <tr><td class="kv-k">Voltage Issues</td>
              <td class="kv-v" style="{{ $survey->voltage_issues ? 'color:#ef4444;font-weight:700;' : '' }}">
                {{ $survey->voltage_issues ? '⚠ Yes' : '—' }}
              </td>
          </tr>
        </table>
      </div>
    </div>

    {{-- Purposes --}}
    @if($survey->purposes && count($survey->purposes))
    <div class="section">
      <div class="section-header">
        <div class="section-icon">🎯</div>
        <div class="section-title">Purposes &amp; Requirements</div>
        <div class="section-line"></div>
      </div>
      <div class="tags-wrap">
        @foreach($survey->purposes as $p)
          <span class="tag tag-purple">{{ $p }}</span>
        @endforeach
      </div>
    </div>
    @endif

    {{-- Camera Locations --}}
    @if($survey->camera_locations && count($survey->camera_locations))
    <div class="section">
      <div class="section-header">
        <div class="section-icon">📍</div>
        <div class="section-title">Camera Locations</div>
        <div class="section-line"></div>
        <span class="section-count">{{ count($survey->camera_locations) }} camera(s)</span>
      </div>
      <table class="data-table">
        <thead>
          <tr>
            <th style="width:32px;">#</th>
            <th>Location / Description</th>
            <th>Indoor / Outdoor</th>
            <th>Camera Type</th>
            <th>MP</th>
            <th>Night Vision</th>
            <th>Audio</th>
          </tr>
        </thead>
        <tbody>
          @foreach($survey->camera_locations as $i => $cam)
          <tr>
            <td class="col-num">{{ $i + 1 }}</td>
            <td style="font-weight:500;">{{ $cam['location'] ?? '—' }}</td>
            <td>{{ $cam['indoor_outdoor'] ?? '—' }}</td>
            <td>{{ $cam['camera_type'] ?? '—' }}</td>
            <td>{{ $cam['mp'] ? $cam['mp'].' MP' : '—' }}</td>
            <td class="col-check">
              <span class="{{ ($cam['night_vision'] ?? false) ? 'check-yes' : 'check-no' }}">
                {{ ($cam['night_vision'] ?? false) ? '✓' : '—' }}
              </span>
            </td>
            <td class="col-check">
              <span class="{{ ($cam['audio'] ?? false) ? 'check-yes' : 'check-no' }}">
                {{ ($cam['audio'] ?? false) ? '✓' : '—' }}
              </span>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @endif

    {{-- Installation --}}
    <div class="two-col">
      <div class="col">
        <div class="section-header">
          <div class="section-icon">🔧</div>
          <div class="section-title">Installation Details</div>
          <div class="section-line"></div>
        </div>
        <table class="kv-table">
          <tr><td class="kv-k">Cable Route</td><td class="kv-v">{{ $survey->cable_route ?: '—' }}</td></tr>
          <tr><td class="kv-k">Ceiling Type</td><td class="kv-v">{{ $survey->ceiling_type ?: '—' }}</td></tr>
          <tr><td class="kv-k">Wall Type</td><td class="kv-v">{{ $survey->wall_type ?: '—' }}</td></tr>
          <tr><td class="kv-k">Ladder Required</td>
              <td class="kv-v {{ $survey->ladder_required ? 'fval green' : '' }}">
                {{ $survey->ladder_required ? '✓ Yes' : '—' }}
              </td>
          </tr>
          <tr><td class="kv-k">Scaffolding</td>
              <td class="kv-v" style="{{ $survey->scaffolding_required ? 'color:#f59e0b;font-weight:700;' : '' }}">
                {{ $survey->scaffolding_required ? '⚠ Required' : '—' }}
              </td>
          </tr>
          @if($survey->special_safety_equipment)
          <tr><td class="kv-k">Safety Equipment</td><td class="kv-v">{{ $survey->special_safety_equipment }}</td></tr>
          @endif
        </table>
      </div>

      {{-- Material Estimation --}}
      <div class="col">
        <div class="section-header">
          <div class="section-icon">📦</div>
          <div class="section-title">Material Estimation</div>
          <div class="section-line"></div>
        </div>
        <div class="fields">
          <div class="field w50">
            <div class="flbl">Cameras Qty</div>
            <div class="fval large">{{ $survey->cameras_qty ?? 0 }}</div>
          </div>
          <div class="field w50">
            <div class="flbl">DVR Channels</div>
            <div class="fval large">{{ $survey->dvr_channels ?? 0 }}</div>
          </div>
          <div class="field w50">
            <div class="flbl">Cable Length</div>
            <div class="fval bold">{{ $survey->cable_meters ?? 0 }} m</div>
          </div>
          <div class="field w50">
            <div class="flbl">Storage (HDD)</div>
            <div class="fval bold">{{ $survey->hdd_storage_days ?? 30 }} days</div>
          </div>
        </div>

        {{-- Height risk bar --}}
        @php
          $hr = $survey->height_risk ?? 0;
          $hrc = $hr >= 8 ? '#ef4444' : ($hr >= 5 ? '#f59e0b' : '#10b981');
        @endphp
        <div style="margin-top:10px;">
          <div class="flbl">Height Risk &nbsp; <strong style="color:{{ $hrc }};">{{ $hr }} / 10</strong></div>
          <div class="prog-wrap">
            <div class="prog-fill" style="width:{{ ($hr/10)*100 }}%;background:{{ $hrc }};"></div>
          </div>
          <div class="prog-lbl"><span>Low</span><span>High</span></div>
        </div>
      </div>
    </div>

    {{-- Accessories --}}
    @if($survey->accessories && count($survey->accessories))
    <div class="section">
      <div class="section-header">
        <div class="section-icon">🔌</div>
        <div class="section-title">Accessories Required</div>
        <div class="section-line"></div>
        <span class="section-count">{{ count($survey->accessories) }} item(s)</span>
      </div>
      <div class="tags-wrap">
        @foreach($survey->accessories as $acc)
          <span class="tag tag-gray">{{ $acc['name'] }} &times; {{ $acc['qty'] }}</span>
        @endforeach
      </div>
    </div>
    @endif

    {{-- Identified Risks --}}
    @if($survey->risks && count($survey->risks))
    <div class="section">
      <div class="risk-box">
        <div class="risk-title">⚠ Identified Risks</div>
        <div class="tags-wrap">
          @foreach($survey->risks as $r)
            <span class="tag tag-orange">{{ $r }}</span>
          @endforeach
        </div>
      </div>
    </div>
    @endif

    {{-- Special Notes --}}
    @if($survey->special_notes)
    <div class="section">
      <div class="section-header">
        <div class="section-icon">📝</div>
        <div class="section-title">Special Notes</div>
        <div class="section-line"></div>
      </div>
      <div class="notes-box">{{ $survey->special_notes }}</div>
    </div>
    @endif

    @endif {{-- /simple vs detailed --}}

    {{-- Site Photos --}}
    @if($survey->site_photos && count($survey->site_photos))
    <div class="section">
      <div class="section-header">
        <div class="section-icon">🖼</div>
        <div class="section-title">Site Photos</div>
        <div class="section-line"></div>
        <span class="section-count">{{ count($survey->site_photos) }} photo(s)</span>
      </div>
      <div class="photo-grid">
        @foreach($survey->site_photos as $photo)
          <img src="{{ asset('storage/'.$photo) }}" alt="Site photo">
        @endforeach
      </div>
    </div>
    @endif

    {{-- ══ SIGNATURE SECTION ══ --}}
    <div class="sig-section">
      <div class="sig-cell">
        <div class="sig-line-box"></div>
        <div class="sig-label">Surveyed By</div>
        <div class="sig-name">{{ $survey->technician?->employee_name ?? '____________________' }}</div>
      </div>
      <div class="sig-cell">
        <div class="sig-line-box"></div>
        <div class="sig-label">Customer Acknowledgement</div>
        <div class="sig-name">{{ $survey->customer_name }}</div>
      </div>
      <div class="sig-cell">
        <div class="sig-line-box"></div>
        <div class="sig-label">Authorized By</div>
        <div class="sig-name">{{ $store?->owner_name ?? '____________________' }}</div>
      </div>
    </div>

  </div>{{-- /doc-body --}}

  {{-- ══ FOOTER ══ --}}
  <div class="doc-footer">
    <div class="footer-left">
      Generated {{ now()->format('d M Y, h:i A') }}
    </div>
    <div class="footer-mid">
      <div class="footer-brand">{{ $store?->store_name ?? config('app.name') }}</div>
      <div style="font-size:9.5px;color:rgba(255,255,255,.3);margin-top:1px;">CCTV Security Solutions</div>
    </div>
    <div class="footer-right">
      {{ $survey->survey_no }}<br>
      <span style="font-size:9px;">Confidential Document</span>
    </div>
  </div>

</div>{{-- /page-wrap --}}

<script>
function copyLink() {
  navigator.clipboard.writeText(window.location.href).then(() => {
    const btn = document.querySelector('.tbtn-copy');
    const orig = btn.innerHTML;
    btn.innerHTML = '&#10003; Copied!';
    btn.style.background = 'rgba(16,185,129,.3)';
    btn.style.borderColor = 'rgba(16,185,129,.5)';
    setTimeout(() => {
      btn.innerHTML = orig;
      btn.style.background = '';
      btn.style.borderColor = '';
    }, 2000);
  });
}
</script>
</body>
</html>
