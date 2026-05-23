<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'TrackUp') - Employee</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
  <style>
    :root { --sidebar-bg: #1a1a2e; --sidebar-hover: #16213e; --accent: #0f3460; --accent2: #e94560; }
    body { background: #f4f5fb; font-family: 'Segoe UI', sans-serif; }
    .layout-wrapper { display: flex; min-height: 100vh; }
    .sidebar { width: 240px; min-height: 100vh; background: var(--sidebar-bg); display: flex; flex-direction: column; position: fixed; top: 0; left: 0; z-index: 1000; }
    .sidebar-brand { padding: 1.5rem 1.25rem 1rem; border-bottom: 1px solid rgba(255,255,255,.1); }
    .sidebar-brand h4 { color: #fff; margin: 0; font-weight: 700; font-size: 1.2rem; }
    .sidebar-brand small { color: var(--accent2); font-size: .7rem; }
    .sidebar-menu { flex: 1; padding: .75rem 0; }
    .menu-section { color: rgba(255,255,255,.4); font-size: .65rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; padding: .75rem 1.25rem .25rem; }
    .nav-item a { display: flex; align-items: center; gap: .75rem; color: rgba(255,255,255,.75); padding: .6rem 1.25rem; text-decoration: none; font-size: .875rem; transition: all .2s; }
    .nav-item a:hover, .nav-item a.active { background: var(--sidebar-hover); color: #fff; border-left: 3px solid var(--accent2); }
    .nav-item a i { font-size: 1.1rem; }
    .sidebar-footer { padding: 1rem 1.25rem; border-top: 1px solid rgba(255,255,255,.1); }
    .layout-page { margin-left: 240px; display: flex; flex-direction: column; min-height: 100vh; }
    .navbar-top { background: #fff; border-bottom: 1px solid #e8eaf0; padding: .75rem 1.5rem; display: flex; align-items: center; justify-content: space-between; }
    .content-wrapper { flex: 1; padding: 1.5rem; }
    .card { border: none; border-radius: 12px; box-shadow: 0 2px 15px rgba(0,0,0,.06); }
    .card-header { background: #fff; border-bottom: 1px solid #f0f0f0; border-radius: 12px 12px 0 0 !important; font-weight: 600; }
    .stat-card { border-radius: 12px; color: #fff; padding: 1.25rem; position: relative; overflow: hidden; }
    .stat-card i { font-size: 2.5rem; opacity: .2; position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); }
    .stat-card .stat-value { font-size: 1.75rem; font-weight: 700; }
    .stat-card .stat-label { font-size: .8rem; opacity: .85; }
    .table { font-size: .875rem; }
    .table thead th { background: #f8f9fa; font-weight: 600; }
  </style>
  @stack('styles')
</head>
<body>
<div class="layout-wrapper">
  <aside class="sidebar">
    <div class="sidebar-brand">
      <h4><i class='bx bx-wrench' style="color:var(--accent2)"></i> TrackUp</h4>
      <small>Employee Portal</small>
    </div>
    <nav class="sidebar-menu">
      <div class="menu-section">Navigation</div>
      <ul class="nav flex-column list-unstyled">
        <li class="nav-item">
          <a href="{{ route('employee.dashboard') }}" class="{{ request()->routeIs('employee.dashboard') ? 'active' : '' }}">
            <i class='bx bx-home-circle'></i> Dashboard
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('employee.jobs') }}" class="{{ request()->routeIs('employee.jobs*') ? 'active' : '' }}">
            <i class='bx bx-list-check'></i> My Assigned Jobs
          </a>
        </li>
      </ul>
    </nav>
    <div class="sidebar-footer">
      <div class="mb-2" style="color:rgba(255,255,255,.7);font-size:.85rem">
        <i class='bx bx-user'></i> {{ session('employee_name', 'Employee') }}
      </div>
      <form action="{{ route('employee.logout') }}" method="POST">
        @csrf
        <button class="btn btn-sm w-100" style="background:rgba(255,255,255,.1);color:#fff;border:none">
          <i class='bx bx-log-out'></i> Logout
        </button>
      </form>
    </div>
  </aside>
  <div class="layout-page">
    <div class="navbar-top">
      <span class="fw-semibold">@yield('page-title', 'Dashboard')</span>
      <span class="text-muted small">{{ now()->format('d M Y') }}</span>
    </div>
    <div class="content-wrapper">
      @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
          <i class='bx bx-check-circle me-1'></i> {{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif
      @yield('content')
    </div>
  </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
@stack('scripts')
</body>
</html>
