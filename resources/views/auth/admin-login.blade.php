<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>TrackUp - Admin Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  <style>
    body { background: linear-gradient(135deg, #2c2c54 0%, #40407a 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
    .login-card { background: #fff; border-radius: 16px; padding: 2.5rem; width: 100%; max-width: 420px; box-shadow: 0 20px 60px rgba(0,0,0,.3); }
    .brand-logo { text-align: center; margin-bottom: 1.5rem; }
    .brand-logo h2 { color: #2c2c54; font-weight: 800; margin: 0; }
    .brand-logo span { color: #7c4dff; }
    .brand-logo p { color: #6c757d; font-size: .85rem; margin-top: .25rem; }
    .btn-login { background: #7c4dff; color: #fff; border: none; padding: .75rem; font-weight: 600; border-radius: 8px; }
    .btn-login:hover { background: #6a3de8; color: #fff; }
    .demo-info { background: #f8f9fa; border-radius: 8px; padding: .75rem; font-size: .8rem; color: #6c757d; }
    .form-control:focus { border-color: #7c4dff; box-shadow: 0 0 0 .2rem rgba(124,77,255,.15); }
    .employee-link { text-align: center; margin-top: 1rem; }
    .employee-link a { color: #7c4dff; text-decoration: none; font-size: .875rem; }
  </style>
</head>
<body>
<div class="login-card">
  <div class="brand-logo">
    <h2><i class='bx bx-pulse' style="color:#7c4dff"></i> Track<span>Up</span></h2>
    <p>Repair Management System — Admin Portal</p>
  </div>
  @if($errors->any())
    <div class="alert alert-danger py-2 small">{{ $errors->first() }}</div>
  @endif
  <form action="{{ route('admin.login.post') }}" method="POST">
    @csrf
    <div class="mb-3">
      <label class="form-label fw-semibold small">Username</label>
      <div class="input-group">
        <span class="input-group-text"><i class='bx bx-user'></i></span>
        <input type="text" name="user_name" class="form-control" value="{{ old('user_name') }}" placeholder="Enter username" required autofocus />
      </div>
    </div>
    <div class="mb-4">
      <label class="form-label fw-semibold small">Password</label>
      <div class="input-group">
        <span class="input-group-text"><i class='bx bx-lock'></i></span>
        <input type="password" name="password" class="form-control" placeholder="Enter password" required />
      </div>
    </div>
    <button type="submit" class="btn btn-login w-100"><i class='bx bx-log-in me-1'></i> Login</button>
  </form>
  <div class="demo-info mt-3">
    <strong>Demo credentials:</strong> admin / admin123
  </div>
  <div class="employee-link">
    <a href="{{ route('employee.login') }}"><i class='bx bx-user-check'></i> Employee Login</a>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
