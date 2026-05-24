@extends('layouts.admin')
@section('title', 'SMS Settings')
@section('page-title', 'SMS Settings')
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.store.edit') }}">Settings</a></li>
  <li class="breadcrumb-item active">SMS</li>
@endsection

@push('styles')
<style>
.sms-header {
  background: linear-gradient(135deg,#0dcaf0 0%,#0085ff 60%,#6554f7 100%);
  border-radius:14px;padding:24px 28px;color:#fff;
  display:flex;align-items:center;gap:18px;flex-wrap:wrap;
  margin-bottom:28px;
}
.sms-header .sms-ico {
  width:56px;height:56px;background:rgba(255,255,255,.2);border-radius:16px;
  display:flex;align-items:center;justify-content:center;font-size:1.7rem;flex-shrink:0;
}
.sms-card {
  border:0;border-radius:14px;
  box-shadow:0 2px 20px rgba(5,130,241,.1);
  margin-bottom:24px;
}
.sms-card .card-body { padding:28px; }
.ss-section {
  font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;
  color:#0085ff;border-bottom:2px solid #f0f8ff;
  padding-bottom:8px;margin:24px 0 18px;
  display:flex;align-items:center;gap:8px;
}
.ss-section:first-of-type { margin-top:0; }
.form-label { font-weight:600;font-size:.83rem;color:#444;margin-bottom:5px; }
.form-control:focus,.form-select:focus {
  border-color:#0085ff;box-shadow:0 0 0 3px rgba(0,133,255,.1);
}
.form-control { border-radius:10px; }

/* Toggle switch */
.sms-toggle-wrap {
  display:flex;align-items:center;gap:14px;padding:16px 20px;
  background:#f0f9ff;border-radius:12px;border:1.5px solid #d0eeff;
  margin-bottom:20px;
}
.sms-toggle-label { font-weight:700;font-size:.95rem;color:#1d3557; }
.sms-toggle-label small { display:block;font-weight:400;font-size:.78rem;color:#888;margin-top:1px; }
.form-check-input[type=checkbox] { width:44px;height:22px;border-radius:11px;cursor:pointer; }
.form-check-input:checked { background-color:#0085ff;border-color:#0085ff; }

/* Template cards */
.tmpl-card {
  background:#f8fbff;border:1.5px solid #e0f0ff;border-radius:12px;
  padding:18px;margin-bottom:16px;
}
.tmpl-card-header {
  display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;
}
.tmpl-title { font-weight:700;font-size:.88rem;color:#1d3557; }
.tmpl-key { font-size:.7rem;color:#aaa;font-family:monospace; }

/* Placeholder chips */
.ph-chips { display:flex;flex-wrap:wrap;gap:6px;margin-top:8px; }
.ph-chip {
  font-size:.72rem;font-family:monospace;
  background:#e8f4ff;color:#0064cc;border:1px solid #c0deff;
  border-radius:6px;padding:2px 8px;cursor:pointer;
  transition:.15s;user-select:none;
}
.ph-chip:hover { background:#cce4ff;border-color:#0085ff; }

/* Save bar */
.sms-save-bar {
  background:#f0f9ff;border-radius:12px;padding:16px 20px;
  display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;
  margin-top:28px;border:1px solid #d0eeff;
}
.btn-save-sms {
  background:linear-gradient(135deg,#0085ff,#6554f7);color:#fff;border:0;
  padding:10px 32px;border-radius:10px;font-weight:700;font-size:.95rem;
  transition:.2s;box-shadow:0 4px 14px rgba(0,133,255,.3);
}
.btn-save-sms:hover { opacity:.9;color:#fff;transform:translateY(-1px); }

/* Test panel */
.test-panel {
  background:#fff8f0;border:1.5px solid #ffd599;border-radius:12px;padding:18px;
}
</style>
@endpush

@section('content')

<div class="sms-header">
  <div class="sms-ico"><i class='bx bx-message-rounded-dots'></i></div>
  <div>
    <h4 style="margin:0;font-weight:700;font-size:1.25rem;">SMS Notifications</h4>
    <p style="margin:0;opacity:.8;font-size:.85rem;">Configure SMS gateway and message templates for customer notifications</p>
  </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mb-3">
  <i class='bx bx-check-circle me-1'></i>{{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<form action="{{ route('admin.sms-settings.update') }}" method="POST" id="smsForm">
  @csrf @method('PUT')

  <div class="row g-4">
    {{-- Left: Config + Templates --}}
    <div class="col-lg-8">

      {{-- API Config Card --}}
      <div class="card sms-card">
        <div class="card-body">

          {{-- Master toggle --}}
          <div class="sms-toggle-wrap">
            <div class="flex-grow-1">
              <div class="sms-toggle-label">
                Enable SMS Notifications
                <small>When disabled, messages are logged to <code>storage/logs/sms.log</code> instead of sending</small>
              </div>
            </div>
            <div class="form-check form-switch mb-0">
              <input class="form-check-input" type="checkbox" name="enabled" id="smsEnabled" role="switch"
                     {{ $settings->enabled ? 'checked' : '' }}>
            </div>
          </div>

          <div class="ss-section"><i class='bx bx-link'></i> SMS Gateway API</div>
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label">API Endpoint URL</label>
              <input type="url" name="api_url" class="form-control"
                     value="{{ old('api_url', $settings->api_url) }}"
                     placeholder="https://api.smsprovider.com/send" />
              <div class="form-text">The POST endpoint of your SMS provider (e.g. SMS Lanka, Textit, Twilio)</div>
            </div>
            <div class="col-md-8">
              <label class="form-label">API Key / Token</label>
              <div class="input-group">
                <input type="password" name="api_key" class="form-control" id="apiKeyInput"
                       value="{{ old('api_key', $settings->api_key) }}"
                       placeholder="Your secret API key" autocomplete="off" />
                <button type="button" class="btn btn-outline-secondary" onclick="toggleApiKey()"
                        style="border-radius:0 10px 10px 0;">
                  <i class='bx bx-show' id="apiKeyEye"></i>
                </button>
              </div>
            </div>
            <div class="col-md-4">
              <label class="form-label">Sender ID</label>
              <input type="text" name="sender_id" class="form-control"
                     value="{{ old('sender_id', $settings->sender_id) }}"
                     placeholder="TRACKUP" />
              <div class="form-text">Shown as sender name</div>
            </div>
          </div>

        </div>
      </div>

      {{-- Templates Card --}}
      <div class="card sms-card">
        <div class="card-body">
          <div class="ss-section" style="margin-top:0;"><i class='bx bx-edit'></i> Message Templates</div>

          <p style="font-size:.82rem;color:#666;margin-bottom:20px;">
            Click any <span style="font-family:monospace;background:#e8f4ff;padding:1px 6px;border-radius:4px;color:#0064cc;">{placeholder}</span> chip to insert it at the cursor position in the textarea.
          </p>

          @php
          $templateMeta = [
            'job_created' => [
              'icon'  => 'bx-package',
              'color' => '#00b09b',
              'chips' => ['{customer_name}', '{order_no}', '{store_name}'],
            ],
            'job_status_changed' => [
              'icon'  => 'bx-refresh',
              'color' => '#f7941d',
              'chips' => ['{customer_name}', '{order_no}', '{status}', '{store_name}'],
            ],
            'field_complaint_created' => [
              'icon'  => 'bx-map-pin',
              'color' => '#6c63ff',
              'chips' => ['{customer_name}', '{complaint_no}', '{store_name}'],
            ],
            'field_service_completed' => [
              'icon'  => 'bx-check-shield',
              'color' => '#0085ff',
              'chips' => ['{customer_name}', '{complaint_no}', '{technician}', '{store_name}'],
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
                  <span class="tmpl-title">{{ $tmpl?->label ?? $key }}</span>
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
                      id="msg_{{ $key }}" rows="3"
                      placeholder="Enter SMS message...">{{ old("templates.{$key}.message", $tmpl?->message ?? '') }}</textarea>
            <div class="d-flex justify-content-end mt-1">
              <small class="text-muted" id="char_{{ $key }}">
                <span id="len_{{ $key }}">{{ strlen($tmpl?->message ?? '') }}</span> / 160 chars
              </small>
            </div>
          </div>
          @endforeach

        </div>
      </div>

      {{-- Save Bar --}}
      <div class="sms-save-bar">
        <div style="font-size:.83rem;color:#888;">
          <i class='bx bx-info-circle me-1'></i>Changes apply to all future notifications
        </div>
        <button type="submit" class="btn-save-sms btn">
          <i class='bx bx-save me-1'></i>Save SMS Settings
        </button>
      </div>

    </div>

    {{-- Right: Info + Test --}}
    <div class="col-lg-4">

      {{-- Status card --}}
      <div class="card sms-card">
        <div class="card-body">
          <div class="ss-section" style="margin-top:0;"><i class='bx bx-signal-5'></i> Current Status</div>
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
            <div><i class='bx bx-link me-1 text-primary'></i>
              <strong>Endpoint:</strong> {{ $settings->api_url ?: '—' }}
            </div>
            <div><i class='bx bx-broadcast me-1 text-primary'></i>
              <strong>Sender:</strong> {{ $settings->sender_id ?: '—' }}
            </div>
          </div>
        </div>
      </div>

      {{-- Placeholder reference --}}
      <div class="card sms-card">
        <div class="card-body">
          <div class="ss-section" style="margin-top:0;"><i class='bx bx-code-curly'></i> All Placeholders</div>
          <div style="font-size:.8rem;color:#555;line-height:2;">
            @foreach([
              '{customer_name}' => 'Customer\'s full name',
              '{order_no}'      => 'Job card order number',
              '{status}'        => 'New job status',
              '{complaint_no}'  => 'Field complaint number',
              '{technician}'    => 'Field staff name',
              '{store_name}'    => 'Auto-filled from store settings',
            ] as $ph => $desc)
            <div class="d-flex gap-2 align-items-baseline mb-1">
              <code style="background:#e8f4ff;color:#0064cc;padding:1px 7px;border-radius:5px;flex-shrink:0;font-size:.72rem;">{{ $ph }}</code>
              <span style="color:#888;font-size:.75rem;">{{ $desc }}</span>
            </div>
            @endforeach
          </div>
        </div>
      </div>

      {{-- Test SMS --}}
      <div class="card sms-card">
        <div class="card-body">
          <div class="ss-section" style="margin-top:0;"><i class='bx bx-send'></i> Send Test SMS</div>
          <div class="test-panel">
            <div class="mb-3">
              <label class="form-label">Phone Number</label>
              <input type="text" id="testPhone" class="form-control" placeholder="07X XXX XXXX" />
            </div>
            <div class="mb-3">
              <label class="form-label">Message</label>
              <textarea id="testMessage" class="form-control" rows="3" placeholder="Test message..."></textarea>
            </div>
            <button type="button" class="btn btn-warning w-100 fw-bold" onclick="sendTestSms()">
              <i class='bx bx-send me-1'></i>Send Test
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
// Toggle API key visibility
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

// Insert placeholder at cursor
function insertPlaceholder(textareaId, placeholder) {
  const ta = document.getElementById(textareaId);
  if (!ta) return;
  const start = ta.selectionStart;
  const end   = ta.selectionEnd;
  const val   = ta.value;
  ta.value = val.substring(0, start) + placeholder + val.substring(end);
  ta.selectionStart = ta.selectionEnd = start + placeholder.length;
  ta.focus();
  updateCharCount(textareaId);
}

// Character counter
function updateCharCount(textareaId) {
  const ta  = document.getElementById(textareaId);
  const key = textareaId.replace('msg_', '');
  const len = document.getElementById('len_' + key);
  if (ta && len) {
    len.textContent = ta.value.length;
    len.style.color = ta.value.length > 160 ? '#dc3545' : '#888';
  }
}

// Attach char counters
document.querySelectorAll('textarea[id^="msg_"]').forEach(ta => {
  ta.addEventListener('input', () => updateCharCount(ta.id));
});

// Send test SMS
function sendTestSms() {
  const phone   = document.getElementById('testPhone').value.trim();
  const message = document.getElementById('testMessage').value.trim();
  const result  = document.getElementById('testResult');
  if (!phone || !message) {
    result.innerHTML = '<span class="text-danger">Phone and message required.</span>';
    return;
  }
  result.innerHTML = '<span class="text-muted"><i class="bx bx-loader bx-spin me-1"></i>Sending...</span>';

  fetch('{{ route('admin.sms-settings.test') }}', {
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
