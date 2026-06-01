@extends('layouts.admin')
@section('title', 'Invoices')
@section('page-title', 'Invoice Management')
@section('breadcrumb')<li class="breadcrumb-item active">Invoices</li>@endsection

@push('styles')
<style>
.inv-hero {
  background: linear-gradient(135deg, #696cff 0%, #8c57ff 60%, #a855f7 100%);
  border-radius: 18px;
  padding: 32px 36px;
  color: #fff;
  margin-bottom: 28px;
  position: relative;
  overflow: hidden;
}
.inv-hero::after {
  content: '\f157';
  font-family: 'boxicons';
  position: absolute;
  right: 36px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 120px;
  opacity: .08;
  line-height: 1;
}
.inv-hero h1 { font-size: 1.8rem; font-weight: 800; margin: 0 0 6px; }
.inv-hero p  { margin: 0; opacity: .8; font-size: .92rem; }
.search-wrap { position: relative; }
.search-wrap .search-icon { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #696cff; font-size: 1.2rem; }
.search-wrap input { padding-left: 46px; border-radius: 12px; height: 50px; font-size: 1rem; border: 2px solid #e7e7ff; }
.search-wrap input:focus { border-color: #696cff; box-shadow: 0 0 0 3px rgba(105,108,255,.15); }
.result-card {
  background: #fff;
  border-radius: 14px;
  border: 1px solid #f0f0ff;
  padding: 16px 20px;
  display: flex; align-items: center; gap: 16px;
  transition: all .2s;
  cursor: pointer;
  text-decoration: none;
  color: inherit;
}
.result-card:hover { border-color: #696cff; box-shadow: 0 4px 18px rgba(105,108,255,.15); transform: translateY(-1px); }
.result-icon {
  width: 48px; height: 48px; border-radius: 12px;
  background: linear-gradient(135deg, #696cff22, #a855f722);
  display: flex; align-items: center; justify-content: center;
  font-size: 1.4rem; color: #696cff; flex-shrink: 0;
}
.result-icon.field-icon {
  background: linear-gradient(135deg, #E8490F22, #ff6b3522);
  color: #E8490F;
}
.result-order { font-weight: 800; font-size: .95rem; color: #333; }
.result-customer { font-size: .82rem; color: #555; margin-top: 2px; }
.result-meta { font-size: .75rem; color: #aaa; margin-top: 4px; display:flex; gap: 12px; flex-wrap: wrap; }
.result-meta span { display:flex; align-items:center; gap: 4px; }
.result-right { margin-left: auto; text-align: right; flex-shrink: 0; }
.result-amount { font-size: 1.1rem; font-weight: 800; color: #696cff; }
.result-status { display: inline-block; margin-top: 4px; }
.badge-paid   { background: #d1fae5; color: #065f46; border-radius: 20px; padding: 3px 10px; font-size: .72rem; font-weight: 700; }
.badge-unpaid { background: #fee2e2; color: #991b1b; border-radius: 20px; padding: 3px 10px; font-size: .72rem; font-weight: 700; }
.badge-partial{ background: #fef3c7; color: #92400e; border-radius: 20px; padding: 3px 10px; font-size: .72rem; font-weight: 700; }
.badge-type-job   { background: #ede9fe; color: #5b21b6; border-radius: 20px; padding: 2px 8px; font-size: .68rem; font-weight: 700; margin-left: 6px; }
.badge-type-field { background: #fff0eb; color: #E8490F; border-radius: 20px; padding: 2px 8px; font-size: .68rem; font-weight: 700; margin-left: 6px; }
.empty-state { text-align: center; padding: 60px 20px; color: #aaa; }
.empty-state i { font-size: 4rem; display: block; margin-bottom: 14px; color: #d0d0ff; }
.quick-filter-btn { border-radius: 20px; font-size: .78rem; font-weight: 600; padding: 5px 14px; border: 1.5px solid #e0e0f5; background: #fff; color: #555; cursor: pointer; transition: all .15s; text-decoration: none; display: inline-block; }
.quick-filter-btn:hover, .quick-filter-btn.active { background: #696cff; border-color: #696cff; color: #fff; }
.quick-filter-btn.active-field { background: #E8490F; border-color: #E8490F; color: #fff; }
.type-tabs { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 4px; }
</style>
@endpush

@section('content')
<div class="inv-hero">
  <h1><i class='bx bx-receipt' style="vertical-align:middle;margin-right:10px;font-size:1.6rem"></i>Invoices</h1>
  <p>Job Orders &amp; Field Services — search by Order No., Invoice No., Phone, Customer Name, or Device</p>
</div>

{{-- Search Bar --}}
<div class="card" style="border-radius:16px;border:0;box-shadow:0 2px 16px rgba(0,0,0,.07);margin-bottom:24px">
  <div class="card-body p-4">
    <form method="GET" action="{{ route('admin.invoices.index') }}" id="searchForm">
      <input type="hidden" name="type" value="{{ $type }}">
      <div class="search-wrap">
        <i class='bx bx-search search-icon'></i>
        <input type="text" name="q" id="searchInput" value="{{ $query }}"
               class="form-control"
               placeholder="Search: order no., invoice no., phone, customer name, device…"
               autocomplete="off" autofocus>
      </div>
      <div class="d-flex flex-wrap gap-2 mt-3 align-items-center">
        <span style="font-size:.78rem;color:#aaa;font-weight:600">QUICK:</span>
        @foreach(['Pending','In Progress','Completed'] as $s)
          <button type="button" class="quick-filter-btn" onclick="appendSearch('{{ $s }}')">{{ $s }}</button>
        @endforeach
        <button type="button" class="quick-filter-btn" onclick="appendSearch('unpaid')">Unpaid</button>
        <div class="ms-auto">
          <button type="submit" class="btn btn-primary" style="border-radius:10px;padding:8px 24px">
            <i class='bx bx-search me-1'></i> Search
          </button>
          @if($query)
          <a href="{{ route('admin.invoices.index', ['type'=>$type]) }}" class="btn btn-outline-secondary ms-2" style="border-radius:10px">Clear</a>
          @endif
        </div>
      </div>
    </form>
  </div>
</div>

{{-- Results Meta --}}
<div id="resultsMeta" class="d-flex align-items-center justify-content-between mb-3">
@if($query !== '')
  <div style="font-size:.82rem;color:#888">
    <strong style="color:#333">{{ $results->count() }}</strong> result{{ $results->count() !== 1 ? 's' : '' }} for <strong>"{{ $query }}"</strong>
  </div>
@endif
</div>

{{-- Search Results (swapped live by JS) --}}
<div id="resultsArea">
@if($query !== '')

  @if($results->isEmpty())
    <div class="card" style="border-radius:16px;border:0;box-shadow:0 2px 12px rgba(0,0,0,.06)">
      <div class="empty-state">
        <i class='bx bx-search-alt'></i>
        <div style="font-size:1.1rem;font-weight:700;color:#555;margin-bottom:8px">No records found</div>
        <p style="font-size:.85rem">Try a different order no., phone or customer name</p>
      </div>
    </div>
  @else
    <div class="d-flex flex-column gap-2">
      @foreach($results as $inv)
        @php
          $isField   = $inv['type'] === 'field';
          $payStatus = $inv['pay_status'];
          $statusColors = ['Pending'=>'warning','In Progress'=>'info','Completed'=>'success','Not Completed'=>'danger','Cancelled'=>'secondary'];
        @endphp
        <a href="{{ $inv['url'] }}" class="result-card">
          <div class="result-icon {{ $isField ? 'field-icon' : '' }}">
            <i class='bx {{ $isField ? "bx-map-pin" : "bx-file-blank" }}'></i>
          </div>
          <div style="flex:1;min-width:0">
            <div class="result-order">
              {{ $inv['order_no'] }}
              <span class="badge-type-{{ $inv['type'] }}">{{ $isField ? 'Field' : 'Job' }}</span>
              @if($inv['invoice_no'])
                <span style="font-size:.75rem;font-weight:500;color:#aaa;margin-left:8px">{{ $inv['invoice_no'] }}</span>
              @else
                <span style="font-size:.73rem;color:#d0a020;margin-left:8px;font-weight:600">No Invoice Yet</span>
              @endif
            </div>
            <div class="result-customer">{{ $inv['customer_name'] }}</div>
            <div class="result-meta">
              <span><i class='bx bx-phone'></i> {{ $inv['phone_no'] }}</span>
              @if($inv['customer_nic'])
                <span><i class='bx bx-id-card'></i> {{ $inv['customer_nic'] }}</span>
              @endif
              @if($inv['device'])
                <span><i class='bx bx-chip'></i> {{ $inv['device'] }}</span>
              @endif
              <span><i class='bx bx-calendar'></i> {{ $inv['date'] ?? '—' }}</span>
            </div>
          </div>
          <div class="result-right">
            <div class="result-amount">Rs. {{ number_format($inv['grand_total'], 2) }}</div>
            <div class="result-status">
              @if($payStatus === 'paid')
                <span class="badge-paid">✓ Paid</span>
              @elseif($payStatus === 'partial')
                <span class="badge-partial">⚡ Partial · Rs.{{ number_format($inv['balance'],2) }} due</span>
              @else
                <span class="badge-unpaid">● Unpaid</span>
              @endif
            </div>
            <div style="margin-top:6px">
              <span class="badge bg-label-{{ $statusColors[$inv['status']] ?? 'secondary' }}" style="font-size:.72rem">{{ $inv['status'] }}</span>
            </div>
          </div>
          <div style="flex-shrink:0;color:#696cff"><i class='bx bx-chevron-right' style="font-size:1.4rem"></i></div>
        </a>
      @endforeach
    </div>
  @endif

@endif
</div>{{-- #resultsArea --}}

{{-- Stats --}}
<div class="row g-3 mb-4">
  <div class="col-6 col-xl-3">
    <div class="card text-center p-3" style="border-radius:14px;border:0;box-shadow:0 2px 12px rgba(0,0,0,.07)">
      <div style="font-size:2rem;font-weight:800;color:#696cff">{{ $stats['total'] }}</div>
      <div style="font-size:.78rem;color:#888;font-weight:600;text-transform:uppercase;letter-spacing:.06em">Pending Payment</div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="card text-center p-3" style="border-radius:14px;border:0;box-shadow:0 2px 12px rgba(0,0,0,.07)">
      <div style="font-size:2rem;font-weight:800;color:#71dd37">{{ $stats['paid'] }}</div>
      <div style="font-size:.78rem;color:#888;font-weight:600;text-transform:uppercase;letter-spacing:.06em">Paid</div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="card text-center p-3" style="border-radius:14px;border:0;box-shadow:0 2px 12px rgba(0,0,0,.07)">
      <div style="font-size:2rem;font-weight:800;color:#ff3e1d">{{ $stats['unpaid'] }}</div>
      <div style="font-size:.78rem;color:#888;font-weight:600;text-transform:uppercase;letter-spacing:.06em">Unpaid</div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="card text-center p-3" style="border-radius:14px;border:0;box-shadow:0 2px 12px rgba(0,0,0,.07)">
      <div style="font-size:1.4rem;font-weight:800;color:#696cff">Rs. {{ number_format($stats['revenue'],0) }}</div>
      <div style="font-size:.78rem;color:#888;font-weight:600;text-transform:uppercase;letter-spacing:.06em">Total Collected</div>
    </div>
  </div>
</div>

{{-- Full list (shown when no search) --}}
@if($query === '')
<div class="card" style="border-radius:16px;border:0;box-shadow:0 2px 12px rgba(0,0,0,.07)">
  <div class="card-body p-0">

    {{-- Tab bar: Pay filter + Type filter --}}
    <div class="px-4 pt-4 pb-2">
      {{-- Payment filter --}}
      <div class="d-flex align-items-center gap-1 flex-wrap mb-2">
        @foreach(['all'=>'All (Unpaid)','paid'=>'Paid','unpaid'=>'Unpaid','partial'=>'Partial'] as $val=>$label)
          <a href="{{ route('admin.invoices.index', ['filter'=>$val,'type'=>$type]) }}"
             class="quick-filter-btn {{ $filter===$val ? 'active':'' }}">
            {{ $label }}
            @if($val==='unpaid' && $stats['unpaid']>0)
              <span style="background:#ff3e1d;color:#fff;border-radius:10px;padding:0 6px;font-size:.68rem;margin-left:4px">{{ $stats['unpaid'] }}</span>
            @endif
            @if($val==='partial' && $stats['partial']>0)
              <span style="background:#f59e0b;color:#fff;border-radius:10px;padding:0 6px;font-size:.68rem;margin-left:4px">{{ $stats['partial'] }}</span>
            @endif
          </a>
        @endforeach
        <span style="margin-left:auto;font-size:.78rem;color:#aaa">{{ $allInvoices->total() }} records</span>
      </div>

      {{-- Type filter --}}
      <div class="type-tabs">
        <a href="{{ route('admin.invoices.index', ['filter'=>$filter,'type'=>'all']) }}"
           class="quick-filter-btn {{ $type==='all' ? 'active':'' }}">
          <i class='bx bx-list-ul me-1'></i> All Types
        </a>
        <a href="{{ route('admin.invoices.index', ['filter'=>$filter,'type'=>'job']) }}"
           class="quick-filter-btn {{ $type==='job' ? 'active':'' }}" style="{{ $type==='job' ? '' : 'border-color:#ede9fe;color:#5b21b6' }}">
          <i class='bx bx-file-blank me-1'></i> Job Orders
        </a>
        <a href="{{ route('admin.invoices.index', ['filter'=>$filter,'type'=>'field']) }}"
           class="quick-filter-btn {{ $type==='field' ? 'active-field':'' }}" style="{{ $type==='field' ? '' : 'border-color:#fff0eb;color:#E8490F' }}">
          <i class='bx bx-map-pin me-1'></i> Field Services
        </a>
      </div>
    </div>

    {{-- List --}}
    <div class="px-4 pb-4">
      @if($allInvoices->isEmpty())
        <div class="empty-state">
          <i class='bx bx-file-blank'></i>
          <div style="font-size:1.1rem;font-weight:700;color:#555;margin-bottom:8px">No invoices found</div>
        </div>
      @else
        <div class="d-flex flex-column gap-2">
          @foreach($allInvoices as $inv)
            @php
              $isField   = $inv['type'] === 'field';
              $payStatus = $inv['pay_status'];
              $statusColors = ['Pending'=>'warning','In Progress'=>'info','Completed'=>'success','Not Completed'=>'danger','Cancelled'=>'secondary'];
            @endphp
            <a href="{{ $inv['url'] }}" class="result-card">
              <div class="result-icon {{ $isField ? 'field-icon' : '' }}">
                <i class='bx {{ $isField ? "bx-map-pin" : "bx-file-blank" }}'></i>
              </div>
              <div style="flex:1;min-width:0">
                <div class="result-order">
                  {{ $inv['order_no'] }}
                  <span class="badge-type-{{ $inv['type'] }}">{{ $isField ? 'Field' : 'Job' }}</span>
                  @if($inv['invoice_no'])
                    <span style="font-size:.75rem;font-weight:500;color:#aaa;margin-left:8px">{{ $inv['invoice_no'] }}</span>
                  @else
                    <span style="font-size:.73rem;color:#d0a020;margin-left:8px;font-weight:600">No Invoice</span>
                  @endif
                </div>
                <div class="result-customer">{{ $inv['customer_name'] }}</div>
                <div class="result-meta">
                  <span><i class='bx bx-phone'></i> {{ $inv['phone_no'] }}</span>
                  @if($inv['customer_nic'])<span><i class='bx bx-id-card'></i> {{ $inv['customer_nic'] }}</span>@endif
                  @if($inv['device'])<span><i class='bx bx-chip'></i> {{ $inv['device'] }}</span>@endif
                  <span><i class='bx bx-calendar'></i> {{ $inv['date'] ?? '—' }}</span>
                </div>
              </div>
              <div class="result-right">
                <div class="result-amount">Rs. {{ number_format($inv['grand_total'], 2) }}</div>
                <div class="result-status">
                  @if($payStatus === 'paid')
                    <span class="badge-paid">✓ Paid</span>
                  @elseif($payStatus === 'partial')
                    <span class="badge-partial">⚡ Partial · Rs.{{ number_format($inv['balance'],2) }} due</span>
                  @else
                    <span class="badge-unpaid">● Unpaid</span>
                  @endif
                </div>
                <div style="margin-top:6px">
                  <span class="badge bg-label-{{ $statusColors[$inv['status']] ?? 'secondary' }}" style="font-size:.72rem">{{ $inv['status'] }}</span>
                </div>
              </div>
              <div style="flex-shrink:0;color:#696cff"><i class='bx bx-chevron-right' style="font-size:1.4rem"></i></div>
            </a>
          @endforeach
        </div>

        {{-- Pagination --}}
        @if($allInvoices->hasPages())
          <div class="d-flex justify-content-center mt-4">
            {{ $allInvoices->appends(['filter'=>$filter,'type'=>$type])->links() }}
          </div>
        @endif
      @endif
    </div>
  </div>
</div>
@endif
@endsection

@push('scripts')
<script>
let searchTimer = null;
const searchInput = document.getElementById('searchInput');
const resultsArea = document.getElementById('resultsArea');

searchInput.addEventListener('input', function() {
  clearTimeout(searchTimer);
  const q = this.value.trim();
  if (q.length === 0) {
    resultsArea.innerHTML = '';
    updateResultsMeta('', 0);
    return;
  }
  if (q.length < 2) return;
  searchTimer = setTimeout(() => doSearch(q), 280);
});

searchInput.addEventListener('keydown', function(e) {
  if (e.key === 'Enter') {
    e.preventDefault();
    clearTimeout(searchTimer);
    doSearch(this.value.trim());
  }
  if (e.key === 'Escape') {
    this.value = '';
    resultsArea.innerHTML = '';
    updateResultsMeta('', -1);
  }
});

function appendSearch(val) {
  searchInput.value = val;
  searchInput.focus();
  doSearch(val);
}

function updateResultsMeta(q, count) {
  const meta = document.getElementById('resultsMeta');
  if (!meta) return;
  if (count < 0 || q === '') { meta.innerHTML = ''; return; }
  meta.innerHTML = `<div style="font-size:.82rem;color:#888"><strong style="color:#333">${count}</strong> result${count !== 1 ? 's' : ''} for <strong>"${escHtml(q)}"</strong></div>`;
}

function escHtml(s) {
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

async function doSearch(q) {
  if (!q) return;
  resultsArea.innerHTML = `
    <div class="text-center py-4" style="color:#aaa">
      <div class="spinner-border spinner-border-sm text-primary me-2"></div> Searching…
    </div>`;
  updateResultsMeta(q, '…');

  try {
    const resp = await fetch(`{{ route('admin.invoices.search') }}?q=${encodeURIComponent(q)}`, {
      headers: {'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}
    });
    const data = await resp.json();
    updateResultsMeta(q, data.count);
    renderResults(data.results);
  } catch(e) {
    resultsArea.innerHTML = '<div class="text-center py-4 text-danger">Search failed. Try again.</div>';
  }
}

function typeBadge(type) {
  if (type === 'field') return `<span style="background:#fff0eb;color:#E8490F;border-radius:20px;padding:2px 8px;font-size:.68rem;font-weight:700;margin-left:6px">Field</span>`;
  return `<span style="background:#ede9fe;color:#5b21b6;border-radius:20px;padding:2px 8px;font-size:.68rem;font-weight:700;margin-left:6px">Job</span>`;
}

function renderResults(results) {
  if (!results.length) {
    resultsArea.innerHTML = `
      <div class="card" style="border-radius:16px;border:0;box-shadow:0 2px 12px rgba(0,0,0,.06)">
        <div class="empty-state">
          <i class='bx bx-search-alt'></i>
          <div style="font-size:1.1rem;font-weight:700;color:#555;margin-bottom:8px">No records found</div>
          <p style="font-size:.85rem">Try a different order no., phone or customer name</p>
        </div>
      </div>`;
    return;
  }

  let html = '<div class="d-flex flex-column gap-2">';
  results.forEach(inv => {
    const isField  = inv.type === 'field';
    const iconCls  = isField ? 'field-icon' : '';
    const iconName = isField ? 'bx-map-pin' : 'bx-file-blank';
    const payClass = inv.pay_status === 'paid' ? 'badge-paid' : inv.pay_status === 'partial' ? 'badge-partial' : 'badge-unpaid';
    const payLabel = inv.pay_status === 'paid' ? '✓ Paid'
                   : inv.pay_status === 'partial' ? `⚡ Partial · Rs.${inv.balance} due`
                   : '● Unpaid';
    const scMap = {Pending:'warning','In Progress':'info',Completed:'success','Not Completed':'danger'};
    const sc = scMap[inv.status] || 'secondary';
    const invBadge = inv.invoice_no
      ? `<span style="font-size:.75rem;font-weight:500;color:#aaa;margin-left:8px">${escHtml(inv.invoice_no)}</span>`
      : `<span style="font-size:.73rem;color:#d0a020;margin-left:8px;font-weight:600">No Invoice Yet</span>`;
    const nicSpan    = inv.customer_nic ? `<span><i class='bx bx-id-card'></i> ${escHtml(inv.customer_nic)}</span>` : '';
    const deviceSpan = inv.device       ? `<span><i class='bx bx-chip'></i> ${escHtml(inv.device)}</span>` : '';

    html += `
      <a href="${escHtml(inv.url)}" class="result-card">
        <div class="result-icon ${iconCls}"><i class='bx ${iconName}'></i></div>
        <div style="flex:1;min-width:0">
          <div class="result-order">${escHtml(inv.order_no)} ${typeBadge(inv.type)} ${invBadge}</div>
          <div class="result-customer">${escHtml(inv.customer_name)}</div>
          <div class="result-meta">
            <span><i class='bx bx-phone'></i> ${escHtml(inv.phone_no)}</span>
            ${nicSpan}
            ${deviceSpan}
            <span><i class='bx bx-calendar'></i> ${escHtml(inv.date ?? '—')}</span>
          </div>
        </div>
        <div class="result-right">
          <div class="result-amount">Rs. ${inv.grand_total}</div>
          <div class="result-status"><span class="${payClass}">${payLabel}</span></div>
          <div style="margin-top:6px"><span class="badge bg-label-${sc}" style="font-size:.72rem">${escHtml(inv.status)}</span></div>
        </div>
        <div style="flex-shrink:0;color:#696cff"><i class='bx bx-chevron-right' style="font-size:1.4rem"></i></div>
      </a>`;
  });
  html += '</div>';
  resultsArea.innerHTML = html;
}
</script>
@endpush
