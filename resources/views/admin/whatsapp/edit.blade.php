@extends('layouts.admin')
@section('title', 'WhatsApp Settings')
@section('page-title', 'WhatsApp Settings')
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.store.edit') }}">Settings</a></li>
  <li class="breadcrumb-item active">WhatsApp</li>
@endsection

@push('styles')
<style>
.wa-header {
  background: linear-gradient(135deg,#25d366 0%,#128c7e 60%,#075e54 100%);
  border-radius:14px;padding:24px 28px;color:#fff;
  display:flex;align-items:center;gap:18px;flex-wrap:wrap;
  margin-bottom:28px;
}
.wa-header .wa-ico {
  width:56px;height:56px;background:rgba(255,255,255,.2);border-radius:16px;
  display:flex;align-items:center;justify-content:center;font-size:1.9rem;flex-shrink:0;
}
.wa-card {
  border:0;border-radius:14px;
  box-shadow:0 2px 20px rgba(37,211,102,.1);
  margin-bottom:24px;
}
.wa-card .card-body { padding:28px; }
.ws-section {
  font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;
  color:#128c7e;border-bottom:2px solid #f0fff8;
  padding-bottom:8px;margin:24px 0 18px;
  display:flex;align-items:center;gap:8px;
}
.ws-section:first-of-type { margin-top:0; }
.form-label { font-weight:600;font-size:.83rem;color:#444;margin-bottom:5px; }
.form-control:focus,.form-select:focus {
  border-color:#25d366;box-shadow:0 0 0 3px rgba(37,211,102,.12);
}
.form-control { border-radius:10px; }

/* Toggle */
.wa-toggle-wrap {
  display:flex;align-items:center;gap:14px;padding:16px 20px;
  background:#f0fff8;border-radius:12px;border:1.5px solid #b7f0d0;
  margin-bottom:20px;
}
.wa-toggle-label { font-weight:700;font-size:.95rem;color:#1d3d2f; }
.wa-toggle-label small { display:block;font-weight:400;font-size:.78rem;color:#888;margin-top:1px; }
.form-check-input[type=checkbox] { width:44px;height:22px;border-radius:11px;cursor:pointer; }
.form-check-input:checked { background-color:#25d366;border-color:#25d366; }

/* Template cards */
.tmpl-card {
  background:#f4fff8;border:1.5px solid #d0f5e0;border-radius:12px;
  padding:18px;margin-bottom:16px;
}
.tmpl-card-header {
  display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;
}
.tmpl-title { font-weight:700;font-size:.88rem;color:#1d3d2f; }
.tmpl-key { font-size:.7rem;color:#aaa;font-family:monospace; }

/* Placeholder chips */
.ph-chips { display:flex;flex-wrap:wrap;gap:6px;margin-top:8px; }
.ph-chip {
  font-size:.72rem;font-family:monospace;
  background:#e0f9ec;color:#0a6640;border:1px solid #a8e6bf;
  border-radius:6px;padding:2px 8px;cursor:pointer;
  transition:.15s;user-select:none;
}
.ph-chip:hover { background:#b7f0d0;border-color:#25d366; }

/* Save bar */
.wa-save-bar {
  background:#f0fff8;border-radius:12px;padding:16px 20px;
  display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;
  margin-top:28px;border:1px solid #b7f0d0;
}
.btn-save-wa {
  background:linear-gradient(135deg,#25d366,#128c7e);color:#fff;border:0;
  padding:10px 32px;border-radius:10px;font-weight:700;font-size:.95rem;
  transition:.2s;box-shadow:0 4px 14px rgba(37,211,102,.3);
}
.btn-save-wa:hover { opacity:.9;color:#fff;transform:translateY(-1px); }

/* Test panel */
.test-panel {
  background:#f4fff8;border:1.5px solid #b7f0d0;border-radius:12px;padding:18px;
}

/* Preview bubble */
.wa-bubble {
  background:#dcf8c6;border-radius:12px 12px 0 12px;
  padding:10px 14px;font-size:.82rem;line-height:1.6;
  color:#1d3d2f;max-width:100%;word-break:break-word;
  box-shadow:0 1px 3px rgba(0,0,0,.1);
  white-space:pre-wrap;
  margin-top:8px;
}
</style>
@endpush

@section('content')

<div class="wa-header">
  <div class="wa-ico">
    <svg viewBox="0 0 32 32" width="30" height="30" fill="white" xmlns="http://www.w3.org/2000/svg">
      <path d="M16.002 2C8.28 2 2 8.28 2 16.002c0 2.47.65 4.786 1.782 6.79L2 30l7.42-1.748A13.93 13.93 0 0016.002 30C23.72 30 30 23.72 30 16.002 30 8.28 23.72 2 16.002 2zm0 25.4a11.35 11.35 0 01-5.793-1.585l-.415-.247-4.404 1.037 1.057-4.296-.27-.44A11.368 11.368 0 014.6 16.002C4.6 9.714 9.714 4.6 16.002 4.6c6.288 0 11.398 5.114 11.398 11.402 0 6.286-5.11 11.398-11.398 11.398zm6.27-8.543c-.344-.172-2.034-1.004-2.349-1.118-.315-.115-.545-.172-.774.172-.229.344-.887 1.118-1.087 1.348-.2.23-.4.258-.744.086-.344-.172-1.452-.535-2.767-1.708-1.022-.912-1.712-2.037-1.912-2.381-.2-.344-.021-.53.15-.702.155-.154.344-.4.516-.6.172-.2.229-.344.344-.573.115-.229.058-.43-.029-.601-.086-.172-.774-1.866-1.06-2.554-.28-.672-.564-.58-.774-.59l-.659-.011c-.229 0-.601.086-.916.43-.315.344-1.202 1.175-1.202 2.866 0 1.691 1.23 3.325 1.401 3.555.172.229 2.42 3.697 5.864 5.186.82.354 1.46.565 1.958.723.823.261 1.573.224 2.165.136.66-.099 2.034-.832 2.32-1.635.287-.803.287-1.491.2-1.635-.085-.143-.315-.229-.659-.4z"/>
    </svg>
  </div>
  <div>
    <h4 style="margin:0;font-weight:700;font-size:1.25rem;">WhatsApp Notifications</h4>
    <p style="margin:0;opacity:.8;font-size:.85rem;">Send invoices, alerts and notifications to customers via WhatsApp</p>
  </div>
</div>

<form action="{{ route('admin.whatsapp-settings.update') }}" method="POST" id="waForm">
  @csrf @method('PUT')

  <div class="row g-4">
    {{-- Left: Config + Templates --}}
    <div class="col-lg-8">

      {{-- API Config Card --}}
      <div class="card wa-card">
        <div class="card-body">

          {{-- Master toggle --}}
          <div class="wa-toggle-wrap">
            <div class="flex-grow-1">
              <div class="wa-toggle-label">
                Enable WhatsApp Notifications
                <small>When disabled, messages are logged to <code>storage/logs/whatsapp.log</code> instead of sending</small>
              </div>
            </div>
            <div class="form-check form-switch mb-0">
              <input class="form-check-input" type="checkbox" name="enabled" id="waEnabled" role="switch"
                     {{ $settings->enabled ? 'checked' : '' }}>
            </div>
          </div>

          <div class="ws-section"><i class='bx bx-link'></i> WhatsApp API Configuration</div>

          <div class="alert" style="background:#fffbe6;border:1px solid #ffe58f;border-radius:10px;font-size:.82rem;color:#7c6000;padding:12px 16px;margin-bottom:20px;">
            <i class='bx bx-info-circle me-1'></i>
            Compatible with <strong>UltraMsg</strong>, <strong>WA Gateway</strong>, <strong>Green API</strong>, <strong>WhatsApp Cloud API (Meta)</strong>, and any HTTP-based WhatsApp provider.
          </div>

          <div class="row g-3">
            <div class="col-12">
              <label class="form-label">API Endpoint URL</label>
              <input type="url" name="api_url" class="form-control"
                     value="{{ old('api_url', $settings->api_url) }}"
                     placeholder="https://api.ultramsg.com/instance123/messages/chat" />
              <div class="form-text">Your provider's message send endpoint</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">API Key / Token</label>
              <div class="input-group">
                <input type="password" name="api_key" class="form-control" id="apiKeyInput"
                       value="{{ old('api_key', $settings->api_key) }}"
                       placeholder="Your secret API token" autocomplete="off" />
                <button type="button" class="btn btn-outline-secondary" onclick="toggleApiKey()"
                        style="border-radius:0 10px 10px 0;">
                  <i class='bx bx-show' id="apiKeyEye"></i>
                </button>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Instance ID <span style="font-weight:400;color:#aaa;font-size:.78rem;">(optional)</span></label>
              <input type="text" name="instance_id" class="form-control"
                     value="{{ old('instance_id', $settings->instance_id) }}"
                     placeholder="e.g. instance123 (UltraMsg)" />
              <div class="form-text">Required by UltraMsg / WA Gateway</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Phone Number ID <span style="font-weight:400;color:#aaa;font-size:.78rem;">(optional)</span></label>
              <input type="text" name="phone_number_id" class="form-control"
                     value="{{ old('phone_number_id', $settings->phone_number_id) }}"
                     placeholder="e.g. 12345678901234 (Meta Cloud API)" />
              <div class="form-text">Required for WhatsApp Cloud API (Meta)</div>
            </div>
          </div>

        </div>
      </div>

      {{-- Templates Card --}}
      <div class="card wa-card">
        <div class="card-body">
          <div class="ws-section" style="margin-top:0;"><i class='bx bx-edit'></i> Message Templates</div>

          <p style="font-size:.82rem;color:#666;margin-bottom:20px;">
            Click any <span style="font-family:monospace;background:#e0f9ec;padding:1px 6px;border-radius:4px;color:#0a6640;">{placeholder}</span> chip to insert it.
            WhatsApp supports <strong>*bold*</strong>, <em>_italic_</em> and line breaks.
          </p>

          @php
          $templateMeta = [
            'invoice_sent' => [
              'icon'  => 'bx-receipt',
              'color' => '#25d366',
              'label' => 'Invoice Sent to Customer',
              'chips' => ['{customer_name}', '{invoice_no}', '{device}', '{total}', '{balance}', '{store_name}'],
            ],
            'job_alert' => [
              'icon'  => 'bx-wrench',
              'color' => '#128c7e',
              'label' => 'Job Order Alert',
              'chips' => ['{customer_name}', '{order_no}', '{device}', '{status}', '{store_name}'],
            ],
            'field_alert' => [
              'icon'  => 'bx-map-pin',
              'color' => '#E8490F',
              'label' => 'Field Service Alert',
              'chips' => ['{customer_name}', '{complaint_no}', '{status}', '{technician}', '{store_name}'],
            ],
            'payment_reminder' => [
              'icon'  => 'bx-money',
              'color' => '#f7941d',
              'label' => 'Payment Reminder',
              'chips' => ['{customer_name}', '{order_no}', '{balance}', '{store_name}'],
            ],
            'quotation' => [
              'icon'  => 'bx-receipt',
              'color' => '#696cff',
              'label' => 'Quotation / Estimate',
              'chips' => ['{customer_name}', '{device}', '{total}', '{order_no}', '{store_name}'],
            ],
            'uncollected_reminder' => [
              'icon'  => 'bx-time-five',
              'color' => '#ff6b35',
              'label' => 'Uncollected Item Reminder',
              'chips' => ['{customer_name}', '{device}', '{order_no}', '{days_waiting}', '{store_name}'],
            ],
          ];
          @endphp

          @foreach($templateMeta as $key => $meta)
          @php $tmpl = $templates[$key] ?? null; @endphp
          <div class="tmpl-card">
            <div class="tmpl-card-header">
              <div>
                <span style="display:inline-flex;align-items:center;gap:8px;">
                  <span style="width:30px;height:30px;border-radius:8px;background:{{ $meta['color'] }}22;display:inline-flex;align-items:center;justify-content:center;color:{{ $meta['color'] }};">
                    <i class='bx {{ $meta['icon'] }}'></i>
                  </span>
                  <span class="tmpl-title">{{ $tmpl?->label ?? $meta['label'] }}</span>
                </span>
                <div class="tmpl-key">key: {{ $key }}</div>
              </div>
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox"
                       name="templates[{{ $key }}][active]"
                       id="tmpl_active_{{ $key }}"
                       {{ ($tmpl?->active ?? true) ? 'checked' : '' }}>
                <label class="form-check-label" for="tmpl_active_{{ $key }}" style="font-size:.78rem;color:#aaa;">Active</label>
              </div>
            </div>

            <div class="ph-chips mb-2">
              @foreach($meta['chips'] as $chip)
              <span class="ph-chip" onclick="insertPlaceholder('msg_{{ $key }}', '{{ $chip }}')">{{ $chip }}</span>
              @endforeach
            </div>

            <textarea class="form-control" name="templates[{{ $key }}][message]"
                      id="msg_{{ $key }}" rows="4"
                      placeholder="Enter WhatsApp message...">{{ old("templates.{$key}.message", $tmpl?->message ?? '') }}</textarea>

            <div class="wa-bubble" id="preview_{{ $key }}">{{ $tmpl?->message ?? '' }}</div>

            <div class="d-flex justify-content-between mt-1 align-items-center">
              <small style="color:#888;font-size:.72rem;"><i class='bx bx-info-circle'></i> Preview above shows how message looks in WhatsApp</small>
              <small class="text-muted">
                <span id="len_{{ $key }}">{{ strlen($tmpl?->message ?? '') }}</span> chars
              </small>
            </div>
          </div>
          @endforeach

        </div>
      </div>

      {{-- Uncollected Reminder Schedule Settings --}}
      <div class="card mb-3" style="border-radius:14px;border:1px solid #f0f0ff;">
        <div class="card-body">
          <h6 class="fw-bold mb-3" style="color:#ff6b35;"><i class="bx bx-time-five me-2"></i>Auto Uncollected Reminder Schedule</h6>
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label fw-semibold" style="font-size:.83rem;">Enable Auto Reminders</label>
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="uncollected_reminder_enabled" id="uncollRemEnabled"
                       {{ $settings->uncollected_reminder_enabled ? 'checked' : '' }}>
                <label class="form-check-label" for="uncollRemEnabled" style="font-size:.8rem;color:#aaa;">Active</label>
              </div>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold" style="font-size:.83rem;">How many reminders</label>
              <input type="number" name="uncollected_reminder_count" class="form-control form-control-sm"
                     min="1" max="10" value="{{ $settings->uncollected_reminder_count ?? 3 }}">
              <small class="text-muted" style="font-size:.72rem;">Max number of reminder messages per item</small>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold" style="font-size:.83rem;">Interval (hours)</label>
              <input type="number" name="uncollected_reminder_interval_hours" class="form-control form-control-sm"
                     min="1" max="720" value="{{ $settings->uncollected_reminder_interval_hours ?? 48 }}">
              <small class="text-muted" style="font-size:.72rem;">Hours between each reminder</small>
            </div>
          </div>
        </div>
      </div>

      {{-- Save Bar --}}
      <div class="wa-save-bar">
        <div style="font-size:.83rem;color:#888;">
          <i class='bx bx-info-circle me-1'></i>Changes apply to all future notifications
        </div>
        <button type="submit" class="btn-save-wa btn">
          <i class='bx bx-save me-1'></i>Save WhatsApp Settings
        </button>
      </div>

    </div>

    {{-- Right: Status + Test --}}
    <div class="col-lg-4">

      {{-- Status card --}}
      <div class="card wa-card">
        <div class="card-body">
          <div class="ws-section" style="margin-top:0;"><i class='bx bx-signal-5'></i> Current Status</div>
          <div class="d-flex align-items-center gap-3 mb-3">
            @if($settings->enabled && $settings->api_key)
              <span class="badge" style="background:#d4edda;color:#155724;font-size:.82rem;padding:6px 14px;border-radius:20px;">
                <i class='bx bx-check-circle me-1'></i>Active
              </span>
            @elseif($settings->enabled)
              <span class="badge" style="background:#fff3cd;color:#856404;font-size:.82rem;padding:6px 14px;border-radius:20px;">
                <i class='bx bx-error me-1'></i>Enabled — No API Key
              </span>
            @else
              <span class="badge" style="background:#f8d7da;color:#721c24;font-size:.82rem;padding:6px 14px;border-radius:20px;">
                <i class='bx bx-x-circle me-1'></i>Disabled (Log Only)
              </span>
            @endif
          </div>
          <div style="font-size:.8rem;color:#666;line-height:1.7;">
            <div><i class='bx bx-link me-1' style="color:#25d366;"></i>
              <strong>Endpoint:</strong> <span style="word-break:break-all;">{{ $settings->api_url ?: '—' }}</span>
            </div>
            @if($settings->instance_id)
            <div><i class='bx bx-id-card me-1' style="color:#25d366;"></i>
              <strong>Instance:</strong> {{ $settings->instance_id }}
            </div>
            @endif
          </div>
        </div>
      </div>

      {{-- Placeholder reference --}}
      <div class="card wa-card">
        <div class="card-body">
          <div class="ws-section" style="margin-top:0;"><i class='bx bx-code-curly'></i> All Placeholders</div>
          <div style="font-size:.8rem;color:#555;line-height:2;">
            @foreach([
              '{customer_name}' => 'Customer full name',
              '{order_no}'      => 'Job order number',
              '{invoice_no}'    => 'Invoice number',
              '{device}'        => 'Device / service type',
              '{total}'         => 'Grand total amount',
              '{balance}'       => 'Balance due',
              '{status}'        => 'Current status',
              '{complaint_no}'  => 'Field complaint number',
              '{technician}'    => 'Field staff name',
              '{store_name}'    => 'Auto from store settings',
            ] as $ph => $desc)
            <div class="d-flex gap-2 align-items-baseline mb-1">
              <code style="background:#e0f9ec;color:#0a6640;padding:1px 7px;border-radius:5px;flex-shrink:0;font-size:.72rem;">{{ $ph }}</code>
              <span style="color:#888;font-size:.75rem;">{{ $desc }}</span>
            </div>
            @endforeach
          </div>
        </div>
      </div>

      {{-- Test WhatsApp --}}
      <div class="card wa-card">
        <div class="card-body">
          <div class="ws-section" style="margin-top:0;"><i class='bx bx-send'></i> Send Test Message</div>
          <div class="test-panel">
            <div class="mb-3">
              <label class="form-label">WhatsApp Number</label>
              <input type="text" id="testPhone" class="form-control" placeholder="94771234567 (with country code)" />
              <div class="form-text">Include country code, no + sign</div>
            </div>
            <div class="mb-3">
              <label class="form-label">Message</label>
              <textarea id="testMessage" class="form-control" rows="3" placeholder="Test WhatsApp message..."></textarea>
            </div>
            <button type="button" class="btn w-100 fw-bold" onclick="sendTestWa()"
                    style="background:#25d366;color:#fff;border-radius:10px;">
              <svg viewBox="0 0 32 32" width="16" height="16" fill="white" style="margin-right:6px;vertical-align:-2px;" xmlns="http://www.w3.org/2000/svg"><path d="M16.002 2C8.28 2 2 8.28 2 16.002c0 2.47.65 4.786 1.782 6.79L2 30l7.42-1.748A13.93 13.93 0 0016.002 30C23.72 30 30 23.72 30 16.002 30 8.28 23.72 2 16.002 2zm6.27 19.857c-.344-.172-2.034-1.004-2.349-1.118-.315-.115-.545-.172-.774.172-.229.344-.887 1.118-1.087 1.348-.2.23-.4.258-.744.086-.344-.172-1.452-.535-2.767-1.708-1.022-.912-1.712-2.037-1.912-2.381-.2-.344-.021-.53.15-.702.155-.154.344-.4.516-.6.172-.2.229-.344.344-.573.115-.229.058-.43-.029-.601-.086-.172-.774-1.866-1.06-2.554-.28-.672-.564-.58-.774-.59l-.659-.011c-.229 0-.601.086-.916.43-.315.344-1.202 1.175-1.202 2.866 0 1.691 1.23 3.325 1.401 3.555.172.229 2.42 3.697 5.864 5.186.82.354 1.46.565 1.958.723.823.261 1.573.224 2.165.136.66-.099 2.034-.832 2.32-1.635.287-.803.287-1.491.2-1.635-.085-.143-.315-.229-.659-.4z"/></svg>
              Send Test
            </button>
            <div id="testResult" class="mt-2" style="font-size:.82rem;"></div>
          </div>
        </div>
      </div>

    </div>
  </div>
</form>
@endsection

@push('scripts')
<script>
function toggleApiKey() {
  const inp = document.getElementById('apiKeyInput');
  const eye = document.getElementById('apiKeyEye');
  if (inp.type === 'password') {
    inp.type = 'text';
    eye.className = 'bx bx-hide';
  } else {
    inp.type = 'password';
    eye.className = 'bx bx-show';
  }
}

function insertPlaceholder(textareaId, placeholder) {
  const ta = document.getElementById(textareaId);
  if (!ta) return;
  const start = ta.selectionStart;
  const end   = ta.selectionEnd;
  ta.value = ta.value.substring(0, start) + placeholder + ta.value.substring(end);
  ta.selectionStart = ta.selectionEnd = start + placeholder.length;
  ta.focus();
  updatePreview(textareaId);
}

function updatePreview(textareaId) {
  const ta  = document.getElementById(textareaId);
  const key = textareaId.replace('msg_', '');
  const len = document.getElementById('len_' + key);
  const pre = document.getElementById('preview_' + key);
  if (ta && len) len.textContent = ta.value.length;
  if (ta && pre) pre.textContent = ta.value;
}

document.querySelectorAll('textarea[id^="msg_"]').forEach(ta => {
  ta.addEventListener('input', () => updatePreview(ta.id));
});

function sendTestWa() {
  const phone   = document.getElementById('testPhone').value.trim();
  const message = document.getElementById('testMessage').value.trim();
  const result  = document.getElementById('testResult');
  if (!phone || !message) {
    result.innerHTML = '<span class="text-danger">Phone and message required.</span>';
    return;
  }
  result.innerHTML = '<span class="text-muted"><i class="bx bx-loader bx-spin me-1"></i>Sending...</span>';

  fetch('{{ route('admin.whatsapp-settings.test') }}', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
    },
    body: JSON.stringify({ phone, message }),
  })
  .then(r => r.json())
  .then(data => {
    result.innerHTML = data.ok
      ? `<span class="text-success"><i class='bx bx-check-circle me-1'></i>${data.message}</span>`
      : `<span class="text-warning"><i class='bx bx-info-circle me-1'></i>${data.message}</span>`;
  })
  .catch(() => {
    result.innerHTML = '<span class="text-danger">Request failed.</span>';
  });
}
</script>
@endpush
