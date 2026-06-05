@if($q === '')
  {{-- Empty state --}}
  <div class="card shadow-sm border-0">
    <div class="card-body text-center py-5">
      <i class="bx bx-search-alt display-1 text-primary opacity-50 mb-3 d-block"></i>
      <h5 class="fw-semibold">Search for an Order</h5>
      <p class="text-muted mb-4">Enter a customer name, phone number, or any reference number to track the full pipeline status of an order.</p>
      <div class="row g-3 justify-content-center">
        <div class="col-auto"><div class="d-flex align-items-center gap-2 text-muted small"><span class="badge bg-label-primary rounded-pill px-3">LED-YYMM-001</span> Lead No</div></div>
        <div class="col-auto"><div class="d-flex align-items-center gap-2 text-muted small"><span class="badge bg-label-info rounded-pill px-3">SRV-YYMM-001</span> Survey No</div></div>
        <div class="col-auto"><div class="d-flex align-items-center gap-2 text-muted small"><span class="badge bg-label-warning rounded-pill px-3">QT-YYMM-001</span> Quote No</div></div>
        <div class="col-auto"><div class="d-flex align-items-center gap-2 text-muted small"><span class="badge bg-label-success rounded-pill px-3">PRJ-YYMM-001</span> Project No</div></div>
        <div class="col-auto"><div class="d-flex align-items-center gap-2 text-muted small"><span class="badge bg-label-danger rounded-pill px-3">INV-YYMM-001</span> Invoice No</div></div>
      </div>
    </div>
  </div>

@elseif($results->isEmpty())
  <div class="card shadow-sm">
    <div class="card-body text-center py-5">
      <i class="bx bx-search-alt display-4 text-muted mb-3 d-block"></i>
      <h5 class="text-muted">No orders found</h5>
      <p class="text-muted mb-0">No results for "<strong>{{ $q }}</strong>". Try a different name, number, or reference.</p>
    </div>
  </div>

