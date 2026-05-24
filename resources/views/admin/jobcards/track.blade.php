@extends('layouts.admin')
@section('title', 'Track Device')
@section('page-title', 'Track Device')
@section('breadcrumb')<li class="breadcrumb-item active">Track Device</li>@endsection

@push('styles')
<style>
/* ── Page wrapper ── */
.track-page { max-width: 860px; margin: 0 auto; }

/* ── Hero search card ── */
.track-hero {
  background: linear-gradient(135deg, #696cff 0%, #8c57ff 55%, #a855f7 100%);
  border-radius: 18px;
  padding: 36px 40px 32px;
  color: #fff;
  margin-bottom: 28px;
  position: relative;
  overflow: hidden;
}
.track-hero::before {
  content: '';
  position: absolute;
  top: -40px; right: -40px;
  width: 220px; height: 220px;
  border-radius: 50%;
  background: rgba(255,255,255,.07);
}
.track-hero::after {
  content: '';
  position: absolute;
  bottom: -60px; left: 30%;
  width: 160px; height: 160px;
  border-radius: 50%;
  background: rgba(255,255,255,.05);
}
.track-hero-icon {
  width: 52px; height: 52px;
  background: rgba(255,255,255,.18);
  border: 2px solid rgba(255,255,255,.3);
  border-radius: 14px;
  display: flex; align-items: center; justify-content: center;
  font-size: 1.6rem;
  margin-bottom: 14px;
}
.track-hero h4 {
  font-size: 1.4rem; font-weight: 800;
  margin-bottom: 4px; letter-spacing: -.2px;
}
.track-hero p {
  opacity: .8; font-size: .88rem; margin-bottom: 22px;
}

/* Search input */
.track-search-wrap {
  display: flex;
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 8px 30px rgba(0,0,0,.18);
  position: relative; z-index: 1;
}
.track-search-wrap input {
  flex: 1;
  border: 0;
  padding: 14px 18px;
  font-size: .95rem;
  outline: none;
  color: #333;
  background: transparent;
}
.track-search-wrap input::placeholder { color: #bbb; }
.track-search-wrap button {
  background: linear-gradient(135deg, #696cff, #8c57ff);
  border: 0;
  color: #fff;
  padding: 0 28px;
  font-weight: 700;
  font-size: .9rem;
  cursor: pointer;
  display: flex; align-items: center; gap: 7px;
  transition: opacity .15s;
  white-space: nowrap;
}
.track-search-wrap button:hover { opacity: .9; }

/* Scan chip */
.scan-chip {
  display: inline-flex; align-items: center; gap: 6px;
  margin-top: 14px;
  background: rgba(255,255,255,.15);
  border: 1px solid rgba(255,255,255,.28);
  border-radius: 20px;
  padding: 5px 14px;
  font-size: .78rem;
  cursor: pointer;
  color: #fff;
  transition: background .15s;
  position: relative; z-index: 1;
}
.scan-chip:hover { background: rgba(255,255,255,.25); }

/* Scanner container */
#scannerContainer {
  position: relative; z-index: 1;
  margin-top: 16px;
}
#barcode-scanner {
  width: 100%; max-width: 380px;
  border-radius: 10px; overflow: hidden;
  background: #000;
  box-shadow: 0 4px 20px rgba(0,0,0,.3);
}
#barcode-scanner video { width: 100%; display: block; }

/* ── Not found ── */
.track-not-found {
  background: #fff8f0;
  border: 1.5px solid #ffd0a0;
  border-radius: 14px;
  padding: 28px 32px;
  text-align: center;
  color: #8a5000;
}
.track-not-found .nf-icon {
  font-size: 2.8rem; margin-bottom: 10px;
  display: block; color: #ffab00;
}

/* ── Result card ── */
.track-result-card {
  border: 0;
  border-radius: 18px;
  box-shadow: 0 4px 28px rgba(108,92,231,.13);
  overflow: hidden;
}

/* Result header */
.trh {
  padding: 22px 28px;
  color: #fff;
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 12px;
}
.trh-left .label {
  font-size: .7rem; opacity: .75;
  text-transform: uppercase; letter-spacing: .1em;
  margin-bottom: 3px;
}
.trh-left .order-no {
  font-size: 1.6rem; font-weight: 800; letter-spacing: 1px;
}
.trh-left .cust-id {
  font-size: .78rem; opacity: .75; margin-top: 2px;
}
.trh-right { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }

/* Status timeline */
.status-flow {
  display: flex;
  align-items: center;
  gap: 0;
  padding: 20px 28px;
  background: #fafafe;
  border-bottom: 1px solid #efefff;
  overflow-x: auto;
}
.sf-step {
  display: flex; flex-direction: column; align-items: center;
  flex: 1; min-width: 100px; position: relative;
}
.sf-step:not(:last-child)::after {
  content: '';
  position: absolute;
  top: 18px; left: 55%;
  width: 90%; height: 3px;
  background: #e0e0f0;
  z-index: 0;
}
.sf-step.done:not(:last-child)::after,
.sf-step.current:not(:last-child)::after { background: currentColor; }
.sf-dot {
  width: 36px; height: 36px;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: 1rem;
  background: #e8e8f8;
  color: #bbb;
  border: 3px solid #e0e0f0;
  position: relative; z-index: 1;
  transition: all .2s;
}
.sf-step.done .sf-dot   { background: #71dd37; border-color: #71dd37; color: #fff; }
.sf-step.current .sf-dot{ background: #696cff; border-color: #696cff; color: #fff; box-shadow: 0 0 0 5px rgba(108,92,231,.15); }
.sf-label {
  margin-top: 7px;
  font-size: .72rem; font-weight: 700;
  color: #bbb;
  text-align: center; white-space: nowrap;
}
.sf-step.done .sf-label    { color: #71dd37; }
.sf-step.current .sf-label { color: #696cff; }

/* Info grid */
.track-body { padding: 24px 28px; }
.t-section-head {
  font-size: .75rem; font-weight: 800;
  text-transform: uppercase; letter-spacing: .1em;
  color: #696cff;
  border-bottom: 2px solid #f0f0ff;
  padding-bottom: 8px;
  margin-bottom: 14px;
  display: flex; align-items: center; gap: 7px;
}
.t-row {
  display: flex; padding: 7px 0;
  border-bottom: 1px solid #f5f5ff;
}
.t-row:last-child { border-bottom: none; }
.t-lbl { width: 40%; font-size: .76rem; font-weight: 700; color: #aaa; text-transform: uppercase; letter-spacing: .04em; }
.t-val { flex: 1; font-size: .875rem; font-weight: 500; color: #333; word-break: break-word; }

/* Action bar */
.track-actions {
  padding: 16px 28px;
  background: #f8f8ff;
  border-top: 1px solid #efefff;
  display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
}
.btn-track-edit {
  background: linear-gradient(135deg,#696cff,#8c57ff);
  color: #fff; border: 0;
  padding: 9px 22px; border-radius: 10px;
  font-weight: 700; font-size: .88rem;
  text-decoration: none;
  display: inline-flex; align-items: center; gap: 6px;
  transition: opacity .15s;
}
.btn-track-edit:hover { opacity: .88; color: #fff; }

/* Priority badge */
.priority-chip {
  border-radius: 20px;
  padding: 4px 12px;
  font-size: .75rem;
  font-weight: 700;
  border: 1.5px solid transparent;
}
.pc-Low    { background:#edfbd8; color:#3a7c11; border-color:#71dd37; }
.pc-Normal { background:#d9f8fe; color:#0074a0; border-color:#03c3ec; }
.pc-High   { background:#fff4d4; color:#8a5500; border-color:#ffab00; }
.pc-Urgent { background:#ffe0dc; color:#a00000; border-color:#ff3e1d; }
</style>
@endpush

@section('content')
@php
  $statusGradients = [
    'Pending'       => 'linear-gradient(135deg,#b07000,#ffab00)',
    'In Progress'   => 'linear-gradient(135deg,#005f82,#03c3ec)',
    'Completed'     => 'linear-gradient(135deg,#2d6a09,#71dd37)',
    'Not Completed' => 'linear-gradient(135deg,#8a0000,#ff3e1d)',
  ];
  $statusBadge = [
    'Pending'       => 'bg-label-warning',
    'In Progress'   => 'bg-label-info',
    'Completed'     => 'bg-label-success',
    'Not Completed' => 'bg-label-danger',
  ];
  $steps = ['Pending','In Progress','Completed'];
  $stepIcons = ['bx-time-five','bx-wrench','bx-check-circle'];
  $currentStatus = $job ? ($job->status ?: 'Pending') : null;
@endphp

<div class="track-page">

  {{-- ── Hero Search ── --}}
  <div class="track-hero">
    <div class="track-hero-icon"><i class='bx bx-search-alt'></i></div>
    <h4>Track Job Order</h4>
    <p>Enter an order number or device serial number to instantly pull up repair status and details.</p>

    <form method="GET" action="{{ route('admin.jobcards.track') }}">
      <div class="track-search-wrap">
        <input type="text" name="q"
          placeholder="Order No (e.g. 2605011) or Serial / IMEI No"
          value="{{ $search }}" autofocus autocomplete="off" />
        <button type="submit"><i class='bx bx-search'></i> Search</button>
      </div>
    </form>

    <div class="scan-chip" id="toggleScanner">
      <i class='bx bx-scan'></i> Scan Barcode
    </div>

    <div id="scannerContainer" style="display:none">
      <div id="barcode-scanner" class="mt-3"></div>
      <p style="opacity:.75;font-size:.78rem;margin-top:8px;margin-bottom:6px">
        <i class='bx bx-info-circle me-1'></i>Point camera at barcode — auto-detects
      </p>
      <button class="scan-chip" id="stopScanner" style="background:rgba(255,80,60,.3);border-color:rgba(255,100,80,.4)">
        <i class='bx bx-stop'></i> Stop Scanner
      </button>
    </div>
  </div>

  {{-- ── Not Found ── --}}
  @if($search && !$job)
  <div class="track-not-found">
    <i class='bx bx-search-alt nf-icon'></i>
    <div style="font-size:1.05rem;font-weight:700;margin-bottom:6px">No results found</div>
    <div style="font-size:.87rem">No job order matches <strong>"{{ $search }}"</strong>.<br>Check the order number or serial / IMEI and try again.</div>
  </div>
  @endif

  {{-- ── Result ── --}}
  @if($job)
  @php
    $grad     = $statusGradients[$job->status] ?? $statusGradients['Pending'];
    $badgeCls = $statusBadge[$job->status]     ?? 'bg-secondary';
    $priority = $job->priority ?? 'Normal';
    $empName  = $job->employee->employee_name ?? 'Unassigned';
    $amount   = 'Rs. ' . number_format($job->rupees, 2);
    $stepIdx  = array_search($currentStatus, $steps);
    if ($currentStatus === 'Not Completed') $stepIdx = -1; // show broken state
  @endphp

  <div class="track-result-card card">

    {{-- Header --}}
    <div class="trh" style="background: {{ $grad }}">
      <div class="trh-left">
        <div class="label">Job Order</div>
        <div class="order-no"># {{ $job->order_no }}</div>
        <div class="cust-id">{{ $job->customer_id }}</div>
      </div>
      <div class="trh-right">
        <div style="text-align:right">
          <span class="badge {{ $badgeCls }}" style="font-size:.82rem;padding:6px 14px">{{ $job->status ?: 'Pending' }}</span>
          <div style="font-size:.72rem;opacity:.75;margin-top:4px">Updated: {{ $job->updated_at->format('d M Y, h:i A') }}</div>
        </div>
        <span class="priority-chip pc-{{ $priority }}">{{ $priority }} Priority</span>
      </div>
    </div>

    {{-- Status Flow ── --}}
    <div class="status-flow">
      @if($currentStatus === 'Not Completed')
        {{-- Special broken state --}}
        @foreach($steps as $i => $step)
        <div class="sf-step {{ $i === 0 ? 'done' : '' }}">
          <div class="sf-dot">
            @if($i === 0)<i class='bx bx-check'></i>
            @elseif($i === 1)<i class='bx bx-x' style="color:#ff3e1d"></i>
            @else<i class='bx {{ $stepIcons[$i] }}'></i>
            @endif
          </div>
          <div class="sf-label" style="{{ $i === 1 ? 'color:#ff3e1d' : '' }}">{{ $i === 1 ? 'Not Completed' : $step }}</div>
          @if($i === 1)<div style="font-size:.65rem;color:#ff3e1d;margin-top:2px;text-align:center">{{ $job->updated_at->format('d M Y') }}</div>@endif
        </div>
        @endforeach
      @else
        @foreach($steps as $i => $step)
        @php
          $isDone    = $stepIdx !== false && $i < $stepIdx;
          $isCurrent = $stepIdx !== false && $i === $stepIdx;
        @endphp
        <div class="sf-step {{ $isDone ? 'done' : ($isCurrent ? 'current' : '') }}">
          <div class="sf-dot">
            @if($isDone)<i class='bx bx-check'></i>
            @else<i class='bx {{ $stepIcons[$i] }}'></i>
            @endif
          </div>
          <div class="sf-label">{{ $step }}</div>
          @if($isCurrent)<div style="font-size:.65rem;color:#696cff;margin-top:2px;text-align:center">{{ $job->updated_at->format('d M Y') }}</div>@endif
        </div>
        @endforeach
      @endif
    </div>

    {{-- Body ── --}}
    <div class="track-body">
      <div class="row g-4">

        {{-- Customer --}}
        <div class="col-md-6">
          <div class="t-section-head"><i class='bx bx-user'></i> Customer</div>
          <div class="t-row"><div class="t-lbl">Name</div><div class="t-val fw-semibold">{{ $job->customer_name }}</div></div>
          <div class="t-row"><div class="t-lbl">Phone</div><div class="t-val">{{ $job->phone_no }}</div></div>
          <div class="t-row"><div class="t-lbl">Address</div><div class="t-val">{{ $job->customer_address ?: '—' }}</div></div>
          <div class="t-row"><div class="t-lbl">Email</div><div class="t-val">{{ $job->customer_email ?: '—' }}</div></div>
          <div class="t-row"><div class="t-lbl">Received</div><div class="t-val">{{ $job->date ? $job->date->format('d M Y') : '—' }}</div></div>
          @if($job->estimated_delivery)
          <div class="t-row"><div class="t-lbl">Est. Delivery</div><div class="t-val">{{ \Carbon\Carbon::parse($job->estimated_delivery)->format('d M Y') }}</div></div>
          @endif
        </div>

        {{-- Device & Repair --}}
        <div class="col-md-6">
          <div class="t-section-head"><i class='bx bx-chip'></i> Device & Repair</div>
          <div class="t-row"><div class="t-lbl">Device</div><div class="t-val fw-semibold">{{ $job->device_name }}{{ $job->device_brand ? ' · '.$job->device_brand : '' }}</div></div>
          <div class="t-row"><div class="t-lbl">Serial / IMEI</div><div class="t-val">{{ $job->serial_no ?: '—' }}</div></div>
          <div class="t-row"><div class="t-lbl">Fault</div><div class="t-val">{{ $job->device_fault ?: '—' }}</div></div>
          <div class="t-row"><div class="t-lbl">Issue</div><div class="t-val" style="font-size:.83rem;color:#555">{{ $job->issue ?: '—' }}</div></div>
          <div class="t-row"><div class="t-lbl">Amount</div><div class="t-val fw-bold" style="color:#696cff">{{ $amount }}</div></div>
          <div class="t-row"><div class="t-lbl">Technician</div><div class="t-val">{{ $empName }}</div></div>
          @if($job->accessories)
          <div class="t-row"><div class="t-lbl">Accessories</div><div class="t-val" style="font-size:.82rem">{{ $job->accessories }}</div></div>
          @endif
          @if($job->remark)
          <div class="t-row">
            <div class="t-lbl">Remark</div>
            <div class="t-val">
              <span style="background:#f0f0ff;color:#696cff;padding:3px 10px;border-radius:8px;font-size:.8rem">{{ $job->remark }}</span>
            </div>
          </div>
          @endif
        </div>

      </div>
    </div>

    {{-- Actions ── --}}
    <div class="track-actions">
      <a href="{{ route('admin.jobcards.edit', $job) }}" class="btn-track-edit">
        <i class='bx bx-edit'></i> Edit Order
      </a>
      @if($job->status === 'Completed')
      <button type="button" class="btn btn-success btn-sm fw-semibold" style="border-radius:9px;padding:8px 18px;"
        data-bs-toggle="modal" data-bs-target="#trackPaymentModal">
        <i class='bx bx-dollar-circle me-1'></i> Take Payment
      </button>
      @endif
      <a href="{{ route('admin.jobcards.index') }}?search={{ $job->order_no }}"
        class="btn btn-outline-secondary btn-sm" style="border-radius:9px;font-weight:600;padding:8px 18px">
        <i class='bx bx-list-ul me-1'></i> View in List
      </a>
      <form action="{{ route('admin.jobcards.track') }}" method="GET" class="ms-auto">
        <button type="submit" class="btn btn-outline-primary btn-sm" style="border-radius:9px;font-weight:600;padding:8px 18px">
          <i class='bx bx-search me-1'></i> New Search
        </button>
      </form>
    </div>

    @if($job->status === 'Completed')
    {{-- Track Payment Modal --}}
    <div class="modal fade" id="trackPaymentModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px;">
          <div class="modal-header" style="background:linear-gradient(135deg,#28a745,#20c997);color:#fff;border-radius:14px 14px 0 0;">
            <h5 class="modal-title fw-bold"><i class='bx bx-dollar-circle me-2'></i>Complete Payment</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <form method="POST" action="{{ route('admin.jobcards.completePayment', $job) }}">
            @csrf
            <div class="modal-body p-4">
              <div class="mb-3 p-3 rounded" style="background:#f8f9fa;">
                <div class="d-flex justify-content-between mb-1">
                  <span class="text-muted">Order No</span>
                  <strong>{{ $job->order_no }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                  <span class="text-muted">Customer</span>
                  <strong>{{ $job->customer_name }}</strong>
                </div>
              </div>
              <div class="mb-2 d-flex justify-content-between">
                <span class="text-muted">Grand Total</span>
                <span class="fw-semibold">Rs. {{ number_format($job->grand_total, 2) }}</span>
              </div>
              <div class="mb-2 d-flex justify-content-between">
                <span class="text-muted">Already Paid</span>
                <span class="text-success fw-semibold">Rs. {{ number_format($job->paid_amount, 2) }}</span>
              </div>
              <div class="mb-3 d-flex justify-content-between">
                <span class="text-muted">Balance Due</span>
                <span class="text-danger fw-bold">Rs. {{ number_format($job->balance, 2) }}</span>
              </div>
              <hr>
              <div class="mb-2">
                <label class="form-label fw-semibold">Amount Paying Now (Rs.)</label>
                <input type="number" name="amount_paid" class="form-control" step="0.01" min="0"
                  value="{{ number_format($job->balance, 2, '.', '') }}" required>
                <div class="form-text text-muted">Must equal or exceed balance due to complete delivery.</div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-success fw-semibold px-4">
                <i class='bx bx-check-circle me-1'></i>Confirm & Deliver
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
    @endif

  </div>{{-- /track-result-card --}}
  @endif

</div>{{-- /track-page --}}
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>
<script>
let scanning = false;

document.getElementById('toggleScanner').addEventListener('click', function () {
  document.getElementById('scannerContainer').style.display = 'block';
  if (!scanning) startScanner();
});
document.getElementById('stopScanner').addEventListener('click', stopScanner);

function startScanner() {
  scanning = true;
  Quagga.init({
    inputStream: {
      name: 'Live', type: 'LiveStream',
      target: document.querySelector('#barcode-scanner'),
      constraints: { facingMode: 'environment' }
    },
    decoder: { readers: ['code_128_reader','ean_reader'] }
  }, function(err) {
    if (err) { alert('Camera not available'); return; }
    Quagga.start();
  });
  Quagga.onDetected(function(result) {
    const code = result.codeResult.code;
    stopScanner();
    window.location.href = '{{ route("admin.jobcards.track") }}?q=' + encodeURIComponent(code);
  });
}

function stopScanner() {
  if (scanning) { try { Quagga.stop(); } catch(e){} scanning = false; }
  document.getElementById('scannerContainer').style.display = 'none';
}
</script>
@endpush
