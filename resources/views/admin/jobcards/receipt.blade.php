<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Receipt – {{ $orderNo }}</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      background: #f4f4f4;
      display: flex;
      justify-content: center;
      align-items: flex-start;
      min-height: 100vh;
      padding: 30px 16px;
    }

    .receipt-wrap {
      background: #fff;
      width: 100%;
      max-width: 420px;
      border-radius: 16px;
      box-shadow: 0 4px 24px rgba(0,0,0,0.12);
      overflow: hidden;
    }

    /* Header */
    .receipt-header {
      background: linear-gradient(135deg, #696cff, #8c57ff);
      color: #fff;
      padding: 28px 24px 20px;
      text-align: center;
    }
    .receipt-header .brand {
      font-size: 1.5rem;
      font-weight: 800;
      letter-spacing: 2px;
      text-transform: uppercase;
    }
    .receipt-header .brand-sub {
      font-size: .72rem;
      opacity: .75;
      letter-spacing: .15em;
      margin-top: 2px;
      text-transform: uppercase;
    }
    .receipt-header .receipt-title {
      margin-top: 14px;
      font-size: .7rem;
      letter-spacing: .2em;
      text-transform: uppercase;
      opacity: .7;
    }
    .receipt-header .order-no {
      font-size: 1.6rem;
      font-weight: 800;
      letter-spacing: 2px;
      margin-top: 4px;
    }
    .receipt-header .status-pill {
      display: inline-block;
      margin-top: 10px;
      padding: 4px 16px;
      border-radius: 20px;
      font-size: .73rem;
      font-weight: 700;
      letter-spacing: .05em;
    }
    .pill-full    { background: rgba(255,255,255,.25); border: 1px solid rgba(255,255,255,.5); }
    .pill-partial { background: rgba(255,200,50,.3);  border: 1px solid rgba(255,200,50,.6); }

    /* Body */
    .receipt-body { padding: 20px 24px; }

    .section { margin-bottom: 18px; }
    .section-title {
      font-size: .63rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .12em;
      color: #696cff;
      margin-bottom: 8px;
      padding-bottom: 5px;
      border-bottom: 1.5px solid #ebebff;
    }

    .info-row {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      padding: 4px 0;
      font-size: .82rem;
    }
    .info-label { color: #888; flex-shrink: 0; padding-right: 8px; }
    .info-val   { font-weight: 600; color: #222; text-align: right; word-break: break-word; max-width: 60%; }

    /* Items table */
    .items-table { width: 100%; border-collapse: collapse; margin-top: 6px; font-size: .8rem; }
    .items-table thead th {
      font-size: .62rem;
      text-transform: uppercase;
      letter-spacing: .08em;
      color: #888;
      font-weight: 700;
      padding: 5px 4px;
      border-bottom: 1.5px solid #eee;
    }
    .items-table thead th:first-child { text-align: left; }
    .items-table thead th:not(:first-child) { text-align: right; }
    .items-table tbody td {
      padding: 5px 4px;
      border-bottom: 1px dashed #f0f0f0;
      color: #333;
    }
    .items-table tbody td:first-child { font-weight: 500; }
    .items-table tbody td:not(:first-child) { text-align: right; }
    .items-table tbody tr:last-child td { border-bottom: none; }

    /* Totals */
    .totals { border-top: 1.5px dashed #e0e0e0; padding-top: 12px; margin-top: 4px; }
    .total-row {
      display: flex;
      justify-content: space-between;
      font-size: .83rem;
      padding: 3px 0;
      color: #555;
    }
    .total-row.total-grand {
      font-size: 1rem;
      font-weight: 800;
      color: #222;
      padding-top: 8px;
      margin-top: 4px;
      border-top: 2px solid #222;
    }
    .total-row.total-paid { color: #28a745; font-weight: 700; }
    .total-row.total-balance { color: #e53e3e; font-weight: 700; }

    /* Partial banner */
    .partial-banner {
      background: #fff8e1;
      border: 1px solid #ffe082;
      border-radius: 8px;
      padding: 10px 14px;
      margin: 14px 0 4px;
      font-size: .78rem;
      color: #7a5800;
      text-align: center;
    }
    .partial-banner strong { display: block; font-size: .85rem; margin-bottom: 2px; }

    /* Footer */
    .receipt-footer {
      background: #f8f8ff;
      border-top: 1px dashed #e0e0e0;
      padding: 16px 24px;
      text-align: center;
    }
    .receipt-footer .thank-you { font-size: .95rem; font-weight: 700; color: #696cff; margin-bottom: 4px; }
    .receipt-footer .footer-note { font-size: .7rem; color: #aaa; }
    .receipt-footer .print-date { font-size: .68rem; color: #bbb; margin-top: 8px; }

    /* Print button */
    .print-bar {
      padding: 16px 24px;
      display: flex;
      gap: 10px;
      justify-content: center;
      background: #fff;
      border-top: 1px solid #f0f0f0;
    }
    .btn-print {
      background: linear-gradient(135deg, #696cff, #8c57ff);
      color: #fff;
      border: none;
      padding: 10px 28px;
      border-radius: 10px;
      font-size: .9rem;
      font-weight: 700;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .btn-back {
      background: #f0f0f0;
      color: #555;
      border: none;
      padding: 10px 20px;
      border-radius: 10px;
      font-size: .88rem;
      font-weight: 600;
      cursor: pointer;
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 6px;
    }

    /* Print media */
    @media print {
      * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
      body { background: #fff; padding: 0; }
      .receipt-wrap { box-shadow: none; border-radius: 0; max-width: 100%; }
      .print-bar { display: none !important; }

      /* Darken all text for thermal printing */
      body, .info-val, .info-label, .total-row, .items-table td, .items-table th {
        color: #000 !important;
        -webkit-text-stroke: 0.4px #000;
        font-weight: 600 !important;
      }
      .info-label { color: #333 !important; }
      .section-title { color: #000 !important; border-bottom-color: #000 !important; }
      .totals { border-top-color: #000 !important; }
      .total-row.total-grand { border-top-color: #000 !important; color: #000 !important; }
      .total-row.total-paid { color: #000 !important; }
      .total-row.total-balance { color: #000 !important; }
      .items-table tbody td { border-bottom-color: #999 !important; }
      .items-table thead th { border-bottom-color: #000 !important; color: #000 !important; }
      .receipt-footer .thank-you { color: #000 !important; }
      .receipt-footer .footer-note,
      .receipt-footer .print-date { color: #333 !important; }
      .receipt-header { background: #000 !important; }
      .receipt-header * { color: #fff !important; }
      .partial-banner { border-color: #000 !important; color: #000 !important; background: #f5f5f5 !important; }

      /* Thicker borders for thermal */
      .receipt-footer { border-top: 2px solid #000 !important; }
      .totals .total-row.total-grand { border-top: 2.5px solid #000 !important; }
    }

    @media (max-width: 480px) {
      .receipt-wrap { border-radius: 0; }
      body { padding: 0; }
    }
  </style>
</head>
<body>

<div class="receipt-wrap">

  {{-- ── Header ── --}}
  <div class="receipt-header">
    <div class="brand">{{ config('app.name', 'TrackUp') }}</div>
    <div class="brand-sub">Repair & Service</div>
    <div class="receipt-title">Payment Receipt</div>
    <div class="order-no"># {{ $orderNo }}</div>
    @if($paymentType === 'full')
      <span class="status-pill pill-full">✓ Fully Paid &amp; Delivered</span>
    @else
      <span class="status-pill pill-partial">⚠ Partial Payment</span>
    @endif
  </div>

  <div class="receipt-body">

    {{-- ── Customer & Job Info ── --}}
    <div class="section">
      <div class="section-title">Customer &amp; Job</div>
      @if($customer)
        <div class="info-row"><span class="info-label">Customer</span><span class="info-val">{{ $customer }}</span></div>
      @endif
      @if($phone)
        <div class="info-row"><span class="info-label">Phone</span><span class="info-val">{{ $phone }}</span></div>
      @endif
      @if($address)
        <div class="info-row"><span class="info-label">Address</span><span class="info-val">{{ $address }}</span></div>
      @endif
      @if($invoiceNo)
        <div class="info-row"><span class="info-label">Invoice No</span><span class="info-val">{{ $invoiceNo }}</span></div>
      @endif
      @if($receivedDate)
        <div class="info-row"><span class="info-label">Received</span><span class="info-val">{{ $receivedDate instanceof \Carbon\Carbon ? $receivedDate->format('d M Y') : $receivedDate }}</span></div>
      @endif
      @if($deliveredAt)
        <div class="info-row"><span class="info-label">Delivered</span><span class="info-val">{{ $deliveredAt instanceof \Carbon\Carbon ? $deliveredAt->format('d M Y, h:i A') : $deliveredAt }}</span></div>
      @endif
    </div>

    {{-- ── Device ── --}}
    @if($device || $serial || $fault)
    <div class="section">
      <div class="section-title">Device</div>
      @if($device)
        <div class="info-row"><span class="info-label">Device</span><span class="info-val">{{ $device }}</span></div>
      @endif
      @if($serial)
        <div class="info-row"><span class="info-label">Serial / IMEI</span><span class="info-val">{{ $serial }}</span></div>
      @endif
      @if($fault)
        <div class="info-row"><span class="info-label">Fault</span><span class="info-val">{{ $fault }}</span></div>
      @endif
    </div>
    @endif

    {{-- ── Invoice Items ── --}}
    <div class="section">
      <div class="section-title">Invoice</div>

      @if($serviceCharge > 0 || count($invoiceItems) > 0)
        <table class="items-table">
          <thead>
            <tr>
              <th>Description</th>
              <th>Qty</th>
              <th>Price</th>
              <th>Total</th>
            </tr>
          </thead>
          <tbody>
            @if($serviceCharge > 0)
            <tr>
              <td>Service Charge</td>
              <td>1</td>
              <td>Rs.{{ number_format($serviceCharge, 2) }}</td>
              <td>Rs.{{ number_format($serviceCharge, 2) }}</td>
            </tr>
            @endif
            @foreach($invoiceItems as $item)
            <tr>
              <td>{{ $item['description'] ?? '—' }}</td>
              <td>{{ $item['quantity'] ?? 1 }}</td>
              <td>Rs.{{ number_format($item['unit_price'] ?? 0, 2) }}</td>
              <td>Rs.{{ number_format($item['total'] ?? 0, 2) }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      @else
        <div style="font-size:.8rem; color:#aaa; text-align:center; padding:8px 0;">No line items</div>
      @endif

      <div class="totals">
        @php
          $subtotal = $serviceCharge + collect($invoiceItems)->sum('total');
        @endphp
        @if($discount > 0)
          <div class="total-row">
            <span>Subtotal</span>
            <span>Rs.{{ number_format($subtotal, 2) }}</span>
          </div>
          <div class="total-row" style="color:#e53e3e;">
            <span>Discount</span>
            <span>– Rs.{{ number_format($discount, 2) }}</span>
          </div>
        @endif
        <div class="total-row total-grand">
          <span>Grand Total</span>
          <span>Rs.{{ number_format($grandTotal, 2) }}</span>
        </div>
      </div>
    </div>

    {{-- ── Payment History ── --}}
    <div class="section">
      <div class="section-title">Payment Summary</div>
      @if(isset($advanceAmount) && $advanceAmount > 0)
      <div class="total-row" style="color:#555; font-size:.8rem;">
        <span>Advance Paid</span>
        <span>Rs.{{ number_format($advanceAmount, 2) }}</span>
      </div>
      @endif

      {{-- Per-payment breakdown with dates --}}
      @if(isset($paymentLogs) && $paymentLogs->count() > 0)
        @foreach($paymentLogs as $log)
        <div class="total-row" style="font-size:.8rem;color:#2d6a4f;">
          <span>{{ $log->note ?? 'Payment' }} <span style="color:#aaa;font-size:.75rem;">({{ $log->paid_at->format('d M Y, h:i A') }})</span></span>
          <span>Rs.{{ number_format($log->amount, 2) }}</span>
        </div>
        @endforeach
        <div class="total-row total-paid" style="border-top:1px dashed #ccc;padding-top:6px;margin-top:4px">
          <span>Total Paid</span>
          <span>Rs.{{ number_format($paidAmount, 2) }}</span>
        </div>
      @else
      <div class="total-row total-paid">
        <span>Amount Paid</span>
        <span>Rs.{{ number_format($paidAmount, 2) }}</span>
      </div>
      @endif

      @if($balance > 0)
        <div class="total-row total-balance">
          <span>Balance Due</span>
          <span>Rs.{{ number_format($balance, 2) }}</span>
        </div>
      @else
        <div class="total-row" style="color:#28a745; font-size:.8rem;">
          <span>Balance</span>
          <span>Fully Settled ✓</span>
        </div>
      @endif
    </div>

    {{-- ── Partial Warning ── --}}
    @if($paymentType === 'partial')
      <div class="partial-banner">
        <strong>⚠ Partial Payment Recorded</strong>
        Balance of Rs.{{ number_format($balance, 2) }} is still outstanding.<br>
        Device will be released upon full payment.
      </div>
    @endif

    @if($remark)
      <div class="section" style="margin-top:12px;">
        <div class="section-title">Remark</div>
        <div style="font-size:.8rem; color:#555; line-height:1.5;">{{ $remark }}</div>
      </div>
    @endif

  </div>

  {{-- ── Footer ── --}}
  <div class="receipt-footer">
    <div class="thank-you">Thank you for your trust!</div>
    <div class="footer-note">Please keep this receipt for your records.</div>
    <div class="print-date">Printed: {{ now()->format('d M Y, h:i A') }}</div>
  </div>

  {{-- ── Print / Back Buttons ── --}}
  <div class="print-bar">
    <a href="{{ url()->previous() }}" class="btn-back" id="backBtn">
      ← Back
    </a>
    <button class="btn-print" onclick="window.print()">
      🖨 Print Receipt
    </button>
  </div>

</div>

<script>
  // Fix "back" — if opened in new tab, close it; otherwise go back
  document.getElementById('backBtn').addEventListener('click', function(e) {
    if (window.history.length <= 1 || window.opener) {
      e.preventDefault();
      window.close();
    }
  });

  // Auto-print on load, then redirect based on origin
  window.addEventListener('load', function() {
    setTimeout(function() {
      window.print();
      window.onafterprint = function() {
        var params = new URLSearchParams(window.location.search);
        if (params.get('redirect') === 'invoices') {
          window.location.href = '{{ route("admin.invoices.index") }}';
        } else {
          window.location.href = '{{ route("admin.jobcards.index") }}';
        }
      };
    }, 600);
  });
</script>

</body>
</html>
