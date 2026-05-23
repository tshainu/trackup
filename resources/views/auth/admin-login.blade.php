<!DOCTYPE html>
<html
  lang="en"
  class="light-style customizer-hide"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="/assets/"
  data-template="vertical-menu-template-free"
>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Admin Login | TrackUp</title>
    <meta name="description" content="" />
    <link rel="icon" type="image/x-icon" href="/assets/img/favicon/favicon.ico" />
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
    <!-- Icons -->
    <link rel="stylesheet" href="/assets/vendor/fonts/boxicons.css" />
    <!-- Core CSS -->
    <link rel="stylesheet" href="/assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="/assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="/assets/css/demo.css" />
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <!-- Page CSS -->
    <link rel="stylesheet" href="/assets/vendor/css/pages/page-auth.css" />
    <!-- Helpers -->
    <script src="/assets/vendor/js/helpers.js"></script>
    <script src="/assets/js/config.js"></script>
  </head>

  <body>
    <div class="container-xxl">
      <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner">
          <!-- Card -->
          <div class="card">
            <div class="card-body">
              <!-- Logo -->
              <div class="text-center mb-3">
                <a href="/">
                  <img src="/assets/img/trackup-logo.png" alt="TrackUp" style="height: 56px; width: auto;" />
                </a>
              </div>
              <!-- /Logo -->

              <h4 class="mb-2 text-center">Admin Login</h4>
              <p class="mb-4 text-center">Sign in to the admin panel</p>

              @if($errors->any())
                <div class="alert alert-danger">
                  {{ $errors->first() }}
                </div>
              @endif

              <form id="formAuthentication" class="mb-3" action="{{ route('admin.login.post') }}" method="POST">
                @csrf
                <div class="mb-3">
                  <label for="user_name" class="form-label">Username</label>
                  <input
                    type="text"
                    class="form-control"
                    id="user_name"
                    name="user_name"
                    placeholder="Enter your username"
                    value="{{ old('user_name') }}"
                    autofocus
                  />
                </div>
                <div class="mb-3 form-password-toggle">
                  <div class="d-flex justify-content-between">
                    <label class="form-label" for="password">Password</label>
                  </div>
                  <div class="input-group input-group-merge">
                    <input
                      type="password"
                      id="password"
                      class="form-control"
                      name="password"
                      placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                    />
                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                  </div>
                </div>
                <div class="mb-3">
                  <button class="btn btn-primary d-grid w-100" type="submit">Sign in</button>
                </div>
              </form>

              <p class="text-center">
                <a href="{{ route('employee.login') }}">Employee Login</a>
              </p>
            </div>
          </div>
          <!-- /Card -->
        </div>
      </div>
    </div>

    <!-- Core JS -->
    <script src="/assets/vendor/libs/jquery/jquery.js"></script>
    <script src="/assets/vendor/libs/popper/popper.js"></script>
    <script src="/assets/vendor/js/bootstrap.js"></script>
    <script src="/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="/assets/vendor/js/menu.js"></script>
    <script src="/assets/js/main.js"></script>
  </body>
</html>
