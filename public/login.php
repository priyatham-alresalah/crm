<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Login | Al Resalah CRM</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" href="<?= base_url('favicon.ico') ?>">
  <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('favicon-16x16.png') ?>">
  <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('favicon-32x32.png') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/bootstrap/css/bootstrap.min.css') ?>">
  <style>
    :root {
      --login-header-footer-bg: #212529;
      --login-body-bg: #f0f2f5;
    }
    html, body { height: 100%; margin: 0; }
    body.login-page {
      background-color: var(--login-body-bg);
      display: flex;
      flex-direction: column;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }
    .login-header {
      background-color: var(--login-header-footer-bg);
      color: #fff;
      padding: 0.75rem 1.5rem;
      flex-shrink: 0;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .login-header a {
      color: #fff;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 0.75rem;
    }
    .login-header a:hover { color: rgba(255,255,255,0.9); }
    .login-header .logo-img { height: 36px; width: auto; }
    .login-header .brand-text { font-weight: 600; font-size: 1.1rem; }
    .login-main {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem 1rem;
    }
    .login-card {
      background: #fff;
      border-radius: 0.5rem;
      box-shadow: 0 2px 12px rgba(0,0,0,0.08);
      padding: 2rem;
      width: 100%;
      max-width: 400px;
    }
    .login-card h1 {
      font-size: 1.5rem;
      font-weight: 700;
      color: #212529;
      margin-bottom: 1.5rem;
      text-align: center;
    }
    .login-card .alert { text-align: center; }
    .login-card .form-control {
      border-radius: 0.375rem;
      padding: 0.6rem 0.75rem;
      border: 1px solid #dee2e6;
    }
    .login-card .form-control:focus {
      border-color: #86b7fe;
      box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
    }
    .login-card .btn-primary {
      width: 100%;
      padding: 0.5rem 1rem;
      font-weight: 500;
      border-radius: 0.375rem;
      background-color: #0d6efd;
      border-color: #0d6efd;
    }
    .login-card .btn-primary:hover {
      background-color: #0b5ed7;
      border-color: #0a58ca;
    }
    .login-footer {
      background-color: var(--login-header-footer-bg);
      color: rgba(255,255,255,0.9);
      text-align: center;
      padding: 0.75rem 1rem;
      font-size: 0.875rem;
      flex-shrink: 0;
    }
  </style>
</head>
<body class="login-page">
  <header class="login-header">
    <a href="<?= base_url() ?>">
      <img src="<?= base_url('assets/images/logo.png') ?>" alt="Al Resalah" class="logo-img">
      <span class="brand-text">Al Resalah Consulting &amp; Training</span>
    </a>
  </header>

  <main class="login-main">
    <div class="login-card">
      <h1>Login</h1>
      <?php
      $loginError = Session::getFlash('login_error');
      if (!$loginError && Session::has('login_error')) {
        $loginError = Session::get('login_error');
        Session::remove('login_error');
      }
      if ($loginError):
      ?>
        <div class="alert alert-danger small"><?= htmlspecialchars($loginError) ?></div>
      <?php endif; ?>

      <form method="post" action="">
        <div class="mb-3">
          <label for="login-email" class="form-label visually-hidden">Email</label>
          <input id="login-email" type="email" name="email" class="form-control" placeholder="Email" required autocomplete="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        <div class="mb-3">
          <label for="login-password" class="form-label visually-hidden">Password</label>
          <input id="login-password" type="password" name="password" class="form-control" placeholder="Password" required autocomplete="current-password">
        </div>
        <div class="mb-3">
          <button type="submit" class="btn btn-primary">Login</button>
        </div>
        <p class="mb-0 text-center">
          <a href="#" class="text-primary small text-decoration-none">Forgot password?</a>
        </p>
      </form>
    </div>
  </main>

  <footer class="login-footer">
    © <?= date('Y') ?> Al Resalah Consulting &amp; Training
  </footer>
</body>
</html>
