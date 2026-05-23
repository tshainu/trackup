<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'TrackUp') - Admin</title>
  <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon.ico') }}" />
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- BoxIcons -->
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  <!-- DataTables -->
  <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
  <style>
    :root { --sidebar-bg: #2c2c54; --sidebar-hover: #40407a; --accent: #7c4dff; --accent-light: #a78bfa; }
    body { background: #f4f5fb; font-family: 'Segoe UI', sans-serif; }
    .layout-wrapper { display: flex; min-height: 100vh; }
    /* Sidebar */
    .sidebar { width: 260px; min-height: 100vh; background: var(--sidebar-bg); display: flex; flex-direction: column; position: fixed; top: 0; left: 0; z-index: 1000; transition: width .25s; }
    .sidebar-brand { padding: 1.5rem 1.25rem 1rem; border-bottom: 1px solid rgba(255,255,255,.1); }
    .sidebar-brand h4 { color: #fff; margin: 0; font-weight: 700; font-size: 1.25rem; letter-spacing: 1px; }
    .sidebar-brand small { color: var(--accent-light); font-size: .7rem; }
    .sidebar-menu { flex: 1; padding: .75rem 0; overflow-y: auto; }
    .menu-section { color: rgba(255,255,255,.4); font-size: .65rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; padding: .75rem 1.25rem .25rem; }
    .nav-item a { display: flex; align-items: center; gap: .75rem; color: rgba(255,255,255,.75); padding: .6rem 1.25rem; border-radius: 0 50px 50px 0; margin-right: .75rem; text-decoration: none; font-size: .875rem; transition: all .2s; }
    .nav-item a:hover, .nav-item a.active { background: var(--sidebar-hover); color: #fff; }
    .nav-item a.active { background: var(--accent); }
    .nav-item a i { font-size: 1.1rem; width: 1.25rem; text-align: center; }
    .sidebar-footer { padding: 1rem 1.25rem; border-top: 1px solid rgba(255,255,255,.1); }
    /* Content */
    .layout-page { margin-left: 260px; display: flex; flex-direction: column; min-height: 100vh; flex: 1; }
    .navbar-top { background: #fff; border-bottom: 1px solid #e8eaf0; padding: .75rem 1.5rem; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 100; }
    .content-wrapper { flex: 1; padding: 1.5rem; }
    /* Cards */
    .card { border: none; border-radius: 12px; box-shadow: 0 2px 15px rgba(0,0,0,.06); }
    .card-header { background: #fff; border-bottom: 1px solid #f0f0f0; border-radius: 12px 12px 0 0 !important; font-weight: 600; }
    /* Stats */
    .stat-card { border-radius: 12px; color: #fff; padding: 1.25rem; position: relative; overflow: hidden; }
    .stat-card i { font-size: 2.5rem; opacity: .25; position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); }
    .stat-card .stat-value { font-size: 1.75rem; font-weight: 700; }
    .stat-card .stat-label { font-size: .8rem; opacity: .85; }
    /* Badges */
    .badge-pending { background: #fff3cd; color: #856404; }
    .badge-progress { background: #cfe2ff; color: #0a58ca; }
    .badge-completed { background: #d1e7dd; color: #0f5132; }
    .badge-not-completed { background: #f8d7da; color: #842029; }
    /* Table */
    .table { font-size: .875rem; }
    .table thead th { background: #f8f9fa; font-weight: 600; border-bottom: 2px solid #dee2e6; }
    /* Form */
    .form-label { font-weight: 500; font-size: .875rem; }
    .section-title { font-size: .95rem; font-weight: 600; color: var(--sidebar-bg); border-left: 4px solid var(--accent); padding-left: .75rem; margin-bottom: 1rem; }
    @media (max-width: 768px) { .sidebar { width: 0; overflow: hidden; } .layout-page { margin-left: 0; } }
  </style>
  @stack('styles')
</head>
<body>
<div class="layout-wrapper">
  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="sidebar-brand">
      <h4><i class='bx bx-pulse' style="color:var(--accent-light)"></i> TrackUp</h4>
      <small>Repair Management System</small>
    </div>
    <nav class="sidebar-menu">
      <div class="menu-section">Main</div>
      <ul class="nav flex-column list-unstyled">
        <li class="nav-item">
          <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class='bx bx-home-circle'></i> Dashboard
          </a>
        </li>
      </ul>
      <div class="menu-section">Job Management</div>
      <ul class="nav flex-column list-unstyled">
        <li class="nav-item">
          <a href="{{ route('admin.jobcards.index') }}" class="{{ request()->routeIs('admin.jobcards.*') && !request()->routeIs('admin.jobcards.track') ? 'active' : '' }}">
            <i class='bx bx-list-ul'></i> All Job Cards
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('admin.jobcards.create') }}" class="{{ request()->routeIs('admin.jobcards.create') ? 'active' : '' }}">
            <i class='bx bx-plus-circle'></i> New Job Card
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('admin.jobcards.track') }}" class="{{ request()->routeIs('admin.jobcards.track') ? 'active' : '' }}">
            <i class='bx bx-search-alt'></i> Track Device
          </a>
        </li>
      </ul>
      <div class="menu-section">Administration</div>
      <ul class="nav flex-column list-unstyled">
        <li class="nav-item">
          <a href="{{ route('admin.employees.index') }}" class="{{ request()->routeIs('admin.employees.*') ? 'active' : '' }}">
            <i class='bx bx-group'></i> Employees
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('admin.devices.index') }}" class="{{ request()->routeIs('admin.devices.*') ? 'active' : '' }}">
            <i class='bx bx-devices'></i> Device Management
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
            <i class='bx bx-bar-chart-alt-2'></i> Reports
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('admin.store.edit') }}" class="{{ request()->routeIs('admin.store.*') ? 'active' : '' }}">
            <i class='bx bx-cog'></i> Store Settings
          </a>
        </li>
      </ul>
    </nav>
    <div class="sidebar-footer">
      <div class="d-flex align-items-center gap-2 mb-2">
        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;background:var(--accent)">
          <i class='bx bx-user' style="color:#fff;font-size:.9rem"></i>
        </div>
        <div>
          <div style="color:#fff;font-size:.8rem;font-weight:600">{{ session('admin_name', 'Admin') }}</div>
          <div style="color:rgba(255,255,255,.5);font-size:.7rem">Administrator</div>
        </div>
      </div>
      <form action="{{ route('admin.logout') }}" method="POST">
        @csrf
        <button class="btn btn-sm w-100" style="background:rgba(255,255,255,.1);color:#fff;border:none">
          <i class='bx bx-log-out'></i> Logout
        </button>
      </form>
    </div>
  </aside>

  <!-- Main Content -->
  <div class="layout-page">
    <div class="navbar-top">
      <div>
        <span class="fw-semibold text-dark">@yield('page-title', 'Dashboard')</span>
        <nav aria-label="breadcrumb" class="d-inline ms-2">
          <ol class="breadcrumb mb-0 small">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            @yield('breadcrumb')
          </ol>
        </nav>
      </div>
      <div class="d-flex align-items-center gap-3">
        <span class="text-muted small"><i class='bx bx-calendar'></i> {{ now()->format('d M Y') }}</span>
        <a href="{{ route('admin.jobcards.create') }}" class="btn btn-sm" style="background:var(--accent);color:#fff">
          <i class='bx bx-plus'></i> New Job
        </a>
      </div>
    </div>
    <div class="content-wrapper">
      @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <i class='bx bx-check-circle me-1'></i> {{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif
      @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <i class='bx bx-error me-1'></i> {{ session('error') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif
      @yield('content')
    </div>
  </div>
</div>
<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
@stack('scripts')
</body>
</html>
