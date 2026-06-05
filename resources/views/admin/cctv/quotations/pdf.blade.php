<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Quotation – {{ $quotation->quote_no }}</title>
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
    .tbtn-print { background: #696cff; color: #fff; }
    .tbtn-wa    { background: #25d366; color: #fff; }
    .tbtn-copy  { background: rgba(255,255,255,.12); color: #fff; border: 1px solid rgba(255,255,255,.2); }
    .tbtn-back  { background: transparent; color: rgba(255,255,255,.65); border: 1px solid rgba(255,255,255,.15); }
    .tbtn:hover { opacity: .85; transform: translateY(-1px); }

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
      position: relative; overflow: hidden;
    }
    .doc-header::before {
      content: ''; position: absolute; top: -60px; right: -60px;
      width: 240px; height: 240px;
      background: radial-gradient(circle, rgba(105,108,255,.25) 0%, transparent 70%);
      border-radius: 50%;
    }
    .doc-header::after {
      content: ''; position: absolute; bottom: -40px; left: 30%;
      width: 160px; height: 160px;
      background: radial-gradient(circle, rgba(105,108,255,.12) 0%, transparent 70%);
      border-radius: 50%;
    }
    .header-inner {
      position: relative; z-index: 1;
      display: flex; justify-content: space-between; align-items: flex-start;
      padding: 28px 36px 24px;
    }
    .brand-logo-row {
      display: flex; align-items: center; gap: 10px; margin-bottom: 6px;
    }
    .brand-icon {
      width: 36px; height: 36px; background: #696cff; border-radius: 8px;
      display: flex; align-items: center; justify-content: center;
      font-size: 18px; font-weight: 900; color: #fff; flex-shrink: 0;
    }
    .brand-name    { font-size: 20px; font-weight: 800; color: #fff; letter-spacing: -.2px; }
    .brand-tagline { font-size: 10px; color: rgba(255,255,255,.5); text-transform: uppercase; letter-spacing: .1em; }
    .brand-contact { margin-top: 10px; font-size: 10.5px; color: rgba(255,255,255,.65); line-height: 1.8; }
    .brand-contact strong { color: rgba(255,255,255,.9); }

    .doc-title-block { text-align: right; }
    .doc-type-label {
      display: inline-block; background: rgba(255,255,255,.1);
      border: 1px solid rgba(255,255,255,.2); color: rgba(255,255,255,.7);
      font-size: 9.5px; font-weight: 700; text-transform: uppercase;
      letter-spacing: .12em; padding: 3px 10px; border-radius: 20px; margin-bottom: 6px;
    }
    .doc-type-main { font-size: 26px; font-weight: 900; color: #fff; letter-spacing: -.5px; line-height: 1; }
    .doc-type-sub  { font-size: 12px; color: rgba(255,255,255,.5); margin-top: 2px; }
    .doc-no-box {
      margin-top: 10px; display: inline-block;
      background: rgba(105,108,255,.3); border: 1px solid rgba(105,108,255,.5);
      border-radius: 6px; padding: 5px 14px;
      font-size: 14px; font-weight: 700; color: #fff; letter-spacing: .04em;
    }
    .doc-date-row { margin-top: 6px; font-size: 10.5px; color: rgba(255,255,255,.5); }
    .header-accent {
      height: 4px;
      background: linear-gradient(90deg, #696cff 0%, #8b8fff 50%, #b8baff 100%);
    }

    /* ════════════════════════════════
       STATUS BAR
    ════════════════════════════════ */
    .status-bar {
      display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
      padding: 10px 36px; background: #f8f8ff; border-bottom: 1px solid #ebebff;
    }
    .pill {
      display: inline-flex; align-items: center; gap: 5px;
      padding: 4px 12px; border-radius: 20px;
      font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em;
    }
    .pill-dot { width: 6px; height: 6px; border-radius: 50%; display: inline-block; }
    .pill-draft    { background: #f3f4f6; color: #374151; }
    .pill-draft .pill-dot    { background: #9ca3af; }
    .pill-sent     { background: #dbeafe; color: #1d4ed8; }
    .pill-sent .pill-dot     { background: #3b82f6; }
    .pill-approved { background: #d1fae5; color: #065f46; }
    .pill-approved .pill-dot { background: #059669; }
    .pill-rejected { background: #fee2e2; color: #991b1b; }
    .pill-rejected .pill-dot { background: #ef4444; }
    .pill-validity { background: #fef3c7; color: #92400e; }
    .pill-validity .pill-dot { background: #f59e0b; }
    .status-bar-spacer { flex: 1; }
    .status-bar-ref { font-size: 10px; color: #aaa; }

    /* ════════════════════════════════
       STAT CARDS
    ════════════════════════════════ */
    .stat-row {
      display: flex; gap: 0;
      border-bottom: 2px solid #ebebff;
    }
    .stat-card {
      flex: 1; padding: 14px 18px;
      border-right: 1px solid #ebebff; position: relative;
    }
    .stat-card:last-child { border-right: none; }
    .stat-card::before {
      content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    }
    .stat-card.purple::before { background: #696cff; }
    .stat-card.blue::before   { background: #3b82f6; }
    .stat-card.green::before  { background: #10b981; }
    .stat-card.orange::before { background: #f59e0b; }
    .stat-card.red::before    { background: #ef4444; }
    .stat-num { font-size: 22px; font-weight: 900; color: #1e1e2d; line-height: 1; }
    .stat-num.purple { color: #696cff; }
    .stat-num.blue   { color: #3b82f6; }
    .stat-num.green  { color: #10b981; }
    .stat-num.orange { color: #f59e0b; }
    .stat-num.red    { color: #ef4444; }
    .stat-lbl { font-size: 9.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; color: #aaa; margin-top: 3px; }
    .stat-sub { font-size: 10px; color: #888; margin-top: 2px; }

    /* ════════════════════════════════
       BODY
    ════════════════════════════════ */
    .doc-body { padding: 28px 36px; position: relative; }

    /* ════════════════════════════════
       WATERMARK
    ════════════════════════════════ */
    .watermark {
      position: absolute; top: 50%; left: 50%;
      transform: translate(-50%,-50%) rotate(-35deg);
      font-size: 80px; font-weight: 900;
      color: rgba(105,108,255,.04);
      white-space: nowrap; pointer-events: none;
      letter-spacing: 8px; text-transform: uppercase; z-index: 0;
    }

    /* ════════════════════════════════
       SECTION
    ════════════════════════════════ */
    .section { margin-bottom: 24px; position: relative; z-index: 1; }
    .section-header {
      display: flex; align-items: center; gap: 10px;
      margin-bottom: 14px; padding-bottom: 8px; border-bottom: 1.5px solid #f0f0f0;
    }
    .section-icon {
      width: 26px; height: 26px; background: #eef0ff; border-radius: 6px;
      display: flex; align-items: center; justify-content: center;
      font-size: 13px; flex-shrink: 0;
    }
    .section-title {
      font-size: 11px; font-weight: 800; text-transform: uppercase;
      letter-spacing: .08em; color: #1e1e2d;
    }
    .section-line { flex: 1; height: 1px; background: #f0f0f0; }

    /* ════════════════════════════════
       FIELDS GRID
    ════════════════════════════════ */
    .fields { display: flex; flex-wrap: wrap; gap: 0; }
    .field { padding: 6px 12px 8px 0; }
    .field.w25  { flex: 0 0 25%; }
    .field.w33  { flex: 0 0 33.333%; }
    .field.w50  { flex: 0 0 50%; }
    .field.w100 { flex: 0 0 100%; }
    .flbl {
      font-size: 9px; font-weight: 700; text-transform: uppercase;
      letter-spacing: .07em; color: #b0b0c3; margin-bottom: 3px;
    }
    .fval { font-size: 12px; font-weight: 500; color: #1e1e2d; line-height: 1.4; }
    .fval.bold  { font-weight: 700; font-size: 13px; }
    .fval.mono  { font-family: 'Courier New', monospace; }
    .fval.muted { color: #bbb; font-style: italic; }

    /* ════════════════════════════════
       ITEMS TABLE
    ════════════════════════════════ */
    .data-table { width: 100%; border-collapse: collapse; font-size: 11px; }
    .data-table thead tr {
      background: linear-gradient(135deg, #1e1e2d, #2d2b55);
      color: #fff;
    }
    .data-table thead th {
      padding: 9px 12px; font-size: 9.5px; font-weight: 700;
      text-transform: uppercase; letter-spacing: .06em; text-align: left;
    }
    .data-table thead th.text-right { text-align: right; }
    .data-table thead th.text-center { text-align: center; }
    .data-table tbody tr { border-bottom: 1px solid #f0f0f5; }
    .data-table tbody tr:last-child { border-bottom: 2px solid #ebebff; }
    .data-table tbody tr:nth-child(even) { background: #fafafe; }
    .data-table tbody td { padding: 8px 12px; }
    .data-table tfoot td {
      padding: 7px 12px; font-size: 11px;
      border-top: 1px solid #f0f0f0;
    }
    .text-right  { text-align: right; }
    .text-center { text-align: center; }
    .col-num { width: 36px; text-align: center; font-weight: 700; color: #696cff; }

    /* ════════════════════════════════
       TOTALS BOX
    ════════════════════════════════ */
    .totals-wrap {
      display: flex; justify-content: flex-end;
      margin-top: 4px; margin-bottom: 24px;
    }
    .totals-box {
      width: 300px;
      border: 1px solid #ebebff;
      border-radius: 8px;
      overflow: hidden;
    }
    .total-row {
      display: flex; justify-content: space-between; align-items: center;
      padding: 8px 14px;
      border-bottom: 1px solid #f0f0f5;
      font-size: 11.5px;
    }
    .total-row:last-child { border-bottom: none; }
    .total-row.subtotal-row { background: #fafafe; }
    .total-row.discount-row .total-val { color: #ef4444; font-weight: 700; }
    .total-row.tax-row      .total-val { color: #f59e0b; font-weight: 700; }
    .total-row.grand-row {
      background: linear-gradient(135deg, #1e1e2d, #2d2b55);
      color: #fff; padding: 12px 14px;
    }
    .total-row.grand-row .total-lbl { color: rgba(255,255,255,.7); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; }
    .total-row.grand-row .total-val { color: #fff; font-size: 16px; font-weight: 900; }
    .total-lbl { color: #666; }
    .total-val { font-weight: 600; color: #1e1e2d; }

    /* ════════════════════════════════
       CHARGE PILLS INLINE
    ════════════════════════════════ */
    .charge-row {
      display: flex; gap: 10px; flex-wrap: wrap;
      padding: 12px 0 4px;
    }
    .charge-pill {
      display: flex; flex-direction: column;
      background: #fafafe; border: 1px solid #ebebff;
      border-radius: 8px; padding: 8px 16px; min-width: 110px;
    }
    .charge-pill-lbl { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; color: #b0b0c3; margin-bottom: 3px; }
    .charge-pill-val { font-size: 13px; font-weight: 700; color: #1e1e2d; }

    /* ════════════════════════════════
       NOTES BOX
    ════════════════════════════════ */
    .notes-box {
      background: #fafafe; border-left: 3px solid #696cff;
      border-radius: 0 6px 6px 0; padding: 12px 16px;
      font-size: 12px; color: #333; line-height: 1.75; white-space: pre-line;
    }

    /* ════════════════════════════════
       SIGNATURE SECTION
    ════════════════════════════════ */
    .sig-section {
      display: flex; gap: 0;
      border-top: 2px solid #f0f0f0; margin-top: 32px;
    }
    .sig-cell {
      flex: 1; padding: 24px 20px 16px;
      border-right: 1px solid #f0f0f0; text-align: center;
    }
    .sig-cell:last-child { border-right: none; }
    .sig-line-box { height: 42px; border-bottom: 1.5px solid #333; margin-bottom: 8px; }
    .sig-label { font-size: 10px; font-weight: 700; color: #888; text-transform: uppercase; letter-spacing: .07em; }
    .sig-name  { font-size: 11px; color: #555; margin-top: 2px; }

    /* ════════════════════════════════
       FOOTER
    ════════════════════════════════ */
    .doc-footer {
      background: #1e1e2d; padding: 12px 36px;
      display: flex; align-items: center; justify-content: space-between;
    }
    .footer-left  { font-size: 10px; color: rgba(255,255,255,.4); }
    .footer-mid   { font-size: 10px; color: rgba(255,255,255,.6); font-weight: 600; text-align: center; }
    .footer-brand { font-size: 11px; font-weight: 800; color: #696cff; }
    .footer-right { font-size: 10px; color: rgba(255,255,255,.4); text-align: right; }

    /* ════════════════════════════════
       PRINT
    ════════════════════════════════ */
    @media print {
      body { background: #fff; }
      .toolbar { display: none !important; }
      .page-wrap { margin: 0; box-shadow: none; border-radius: 0; max-width: 100%; }
      .doc-body { padding: 20px 28px; }
      .doc-header, .doc-footer, .stat-row, .data-table thead tr, .total-row.grand-row {
        -webkit-print-color-adjust: exact; print-color-adjust: exact;
      }
      .section { page-break-inside: avoid; }
      a { color: inherit !important; text-decoration: none !important; }
      @page { size: A4; margin: 10mm; }
    }
  </style>
</head>
<body>

@php
  $items     = is_array($quotation->equipment_list) ? $quotation->equipment_list : (json_decode($quotation->equipment_list ?? '[]', true) ?? []);
  $equipTotal = collect($items)->sum(fn($i) => ($i['qty'] ?? 0) * ($i['unit_price'] ?? 0));
  $subtotal   = $equipTotal + ($quotation->labour_cost ?? 0) + ($quotation->installation_cost ?? 0) + ($quotation->transport_cost ?? 0);
  $discount   = $quotation->discount ?? 0;
  $tax        = $quotation->tax ?? 0;
  $grand      = $quotation->grand_total ?? max(0, $subtotal - $discount + $tax);

  $statusSlug = strtolower($quotation->status ?? 'draft');
  $pillClass  = match($statusSlug) {
    'sent'     => 'pill-sent',
    'approved' => 'pill-approved',
    'rejected' => 'pill-rejected',
    default    => 'pill-draft',
  };

  $printUrl = request()->url();
@endphp

{{-- ══ TOOLBAR ══ --}}
<div class="toolbar">
  <div class="tbar-logo">{{ $store?->store_name ?? config('app.name') }}</div>
  <div class="tbar-divider"></div>
  <span class="tbar-title">{{ $quotation->quote_no }}</span>
  <span class="tbar-sub">{{ $quotation->customer_name }} &middot; Quotation</span>
  <div class="tbar-spacer"></div>
  <a href="{{ route('admin.cctv.quotations.show', $quotation) }}" class="tbtn tbtn-back">&#8592; Back</a>
  <button class="tbtn tbtn-copy" onclick="copyLink()">&#128279; Copy Link</button>
  <a class="tbtn tbtn-wa"
     href="https://wa.me/?text={{ urlencode('CCTV Quotation '.$quotation->quote_no.' for '.$quotation->customer_name.'. View: '.$printUrl) }}"
     target="_blank">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.554 4.117 1.528 5.849L.057 23.429a.5.5 0 00.614.614l5.58-1.471A11.95 11.95 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.797 9.797 0 01-5.003-1.376l-.359-.213-3.712.979.994-3.618-.233-.372A9.808 9.808 0 012.182 12C2.182 6.57 6.57 2.182 12 2.182S21.818 6.57 21.818 12 17.43 21.818 12 21.818z"/></svg>
    WhatsApp
  </a>
  <button class="tbtn tbtn-print" onclick="window.print()">&#128438; Print / PDF</button>
</div>

{{-- ══ PAGE ══ --}}
<div class="page-wrap">

  {{-- ── HEADER ── --}}
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
        <div class="doc-type-main">Quotation</div>
        <div class="doc-type-sub">Price Estimate &amp; Proposal</div>
        <div class="doc-no-box">{{ $quotation->quote_no }}</div>
        <div class="doc-date-row">
          Dated: {{ $quotation->created_at->format('d M Y') }}
        </div>
      </div>
    </div>
    <div class="header-accent"></div>
  </div>

  {{-- ── STATUS BAR ── --}}
  <div class="status-bar">
    <span class="pill {{ $pillClass }}">
      <span class="pill-dot"></span>{{ $quotation->status ?? 'Draft' }}
    </span>
    @if($quotation->valid_until)
    <span class="pill pill-validity">
      <span class="pill-dot"></span>Valid until {{ \Carbon\Carbon::parse($quotation->valid_until)->format('d M Y') }}
    </span>
    @endif
    <div class="status-bar-spacer"></div>
    <span class="status-bar-ref">
      Ref: {{ $quotation->quote_no }} &nbsp;|&nbsp; Generated {{ now()->format('d M Y') }}
    </span>
  </div>

  {{-- ── STAT CARDS ── --}}
  <div class="stat-row">
    <div class="stat-card purple">
      <div class="stat-num purple">{{ count($items) }}</div>
      <div class="stat-lbl">Line Items</div>
      <div class="stat-sub">Equipment &amp; services</div>
    </div>
    <div class="stat-card blue">
      <div class="stat-num blue" style="font-size:16px;">Rs. {{ number_format($equipTotal, 0) }}</div>
      <div class="stat-lbl">Equipment Total</div>
    </div>
    @if(($quotation->labour_cost ?? 0) + ($quotation->installation_cost ?? 0) + ($quotation->transport_cost ?? 0) > 0)
    <div class="stat-card orange">
      <div class="stat-num orange" style="font-size:16px;">Rs. {{ number_format(($quotation->labour_cost ?? 0)+($quotation->installation_cost ?? 0)+($quotation->transport_cost ?? 0), 0) }}</div>
      <div class="stat-lbl">Additional Charges</div>
      <div class="stat-sub">Labour + Install + Transport</div>
    </div>
    @endif
    @if($discount > 0)
    <div class="stat-card red">
      <div class="stat-num red" style="font-size:16px;">Rs. {{ number_format($discount, 0) }}</div>
      <div class="stat-lbl">Discount</div>
    </div>
    @endif
    <div class="stat-card green">
      <div class="stat-num green" style="font-size:16px;">Rs. {{ number_format($grand, 0) }}</div>
      <div class="stat-lbl">Grand Total</div>
      <div class="stat-sub">Inc. all charges</div>
    </div>
  </div>

  {{-- ══ BODY ══ --}}
  <div class="doc-body">
    <div class="watermark">{{ $store?->store_name ?? 'TRACKUP' }}</div>

    {{-- ── BILL TO ── --}}
    <div class="section">
      <div class="section-header">
        <div class="section-icon">👤</div>
        <div class="section-title">Bill To</div>
        <div class="section-line"></div>
      </div>
      <div class="fields">
        <div class="field w50">
          <div class="flbl">Customer Name</div>
          <div class="fval bold">{{ $quotation->customer_name ?: '—' }}</div>
        </div>
        <div class="field w50">
          <div class="flbl">Mobile</div>
          <div class="fval mono">{{ $quotation->mobile ?: '—' }}</div>
        </div>
        @if($quotation->lead_id)
        <div class="field w50">
          <div class="flbl">Lead Reference</div>
          <div class="fval" style="color:#696cff;font-weight:600;">
            {{ $quotation->lead?->lead_no ?? 'Lead #'.$quotation->lead_id }}
          </div>
        </div>
        @endif
        @if($quotation->valid_until)
        <div class="field w50">
          <div class="flbl">Valid Until</div>
          <div class="fval" style="color:#f59e0b;font-weight:600;">
            {{ \Carbon\Carbon::parse($quotation->valid_until)->format('d M Y') }}
          </div>
        </div>
        @endif
      </div>
    </div>

    {{-- ── EQUIPMENT / LINE ITEMS ── --}}
    <div class="section">
      <div class="section-header">
        <div class="section-icon">📦</div>
        <div class="section-title">Equipment &amp; Services</div>
        <div class="section-line"></div>
      </div>

      @if(count($items))
      <table class="data-table">
        <thead>
          <tr>
            <th style="width:36px;">#</th>
            <th>Description / Item</th>
            <th class="text-center" style="width:60px;">Qty</th>
            <th class="text-right" style="width:130px;">Unit Price (Rs.)</th>
            <th class="text-right" style="width:130px;">Total (Rs.)</th>
          </tr>
        </thead>
        <tbody>
          @foreach($items as $i => $item)
          @php
            $itemName  = $item['name'] ?? ($item['description'] ?? '');
            $itemQty   = $item['qty'] ?? 1;
            $itemPrice = $item['unit_price'] ?? 0;
            $itemTotal = $itemQty * $itemPrice;
          @endphp
          <tr>
            <td class="col-num">{{ $i + 1 }}</td>
            <td style="font-weight:500;">{{ $itemName }}</td>
            <td class="text-center">{{ $itemQty }}</td>
            <td class="text-right">{{ number_format($itemPrice, 2) }}</td>
            <td class="text-right" style="font-weight:600;">{{ number_format($itemTotal, 2) }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
      @else
      <div style="padding:16px;text-align:center;color:#bbb;font-style:italic;">No items listed.</div>
      @endif
    </div>

    {{-- ── ADDITIONAL CHARGES + TOTALS ── --}}
    @if(($quotation->labour_cost ?? 0) > 0 || ($quotation->installation_cost ?? 0) > 0 || ($quotation->transport_cost ?? 0) > 0)
    <div class="section">
      <div class="section-header">
        <div class="section-icon">🔧</div>
        <div class="section-title">Additional Charges</div>
        <div class="section-line"></div>
      </div>
      <div class="charge-row">
        @if(($quotation->labour_cost ?? 0) > 0)
        <div class="charge-pill">
          <span class="charge-pill-lbl">Labour Cost</span>
          <span class="charge-pill-val">Rs. {{ number_format($quotation->labour_cost, 2) }}</span>
        </div>
        @endif
        @if(($quotation->installation_cost ?? 0) > 0)
        <div class="charge-pill">
          <span class="charge-pill-lbl">Installation</span>
          <span class="charge-pill-val">Rs. {{ number_format($quotation->installation_cost, 2) }}</span>
        </div>
        @endif
        @if(($quotation->transport_cost ?? 0) > 0)
        <div class="charge-pill">
          <span class="charge-pill-lbl">Transport</span>
          <span class="charge-pill-val">Rs. {{ number_format($quotation->transport_cost, 2) }}</span>
        </div>
        @endif
      </div>
    </div>
    @endif

    {{-- ── TOTALS BOX ── --}}
    <div class="totals-wrap">
      <div class="totals-box">
        <div class="total-row subtotal-row">
          <span class="total-lbl">Equipment Subtotal</span>
          <span class="total-val">Rs. {{ number_format($equipTotal, 2) }}</span>
        </div>
        @if(($quotation->labour_cost ?? 0) > 0)
        <div class="total-row">
          <span class="total-lbl">Labour</span>
          <span class="total-val">Rs. {{ number_format($quotation->labour_cost, 2) }}</span>
        </div>
        @endif
        @if(($quotation->installation_cost ?? 0) > 0)
        <div class="total-row">
          <span class="total-lbl">Installation</span>
          <span class="total-val">Rs. {{ number_format($quotation->installation_cost, 2) }}</span>
        </div>
        @endif
        @if(($quotation->transport_cost ?? 0) > 0)
        <div class="total-row">
          <span class="total-lbl">Transport</span>
          <span class="total-val">Rs. {{ number_format($quotation->transport_cost, 2) }}</span>
        </div>
        @endif
        @if($discount > 0)
        <div class="total-row discount-row">
          <span class="total-lbl">Discount</span>
          <span class="total-val">- Rs. {{ number_format($discount, 2) }}</span>
        </div>
        @endif
        @if($tax > 0)
        <div class="total-row tax-row">
          <span class="total-lbl">Tax / VAT</span>
          <span class="total-val">+ Rs. {{ number_format($tax, 2) }}</span>
        </div>
        @endif
        <div class="total-row grand-row">
          <span class="total-lbl">Grand Total</span>
          <span class="total-val">Rs. {{ number_format($grand, 2) }}</span>
        </div>
      </div>
    </div>

    {{-- ── NOTES / TERMS ── --}}
    @if($quotation->notes)
    <div class="section">
      <div class="section-header">
        <div class="section-icon">📝</div>
        <div class="section-title">Notes &amp; Terms</div>
        <div class="section-line"></div>
      </div>
      <div class="notes-box">{{ $quotation->notes }}</div>
    </div>
    @endif

    {{-- ── SIGNATURE ── --}}
    <div class="sig-section">
      <div class="sig-cell">
        <div class="sig-line-box"></div>
        <div class="sig-label">Prepared By</div>
        <div class="sig-name">{{ $store?->owner_name ?? '____________________' }}</div>
      </div>
      <div class="sig-cell">
        <div class="sig-line-box"></div>
        <div class="sig-label">Customer Acceptance</div>
        <div class="sig-name">{{ $quotation->customer_name }}</div>
      </div>
      <div class="sig-cell">
        <div class="sig-line-box"></div>
        <div class="sig-label">Authorized By</div>
        <div class="sig-name">{{ $store?->owner_name ?? '____________________' }}</div>
      </div>
    </div>

  </div>{{-- /doc-body --}}

  {{-- ── FOOTER ── --}}
  <div class="doc-footer">
    <div class="footer-left">Generated {{ now()->format('d M Y, h:i A') }}</div>
    <div class="footer-mid">
      <div class="footer-brand">{{ $store?->store_name ?? config('app.name') }}</div>
      <div style="font-size:9.5px;color:rgba(255,255,255,.3);margin-top:1px;">CCTV Security Solutions</div>
    </div>
    <div class="footer-right">
      {{ $quotation->quote_no }}<br>
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
