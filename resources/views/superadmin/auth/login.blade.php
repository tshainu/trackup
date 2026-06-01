<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Super Admin Login | TrackUp</title>
  <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}" />
  <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="/assets/vendor/fonts/boxicons.css" />
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Public Sans', sans-serif;
      min-height: 100vh;
      background: linear-gradient(135deg, #1e1040 0%, #3b1d8a 50%, #7c3aed 100%);
      display: flex; align-items: center; justify-content: center;
    }
    .login-wrap {
      width: 100%; max-width: 420px; padding: 1rem;
    }
    .login-card {
      background: #fff; border-radius: 20px;
      padding: 2.5rem 2rem;
      box-shadow: 0 20px 60px rgba(0,0,0,0.35);
    }
    .brand-area { text-align: center; margin-bottom: 2rem; }
    .brand-area img { height: 42px; margin-bottom: 1rem; }
    .root-badge {
      display: inline-flex; align-items: center; gap: 6px;
      background: linear-gradient(135deg, #7c3aed, #a855f7);
      color: #fff; font-size: .7rem; font-weight: 700;
      text-transform: uppercase; letter-spacing: .08em;
      padding: 4px 14px; border-radius: 20px; margin-bottom: .75rem;
    }
    .brand-area h4 { font-size: 1.3rem; font-weight: 700; color: #1e1040; }
    .brand-area p  { font-size: .82rem; color: #888; margin-top: 4px; }

    .form-group { margin-bottom: 1.25rem; }
    .form-group label { display: block; font-size: .82rem; font-weight: 600; color: #444; margin-bottom: 6px; }
    .form-group input {
      width: 100%; padding: .7rem 1rem;
      border: 1.5px solid #e0e0e0; border-radius: 10px;
      font-size: .9rem; font-family: inherit;
      transition: border .2s, box-shadow .2s; outline: none;
    }
    .form-group input:focus { border-color: #7c3aed; box-shadow: 0 0 0 3px rgba(124,58,237,.12); }

    .input-icon-wrap { position: relative; }
    .input-icon-wrap i {
      position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
      color: #aaa; font-size: 1.1rem; cursor: pointer;
    }
    .input-icon-wrap input { padding-right: 2.5rem; }

    .btn-login {
      width: 100%; padding: .8rem;
      background: linear-gradient(135deg, #7c3aed, #a855f7);
      color: #fff; border: none; border-radius: 10px;
      font-size: .95rem; font-weight: 700; cursor: pointer;
      transition: opacity .2s, transform .15s; letter-spacing: .02em;
    }
    .btn-login:hover { opacity: .9; transform: translateY(-1px); }
    .btn-login:active { transform: scale(.98); }

    .error-msg {
      background: #fde8e4; border: 1px solid #ffbcb3;
      color: #c0392b; border-radius: 8px; padding: .6rem 1rem;
      font-size: .82rem; margin-bottom: 1.2rem;
      display: flex; align-items: center; gap: 6px;
    }
    .footer-note { text-align: center; margin-top: 1.5rem; font-size: .75rem; color: #bbb; }
  </style>
</head>
<body>
  <div class="login-wrap">
    <div class="login-card">
      <div class="brand-area">
        <img src="/assets/img/trackup-logo.png" alt="TrackUp" onerror="this.style.display='none'" />
        <div class="root-badge"><i class="bx bx-shield-alt-2"></i> Root Access</div>
        <h4>Super Admin</h4>
        <p>Sign in to the TrackUp control panel</p>
      </div>

      @if($errors->any())
        <div class="error-msg">
          <i class="bx bx-error-circle"></i>
          {{ $errors->first() }}
        </div>
      @endif

      <form method="POST" action="{{ route('superadmin.login.post') }}">
        @csrf
        <div class="form-group">
          <label>Email Address</label>
          <input type="email" name="email" value="{{ old('email') }}" placeholder="superadmin@trackup.com" required autofocus />
        </div>
        <div class="form-group">
          <label>Password</label>
          <div class="input-icon-wrap">
            <input type="password" name="password" id="pwdInput" placeholder="••••••••" required />
            <i class="bx bx-hide" id="pwdToggle" onclick="togglePwd()"></i>
          </div>
        </div>
        <button type="submit" class="btn-login">
          <i class="bx bx-log-in me-1"></i> Sign In
        </button>
      </form>

      <div class="footer-note">TrackUp Root Panel &mdash; Authorized access only</div>
    </div>
  </div>
  <script>
    function togglePwd() {
      const inp = document.getElementById('pwdInput');
      const ico = document.getElementById('pwdToggle');
      if (inp.type === 'password') { inp.type = 'text'; ico.className = 'bx bx-show'; }
      else { inp.type = 'password'; ico.className = 'bx bx-hide'; }
    }
  </script>
</body>
</html>
