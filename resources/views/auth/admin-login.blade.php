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
    <link rel="icon" type="image/x-icon" href="/assets/img/favicon/favicon.ico" />
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/img/favicon/favicon-32x32.png" />
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/img/favicon/apple-touch-icon.png" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="/assets/vendor/fonts/boxicons.css" />
    <link rel="stylesheet" href="/assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="/assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="/assets/css/demo.css" />
    <link rel="stylesheet" href="/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="/assets/vendor/css/pages/page-auth.css" />
    <script src="/assets/vendor/js/helpers.js"></script>
    <script src="/assets/js/config.js"></script>
    <style>
      .shop-id-badge {
        display: inline-flex; align-items: center; gap: 5px;
        background: #f0f0ff; color: #7c3aed;
        font-size: .7rem; font-weight: 700; letter-spacing: .04em;
        padding: 2px 10px; border-radius: 20px; margin-bottom: .5rem;
      }
      .form-control:focus { border-color: #696cff; box-shadow: 0 0 0 .2rem rgba(105,108,255,.15); }
      .shop-id-field { text-transform: uppercase; letter-spacing: .08em; font-weight: 700; }
    </style>
  </head>

  <body>
    <div class="container-xxl">
      <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner">
          <div class="card">
            <div class="card-body">

              {{-- Logo --}}
              <div class="text-center mb-3">
                <a href="/">
                  <img src="/assets/img/trackup-logo.png" alt="TrackUp" style="height:56px;width:auto;" />
                </a>
              </div>

              <h4 class="mb-1 text-center">Admin Login</h4>
              <p class="mb-4 text-center text-muted" style="font-size:.85rem;">Enter your Shop ID and credentials</p>

              {{-- Errors --}}
              @if($errors->any())
                <div class="alert alert-danger py-2" style="font-size:.85rem;">
                  <i class="bx bx-error-circle me-1"></i>{{ $errors->first() }}
                </div>
              @endif

              <form id="formAuthentication" class="mb-3" action="{{ route('admin.login.post') }}" method="POST">
                @csrf

                {{-- Shop ID --}}
                <div class="mb-3">
                  <label for="shop_id" class="form-label fw-semibold">
                    Shop ID <span class="text-danger">*</span>
                  </label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-store"></i></span>
                    <input
                      type="text"
                      class="form-control shop-id-field @error('shop_id') is-invalid @enderror"
                      id="shop_id"
                      name="shop_id"
                      placeholder="e.g. D785"
                      value="{{ old('shop_id') }}"
                      autocomplete="off"
                      maxlength="4"
                      autofocus
                    />
                  </div>
                  @error('shop_id')
                    <div class="text-danger mt-1" style="font-size:.78rem;">{{ $message }}</div>
                  @enderror
                </div>

                {{-- Username --}}
                <div class="mb-3">
                  <label for="user_name" class="form-label fw-semibold">Username</label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-user"></i></span>
                    <input
                      type="text"
                      class="form-control @error('user_name') is-invalid @enderror"
                      id="user_name"
                      name="user_name"
                      placeholder="admin"
                      value="{{ old('user_name') }}"
                    />
                  </div>
                  @error('user_name')
                    <div class="text-danger mt-1" style="font-size:.78rem;">{{ $message }}</div>
                  @enderror
                </div>

                {{-- Password --}}
                <div class="mb-4 form-password-toggle">
                  <label class="form-label fw-semibold" for="password">Password</label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-lock-alt"></i></span>
                    <input
                      type="password"
                      id="password"
                      class="form-control"
                      name="password"
                      placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                    />
                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                  </div>
                </div>

                <div class="mb-3">
                  <button class="btn btn-primary d-grid w-100" type="submit">
                    <i class="bx bx-log-in me-1"></i> Sign In
                  </button>
                </div>
              </form>

              <p class="text-center mb-0">
                <a href="{{ route('employee.login') }}" class="text-muted" style="font-size:.82rem;">
                  <i class="bx bx-user-circle me-1"></i>Employee Login
                </a>
              </p>

            </div>
          </div>
        </div>
      </div>
    </div>

    <script src="/assets/vendor/libs/jquery/jquery.js"></script>
    <script src="/assets/vendor/libs/popper/popper.js"></script>
    <script src="/assets/vendor/js/bootstrap.js"></script>
    <script src="/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="/assets/vendor/js/menu.js"></script>
    <script src="/assets/js/main.js"></script>
    <script>
      // Auto uppercase shop ID
      document.getElementById('shop_id').addEventListener('input', function() {
        this.value = this.value.toUpperCase();
      });
    </script>
  </body>
</html>
