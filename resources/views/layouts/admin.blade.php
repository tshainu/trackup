<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template-free">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
  <title>@yield('title', 'Dashboard') | TrackUp</title>
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
    /* ── Sidebar tweaks ── */
    .menu-section-label {
      padding: 0.65rem 1.25rem 0.25rem;
      font-size: 0.65rem;
      font-weight: 700;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      color: #a8aaae;
      margin-top: 0.25rem;
    }
    .menu-divider {
      height: 1px;
      background: rgba(255,255,255,0.06);
      margin: 0.5rem 1rem;
    }
    /* ── Notification bell ── */
    .notif-bell-btn {
      position: relative;
      background: none;
      border: none;
      cursor: pointer;
      padding: 0.55rem 0.6rem;
      border-radius: 50%;
      color: #697a8d;
      font-size: 1.7rem;
      display: flex;
      align-items: center;
      transition: background .15s;
    }
    .notif-bell-btn:hover { background: rgba(105,108,255,.08); color: #696cff; }
    .notif-count-badge {
      position: absolute;
      top: 3px; right: 1px;
      min-width: 22px; height: 16px;
      padding: 0 6px;
      background: #ff3e1d;
      color: #fff;
      font-size: 0.62rem;
      font-weight: 700;
      border-radius: 4px;
      display: flex;
      align-items: center;
      justify-content: center;
      line-height: 1;
      letter-spacing: 0.02em;
    }
    /* ── Notification dropdown ── */
    .notif-dropdown {
      display: none;
      position: absolute;
      top: calc(100% + 6px);
      right: 0;
      width: 380px;
      max-height: 520px;
      overflow-y: auto;
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.14);
      z-index: 9999;
      border: 1px solid #e7e7e7;
    }
    .notif-dropdown.open { display: block; }
    .notif-dropdown-header {
      padding: 1rem 1.2rem 0.75rem;
      border-bottom: 1px solid #f0f0f0;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .notif-dropdown-header h6 { margin: 0; font-weight: 700; font-size: 0.9rem; }
    .notif-section-head {
      padding: 0.6rem 1.2rem 0.3rem;
      font-size: 0.65rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.07em;
      color: #a0a0a0;
      background: #fafafa;
      border-bottom: 1px solid #f0f0f0;
    }
    .notif-item {
      display: flex;
      align-items: flex-start;
      gap: 0.75rem;
      padding: 0.75rem 1.2rem;
      border-bottom: 1px solid #f5f5f5;
      text-decoration: none;
      color: inherit;
      transition: background .12s;
    }
    .notif-item:hover { background: #f8f8ff; }
    .notif-item-icon {
      width: 34px; height: 34px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      font-size: 1rem;
    }
    .icon-warning  { background: #fff3cd; color: #e6a817; }
    .icon-danger   { background: #fde8e4; color: #ff3e1d; }
    .icon-success  { background: #e3f9e5; color: #28a745; }
    .notif-item-body { flex: 1; min-width: 0; }
    .notif-item-body strong { font-size: 0.82rem; display: block; }
    .notif-item-body span   { font-size: 0.75rem; color: #8a8a8a; }
    .notif-empty {
      padding: 1.5rem 1.2rem;
      text-align: center;
      color: #b0b0b0;
      font-size: 0.82rem;
    }
    .notif-footer {
      padding: 0.7rem 1.2rem;
      border-top: 1px solid #f0f0f0;
      text-align: center;
    }
    .notif-footer a { font-size: 0.82rem; color: #696cff; text-decoration: none; font-weight: 600; }
    /* ── Notification item with action buttons ── */
    .notif-item-row {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      padding: 0.65rem 1.2rem;
      border-bottom: 1px solid #f5f5f5;
    }
    .notif-item-row .notif-item-body { flex: 1; min-width: 0; }
    .notif-item-actions {
      display: flex;
      align-items: center;
      gap: 4px;
      flex-shrink: 0;
    }
    .notif-action-btn {
      border: none;
      border-radius: 7px;
      padding: 4px 9px;
      font-size: 0.72rem;
      font-weight: 700;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 3px;
      transition: opacity .15s, transform .1s;
      white-space: nowrap;
    }
    .notif-action-btn:hover { opacity: .85; transform: translateY(-1px); }
    .notif-action-btn:active { transform: scale(.95); }
    .notif-done  { background: #28a74520; color: #1e7e34; }
    .notif-done:hover { background: #28a74535; }
    .notif-pay   { background: #696cff20; color: #696cff; }
    .notif-pay:hover { background: #696cff35; }
    .notif-close { background: #f0f0f0; color: #888; padding: 4px 7px; }
    .notif-close:hover { background: #ffe0e0; color: #e55; }
    /* ── Sidebar badge ── */
    .menu-item .notif-sidebar-badge {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 18px;
      height: 18px;
      padding: 0 5px;
      border-radius: 20px;
      font-size: 0.65rem;
      font-weight: 700;
      background: #ff3e1d;
      color: #fff;
      margin-left: auto;
    }
  </style>
</head>

<body>
<div class="layout-wrapper layout-content-navbar">
  <div class="layout-container">

    {{-- ═══════════════════════════════════════ SIDEBAR ═══════════════════════════════════════ --}}
    <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

      {{-- Brand --}}
      <div class="app-brand demo">
        <a href="{{ route('admin.dashboard') }}" class="app-brand-link">
          <img src="/assets/img/trackup-logo.png" alt="TrackUp" style="height: 36px; width: auto;" />
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
          <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
      </div>

      <div class="menu-inner-shadow"></div>

      <ul class="menu-inner py-1">

        {{-- ── MAIN ── --}}
        <li class="menu-section-label">Main</li>
        <li class="menu-item {{ Request::routeIs('admin.dashboard') || Request::is('/') ? 'active' : '' }}">
          <a href="{{ route('admin.dashboard') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-home-circle"></i>
            <div>Dashboard</div>
          </a>
        </li>

        @php $shopModules = session('shop_modules', ['job_orders','field_services']); @endphp

        {{-- ── OPERATIONS ── --}}
        @if(in_array('job_orders', $shopModules))
        <div class="menu-divider"></div>
        <li class="menu-section-label">Operations</li>
        <li class="menu-item {{ Request::routeIs('admin.jobcards.*') ? 'open active' : '' }}">
          <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-file"></i>
            <div>Job Orders</div>
          </a>
          <ul class="menu-sub">
            <li class="menu-item {{ Request::routeIs('admin.jobcards.index') ? 'active' : '' }}">
              <a href="{{ route('admin.jobcards.index') }}" class="menu-link">
                <div>All Orders</div>
              </a>
            </li>
            <li class="menu-item {{ Request::routeIs('admin.jobcards.create') ? 'active' : '' }}">
              <a href="{{ route('admin.jobcards.create') }}" class="menu-link">
                <div>New Order</div>
              </a>
            </li>
            <li class="menu-item {{ Request::routeIs('admin.jobcards.track') ? 'active' : '' }}">
              <a href="{{ route('admin.jobcards.track') }}" class="menu-link">
                <div>Track Device</div>
              </a>
            </li>
            <li class="menu-item {{ Request::routeIs('admin.jobcards.delivered') ? 'active' : '' }}">
              <a href="{{ route('admin.jobcards.delivered') }}" class="menu-link">
                <i class='menu-icon tf-icons bx bx-package'></i>
                <div>Delivered Orders</div>
              </a>
            </li>
          </ul>
        </li>
        @endif

        {{-- ── FIELD SERVICES ── --}}
        @if(in_array('field_services', $shopModules))
        <div class="menu-divider"></div>
        <li class="menu-section-label">Field Services</li>
        <li class="menu-item {{ Request::routeIs('admin.field-complaints.*') ? 'open active' : '' }}">
          <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-map-pin"></i>
            <div>Field Complaints</div>
          </a>
          <ul class="menu-sub">
            <li class="menu-item {{ Request::routeIs('admin.field-complaints.index') ? 'active' : '' }}">
              <a href="{{ route('admin.field-complaints.index') }}" class="menu-link">
                <div>All Complaints</div>
              </a>
            </li>
            <li class="menu-item {{ Request::routeIs('admin.field-complaints.create') ? 'active' : '' }}">
              <a href="{{ route('admin.field-complaints.create') }}" class="menu-link">
                <div>New Complaint</div>
              </a>
            </li>
          </ul>
        </li>
        <li class="menu-item {{ Request::routeIs('admin.service-types.*') ? 'active' : '' }}">
          <a href="{{ route('admin.service-types.index') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-wrench"></i>
            <div>Service Types</div>
          </a>
        </li>
        @endif

        <div class="menu-divider"></div>

        {{-- ── PEOPLE ── --}}
        <li class="menu-section-label">People</li>
        <li class="menu-item {{ Request::routeIs('admin.employees.*') ? 'active' : '' }}">
          <a href="{{ route('admin.employees.index') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-group"></i>
            <div>Employees</div>
          </a>
        </li>

        <div class="menu-divider"></div>

        {{-- ── SYSTEM ── --}}
        <li class="menu-section-label">System</li>
        <li class="menu-item {{ Request::routeIs('admin.devices.*') ? 'open active' : '' }}">
          <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-devices"></i>
            <div>Device Management</div>
          </a>
          <ul class="menu-sub">
            <li class="menu-item {{ Request::routeIs('admin.devices.index') ? 'active' : '' }}">
              <a href="{{ route('admin.devices.index') }}" class="menu-link">
                <div>Devices & Brands</div>
              </a>
            </li>
            <li class="menu-item {{ Request::routeIs('admin.devices.accessories.index') ? 'active' : '' }}">
              <a href="{{ route('admin.devices.accessories.index') }}" class="menu-link">
                <div>Accessories Received</div>
              </a>
            </li>
          </ul>
        </li>
        <li class="menu-item {{ Request::routeIs('admin.invoices.*') ? 'active' : '' }}">
          <a href="{{ route('admin.invoices.index') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-receipt"></i>
            <div>Invoices</div>
          </a>
        </li>

        <li class="menu-item {{ Request::routeIs('admin.reports.*') ? 'active' : '' }}">
          <a href="{{ route('admin.reports.index') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-bar-chart-alt-2"></i>
            <div>Reports</div>
          </a>
        </li>

        <div class="menu-divider"></div>

        {{-- ── SETTINGS ── --}}
        <li class="menu-section-label">Settings</li>
        @php
          $onSettings = Request::routeIs('admin.store.*')
                     || Request::routeIs('admin.sms-settings.*')
                     || Request::routeIs('admin.label-settings.*')
                     || Request::routeIs('admin.whatsapp-settings.*');
        @endphp
        <li class="menu-item {{ $onSettings ? 'open active' : '' }}">
          <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-cog"></i>
            <div>Settings</div>
          </a>
          <ul class="menu-sub">
            <li class="menu-item {{ Request::routeIs('admin.store.*') ? 'active' : '' }}">
              <a href="{{ route('admin.store.edit') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-store"></i>
                <div>Store Settings</div>
              </a>
            </li>
            <li class="menu-item {{ Request::routeIs('admin.sms-settings.*') ? 'active' : '' }}">
              <a href="{{ route('admin.sms-settings.edit') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-message-rounded-dots"></i>
                <div>SMS Settings</div>
              </a>
            </li>
            <li class="menu-item {{ Request::routeIs('admin.whatsapp-settings.*') ? 'active' : '' }}">
              <a href="{{ route('admin.whatsapp-settings.edit') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bxl-whatsapp"></i>
                <div>WhatsApp Settings</div>
              </a>
            </li>
            <li class="menu-item {{ Request::routeIs('admin.label-settings.*') ? 'active' : '' }}">
              <a href="{{ route('admin.label-settings.edit') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-barcode"></i>
                <div>Label Settings</div>
              </a>
            </li>
          </ul>
        </li>

        <div class="menu-divider"></div>

        {{-- ── ALERTS ── --}}
        <li class="menu-section-label">Alerts</li>
        <li class="menu-item {{ Request::routeIs('admin.notifications.*') ? 'active' : '' }}">
          <a href="{{ route('admin.notifications.index') }}" class="menu-link d-flex align-items-center">
            <i class="menu-icon tf-icons bx bx-bell"></i>
            <div>Notifications</div>
            @if(isset($notifData) && $notifData['total'] > 0)
              <span class="notif-sidebar-badge">{{ $notifData['total'] }}</span>
            @endif
          </a>
        </li>

      </ul>
    </aside>
    {{-- ═══════════════════════════════════════ /SIDEBAR ═══════════════════════════════════════ --}}

    <div class="layout-page">

      {{-- ═══════════════════════════════════════ NAVBAR ═══════════════════════════════════════ --}}
      <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
        <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
          <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="bx bx-menu bx-sm"></i>
          </a>
        </div>

        <div class="navbar-nav-right d-flex align-items-center w-100" id="navbar-collapse">
          {{-- Store logo + name + greeting --}}
          <div class="me-auto d-flex align-items-center gap-3">
            {{-- Store logo --}}
            @if(isset($storeInfo) && $storeInfo && $storeInfo->logo)
              <img src="{{ asset('storage/'.$storeInfo->logo) }}"
                   alt="logo"
                   style="height:38px;width:38px;object-fit:contain;border-radius:10px;border:1.5px solid #ebebff;background:#fafafe;padding:3px;">
            @else
              <div style="width:38px;height:38px;border-radius:10px;background:linear-gradient(135deg,#696cff,#8c57ff);display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.1rem;flex-shrink:0;">
                <i class="bx bx-store"></i>
              </div>
            @endif
            <div>
              <div style="font-size:.92rem;font-weight:700;color:#2d2d3a;line-height:1.2;">
                {{ isset($storeInfo) && $storeInfo && $storeInfo->store_name ? $storeInfo->store_name : (session('shop_name') ?? 'TrackUp') }}
              </div>
              <div style="font-size:.76rem;color:#a0a0b0;line-height:1;">
                Hello, <span style="font-weight:600;color:#696cff;">{{ $loggedInName ?? 'User' }}</span> &nbsp;·&nbsp; <span style="font-weight:600;color:#696cff;">{{ session('shop_code') }}</span>
              </div>
            </div>
          </div>

          <ul class="navbar-nav flex-row align-items-center ms-auto gap-2">

            {{-- ── Notification Bell ── --}}
            <li class="nav-item" style="position:relative;">
              <button class="notif-bell-btn" id="notifBellBtn" type="button" aria-label="Notifications">
                <img src="/assets/img/notification-bell.gif" style="width:34px;height:34px;display:block;" alt="notifications">
                @if(isset($notifData) && $notifData['total'] > 0)
                  <span class="notif-count-badge">{{ $notifData['total'] }}</span>
                @endif
              </button>

              {{-- Dropdown panel --}}
              <div class="notif-dropdown" id="notifDropdown">
                <div class="notif-dropdown-header">
                  <h6>Notifications</h6>
                  @if(isset($notifData) && $notifData['total'] > 0)
                    <span class="badge bg-danger" style="font-size:.7rem;">{{ $notifData['total'] }} New</span>
                  @endif
                </div>

                @if(isset($notifData))

                  {{-- Due Today --}}
                  @if($notifData['dueToday']->count() > 0)
                    <div class="notif-section-head">Due Today</div>
                    @foreach($notifData['dueToday'] as $job)
                      <a href="{{ route('admin.jobcards.edit', $job->id) }}" class="notif-item">
                        <div class="notif-item-icon icon-warning">
                          <i class="bx bx-time-five"></i>
                        </div>
                        <div class="notif-item-body">
                          <strong>{{ $job->device_name }} — #{{ $job->order_no }}</strong>
                          <span>{{ $job->customer_name }} · {{ ucfirst($job->status) }}</span>
                        </div>
                      </a>
                    @endforeach
                  @endif

                  {{-- Need Assistant --}}
                  @if($notifData['needAssistant']->count() > 0)
                    <div class="notif-section-head">Needs Assistance</div>
                    @foreach($notifData['needAssistant'] as $job)
                      <div class="notif-item notif-item-row" id="notif-assist-{{ $job->id }}">
                        <div class="notif-item-icon icon-danger">
                          <i class="bx bx-help-circle"></i>
                        </div>
                        <div class="notif-item-body">
                          <strong>{{ $job->device_name }} — #{{ $job->order_no }}</strong>
                          <span>{{ $job->customer_name }} · Staff requested help</span>
                        </div>
                        <div class="notif-item-actions">
                          <button class="notif-action-btn notif-done"
                            onclick="notifAction(this,'{{ route('admin.notifications.dismiss-assistant', $job->id) }}','notif-assist-{{ $job->id }}')"
                            title="Mark Done">
                            <i class="bx bx-check"></i> Done
                          </button>
                          <button class="notif-action-btn notif-close"
                            onclick="notifAction(this,'{{ route('admin.notifications.dismiss-assistant', $job->id) }}','notif-assist-{{ $job->id }}')"
                            title="Dismiss">
                            <i class="bx bx-x"></i>
                          </button>
                        </div>
                      </div>
                    @endforeach
                  @endif

                  {{-- Unpaid Completed --}}
                  @if($notifData['unpaidCompleted']->count() > 0)
                    <div class="notif-section-head">Payment Pending</div>
                    @foreach($notifData['unpaidCompleted'] as $job)
                      <div class="notif-item notif-item-row" id="notif-pay-{{ $job->id }}">
                        <div class="notif-item-icon icon-success">
                          <i class="bx bx-money"></i>
                        </div>
                        <div class="notif-item-body">
                          <strong>{{ $job->device_name }} — #{{ $job->order_no }}</strong>
                          <span>{{ $job->customer_name }} · Rs. {{ number_format($job->rupees) }} due</span>
                        </div>
                        <div class="notif-item-actions">
                          <a class="notif-action-btn notif-pay"
                            href="{{ route('admin.invoices.show', $job->id) }}?pay=1"
                            title="Go to payment">
                            <i class="bx bx-receipt"></i> Pay
                          </a>
                          <button class="notif-action-btn notif-close"
                            onclick="notifDismissOnly('notif-pay-{{ $job->id }}')"
                            title="Close">
                            <i class="bx bx-x"></i>
                          </button>
                        </div>
                      </div>
                    @endforeach
                  @endif

                  {{-- Field Complaints Unpaid --}}
                  @if(in_array('field_services', session('shop_modules', ['job_orders','field_services'])) && isset($notifData['fieldCompleted']) && $notifData['fieldCompleted']->count() > 0)
                    <div class="notif-section-head" style="color:#d97706;">Field Services — Payment</div>
                    @foreach($notifData['fieldCompleted'] as $fc)
                      <a href="{{ route('admin.field-complaints.show', $fc->id) }}" class="notif-item">
                        <div class="notif-item-icon" style="background:#fff3cd;color:#d97706;">
                          <i class="bx bx-map-pin"></i>
                        </div>
                        <div class="notif-item-body">
                          <strong>{{ $fc->complaint_no }} — {{ $fc->customer_name }}</strong>
                          <span>{{ $fc->service_type_name ?? 'Field Service' }} · Completed, unpaid</span>
                        </div>
                      </a>
                    @endforeach
                  @endif

                  @if($notifData['total'] === 0)
                    <div class="notif-empty">
                      <i class="bx bx-check-circle" style="font-size:2rem;color:#28a745;display:block;margin-bottom:.5rem;"></i>
                      All caught up!
                    </div>
                  @endif

                @endif

                <div class="notif-footer">
                  <a href="{{ route('admin.notifications.index') }}">View all notifications →</a>
                </div>
              </div>
            </li>

            {{-- ── Admin Avatar ── --}}
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
              <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                <div class="avatar avatar-online">
                  <img src="/assets/img/avatars/1.png" alt class="w-px-40 h-auto rounded-circle" />
                </div>
              </a>
              <ul class="dropdown-menu dropdown-menu-end">
                <li>
                  <a class="dropdown-item" href="#">
                    <div class="d-flex">
                      <div class="flex-shrink-0 me-3">
                        <div class="avatar avatar-online">
                          <img src="/assets/img/avatars/1.png" alt class="w-px-40 h-auto rounded-circle" />
                        </div>
                      </div>
                      <div class="flex-grow-1">
                        <span class="fw-semibold d-block">Admin</span>
                        <small class="text-muted">TrackUp Admin</small>
                      </div>
                    </div>
                  </a>
                </li>
                <li><div class="dropdown-divider"></div></li>
                <li>
                  <a class="dropdown-item" href="{{ route('admin.store.edit') }}">
                    <i class="bx bx-cog me-2"></i> Store Settings
                  </a>
                </li>
                <li><div class="dropdown-divider"></div></li>
                <li>
                  <form method="POST" action="{{ route('admin.logout') }}">
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
      {{-- ═══════════════════════════════════════ /NAVBAR ═══════════════════════════════════════ --}}

      <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
          @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
              <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          @endif
          @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
              <i class="bx bx-error-circle me-1"></i> {{ session('error') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          @endif
          @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show mb-3" role="alert">
              <i class="bx bx-info-circle me-1"></i> {{ session('warning') }}
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

{{-- Core scripts --}}
<script src="/assets/vendor/libs/jquery/jquery.js"></script>
<script src="/assets/vendor/libs/popper/popper.js"></script>
<script src="/assets/vendor/js/bootstrap.js"></script>
<script src="/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="/assets/vendor/js/menu.js"></script>
<script src="/assets/js/main.js"></script>

<script>
  // ── Notification bell toggle ──
  const bellBtn  = document.getElementById('notifBellBtn');
  const dropdown = document.getElementById('notifDropdown');

  if (bellBtn && dropdown) {
    bellBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      dropdown.classList.toggle('open');
    });
    document.addEventListener('click', function (e) {
      if (!dropdown.contains(e.target) && e.target !== bellBtn) {
        dropdown.classList.remove('open');
      }
    });
  }
</script>

<script>
  // Send PATCH and fade-remove the notification row
  function notifAction(btn, url, rowId) {
    btn.disabled = true;
    const csrfToken = '{{ csrf_token() }}';
    fetch(url, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': csrfToken,
        'X-HTTP-Method-Override': 'PATCH',
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify({ _method: 'PATCH' })
    })
    .then(() => notifFadeRemove(rowId))
    .catch(() => { btn.disabled = false; });
  }

  // Just hide locally (X on payment — don't mark paid, just close bell)
  function notifDismissOnly(rowId) {
    notifFadeRemove(rowId);
  }

  function notifFadeRemove(rowId) {
    const el = document.getElementById(rowId);
    if (!el) return;
    el.style.transition = 'opacity .3s, max-height .3s, padding .3s';
    el.style.overflow = 'hidden';
    el.style.opacity = '0';
    el.style.maxHeight = '0';
    el.style.padding = '0';
    setTimeout(() => {
      el.remove();
      // Update badge count
      const badge = document.querySelector('.notif-count-badge');
      if (badge) {
        const current = parseInt(badge.textContent) || 0;
        const next = current - 1;
        if (next <= 0) badge.remove();
        else badge.textContent = next;
      }
    }, 320);
  }
</script>

@stack('scripts')
</body>
</html>
