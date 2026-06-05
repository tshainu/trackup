@extends('layouts.admin')
@section('title', 'Lead – ' . $lead->lead_no)

@push('styles')
<style>
  .hero-bar { background:linear-gradient(135deg,#696cff,#8c57ff); border-radius:16px; padding:1.25rem 1.75rem; color:#fff; margin-bottom:1rem; display:flex; align-items:center; gap:1rem; flex-wrap:wrap; }
  .hero-bar .back-btn { width:38px; height:38px; border-radius:10px; background:rgba(255,255,255,.2); border:0; color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.2rem; flex-shrink:0; text-decoration:none; transition:background .15s; }
  .hero-bar .back-btn:hover { background:rgba(255,255,255,.32); color:#fff; }
  .hero-bar h4 { margin:0; font-size:1.2rem; font-weight:700; }
  .section-card { border-radius:14px; border:0; box-shadow:0 2px 12px rgba(105,108,255,.08); margin-bottom:1.25rem; }
  .section-card .card-header { background:#f8f9fa; border-radius:14px 14px 0 0; padding:.85rem 1.25rem; font-weight:700; font-size:.875rem; display:flex; align-items:center; gap:.5rem; border-bottom:1px solid rgba(0,0,0,.06); }
  .section-icon { width:28px; height:28px; border-radius:8px; background:#eef0ff; color:#696cff; display:flex; align-items:center; justify-content:center; font-size:.9rem; }
  .info-label { font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#a1acb8; margin-bottom:.25rem; }
  .info-value { font-size:.92rem; font-weight:500; color:#32325d; }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  {{-- Hero --}}
  <div class="hero-bar">
    <a href="{{ route('admin.cctv.leads.index') }}" class="back-btn"><i class="bx bx-arrow-back"></i></a>
    <div class="flex-grow-1">
      <h4>{{ $lead->lead_no }} — {{ $lead->customer_name }}</h4>
      <div class="d-flex align-items-center gap-2 mt-1" style="opacity:.9;">
        @php
          $statusColor = match($lead->status) {
            'New Lead'           => 'secondary',
            'Survey Scheduled'   => 'info',
            'Survey Completed'   => 'primary',
            'Estimation Sent'     => 'warning',
            'Approved'           => 'success',
            'Installation'       => 'warning',
            'Completed'          => 'success',
            'Cancelled'          => 'danger',
            'Rejected'           => 'danger',
            'Postponed'          => 'secondary',
            'Rescheduled'        => 'info',
            'Lost'               => 'danger',
            default              => 'secondary',
          };
        @endphp
        <span class="badge bg-label-{{ $statusColor }}">{{ $lead->status }}</span>
        <span style="opacity:.7;font-size:.82rem;">{{ $lead->source ?? '' }}</span>
      </div>
    </div>
    <a href="{{ route('admin.cctv.leads.edit', $lead) }}" class="btn btn-light btn-sm"><i class="bx bx-edit me-1"></i> Edit</a>
  </div>

  {{-- Pipeline Banner --}}
  @include('admin.cctv._pipeline_banner', [
    'lead'      => $lead,
    'survey'    => $survey    ?? null,
    'quotation' => $quotation ?? null,
    'project'   => $project   ?? null,
    'invoice'   => $invoice   ?? null,
    'currentStep' => 'lead',
  ])

  @if(session('success'))
  <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
    <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif

  <div class="row g-3">
    <div class="col-lg-8">
      {{-- Customer Info --}}
      <div class="card section-card">
        <div class="card-header"><div class="section-icon"><i class="bx bx-user"></i></div> Customer Information</div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-sm-6"><div class="info-label">Name</div><div class="info-value">{{ $lead->customer_name }}</div></div>
            <div class="col-sm-6"><div class="info-label">Mobile</div><div class="info-value font-monospace">{{ $lead->mobile }}</div></div>
            <div class="col-sm-6"><div class="info-label">Email</div><div class="info-value">{{ $lead->email ?? '—' }}</div></div>
            <div class="col-sm-6"><div class="info-label">Location</div><div class="info-value">{{ $lead->location ?? '—' }}</div></div>
            <div class="col-12"><div class="info-label">Address</div><div class="info-value">{{ $lead->address ?? '—' }}</div></div>
          </div>
        </div>
      </div>

      {{-- Lead Details --}}
      <div class="card section-card">
        <div class="card-header"><div class="section-icon"><i class="bx bx-detail"></i></div> Lead Details</div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-sm-4"><div class="info-label">Requirement</div><div class="info-value">{{ $lead->requirement_type ?? '—' }}</div></div>
            <div class="col-sm-4"><div class="info-label">Budget</div><div class="info-value">{{ $lead->budget ? 'Rs. '.number_format($lead->budget,2) : '—' }}</div></div>
            <div class="col-sm-4"><div class="info-label">Follow Up</div>
              <div class="info-value">
                @if(isset($lead->follow_up_date) && $lead->follow_up_date)
                  {{ is_string($lead->follow_up_date) ? \Carbon\Carbon::parse($lead->follow_up_date)->format('d M Y') : $lead->follow_up_date->format('d M Y') }}
                @else —
                @endif
              </div>
            </div>
            <div class="col-12"><div class="info-label">Notes</div><div class="info-value" style="white-space:pre-line">{{ $lead->notes ?? '—' }}</div></div>
          </div>
        </div>
      </div>

      {{-- Linked Surveys --}}
      @if(($lead->surveys ?? collect())->count())
      <div class="card section-card">
        <div class="card-header"><div class="section-icon" style="background:#e0f9ef;color:#28c76f;"><i class="bx bx-clipboard"></i></div> Surveys</div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
              <thead class="table-light"><tr><th>Survey No</th><th>Mode</th><th>Date</th><th>Status</th><th></th></tr></thead>
              <tbody>
                @foreach($lead->surveys as $s)
                <tr>
                  <td class="font-monospace fw-600">{{ $s->survey_no }}</td>
                  <td>{{ $s->survey_mode ?? 'Detailed' }}</td>
                  <td>{{ $s->survey_date ? \Carbon\Carbon::parse($s->survey_date)->format('d M Y') : '—' }}</td>
                  <td><span class="badge bg-label-{{ match(strtolower($s->status)) { 'completed'=>'success','cancelled'=>'danger',default=>'secondary' } }}">{{ $s->status }}</span></td>
                  <td><a href="{{ route('admin.cctv.surveys.show', $s) }}" class="btn btn-sm btn-outline-primary py-0 px-2">View</a></td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
      @endif

      {{-- Linked Estimations --}}
      @if(($lead->quotations ?? collect())->count())
      <div class="card section-card">
        <div class="card-header"><div class="section-icon" style="background:#f3eeff;color:#8c57ff;"><i class="bx bx-file-blank"></i></div> Estimations</div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
              <thead class="table-light"><tr><th>Quote No</th><th>Total</th><th>Valid Until</th><th>Status</th><th></th></tr></thead>
              <tbody>
                @foreach($lead->quotations as $q)
                <tr>
                  <td class="font-monospace fw-600">{{ $q->quote_no }}</td>
                  <td>Rs. {{ number_format($q->grand_total ?? 0, 2) }}</td>
                  <td>{{ $q->valid_until ? \Carbon\Carbon::parse($q->valid_until)->format('d M Y') : '—' }}</td>
                  <td><span class="badge bg-label-{{ ['draft'=>'secondary','sent'=>'info','approved'=>'success','rejected'=>'danger','expired'=>'warning'][$q->status] ?? 'secondary' }}">{{ ucfirst($q->status) }}</span></td>
                  <td><a href="{{ route('admin.cctv.quotations.show', $q) }}" class="btn btn-sm btn-outline-primary py-0 px-2">View</a></td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
      @endif
    </div>

    {{-- Sidebar --}}
    <div class="col-lg-4">
      <div class="card section-card">
        <div class="card-header"><div class="section-icon"><i class="bx bx-info-circle"></i></div> Quick Info</div>
        <div class="card-body">
          <div class="mb-3"><div class="info-label">Lead Number</div><div class="fw-700 font-monospace text-primary">{{ $lead->lead_no }}</div></div>
          <div class="mb-3"><div class="info-label">Status</div><span class="badge bg-label-{{ $statusColor }}">{{ $lead->status }}</span></div>
          <div class="mb-3"><div class="info-label">Source</div><div class="info-value">{{ $lead->source ?? '—' }}</div></div>
          <div class="mb-3"><div class="info-label">Created</div><div class="info-value">{{ $lead->created_at->format('d M Y, h:i A') }}</div></div>
          <div class="mb-3"><div class="info-label">Last Updated</div><div class="info-value">{{ $lead->updated_at->format('d M Y, h:i A') }}</div></div>
          <hr>
          <div class="d-grid gap-2">
            <a href="{{ route('admin.cctv.leads.edit', $lead) }}" class="btn btn-primary btn-sm"><i class="bx bx-edit me-1"></i> Edit Lead</a>
            @if(!($survey ?? null))
              <a href="{{ route('admin.cctv.surveys.create', ['lead_id'=>$lead->id]) }}" class="btn btn-outline-info btn-sm"><i class="bx bx-clipboard me-1"></i> Create Survey</a>
            @endif
            @if(!($quotation ?? null))
              <a href="{{ route('admin.cctv.quotations.create', ['lead_id'=>$lead->id]) }}" class="btn btn-outline-secondary btn-sm"><i class="bx bx-file-blank me-1"></i> Create Estimation</a>
            @endif
          </div>
        </div>
      </div>

      {{-- Status Actions --}}
      <div class="card section-card">
        <div class="card-header"><div class="section-icon" style="background:#fff3e8;color:#fd7e14;"><i class="bx bx-transfer"></i></div> Update Status</div>
        <div class="card-body">
          <form method="POST" action="{{ route('admin.cctv.leads.update', $lead) }}">
            @csrf @method('PUT')
            {{-- preserve required fields --}}
            <input type="hidden" name="customer_name" value="{{ $lead->customer_name }}">
            <input type="hidden" name="mobile"        value="{{ $lead->mobile }}">
            <select name="status" class="form-select form-select-sm mb-2">
              @foreach(['New Lead','Survey Scheduled','Survey Completed','Estimation Sent','Approved','Installation','Completed','Cancelled','Rejected','Postponed','Rescheduled','Lost'] as $st)
                <option value="{{ $st }}" @selected($lead->status === $st)>{{ $st }}</option>
              @endforeach
            </select>
            <button class="btn btn-sm btn-outline-warning w-100"><i class="bx bx-check me-1"></i> Save Status</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
