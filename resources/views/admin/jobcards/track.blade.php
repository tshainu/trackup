@extends('layouts.admin')
@section('title', 'Track Device')
@section('page-title', 'Track Device')
@section('breadcrumb')<li class="breadcrumb-item active">Track Device</li>@endsection

@push('styles')
<style>
#barcode-scanner { width: 100%; max-width: 480px; border-radius: 8px; overflow: hidden; background: #000; }
#barcode-scanner video { width: 100%; }
.track-result { border-left: 5px solid #7c4dff; border-radius: 8px; }
.status-timeline { list-style: none; padding: 0; }
.status-timeline li { display: flex; align-items: center; gap: .75rem; padding: .5rem 0; border-bottom: 1px solid #f0f0f0; }
.status-timeline li:last-child { border-bottom: none; }
.status-dot { width: 12px; height: 12px; border-radius: 50%; flex-shrink: 0; }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-8">
    <!-- Search Bar -->
    <div class="card mb-4">
      <div class="card-header py-3"><div class="section-title mb-0"><i class='bx bx-search-alt me-1'></i> Search by Order No or Serial No</div></div>
      <div class="card-body">
        <form method="GET" action="{{ route('admin.jobcards.track') }}">
          <div class="input-group input-group-lg">
            <input type="text" name="q" class="form-control" placeholder="Enter Order No (e.g. ORD-2024-001) or Serial No..." value="{{ $search }}" autofocus />
            <button type="submit" class="btn" style="background:#7c4dff;color:#fff"><i class='bx bx-search'></i> Track</button>
          </div>
        </form>

        <!-- Barcode Scanner -->
        <div class="mt-3">
          <button class="btn btn-outline-secondary btn-sm" id="toggleScanner">
            <i class='bx bx-scan'></i> Scan Barcode
          </button>
          <div id="scannerContainer" style="display:none; margin-top: 1rem;">
            <div id="barcode-scanner"></div>
            <p class="text-muted small mt-2"><i class='bx bx-info-circle'></i> Point camera at barcode — auto-detects order number</p>
            <button class="btn btn-sm btn-danger" id="stopScanner"><i class='bx bx-stop'></i> Stop Scanner</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Results -->
    @if($search && !$job)
      <div class="alert alert-warning">
        <i class='bx bx-error me-1'></i> No job card found for "<strong>{{ $search }}</strong>"
      </div>
    @endif

    @if($job)
    <div class="card track-result">
      <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <div>
          <h5 class="mb-0">{{ $job->order_no }}</h5>
          <small class="text-muted">{{ $job->customer_id }}</small>
        </div>
        @php $sc = ['Pending'=>'badge-pending','In Progress'=>'badge-progress','Completed'=>'badge-completed','Not Completed'=>'badge-not-completed']; @endphp
        <span class="badge fs-6 {{ $sc[$job->status] ?? 'bg-secondary' }}">{{ $job->status ?: 'Pending' }}</span>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <h6 class="text-muted mb-2">Customer</h6>
            <p class="mb-1"><strong>{{ $job->customer_name }}</strong></p>
            <p class="mb-1 text-muted"><i class='bx bx-phone'></i> {{ $job->phone_no }}</p>
            <p class="mb-1 text-muted"><i class='bx bx-map'></i> {{ $job->customer_address ?: '—' }}</p>
          </div>
          <div class="col-md-6">
            <h6 class="text-muted mb-2">Device</h6>
            <p class="mb-1"><strong>{{ $job->device_name }}</strong> — {{ $job->device_brand }}</p>
            <p class="mb-1 text-muted"><i class='bx bx-barcode'></i> S/N: {{ $job->serial_no ?: '—' }}</p>
            <p class="mb-1 text-muted"><i class='bx bx-error-circle'></i> {{ $job->device_fault ?: '—' }}</p>
          </div>
          <div class="col-md-6">
            <h6 class="text-muted mb-2">Repair Info</h6>
            <p class="mb-1">Date In: <strong>{{ $job->date ? $job->date->format('d M Y') : '—' }}</strong></p>
            <p class="mb-1">Amount: <strong>Rs. {{ number_format($job->rupees, 2) }}</strong></p>
            <p class="mb-1">Technician: <strong>{{ $job->employee->employee_name ?? 'Unassigned' }}</strong></p>
          </div>
          <div class="col-md-6">
            <h6 class="text-muted mb-2">Issue & Remark</h6>
            <p class="mb-1 small">{{ $job->issue ?: 'No description' }}</p>
            @if($job->remark)
              <div class="alert alert-info py-1 small mb-0">
                <i class='bx bx-comment me-1'></i> {{ $job->remark }}
              </div>
            @endif
          </div>
        </div>
        <div class="mt-3 d-flex gap-2">
          <a href="{{ route('admin.jobcards.edit', $job) }}" class="btn btn-sm" style="background:#7c4dff;color:#fff"><i class='bx bx-edit'></i> Edit</a>
          <a href="{{ route('admin.jobcards.show', $job) }}" class="btn btn-sm btn-outline-primary"><i class='bx bx-eye'></i> Full Details</a>
        </div>
      </div>
    </div>
    @endif
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>
<script>
let scanning = false;
$('#toggleScanner').on('click', function () {
  $('#scannerContainer').show();
  if (!scanning) startScanner();
});
$('#stopScanner').on('click', stopScanner);

function startScanner() {
  scanning = true;
  Quagga.init({
    inputStream: { name: 'Live', type: 'LiveStream', target: document.querySelector('#barcode-scanner'), constraints: { facingMode: 'environment' } },
    decoder: { readers: ['code_128_reader', 'ean_reader', 'qr_reader'] }
  }, err => { if (err) { alert('Camera not available'); return; } Quagga.start(); });

  Quagga.onDetected(result => {
    const code = result.codeResult.code;
    stopScanner();
    window.location.href = '{{ route("admin.jobcards.track") }}?q=' + encodeURIComponent(code);
  });
}

function stopScanner() {
  if (scanning) { Quagga.stop(); scanning = false; }
  $('#scannerContainer').hide();
}
</script>
@endpush
