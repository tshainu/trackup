@extends('layouts.admin')
@section('title', 'CCTV Quotations')

@push('styles')
<style>
  .stat-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:1.5rem; }
  @media(max-width:991px){.stat-grid{grid-template-columns:repeat(2,1fr);}}
  .stat-card { background:#fff; border-radius:14px; padding:16px 18px; display:flex; align-items:center; gap:14px; box-shadow:0 2px 12px rgba(0,0,0,.06); border-left:4px solid transparent; }
  .stat-icon { width:46px; height:46px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.35rem; flex-shrink:0; }
  .stat-num { font-size:1.65rem; font-weight:800; line-height:1.1; }
  .stat-lbl { font-size:.72rem; font-weight:600; color:#8592a3; text-transform:uppercase; letter-spacing:.04em; margin-top:1px; }
  .sc-blue { border-color:#696cff; } .sc-blue .stat-icon { background:#eef0ff; color:#696cff; } .sc-blue .stat-num { color:#696cff; }
  .sc-orange { border-color:#fd7e14; } .sc-orange .stat-icon { background:#fff3e8; color:#fd7e14; } .sc-orange .stat-num { color:#fd7e14; }
  .sc-green { border-color:#28c76f; } .sc-green .stat-icon { background:#e8faf0; color:#28c76f; } .sc-green .stat-num { color:#28c76f; }
  .sc-red { border-color:#ea5455; } .sc-red .stat-icon { background:#fdeaea; color:#ea5455; } .sc-red .stat-num { color:#ea5455; }
  .hero-bar { background:linear-gradient(135deg,#8c57ff,#696cff); border-radius:16px; padding:1.25rem 1.75rem; color:#fff; margin-bottom:1.5rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem; }
  .hero-bar h4 { margin:0; font-size:1.2rem; font-weight:700; }
  .hero-bar p { margin:0; opacity:.85; font-size:.85rem; }
  .filter-tabs { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:1rem; }
  .filter-tab { padding:6px 16px; border-radius:20px; font-size:.8rem; font-weight:600; border:1.5px solid #d9dee3; color:#697a8d; background:#fff; cursor:pointer; text-decoration:none; transition:all .15s; }
  .filter-tab.active, .filter-tab:hover { background:#8c57ff; color:#fff; border-color:#8c57ff; }

  /* Inline status changer */
  .status-badge-btn {
    cursor: pointer;
    user-select: none;
    border: none;
    background: none;
    padding: 0;
    display: inline-flex;
    align-items: center;
    gap: 4px;
  }
  .status-badge-btn .badge { transition: opacity .15s; }
  .status-badge-btn:hover .badge { opacity: .75; }
  .status-badge-btn .caret-icon { font-size: .65rem; opacity: .6; }

  .status-popover {
    display: none;
    position: absolute;
    z-index: 1080;
    background: #fff;
    border: 1px solid #e0e3e7;
    border-radius: 10px;
    box-shadow: 0 6px 24px rgba(0,0,0,.13);
    min-width: 160px;
    padding: 6px 0;
  }
  .status-popover.open { display: block; }
  .status-popover-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 7px 14px;
    font-size: .82rem;
    font-weight: 600;
    cursor: pointer;
    transition: background .1s;
    white-space: nowrap;
  }
  .status-popover-item:hover { background: #f5f5f9; }
  .status-popover-item.current { opacity: .45; cursor: default; pointer-events: none; }
  .status-popover-item .dot {
    width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0;
  }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="hero-bar">
    <div>
      <h4><i class="bx bx-file me-2"></i>Quotations</h4>
      <p>Manage all CCTV quotations</p>
    </div>
    <a href="{{ route('admin.cctv.quotations.create') }}" class="btn btn-light btn-sm fw-600">
      <i class="bx bx-plus me-1"></i> New Quotation
    </a>
  </div>

  <div class="stat-grid">
    <div class="stat-card sc-blue"><div class="stat-icon"><i class="bx bx-file"></i></div><div><div class="stat-num">{{ $stats['total'] ?? 0 }}</div><div class="stat-lbl">Total</div></div></div>
    <div class="stat-card sc-orange"><div class="stat-icon"><i class="bx bx-time"></i></div><div><div class="stat-num">{{ $stats['sent'] ?? 0 }}</div><div class="stat-lbl">Sent</div></div></div>
    <div class="stat-card sc-green"><div class="stat-icon"><i class="bx bx-check-circle"></i></div><div><div class="stat-num">{{ $stats['approved'] ?? 0 }}</div><div class="stat-lbl">Approved</div></div></div>
    <div class="stat-card sc-red"><div class="stat-icon"><i class="bx bx-x-circle"></i></div><div><div class="stat-num">{{ $stats['rejected'] ?? 0 }}</div><div class="stat-lbl">Rejected</div></div></div>
  </div>

  <div class="card border-0 shadow-sm mb-3">
    <div class="card-body pb-2">
      <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <div class="filter-tabs">
          <a href="{{ route('admin.cctv.quotations.index') }}" class="filter-tab {{ !request('status') ? 'active' : '' }}">All</a>
          @foreach(['draft','sent','approved','rejected','expired'] as $s)
            <a href="{{ route('admin.cctv.quotations.index', ['status'=>$s]) }}" class="filter-tab {{ request('status')===$s ? 'active' : '' }}">{{ ucfirst($s) }}</a>
          @endforeach
        </div>
        <form method="GET" class="d-flex gap-2">
          @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
          <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Search…" style="width:200px">
          <button class="btn btn-primary btn-sm"><i class="bx bx-search"></i></button>
        </form>
      </div>
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Quotation No</th>
            <th>Customer</th>
            <th>Mobile</th>
            <th>Total (Rs.)</th>
            <th>Status</th>
            <th>Valid Until</th>
            <th>Date</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($quotations as $q)
          <tr>
            <td><span class="fw-700 text-primary font-monospace">{{ $q->quotation_no }}</span></td>
            <td><div class="fw-600">{{ $q->customer_name }}</div></td>
            <td class="font-monospace small">{{ $q->mobile }}</td>
            <td class="fw-600">{{ number_format($q->total_amount, 2) }}</td>
            <td style="position:relative;">
              @php $sc = ['draft'=>'secondary','sent'=>'info','approved'=>'success','rejected'=>'danger','expired'=>'warning'][strtolower($q->status)] ?? 'secondary' @endphp
              <button type="button"
                class="status-badge-btn"
                data-id="{{ $q->id }}"
                data-current="{{ strtolower($q->status) }}"
                data-url="{{ route('admin.cctv.quotations.updateStatus', $q) }}"
                title="Click to change status">
                <span class="badge bg-label-{{ $sc }}">{{ ucfirst($q->status) }}</span>
                <span class="caret-icon"><i class="bx bx-chevron-down"></i></span>
              </button>
              <div class="status-popover" id="sp-{{ $q->id }}">
                @php
                  $statusOptions = [
                    'draft'       => ['label'=>'Draft',       'color'=>'#8592a3'],
                    'sent'        => ['label'=>'Sent',        'color'=>'#00cfe8'],
                    'approved'    => ['label'=>'Approved',    'color'=>'#28c76f'],
                    'rejected'    => ['label'=>'Rejected',    'color'=>'#ea5455'],
                    'postponed'   => ['label'=>'Postponed',   'color'=>'#ff9f43'],
                    'rescheduled' => ['label'=>'Rescheduled', 'color'=>'#7367f0'],
                  ];
                @endphp
                @foreach($statusOptions as $val => $opt)
                  <div class="status-popover-item {{ strtolower($q->status) === $val ? 'current' : '' }}"
                       data-value="{{ ucfirst($val) }}">
                    <span class="dot" style="background:{{ $opt['color'] }};"></span>
                    {{ $opt['label'] }}
                    @if(strtolower($q->status) === $val)
                      <i class="bx bx-check ms-auto text-success"></i>
                    @endif
                  </div>
                @endforeach
              </div>
            </td>
            <td>{{ $q->valid_until ? \Carbon\Carbon::parse($q->valid_until)->format('d M Y') : '—' }}</td>
            <td>{{ $q->created_at->format('d M Y') }}</td>
            <td class="text-end d-flex gap-1 justify-content-end">
              <a href="{{ route('admin.cctv.quotations.show', $q) }}" class="btn btn-sm btn-outline-primary py-1 px-2"><i class="bx bx-show"></i></a>
              <a href="{{ route('admin.cctv.quotations.edit', $q) }}" class="btn btn-sm btn-outline-secondary py-1 px-2"><i class="bx bx-edit"></i></a>
              <a href="{{ route('admin.cctv.quotations.pdf', $q) }}" target="_blank" class="btn btn-sm btn-outline-danger py-1 px-2"><i class="bx bx-file-pdf"></i></a>
            </td>
          </tr>
          @empty
          <tr><td colspan="8" class="text-center text-muted py-4">No quotations found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($quotations->hasPages())
    <div class="card-footer bg-transparent d-flex justify-content-end">{{ $quotations->withQueryString()->links() }}</div>
    @endif
  </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
  let activePopover = null;

  // Toggle popover on badge click
  document.querySelectorAll('.status-badge-btn').forEach(btn => {
    btn.addEventListener('click', function (e) {
      e.stopPropagation();
      const id  = this.dataset.id;
      const pop = document.getElementById('sp-' + id);

      if (activePopover && activePopover !== pop) {
        activePopover.classList.remove('open');
      }

      pop.classList.toggle('open');
      activePopover = pop.classList.contains('open') ? pop : null;

      if (pop.classList.contains('open')) {
        pop.style.top  = (this.offsetHeight + 4) + 'px';
        pop.style.left = '0px';
      }
    });
  });

  // Click outside to close
  document.addEventListener('click', () => {
    document.querySelectorAll('.status-popover.open').forEach(p => p.classList.remove('open'));
    activePopover = null;
  });

  // Handle option click → PATCH via fetch
  document.querySelectorAll('.status-popover-item').forEach(item => {
    item.addEventListener('click', function (e) {
      e.stopPropagation();
      if (this.classList.contains('current')) return;

      const popover   = this.closest('.status-popover');
      const id        = popover.id.replace('sp-', '');
      const btn       = document.querySelector(`.status-badge-btn[data-id="${id}"]`);
      const url       = btn.dataset.url;
      const newStatus = this.dataset.value;

      const colorMap = {
        'Draft':       'secondary',
        'Sent':        'info',
        'Approved':    'success',
        'Rejected':    'danger',
        'Postponed':   'warning',
        'Rescheduled': 'primary',
      };

      // Optimistic UI
      const badge = btn.querySelector('.badge');
      badge.className = `badge bg-label-${colorMap[newStatus] || 'secondary'}`;
      badge.textContent = newStatus;
      btn.dataset.current = newStatus.toLowerCase();

      // Update checkmark
      popover.querySelectorAll('.status-popover-item').forEach(i => {
        i.classList.remove('current');
        const chk = i.querySelector('.bx-check');
        if (chk) chk.remove();
        if (i.dataset.value === newStatus) {
          i.classList.add('current');
          i.insertAdjacentHTML('beforeend', '<i class="bx bx-check ms-auto text-success"></i>');
        }
      });

      popover.classList.remove('open');
      activePopover = null;

      // PATCH request
      fetch(url, {
        method: 'PATCH',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': (typeof csrfToken !== 'undefined' ? csrfToken : document.querySelector('meta[name="csrf-token"]').content),
        },
        body: JSON.stringify({ status: newStatus }),
      }).then(res => {
        if (!res.ok) {
          alert('Failed to update status. Please try again.');
          location.reload();
        }
      }).catch(() => {
        alert('Network error. Please try again.');
        location.reload();
      });
    });
  });
})();
</script>
@endpush
