<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Quotation – {{ $quotation->quotation_no }}</title>
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size:12px; color:#333; background:#fff; }
    .page { max-width:800px; margin:0 auto; padding:30px; }

    .header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:24px; padding-bottom:18px; border-bottom:2px solid #696cff; }
    .company-name { font-size:22px; font-weight:800; color:#696cff; }
    .company-sub { font-size:11px; color:#888; margin-top:2px; }
    .company-contact { font-size:11px; color:#555; margin-top:6px; line-height:1.6; }
    .doc-title { text-align:right; }
    .doc-title h2 { font-size:20px; font-weight:800; color:#333; text-transform:uppercase; letter-spacing:1px; }
    .doc-title .doc-no { font-size:13px; font-weight:700; color:#696cff; margin-top:4px; }
    .doc-title .doc-date { font-size:11px; color:#888; margin-top:3px; }

    .bill-to-section { display:flex; justify-content:space-between; margin-bottom:24px; }
    .bill-box { flex:1; }
    .bill-box + .bill-box { margin-left:24px; }
    .bill-label { font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:.06em; color:#888; margin-bottom:6px; }
    .bill-name { font-size:13px; font-weight:700; color:#333; }
    .bill-detail { font-size:11px; color:#555; line-height:1.6; margin-top:2px; }

    table { width:100%; border-collapse:collapse; margin-bottom:16px; }
    thead tr { background:#696cff; color:#fff; }
    thead th { padding:9px 12px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.04em; }
    tbody tr { border-bottom:1px solid #eee; }
    tbody tr:nth-child(even) { background:#f9f9ff; }
    tbody td { padding:8px 12px; font-size:12px; }
    .text-right { text-align:right; }
    .text-center { text-align:center; }

    .totals-section { display:flex; justify-content:flex-end; margin-bottom:24px; }
    .totals-box { width:260px; }
    .total-row { display:flex; justify-content:space-between; padding:5px 0; font-size:12px; border-bottom:1px solid #eee; }
    .total-row.grand { font-size:14px; font-weight:800; color:#696cff; border-top:2px solid #696cff; border-bottom:none; padding-top:8px; margin-top:4px; }
    .total-label { color:#555; }

    .terms-section { margin-bottom:24px; }
    .terms-title { font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:.06em; color:#888; margin-bottom:6px; }
    .terms-content { font-size:11px; color:#555; line-height:1.7; }

    .footer { text-align:center; font-size:10px; color:#aaa; border-top:1px solid #eee; padding-top:12px; margin-top:24px; }
    .valid-badge { display:inline-block; background:#eef0ff; color:#696cff; border:1px solid #c5c7ff; border-radius:6px; padding:4px 12px; font-size:11px; font-weight:700; margin-top:10px; }

    @media print {
      body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
      .no-print { display: none; }
    }
  </style>
</head>
<body>
  <div class="page">

    {{-- Print / Back buttons --}}
    <div class="no-print" style="margin-bottom:16px;">
      <button onclick="window.print()" style="background:#696cff;color:#fff;border:none;padding:8px 18px;border-radius:6px;cursor:pointer;font-size:13px;margin-right:8px;">
        🖨 Print / Save PDF
      </button>
      <a href="{{ route('admin.cctv.quotations.show', $quotation) }}" style="background:#f0f0f0;color:#333;border:none;padding:8px 18px;border-radius:6px;cursor:pointer;font-size:13px;text-decoration:none;">
        ← Back
      </a>
    </div>

    {{-- Header --}}
    <div class="header">
      <div>
        <div class="company-name">{{ $shop->name ?? config('app.name') }}</div>
        <div class="company-sub">CCTV Security Solutions</div>
        <div class="company-contact">
          @if(isset($shop->phone)) {{ $shop->phone }}<br>@endif
          @if(isset($shop->email)) {{ $shop->email }}<br>@endif
          @if(isset($shop->address)) {{ $shop->address }}@endif
        </div>
      </div>
      <div class="doc-title">
        <h2>Quotation</h2>
        <div class="doc-no">{{ $quotation->quotation_no }}</div>
        <div class="doc-date">Date: {{ $quotation->created_at->format('d M Y') }}</div>
        @if($quotation->valid_until)
        <div class="valid-badge">Valid until {{ \Carbon\Carbon::parse($quotation->valid_until)->format('d M Y') }}</div>
        @endif
      </div>
    </div>

    {{-- Bill To --}}
    <div class="bill-to-section">
      <div class="bill-box">
        <div class="bill-label">Bill To</div>
        <div class="bill-name">{{ $quotation->customer_name }}</div>
        <div class="bill-detail">
          {{ $quotation->mobile }}<br>
          @if($quotation->email){{ $quotation->email }}<br>@endif
          @if($quotation->address){{ $quotation->address }}@endif
        </div>
      </div>
    </div>

    {{-- Items Table --}}
    @php $items = is_array($quotation->items) ? $quotation->items : (json_decode($quotation->items, true) ?? []) @endphp
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Description</th>
          <th class="text-center">Qty</th>
          <th class="text-right">Unit Price (Rs.)</th>
          <th class="text-right">Total (Rs.)</th>
        </tr>
      </thead>
      <tbody>
        @foreach($items as $i => $item)
        <tr>
          <td>{{ $i+1 }}</td>
          <td>{{ $item['description'] ?? '' }}</td>
          <td class="text-center">{{ $item['qty'] ?? 1 }}</td>
          <td class="text-right">{{ number_format($item['unit_price'] ?? 0, 2) }}</td>
          <td class="text-right">{{ number_format(($item['qty']??1)*($item['unit_price']??0), 2) }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>

    {{-- Totals --}}
    <div class="totals-section">
      <div class="totals-box">
        <div class="total-row"><span class="total-label">Sub Total</span><span>Rs. {{ number_format($quotation->sub_total ?? 0, 2) }}</span></div>
        @if($quotation->discount_amount > 0)
        <div class="total-row"><span class="total-label">Discount</span><span style="color:#ea5455">- Rs. {{ number_format($quotation->discount_amount, 2) }}</span></div>
        @endif
        @if(isset($quotation->installation_charge) && $quotation->installation_charge > 0)
        <div class="total-row"><span class="total-label">Installation</span><span>Rs. {{ number_format($quotation->installation_charge, 2) }}</span></div>
        @endif
        <div class="total-row grand"><span class="total-label">Grand Total</span><span>Rs. {{ number_format($quotation->total_amount ?? 0, 2) }}</span></div>
      </div>
    </div>

    {{-- Terms --}}
    @if($quotation->terms)
    <div class="terms-section">
      <div class="terms-title">Terms & Conditions</div>
      <div class="terms-content" style="white-space:pre-line">{{ $quotation->terms }}</div>
    </div>
    @endif

    @if($quotation->notes)
    <div class="terms-section">
      <div class="terms-title">Notes</div>
      <div class="terms-content" style="white-space:pre-line">{{ $quotation->notes }}</div>
    </div>
    @endif

    <div class="footer">
      This quotation was generated by {{ $shop->name ?? config('app.name') }} &bull; {{ $quotation->quotation_no }} &bull; {{ now()->format('d M Y') }}
    </div>
  </div>
</body>
</html>
