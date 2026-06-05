{{--
  Pipeline banner — shows the full Lead → Survey → Estimation → Project → Invoice chain.
  Pass: $lead, $survey, $quotation, $project, $invoice (any can be null)
  Pass: $currentStep = 'lead'|'survey'|'quotation'|'project'|'invoice'
--}}
@php
  $steps = [
    ['key'=>'lead',      'label'=>'Lead',       'icon'=>'bx-user-plus',   'model'=>$lead      ?? null, 'routeShow'=>'admin.cctv.leads.show',       'routeCreate'=>null],
    ['key'=>'survey',    'label'=>'Survey',     'icon'=>'bx-clipboard',   'model'=>$survey    ?? null, 'routeShow'=>'admin.cctv.surveys.show',     'routeCreate'=>'admin.cctv.surveys.create'],
    ['key'=>'quotation', 'label'=>'Estimation',  'icon'=>'bx-file-blank',  'model'=>$quotation ?? null, 'routeShow'=>'admin.cctv.quotations.show',  'routeCreate'=>'admin.cctv.quotations.create'],
    ['key'=>'project',   'label'=>'Project',    'icon'=>'bx-wrench',      'model'=>$project   ?? null, 'routeShow'=>'admin.cctv.projects.show',    'routeCreate'=>'admin.cctv.projects.create'],
    ['key'=>'invoice',   'label'=>'Invoice',    'icon'=>'bx-receipt',     'model'=>$invoice   ?? null, 'routeShow'=>'admin.cctv.invoices.show',    'routeCreate'=>'admin.cctv.invoices.create'],
  ];

  // Build create query params for each step
  $createParams = [
    'survey'    => ['lead_id'      => $lead?->id],
    'quotation' => ['lead_id'      => $lead?->id],
    'project'   => ['quotation_id' => $quotation?->id, 'lead_id' => $lead?->id],
    'invoice'   => ['project_id'   => $project?->id],
  ];

  $stepIndex = array_search($currentStep, array_column($steps, 'key'));
@endphp

<div class="pipeline-banner mb-3">
  <div class="pipeline-track">
    @foreach($steps as $i => $step)
      @php
        $done    = $step['model'] !== null;
        $current = $step['key'] === $currentStep;
        $future  = !$done && !$current;
        $cls     = $done ? 'done' : ($current ? 'current' : 'future');

        // No number for the label — show ref number
        $refNo = null;
        if ($step['model']) {
          $refNo = $step['model']->lead_no   ?? $step['model']->survey_no ?? $step['model']->quote_no
                ?? $step['model']->project_no ?? $step['model']->invoice_no ?? null;
        }
      @endphp

      <div class="pipe-step {{ $cls }}{{ $current ? ' pipe-current' : '' }}">
        <div class="pipe-node">
          @if($done && !$current)
            <a href="{{ route($step['routeShow'], $step['model']) }}" class="pipe-circle" title="View {{ $step['label'] }}">
              <i class="bx bx-check"></i>
            </a>
          @elseif($current)
            <div class="pipe-circle current-circle">
              <i class="bx {{ $step['icon'] }}"></i>
            </div>
          @else
            {{-- Future step: show create link if previous step done --}}
            @php $prevDone = $i === 0 || $steps[$i-1]['model'] !== null; @endphp
            @if($prevDone && $step['routeCreate'])
              <a href="{{ route($step['routeCreate'], array_filter($createParams[$step['key']] ?? [])) }}" class="pipe-circle future-link" title="Start {{ $step['label'] }}">
                <i class="bx bx-plus"></i>
              </a>
            @else
              <div class="pipe-circle future-circle"><i class="bx {{ $step['icon'] }}"></i></div>
            @endif
          @endif
        </div>
        <div class="pipe-label">
          {{ $step['label'] }}
          @if($refNo)<div class="pipe-ref">{{ $refNo }}</div>@endif
        </div>
      </div>

      @if(!$loop->last)
        <div class="pipe-connector {{ ($done || $current) && ($steps[$i+1]['model'] || $steps[$i+1]['key'] === $currentStep) ? 'connector-done' : '' }}"></div>
      @endif
    @endforeach
  </div>
</div>

<style>
.pipeline-banner { background:#fff; border-radius:14px; box-shadow:0 2px 12px rgba(0,0,0,.06); padding:1rem 1.5rem; }
.pipeline-track  { display:flex; align-items:center; justify-content:space-between; }
.pipe-step       { display:flex; flex-direction:column; align-items:center; gap:6px; flex:0 0 auto; }
.pipe-connector  { flex:1; height:3px; background:#e9ecef; border-radius:2px; margin:0 4px; margin-bottom:22px; }
.pipe-connector.connector-done { background:linear-gradient(90deg,#fd7e14,#fdc98a); }

.pipe-circle {
  width:42px; height:42px; border-radius:50%; display:flex; align-items:center; justify-content:center;
  font-size:1.1rem; text-decoration:none; transition:all .2s;
}
/* Done */
.pipe-step.done .pipe-circle  { background:#28c76f; color:#fff; box-shadow:0 2px 8px rgba(40,199,111,.3); }
.pipe-step.done .pipe-circle:hover { background:#1fa85a; transform:scale(1.07); }
/* Current */
.current-circle { background:linear-gradient(135deg,#fd7e14,#e55a00); color:#fff; box-shadow:0 2px 10px rgba(253,126,20,.4); }
/* Future */
.future-link    { background:#f0f2f5; color:#adb5bd; border:2px dashed #ced4da; }
.future-link:hover { background:#fff3e8; color:#fd7e14; border-color:#fd7e14; transform:scale(1.07); }
.future-circle  { background:#f0f2f5; color:#ced4da; border:2px dashed #ced4da; }

.pipe-label { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:#697a8d; text-align:center; }
.pipe-step.done .pipe-label    { color:#28c76f; }
.pipe-step.current .pipe-label { color:#fd7e14; }
.pipe-ref   { font-size:.65rem; font-weight:600; color:#adb5bd; margin-top:1px; font-family:monospace; }

@media(max-width:600px) {
  .pipeline-track { overflow-x:auto; justify-content:flex-start; gap:0; }
  .pipe-connector { min-width:24px; }
  .pipe-label { font-size:.62rem; }
}
</style>
