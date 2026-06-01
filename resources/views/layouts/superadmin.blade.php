<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template-free">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
  <title>@yield('title', 'Dashboard') | TrackUp Root</title>
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="/assets/vendor/fonts/boxicons.css" />
  <link rel="stylesheet" href="/assets/vendor/css/core.css" class="template-customizer-core-css" />
  <link rel="stylesheet" href="/assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
  <link rel="stylesheet" href="/assets/css/demo.css" />
  <link rel="stylesheet" href="/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
  @stack('styles')
  <script src="/assets/vendor/js/helpers.js"></script>
  <script src="/assets/js/config.js"></script>
  <style>
    :root { --sa-accent: #7c3aed; --sa-accent2: #a855f7; }
    .menu-section-label {
      padding: 0.65rem 1.25rem 0.25rem;
      font-size: 0.65rem; font-weight: 700; letter-spacing: 0.08em;
      text-transform: uppercase; color: #a8aaae; margin-top: 0.25rem;
    }
    .menu-divider { height:1px; background:rgba(255,255,255,0.06); margin:0.5rem 1rem; }

    /* Root badge — purple tint */
    .sa-badge {
      display:inline-flex; align-items:center; gap:5px;
      background:linear-gradient(135deg,#7c3aed,#a855f7);
      color:#fff; font-size:.65rem; font-weight:700; letter-spacing:.06em;
      text-transform:uppercase; padding:2px 9px; border-radius:20px;
    }

    /* Stat cards */
    .sa-stat-card {
      border-radius:14px; padding:1.4rem 1.5rem; border:none;
      position:relative; overflow:hidden; transition:transform .2s, box-shadow .2s;
    }
    .sa-stat-card:hover { transform:translateY(-3px); box-shadow:0 8px 28px rgba(0,0,0,0.1); }
    .sa-stat-card .stat-icon {
      width:52px; height:52px; border-radius:12px;
      display:flex; align-items:center; justify-content:center;
      font-size:1.5rem; flex-shrink:0;
    }
    .sa-stat-card .stat-num { font-size:2rem; font-weight:800; line-height:1; }
    .sa-stat-card .stat-label { font-size:.78rem; font-weight:600; opacity:.7; margin-top:3px; }
    .sa-stat-card .stat-trend { font-size:.72rem; margin-top:6px; }

    /* Shop grid card */
    .shop-card {
      border-radius:14px; border:1px solid #eee;
      padding:1.2rem; transition:box-shadow .2s, transform .2s;
      background:#fff; position:relative;
    }
    .shop-card:hover { box-shadow:0 6px 24px rgba(0,0,0,0.09); transform:translateY(-2px); }
    .shop-avatar {
      width:44px; height:44px; border-radius:11px;
      background:linear-gradient(135deg,#7c3aed,#a855f7);
      display:flex; align-items:center; justify-content:center;
      color:#fff; font-size:1.2rem; font-weight:700; flex-shrink:0;
    }
    .status-dot {
      width:9px; height:9px; border-radius:50%; display:inline-block; margin-right:5px;
    }
    .dot-active   { background:#22c55e; box-shadow:0 0 0 2px #dcfce7; }
    .dot-online   { background:#22c55e; animation:pulse-green 1.5s infinite; }
    .dot-suspended{ background:#ef4444; }
    .dot-pending  { background:#f59e0b; }
    @keyframes pulse-green {
      0%,100% { box-shadow:0 0 0 0 rgba(34,197,94,.4); }
      50%      { box-shadow:0 0 0 5px rgba(34,197,94,0); }
    }

    /* Chart bar */
    .chart-bar-wrap { display:flex; align-items:flex-end; gap:6px; height:80px; }
    .chart-bar-col  { display:flex; flex-direction:column; align-items:center; gap:3px; flex:1; }
    .chart-bar-col .bar {
      width:100%; border-radius:4px 4px 0 0;
      background:linear-gradient(180deg,#a855f7,#7c3aed);
      min-height:4px; transition:height .3s;
    }
    .chart-bar-col .bar-label { font-size:.58rem; color:#aaa; white-space:nowrap; }
    .chart-bar-col .bar-val   { font-size:.65rem; font-weight:700; color:#7c3aed; }

    /* Table */
    .sa-table th { font-size:.72rem; text-transform:uppercase; letter-spacing:.06em; color:#888; font-weight:700; }
    .sa-table td { font-size:.85rem; vertical-align:middle; }

    /* Activity log */
    .activity-item { display:flex; gap:.75rem; padding:.65rem 0; border-bottom:1px solid #f5f5f5; }
    .activity-icon { width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.9rem;flex-shrink:0; }

    /* Navbar super admin bar */
    .sa-topbar-label {
      display:inline-flex; align-items:center; gap:6px;
      background:linear-gradient(135deg,#7c3aed22,#a855f722);
      border:1px solid #a855f733;
      border-radius:8px; padding:4px 12px;
      font-size:.78rem; font-weight:700; color:#7c3aed;
    }
  </style>
</head>
<body>
<div class="layout-wrapper layout-content-navbar">
  <div class="layout-container">

    {{-- ══════════════ SIDEBAR ══════════════ --}}
    <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
      <div class="app-brand demo">
        <a href="{{ route('superadmin.dashboard') }}" class="app-brand-link">
          <img src="/assets/img/trackup-logo.png" alt="TrackUp" style="height:36px;width:auto;" />
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
          <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
      </div>
      <div class="menu-inner-shadow"></div>

      {{-- Root badge --}}
      <div style="padding:.5rem 1.2rem .75rem;">
        <span class="sa-badge"><i class="bx bx-shield-alt-2" style="font-size:.85rem;"></i> Root Panel</span>
      </div>

      <ul class="menu-inner py-1">

        <li class="menu-section-label">Overview</li>
        <li class="menu-item {{ Request::routeIs('superadmin.dashboard') ? 'active' : '' }}">
          <a href="{{ route('superadmin.dashboard') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-home-circle"></i>
            <div>Dashboard</div>
          </a>
        </li>

        <div class="menu-divider"></div>

        <li class="menu-section-label">Shop Management</li>
        <li class="menu-item {{ Request::routeIs('superadmin.shops.index') ? 'active' : '' }}">
          <a href="{{ route('superadmin.shops.index') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-store"></i>
            <div>All Shops</div>
          </a>
        </li>
        <li class="menu-item {{ Request::routeIs('superadmin.shops.create') ? 'active' : '' }}">
          <a href="{{ route('superadmin.shops.create') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-plus-circle"></i>
            <div>Create Shop</div>
          </a>
        </li>

        <div class="menu-divider"></div>

        <li class="menu-section-label">Account</li>
        <li class="menu-item">
          <a href="#" class="menu-link" style="cursor:default;opacity:.6;">
            <i class="menu-icon tf-icons bx bx-user-circle"></i>
            <div>{{ session('super_admin_name','Super Admin') }}</div>
          </a>
        </li>
        <li class="menu-item">
          <form method="POST" action="{{ route('superadmin.logout') }}">
            @csrf
            <button type="submit" class="menu-link w-100 border-0 bg-transparent text-start" style="cursor:pointer;">
              <i class="menu-icon tf-icons bx bx-power-off text-danger"></i>
              <div class="text-danger">Logout</div>
            </button>
          </form>
        </li>

      </ul>
    </aside>
    {{-- ══════════════ /SIDEBAR ══════════════ --}}

    <div class="layout-page">

      {{-- ══════════════ NAVBAR ══════════════ --}}
      <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
        <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
          <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="bx bx-menu bx-sm"></i>
          </a>
        </div>
        <div class="navbar-nav-right d-flex align-items-center w-100" id="navbar-collapse">
          <div class="me-auto">
            <span class="sa-topbar-label">
              <i class="bx bx-shield-alt-2"></i> Super Admin Root
            </span>
          </div>
          <ul class="navbar-nav flex-row align-items-center ms-auto gap-2">
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
              <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                <div class="avatar avatar-online">
                  <span style="width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,#7c3aed,#a855f7);display:flex;align-items:center;justify-content:center;color:#fff;font-size:1rem;font-weight:700;">
                    {{ strtoupper(substr(session('super_admin_name','S'),0,1)) }}
                  </span>
                </div>
              </a>
              <ul class="dropdown-menu dropdown-menu-end">
                <li>
                  <div class="dropdown-item">
                    <div class="fw-semibold">{{ session('super_admin_name','Super Admin') }}</div>
                    <small class="text-muted">{{ session('super_admin_email','') }}</small>
                  </div>
                </li>
                <li><div class="dropdown-divider"></div></li>
                <li>
                  <form method="POST" action="{{ route('superadmin.logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item text-danger">
                      <i class="bx bx-power-off me-2"></i> Logout
                    </button>
                  </form>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </nav>
      {{-- ══════════════ /NAVBAR ══════════════ --}}

      <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">

          @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
              {!! session('success') !!}
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          @endif
          @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
              {!! session('error') !!}
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          @endif
          @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-3">
              <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          @endif

          @yield('content')
        </div>
        <div class="content-backdrop fade"></div>
      </div>
    </div>
  </div>
  <div class="layout-overlay layout-menu-toggle"></div>
</div>

<script src="/assets/vendor/libs/jquery/jquery.js"></script>
<script src="/assets/vendor/libs/popper/popper.js"></script>
<script src="/assets/vendor/js/bootstrap.js"></script>
<script src="/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="/assets/vendor/js/menu.js"></script>
<script src="/assets/js/main.js"></script>
@stack('scripts')
</body>
</html>
