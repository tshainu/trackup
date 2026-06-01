@extends('layouts.superadmin')
@section('title', $shop->shop_name)

@push('styles')
<style>
  .detail-card { background:#fff;border-radius:16px;border:1px solid #eee;padding:1.5rem; }
  .section-title { font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#7c3aed;margin-bottom:1rem;padding-bottom:.5rem;border-bottom:1px solid #f0f0ff; }
  .info-row { display:flex;gap:.5rem;padding:.5rem 0;border-bottom:1px solid #f9f9f9;align-items:flex-start; }
  .info-row:last-child { border-bottom:none; }
  .info-label { font-size:.78rem;color:#999;font-weight:600;min-width:130px;flex-shrink:0; }
  .info-value { font-size:.85rem;color:#333;word-break:break-all; }
  .status-badge { font-size:.7rem;font-weight:700;padding:3px 10px;border-radius:20px; }
  .status-active    { background:#d1fae5;color:#065f46; }
  .status-suspended { background:#fee2e2;color:#991b1b; }
  .status-pending   { background:#fef3c7;color:#92400e; }
  .cred-box { background:#1e1040;border-radius:12px;padding:1rem 1.25rem;font-family:monospace; }
  .cred-row { display:flex;align-items:center;justify-content:space-between;gap:1rem;margin-bottom:.5rem; }
  .cred-row:last-child { margin-bottom:0; }
  .cred-label { font-size:.72rem;color:#a78bfa;font-weight:700;text-transform:uppercase;letter-spacing:.05em; }
  .cred-val { color:#e9d5ff;font-size:.88rem;font-weight:600; }
  .copy-btn { background:rgba(255,255,255,.1);border:none;color:#c4b5fd;border-radius:6px;padding:2px 8px;font-size:.72rem;cursor:pointer;transition:background .2s; }
  .copy-btn:hover { background:rgba(255,255,255,.2); }
  .log-row { padding:.6rem 0;border-bottom:1px solid #f5f5f5;font-size:.8rem; }
  .log-row:last-child { border-bottom:none; }
  .log-action { display:inline-block;padding:2px 9px;border-radius:20px;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.04em; }
  .action-created       { background:#d1fae5;color:#065f46; }
  .action-updated       { background:#dbeafe;color:#1e40af; }
  .action-password_reset{ background:#fef3c7;color:#92400e; }
  .action-status_changed{ background:#ede9fe;color:#5b21b6; }
  .online-dot { width:9px;height:9px;border-radius:50%;background:#16a34a;display:inline-block;animation:pulse-green 1.5s infinite; }
  @keyframes pulse-green { 0%,100%{opacity:1} 50%{opacity:.4} }
</style>
@endpush

@section('content')

@php
  $isOnline = $shop->last_active_at && $shop->last_active_at->diffInMinutes(now()) <= 15;
  $sc = match($shop->status) {
    'active'    => 'status-active',
    'suspended' => 'status-suspended',
    'pending'   => 'status-pending',
    default     => '',
  };
@endphp

{{-- Header --}}
<div class="d-flex align-items-center gap-3 mb-4">
  <a href="{{ route('superadmin.shops.index') }}"
     class="btn btn-sm btn-outline-secondary d-flex align-items-center justify-content-center"
     style="width:36px;height:36px;border-radius:9px;padding:0;">
    <i class="bx bx-arrow-back"></i>
  </a>
  <div class="d-flex align-items-center gap-3 flex-wrap flex-fill">
    <div style="width:48px;height:48px;border-radius:13px;background:linear-gradient(135deg,#7c3aed,#a855f7);color:#fff;font-size:1.4rem;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
      {{ strtoupper(substr($shop->shop_name,0,1)) }}
    </div>
    <div>
      <div class="d-flex align-items-center gap-2 flex-wrap">
        <h4 class="fw-bold mb-0" style="color:#1e1040;">{{ $shop->shop_name }}</h4>
        <span class="status-badge {{ $sc }}">{{ ucfirst($shop->status) }}</span>
        @if($isOnline)
          <span style="font-size:.75rem;color:#16a34a;"><span class="online-dot"></span> Online</span>
        @endif
        <span style="background:#f0f0ff;color:#7c3aed;font-size:.72rem;font-weight:700;padding:2px 9px;border-radius:20px;">{{ $shop->shop_code }}</span>
      </div>
      <p class="text-muted mb-0" style="font-size:.82rem;">{{ $shop->owner_name }}</p>
    </div>
    <div class="ms-auto d-flex gap-2">
      <a href="{{ route('superadmin.shops.edit',$shop) }}"
         class="btn btn-sm fw-semibold" style="background:#eff6ff;color:#2563eb;border-radius:9px;border:1px solid #dbeafe;">
        <i class="bx bx-edit me-1"></i>Edit
      </a>
    </div>
  </div>
</div>

{{-- Flash --}}
@if(session('success'))
  <div class="alert border-0 rounded-3 mb-4" style="background:#d1fae5;color:#065f46;">
    <i class="bx bx-check-circle me-2"></i>{!! session('success') !!}
  </div>
@endif
@if(session('error'))
  <div class="alert alert-danger border-0 rounded-3 mb-4">{{ session('error') }}</div>
@endif

<div class="row g-4">

  {{-- Left --}}
  <div class="col-12 col-lg-7">

    {{-- Shop Details --}}
    <div class="detail-card mb-4">
      <div class="section-title"><i class="bx bx-store me-1"></i>Shop Details</div>
      <div class="info-row"><span class="info-label">Shop Name</span><span class="info-value fw-semibold">{{ $shop->shop_name }}</span></div>
      <div class="info-row"><span class="info-label">Owner</span><span class="info-value">{{ $shop->owner_name }}</span></div>
      <div class="info-row"><span class="info-label">Email</span><span class="info-value">{{ $shop->email }}</span></div>
      <div class="info-row"><span class="info-label">Phone</span><span class="info-value">{{ $shop->phone ?: '—' }}</span></div>
      <div class="info-row"><span class="info-label">Address</span><span class="info-value">{{ $shop->address ?: '—' }}</span></div>
      <div class="info-row"><span class="info-label">City</span><span class="info-value">{{ $shop->city ?: '—' }}</span></div>
      <div class="info-row"><span class="info-label">Country</span><span class="info-value">{{ $shop->country ?: '—' }}</span></div>
      <div class="info-row"><span class="info-label">Created</span><span class="info-value">{{ $shop->created_at->format('d M Y, h:i A') }}</span></div>
      <div class="info-row"><span class="info-label">Last Active</span>
        <span class="info-value">{{ $shop->last_active_at ? $shop->last_active_at->format('d M Y, h:i A').' ('.$shop->last_active_at->diffForHumans().')' : 'Never logged in' }}</span>
      </div>
      @if($shop->notes)
        <div class="info-row"><span class="info-label">Notes</span><span class="info-value" style="white-space:pre-line;">{{ $shop->notes }}</span></div>
      @endif
    </div>

    {{-- Admin Credentials --}}
    <div class="detail-card mb-4">
      <div class="section-title"><i class="bx bx-lock me-1"></i>Admin Login Credentials</div>
      <div class="cred-box">
        <div class="cred-row">
          <span class="cred-label">Shop ID</span>
          <div class="d-flex align-items-center gap-2">
            <span class="cred-val" id="cred-shopid">{{ $shop->shop_code }}</span>
            <button class="copy-btn" onclick="copyText('cred-shopid', this)"><i class="bx bx-copy"></i> Copy</button>
          </div>
        </div>
        <div class="cred-row">
          <span class="cred-label">Username</span>
          <div class="d-flex align-items-center gap-2">
            <span class="cred-val" id="cred-user">{{ $shop->admin_username }}</span>
            <button class="copy-btn" onclick="copyText('cred-user', this)"><i class="bx bx-copy"></i> Copy</button>
          </div>
        </div>
        <div class="cred-row">
          <span class="cred-label">Password</span>
          <div class="d-flex align-items-center gap-2">
            <span class="cred-val" id="cred-pass">{{ $shop->admin_plain_password }}</span>
            <button class="copy-btn" onclick="copyText('cred-pass', this)"><i class="bx bx-copy"></i> Copy</button>
          </div>
        </div>
      </div>
      <div class="mt-3">
        <form method="POST" action="{{ route('superadmin.shops.reset-password',$shop) }}"
              onsubmit="return confirm('Generate a new random password for {{ addslashes($shop->shop_name) }}?')">
          @csrf
          <button type="submit" class="btn btn-sm fw-semibold" style="background:#fef3c7;color:#92400e;border:1px solid #fde68a;border-radius:9px;">
            <i class="bx bx-refresh me-1"></i>Reset Password
          </button>
        </form>
      </div>
    </div>

    {{-- Activity Log --}}
    <div class="detail-card">
      <div class="section-title"><i class="bx bx-history me-1"></i>Activity Log</div>
      @if($logs->count())
        @foreach($logs as $log)
          <div class="log-row">
            <div class="d-flex align-items-start justify-content-between gap-2">
              <div>
                <span class="log-action action-{{ $log->action }}">{{ str_replace('_',' ',ucfirst($log->action)) }}</span>
                <span class="ms-2" style="color:#555;">{{ $log->description }}</span>
              </div>
              <span class="text-muted flex-shrink-0" style="font-size:.72rem;">{{ $log->created_at->diffForHumans() }}</span>
            </div>
          </div>
        @endforeach
      @else
        <p class="text-muted mb-0" style="font-size:.85rem;">No activity recorded yet.</p>
      @endif
    </div>

  </div>

  {{-- Right --}}
  <div class="col-12 col-lg-5">

    {{-- Status Change --}}
    <div class="detail-card mb-4">
      <div class="section-title"><i class="bx bx-toggle-left me-1"></i>Change Status</div>
      <form method="POST" action="{{ route('superadmin.shops.update-status',$shop) }}">
        @csrf @method('PATCH')
        <div class="mb-3">
          <select name="status" class="form-select" style="border-radius:9px;">
            <option value="active"    {{ $shop->status==='active'    ? 'selected':'' }}>Active</option>
            <option value="pending"   {{ $shop->status==='pending'   ? 'selected':'' }}>Pending</option>
            <option value="suspended" {{ $shop->status==='suspended' ? 'selected':'' }}>Suspended</option>
          </select>
        </div>
        <button type="submit" class="btn btn-sm fw-semibold w-100" style="background:linear-gradient(135deg,#7c3aed,#a855f7);color:#fff;border:none;border-radius:9px;padding:.55rem;">
          <i class="bx bx-save me-1"></i>Update Status
        </button>
      </form>
    </div>

    {{-- Quick Stats --}}
    <div class="detail-card mb-4">
      <div class="section-title"><i class="bx bx-bar-chart me-1"></i>Quick Stats</div>
      <div class="row g-2">
        <div class="col-6">
          <div style="background:#f0f0ff;border-radius:10px;padding:.85rem;text-align:center;">
            <div style="font-size:1.5rem;font-weight:800;color:#7c3aed;">{{ $logs->count() }}</div>
            <div style="font-size:.72rem;color:#6d6d6d;font-weight:600;">Activity Logs</div>
          </div>
        </div>
        <div class="col-6">
          <div style="background:#d1fae5;border-radius:10px;padding:.85rem;text-align:center;">
            <div style="font-size:1.5rem;font-weight:800;color:#16a34a;">{{ $isOnline ? 1 : 0 }}</div>
            <div style="font-size:.72rem;color:#065f46;font-weight:600;">Online Now</div>
          </div>
        </div>
      </div>
      <div class="mt-3 text-muted" style="font-size:.78rem;">
        <div><strong>Registered:</strong> {{ $shop->created_at->format('d M Y') }}</div>
        @if($shop->last_active_at)
          <div class="mt-1"><strong>Last Seen:</strong> {{ $shop->last_active_at->diffForHumans() }}</div>
        @endif
      </div>
    </div>

    {{-- Danger Zone --}}
    <div class="detail-card" style="border-color:#fee2e2;">
      <div class="section-title" style="color:#dc2626;border-color:#fee2e2;"><i class="bx bx-error me-1"></i>Danger Zone</div>
      <p class="text-muted mb-3" style="font-size:.8rem;">Permanently delete this shop and all its data. This action cannot be undone.</p>
      <form method="POST" action="{{ route('superadmin.shops.destroy',$shop) }}"
            onsubmit="return confirm('PERMANENTLY DELETE {{ addslashes($shop->shop_name) }}?\n\nThis CANNOT be undone.')">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-sm fw-bold" style="background:#fee2e2;color:#dc2626;border:1px solid #fca5a5;border-radius:9px;">
          <i class="bx bx-trash me-1"></i>Delete This Shop
        </button>
      </form>
    </div>

  </div>
</div>

@push('scripts')
<script>
function copyText(id, btn) {
  var text = document.getElementById(id).textContent.trim();
  // Use clipboard API if available (HTTPS), fallback to execCommand for HTTP
  if (navigator.clipboard && window.isSecureContext) {
    navigator.clipboard.writeText(text).then(function() {
      btn.innerHTML = '<i class=bx bx-check></i> Copied!';
      setTimeout(function(){ btn.innerHTML = '<i class=bx bx-copy></i> Copy'; }, 1500);
    });
  } else {
    var ta = document.createElement('textarea');
    ta.value = text;
    ta.style.cssText = 'position:fixed;opacity:0;';
    document.body.appendChild(ta);
    ta.focus(); ta.select();
    try { document.execCommand('copy'); } catch(e) {}
    document.body.removeChild(ta);
    btn.innerHTML = '<i class=bx bx-check></i> Copied!';
    setTimeout(function(){ btn.innerHTML = '<i class=bx bx-copy></i> Copy'; }, 1500);
  }
}
</script>
@endpush

@endsection
