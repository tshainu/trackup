@extends('layouts.admin')
@section('title', 'Order Management')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  <div class="d-flex align-items-center justify-content-between mb-4">
    <div>
      <h4 class="fw-bold mb-0"><i class="bx bx-search-alt me-2 text-primary"></i>Order Management</h4>
      <small class="text-muted">Search orders by customer name, phone number, or reference number</small>
    </div>
  </div>

  {{-- Search Bar --}}
  <div class="card mb-4 shadow-sm">
    <div class="card-body py-3">
      <div class="input-group input-group-lg">
        <span class="input-group-text bg-white border-end-0">
          <i class="bx bx-search fs-5 text-muted" id="searchIcon"></i>
        </span>
        <input
          type="text"
          id="searchInput"
          class="form-control border-start-0 ps-0 fs-6"
          placeholder="Search by customer name, phone, or reference number (LED-, SRV-, QT-, PRJ-, INV-)…"
          value="{{ $q }}"
          autocomplete="off"
          autofocus
        >
        <button type="button" id="clearBtn" class="btn btn-outline-secondary {{ $q ? '' : 'd-none' }}">
          <i class="bx bx-x"></i>
        </button>
      </div>
      <div class="mt-2 d-flex gap-2 flex-wrap">
        <small class="text-muted"><i class="bx bx-info-circle me-1"></i>Search across:</small>
        <span class="badge bg-label-primary rounded-pill">Leads (LED-)</span>
        <span class="badge bg-label-info rounded-pill">Surveys (SRV-)</span>
        <span class="badge bg-label-warning rounded-pill">Estimations (QT-)</span>
        <span class="badge bg-label-success rounded-pill">Projects (PRJ-)</span>
        <span class="badge bg-label-danger rounded-pill">Invoices (INV-)</span>
      </div>
    </div>
  </div>

  {{-- Results container --}}
  <div id="resultsContainer">
    @include('admin.cctv.order-management._results', ['q' => $q, 'results' => $results])
  </div>

</div>

<script>
(function () {
  const input     = document.getElementById('searchInput');
  const container = document.getElementById('resultsContainer');
  const clearBtn  = document.getElementById('clearBtn');
  const icon      = document.getElementById('searchIcon');
  const url       = '{{ route('admin.cctv.order-management.index') }}';
  let timer       = null;
  let lastQ       = '{{ $q }}';
  let controller  = null;

  function setLoading(on) {
    icon.className = on ? 'bx bx-loader-alt fs-5 text-muted spin' : 'bx bx-search fs-5 text-muted';
  }

  function doSearch(q) {
    if (q === lastQ) return;
    lastQ = q;

    // Update URL without reload
    const newUrl = q ? url + '?q=' + encodeURIComponent(q) : url;
    history.replaceState(null, '', newUrl);

    // Show/hide clear button
    clearBtn.classList.toggle('d-none', q === '');

    if (q === '') {
      container.innerHTML = '';
      doFetch('');
      return;
    }

    setLoading(true);

    if (controller) controller.abort();
    controller = new AbortController();

    fetch(url + '?q=' + encodeURIComponent(q), {
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      signal: controller.signal
    })
    .then(r => r.text())
    .then(html => {
      container.innerHTML = html;
      setLoading(false);
    })
    .catch(err => {
      if (err.name !== 'AbortError') setLoading(false);
    });
  }

  input.addEventListener('input', function () {
    clearTimeout(timer);
    timer = setTimeout(() => doSearch(this.value.trim()), 350);
  });

  clearBtn.addEventListener('click', function () {
    input.value = '';
    doSearch('');
    input.focus();
  });
})();
</script>

<style>
@keyframes spin { to { transform: rotate(360deg); } }
.spin { display: inline-block; animation: spin 0.8s linear infinite; }
</style>
@endsection