@else
  <div class="mb-3">
    <span class="text-muted small">{{ $results->count() }} result(s) for "<strong>{{ $q }}</strong>"</span>
  </div>

  @foreach($results as $row)
    @php
      $lead      = $row['lead'];
      $survey    = $row['survey'];
      $quotation = $row['quotation'];
      $project   = $row['project'];
      $invoice   = $row['invoice'];

      $steps     = ['Lead', 'Survey', 'Quotation', 'Project', 'Invoice'];
      $stepsDone = [];
      if($lead)      $stepsDone[] = 'Lead';
      if($survey)    $stepsDone[] = 'Survey';
      if($quotation) $stepsDone[] = 'Quotation';
      if($project)   $stepsDone[] = 'Project';
      if($invoice)   $stepsDone[] = 'Invoice';
      $currentStep = end($stepsDone) ?: $row['type'];

      $statusColors = [
        'New Lead'         => 'secondary', 'Survey Scheduled' => 'info',
        'Survey Completed' => 'info',      'Quotation Sent'   => 'warning',
        'Approved'         => 'success',   'Installation'     => 'primary',
        'Completed'        => 'success',   'Cancelled'        => 'danger',
        'Rejected'         => 'danger',    'Postponed'        => 'warning',
        'Rescheduled'      => 'warning',   'Lost'             => 'secondary',
        'Unpaid'           => 'danger',    'Partial'          => 'warning',
        'Paid'             => 'success',   'Pending'          => 'warning',
        'In Progress'      => 'primary',
      ];
      $statusColor = $statusColors[$row['status']] ?? 'secondary';
    @endphp

    <div class="card mb-3 shadow-sm border-0" style="border-left: 4px solid var(--bs-{{ $statusColor }}) !important;">
      <div class="card-body pb-2">

        <div class="d-flex align-items-start justify-content-between mb-2">
          <div>
            <h6 class="fw-bold mb-0 fs-6"><i class="bx bx-user-circle me-1 text-muted"></i>{{ $row['customer'] }}</h6>
            <small class="text-muted"><i class="bx bx-phone me-1"></i>{{ $row['mobile'] }}</small>
          </div>
          <div class="text-end">
            <span class="badge bg-label-{{ $statusColor }} fs-6 px-3 py-2">{{ $row['status'] }}</span>
            <div class="mt-1"><small class="text-muted">Updated {{ $row['updated_at']?->diffForHumans() }}</small></div>
          </div>
        </div>

        {{-- Pipeline bar --}}
        <div class="d-flex align-items-center gap-0 mb-3" style="overflow-x:auto;">
          @foreach($steps as $i => $step)
            @php
              $done    = in_array($step, $stepsDone);
              $current = $step === $currentStep;
              $isLast  = $i === count($steps) - 1;
              $ref = $link = '';
              if ($step==='Lead'       && $lead)      { $ref=$lead->lead_no;       $link=route('admin.cctv.leads.show',$lead->id); }
              if ($step==='Survey'     && $survey)    { $ref=$survey->survey_no;   $link=route('admin.cctv.surveys.show',$survey->id); }
              if ($step==='Quotation'  && $quotation) { $ref=$quotation->quote_no; $link=route('admin.cctv.quotations.show',$quotation->id); }
              if ($step==='Project'    && $project)   { $ref=$project->project_no; $link=route('admin.cctv.projects.show',$project->id); }
              if ($step==='Invoice'    && $invoice)   { $ref=$invoice->invoice_no; $link=route('admin.cctv.invoices.show',$invoice->id); }
            @endphp
            <div class="d-flex flex-column align-items-center" style="min-width:80px;">
              @if($done || $current)<a href="{{ $link }}" class="text-decoration-none text-center">@else<span class="text-center">@endif
                <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-1"
                  style="width:34px;height:34px;
                    @if($done && !$current) background:#28a745;border:2px solid #28a745;
                    @elseif($current)       background:#fd7e14;border:2px solid #fd7e14;
                    @else                   background:#fff;border:2px dashed #ced4da;
                    @endif">
                  @if($done || $current)
                    <i class="bx {{ $done && !$current ? 'bx-check' : 'bx-radio-circle-marked' }} text-white" style="font-size:18px;"></i>
                  @else
                    <i class="bx bx-circle text-muted" style="font-size:18px;"></i>
                  @endif
                </div>
                <div style="font-size:11px;font-weight:{{ $current?'600':'400' }};color:{{ $current?'#fd7e14':($done?'#28a745':'#adb5bd') }};">{{ $step }}</div>
                @if($ref)<div style="font-size:10px;color:#6c757d;">{{ $ref }}</div>@endif
              @if($done || $current)</a>@else</span>@endif
            </div>
            @if(!$isLast)
              <div class="flex-grow-1" style="height:2px;min-width:20px;margin-bottom:24px;background:{{ $done?'#28a745':'#dee2e6' }};"></div>
            @endif
          @endforeach
        </div>

        {{-- Action buttons --}}
        <div class="d-flex flex-wrap gap-2 pt-2 border-top">
          @if($lead)      <a href="{{ route('admin.cctv.leads.show',$lead->id) }}"           class="btn btn-sm btn-outline-primary"><i class="bx bx-user-plus me-1"></i>Lead</a>@endif
          @if($survey)    <a href="{{ route('admin.cctv.surveys.show',$survey->id) }}"        class="btn btn-sm btn-outline-info"><i class="bx bx-map-alt me-1"></i>Survey</a>@endif
          @if($quotation) <a href="{{ route('admin.cctv.quotations.show',$quotation->id) }}"  class="btn btn-sm btn-outline-warning"><i class="bx bx-receipt me-1"></i>Quotation</a>@endif
          @if($project)   <a href="{{ route('admin.cctv.projects.show',$project->id) }}"      class="btn btn-sm btn-outline-success"><i class="bx bx-hard-hat me-1"></i>Project</a>@endif
          @if($invoice)   <a href="{{ route('admin.cctv.invoices.show',$invoice->id) }}"      class="btn btn-sm btn-outline-danger"><i class="bx bx-file me-1"></i>Invoice</a>@endif

          @if(!$invoice && $project)
            <a href="{{ route('admin.cctv.invoices.create',['project_id'=>$project->id]) }}" class="btn btn-sm btn-danger ms-auto"><i class="bx bx-plus me-1"></i>Generate Invoice</a>
          @elseif(!$project && $quotation)
            <a href="{{ route('admin.cctv.projects.create',['quotation_id'=>$quotation->id]) }}" class="btn btn-sm btn-success ms-auto"><i class="bx bx-plus me-1"></i>Create Project</a>
          @elseif(!$quotation && $survey)
            <a href="{{ route('admin.cctv.quotations.create',['survey_id'=>$survey->id]) }}" class="btn btn-sm btn-warning ms-auto"><i class="bx bx-plus me-1"></i>Create Quotation</a>
          @elseif(!$survey && $lead)
            <a href="{{ route('admin.cctv.surveys.create',['lead_id'=>$lead->id]) }}" class="btn btn-sm btn-info ms-auto"><i class="bx bx-plus me-1"></i>Schedule Survey</a>
          @endif
        </div>

      </div>
    </div>
  @endforeach
@endif
