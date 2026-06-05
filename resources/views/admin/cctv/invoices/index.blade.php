@extends('layouts.admin')
@section('title', 'CCTV Invoices')

@push('styles')
<style>
  .hero-bar { background:linear-gradient(135deg,#28c76f,#20a255); border-radius:16px; padding:1.25rem 1.75rem; color:#fff; margin-bottom:1.5rem; display:flex; align-items:center; gap:1rem; flex-wrap:wrap; }
  .hero-bar h4 { margin:0; font-size:1.2rem; font-weight:700; }
  .tab-pill { padding:6px 18px; border-radius:20px; font-size:.82rem; font-weight:600; border:0; background:#f0f0f0; color:#697a8d; cursor:pointer; text-decoration:none; transition:all .15s; }
  .tab-pill.active, .tab-pill:hover { background:#28c76f; color:#fff; }
  .stat-card { border-radius:12px; border:0; box-shadow:0 2px 8px rgba(0,0,0,.06); }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  <div class="hero-bar">
    <div class="flex-grow-1">
      <h4><i class="bx bx-receipt me-2"></i> CCTV Invoices</h4>
      <div style="opacity:.85;font-size:.85rem;">Manage and track all customer invoices</div>
    </div>
    <a href="{{ route('admin.cctv.invoices.create') }}" class="btn btn-light btn-sm">
      <i class="bx bx-plus me-1"></i> New Invoice
    </a>
  </div>

  @if(session('success'))
  <div class="alert alert-success alert-dismissible fade show mb-3">
    <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif

  {{-- Stats row --}}
  <div class="row g-3 mb-3">
    @foreach([['label'=>'Total','count'=>$counts['all'],'color'=>'primary','icon'=>'bx-receipt'],['label'=>'Unpaid','count'=>$counts['unpaid'],'color'=>'danger','icon'=>'bx-error-circle'],['label'=>'Partial','count'=>$counts['partial'],'color'=>'warning','icon'=>'bx-time'],['label'=>'Paid','count'=>$counts['paid'],'color'=>'success','icon'=>'bx-check-circle']] as $stat)
    <div class="col-6 col-md-3">
      <div class="card stat-card">
        <div class="card-body d-flex align-items-center gap-3 p-3">
          <div style="width:42px;height:42px;border-radius:10px;background:var(--bs-{{ $stat['color'] }}-light, #e8f9ef);display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:var(--bs-{{ $stat['color'] }});">
            <i class="bx {{ $stat['icon'] }}"></i>
          </div>
          <div>
            <div style="font-size:1.4rem;font-weight:700;line-height:1.1;">{{ $stat['count'] }}</div>
            <div style="font-size:.75rem;color:#a1acb8;">{{ $stat['label'] }}</div>
          </div>
        </div>
      </div>
    </div>
    @endforeach
  </div>

  {{-- Search + Tabs --}}
  <div class="card mb-3" style="border-radius:12px;border:0;box-shadow:0 2px 8px rgba(0,0,0,.06);">
    <div class="card-body d-flex flex-wrap gap-2 align-items-center py-2">
      <form method="GET" class="d-flex gap-2 flex-grow-1">
        <input type="hidden" name="tab" value="{{ $tab }}">
        <input type="text" name="q" value="{{ $search }}" class="form-control form-control-sm" placeholder="Search name or invoice no…" style="max-width:280px;">
        <button class="btn btn-sm btn-primary"><i class="bx bx-search"></i></button>
      </form>
      <div class="d-flex gap-2 flex-wrap">
        @foreach(['all'=>'All','unpaid'=>'Unpaid','partial'=>'Partial','paid'=>'Paid'] as $key=>$label)
          <a href="{{ route('admin.cctv.invoices.index', ['tab'=>$key,'q'=>$search]) }}" class="tab-pill {{ $tab===$key?'active':'' }}">{{ $label }}</a>
        @endforeach
      </div>
    </div>
  </div>

  {{-- Table --}}
  <div class="card" style="border-radius:14px;border:0;box-shadow:0 2px 12px rgba(0,0,0,.07);">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Invoice No</th>
            <th>Customer</th>
            <th>Mobile</th>
            <th>Date</th>
            <th class="text-end">Total</th>
            <th class="text-end">Paid</th>
            <th class="text-end">Balance</th>
            <th>Status</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @forelse($invoices as $inv)
          @php
            $balance = max(0, ($inv->grand_total ?? 0) - ($inv->paid_amount ?? 0));
            $sc = ['Unpaid'=>'danger','Partial'=>'warning','Paid'=>'success'][$inv->status] ?? 'secondary';
          @endphp
          <tr>
            <td class="font-monospace fw-600 text-primary">{{ $inv->invoice_no }}</td>
            <td>{{ $inv->customer_name }}</td>
            <td class="font-monospace small">{{ $inv->mobile ?? '—' }}</td>
            <td class="small">{{ $inv->invoice_date ? \Carbon\Carbon::parse($inv->invoice_date)->format('d M Y') : '—' }}</td>
            <td class="text-end fw-600">Rs. {{ number_format($inv->grand_total ?? 0, 2) }}</td>
            <td class="text-end text-success">Rs. {{ number_format($inv->paid_amount ?? 0, 2) }}</td>
            <td class="text-end {{ $balance > 0 ? 'text-danger' : 'text-muted' }}">Rs. {{ number_format($balance, 2) }}</td>
            <td><span class="badge bg-label-{{ $sc }}">{{ $inv->status }}</span></td>
            <td>
              <a href="{{ route('admin.cctv.invoices.show', $inv) }}" class="btn btn-sm btn-outline-primary py-0 px-2">View</a>
            </td>
          </tr>
          @empty
          <tr><td colspan="9" class="text-center text-muted py-4">No invoices found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if(method_exists($invoices, 'hasPages') && $invoices->hasPages())
    <div class="card-footer">{{ $invoices->links() }}</div>
    @endif
  </div>

</div>
@endsection
