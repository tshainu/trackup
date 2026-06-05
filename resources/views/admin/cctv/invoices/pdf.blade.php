<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Invoice {{ $invoice->invoice_no }}</title>
<style>
  * { box-sizing:border-box; margin:0; padding:0; }
  body { font-family:'Segoe UI',Arial,sans-serif; font-size:13px; color:#1a1a1a; background:#fff; padding:30px 40px; }
  .inv-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:28px; padding-bottom:20px; border-bottom:3px solid #28c76f; }
  .company-info h2 { font-size:20px; color:#28c76f; font-weight:800; margin-bottom:4px; }
  .company-info p  { font-size:11px; color:#555; line-height:1.5; }
  .inv-title { text-align:right; }
  .inv-title h1 { font-size:28px; font-weight:800; color:#1a1a1a; letter-spacing:-0.5px; }
  .inv-title .inv-no { font-size:13px; color:#697a8d; margin-top:4px; font-family:monospace; }
  .inv-title .inv-status { display:inline-block; margin-top:8px; padding:3px 12px; border-radius:20px; font-size:11px; font-weight:700; }
  .status-Paid    { background:#e8faf0; color:#28c76f; }
  .status-Partial { background:#fff8e1; color:#fd9800; }
  .status-Unpaid  { background:#fdeaea; color:#ea5455; }
  .meta-grid { display:grid; grid-template-columns:1fr 1fr; gap:24px; margin-bottom:24px; }
  .meta-box { background:#f8f9fa; border-radius:8px; padding:14px 16px; }
  .meta-box h4 { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#a1acb8; margin-bottom:8px; }
  .meta-box p  { font-size:12px; color:#32325d; line-height:1.6; }
  .meta-box .big { font-size:14px; font-weight:700; color:#28c76f; font-family:monospace; }
  table.items { width:100%; border-collapse:collapse; margin-bottom:16px; }
  table.items thead th { background:#28c76f; color:#fff; padding:8px 12px; text-align:left; font-size:11px; text-transform:uppercase; letter-spacing:.04em; }
  table.items thead th:last-child { text-align:right; }
  table.items tbody td { padding:8px 12px; border-bottom:1px solid #f0f0f0; font-size:12px; vertical-align:middle; }
  table.items tbody td:nth-child(2), table.items tbody td:nth-child(3) { text-align:center; }
  table.items tbody td:last-child { text-align:right; font-weight:600; }
  table.items tbody tr:nth-child(even) { background:#fafafa; }
  .totals { width:300px; margin-left:auto; }
  .totals-row { display:flex; justify-content:space-between; padding:5px 0; font-size:12px; border-bottom:1px solid #f5f5f5; }
  .totals-row.grand { font-size:15px; font-weight:800; border-top:2px solid #28c76f; border-bottom:none; padding-top:8px; color:#28c76f; }
  .totals-row.paid  { color:#28c76f; }
  .totals-row.balance { font-weight:700; color:#ea5455; }
  .footer { margin-top:36px; padding-top:16px; border-top:1px solid #e9ecef; display:flex; justify-content:space-between; font-size:10px; color:#adb5bd; }
  .notes-box { background:#f8f9fa; border-radius:8px; padding:12px 16px; margin-bottom:20px; }
  .notes-box h4 { font-size:10px; font-weight:700; text-transform:uppercase; color:#a1acb8; margin-bottom:6px; }
  .notes-box p  { font-size:11px; color:#555; line-height:1.5; white-space:pre-line; }
  @media print {
    body { padding:15px 20px; }
    @page { margin:10mm; }
  }
</style>
</head>
<body>

  <div class="inv-header">
    <div class="company-info">
      @if($store ?? null)
        <h2>{{ $store->name ?? $store->shop_name ?? 'Company Name' }}</h2>
        <p>
          {{ $store->address ?? '' }}<br>
          @if($store->phone ?? $store->mobile ?? null) Tel: {{ $store->phone ?? $store->mobile }}<br> @endif
          @if($store->email ?? null) Email: {{ $store->email }}<br> @endif
          @if($store->website ?? null) {{ $store->website }} @endif
        </p>
      @else
        <h2>Your Company</h2>
      @endif
    </div>
    <div class="inv-title">
      <h1>INVOICE</h1>
      <div class="inv-no">{{ $invoice->invoice_no }}</div>
      <span class="inv-status status-{{ $invoice->status }}">{{ $invoice->status }}</span>
    </div>
  </div>

  <div class="meta-grid">
    <div class="meta-box">
      <h4>Bill To</h4>
      <p>
        <strong>{{ $invoice->customer_name }}</strong><br>
        @if($invoice->mobile) {{ $invoice->mobile }}<br> @endif
        @if($invoice->address) {{ $invoice->address }} @endif
      </p>
    </div>
    <div class="meta-box">
      <h4>Invoice Info</h4>
      <p>
        <span class="big">{{ $invoice->invoice_no }}</span><br>
        Date: {{ $invoice->invoice_date ? \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') : '—' }}<br>
        Due:  {{ $invoice->due_date    ? \Carbon\Carbon::parse($invoice->due_date)->format('d M Y')    : '—' }}<br>
        @if($invoice->project) Project: {{ $invoice->project->project_no }} @endif
      </p>
    </div>
  </div>

  <table class="items">
    <thead>
      <tr>
        <th style="width:50%">Description</th>
        <th style="width:10%">Qty</th>
        <th style="width:20%">Unit Price</th>
        <th style="width:20%">Amount</th>
      </tr>
    </thead>
    <tbody>
      @php $items = $invoice->equipment_list ?? [] @endphp
      @forelse($items as $item)
      <tr>
        <td>{{ $item['name'] ?? '—' }}</td>
        <td>{{ $item['qty'] ?? 1 }}</td>
        <td>Rs. {{ number_format($item['unit_price'] ?? 0, 2) }}</td>
        <td>Rs. {{ number_format($item['total'] ?? (($item['qty']??1)*($item['unit_price']??0)), 2) }}</td>
      </tr>
      @empty
      <tr><td colspan="4" style="text-align:center;color:#aaa;padding:16px;">No items.</td></tr>
      @endforelse
    </tbody>
  </table>

  <div class="totals">
    @php
      $grand   = $invoice->grand_total ?? 0;
      $paid    = $invoice->paid_amount ?? 0;
      $balance = max(0, $grand - $paid);
    @endphp
    @if(($invoice->labour_cost ?? 0) > 0)
    <div class="totals-row"><span>Labour</span><span>Rs. {{ number_format($invoice->labour_cost, 2) }}</span></div>
    @endif
    @if(($invoice->installation_cost ?? 0) > 0)
    <div class="totals-row"><span>Installation</span><span>Rs. {{ number_format($invoice->installation_cost, 2) }}</span></div>
    @endif
    @if(($invoice->transport_cost ?? 0) > 0)
    <div class="totals-row"><span>Transport</span><span>Rs. {{ number_format($invoice->transport_cost, 2) }}</span></div>
    @endif
    @if(($invoice->discount ?? 0) > 0)
    <div class="totals-row" style="color:#ea5455;"><span>Discount</span><span>- Rs. {{ number_format($invoice->discount, 2) }}</span></div>
    @endif
    @if(($invoice->tax ?? 0) > 0)
    <div class="totals-row"><span>Tax</span><span>Rs. {{ number_format($invoice->tax, 2) }}</span></div>
    @endif
    <div class="totals-row grand"><span>Grand Total</span><span>Rs. {{ number_format($grand, 2) }}</span></div>
    @if($paid > 0)
    <div class="totals-row paid"><span>Paid</span><span>Rs. {{ number_format($paid, 2) }}</span></div>
    <div class="totals-row balance"><span>Balance Due</span><span>Rs. {{ number_format($balance, 2) }}</span></div>
    @endif
  </div>

  @if($invoice->notes)
  <div class="notes-box" style="margin-top:20px;">
    <h4>Notes</h4>
    <p>{{ $invoice->notes }}</p>
  </div>
  @endif

  <div class="footer">
    <span>Generated: {{ now()->format('d M Y, h:i A') }}</span>
    <span>{{ $store->name ?? $store->shop_name ?? '' }} — Thank you for your business!</span>
  </div>

  <script>window.onload = function(){ window.print(); }</script>
</body>
</html>
