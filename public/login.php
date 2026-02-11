<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Login | Al Resalah CRM</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="icon" href="<?= base_url('favicon.ico') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/bootstrap/css/bootstrap.min.css') ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    :root {
      --header-footer-bg: #212529;
      --primary: #2f5fd0;
    }

    html, body { height: 100%; margin: 0; }

    body.login-page {
      background: #ffffff;
      display: flex;
      flex-direction: column;
      font-family: "Segoe UI", Arial, sans-serif;
    }

    /* HEADER */
    .login-header {
      background: var(--header-footer-bg);
      color: #fff;
      padding: 14px;
      text-align: center;
    }

    .login-header img {
      height: 36px;
      margin-right: 10px;
      vertical-align: middle;
    }

    .login-header span {
      font-weight: 600;
      font-size: 18px;
      vertical-align: middle;
    }

    /* MAIN */
    .login-main {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .login-card {
      width: 360px;
      background: #ffffff;
      padding: 35px;
      border-radius: 12px;
      box-shadow: 0 15px 35px rgba(0,0,0,0.08);
      text-align: center;
    }

    .login-card h1 {
      font-size: 28px;
      font-weight: 600;
      margin-bottom: 30px;
      color: #1f2937;
    }

    /* INPUT WRAPPER */
    .input-group-custom {
      position: relative;
      width: 100%;
      margin-bottom: 18px;
    }

    .input-group-custom input {
      width: 100%;
      height: 48px;
      padding-left: 42px;
      padding-right: 42px;
      border-radius: 10px;
      border: 1px solid #d1d5db;
      font-size: 14px;
      box-sizing: border-box;
      transition: 0.2s;
    }

    .input-group-custom input:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(47,95,208,0.15);
      outline: none;
    }

    .input-group-custom .left-icon {
      position: absolute;
      top: 50%;
      left: 14px;
      transform: translateY(-50%);
      color: #9ca3af;
      font-size: 14px;
    }

    .input-group-custom .right-icon {
      position: absolute;
      top: 50%;
      right: 14px;
      transform: translateY(-50%);
      color: #9ca3af;
      font-size: 14px;
      cursor: pointer;
    }

    /* BUTTON */
    .btn-login {
      width: 100%;
      height: 48px;
      background: linear-gradient(90deg, #3461d8, #2a52be);
      border: none;
      border-radius: 10px;
      color: #fff;
      font-weight: 600;
      font-size: 15px;
      transition: 0.3s;
    }

    .btn-login:hover {
      opacity: 0.9;
    }

    .forgot-link {
      display: block;
      margin-top: 15px;
      font-size: 14px;
      color: var(--primary);
      text-decoration: none;
    }

    .forgot-link:hover {
      text-decoration: underline;
    }

    /* FOOTER */
    .login-footer {
      background: var(--header-footer-bg);
      color: #fff;
      text-align: center;
      padding: 12px;
      font-size: 14px;
    }

  </style>
</head>

<body class="login-page">

<header class="login-header">
  <img src="<?= base_url('assets/images/logo.png') ?>" alt="Logo">
  <span>Al Resalah Consulting & Training</span>
</header>

<main class="login-main">
  <div class="login-card">

    <h1>Login</h1>

    <form method="post" action="">

      <!-- EMAIL -->
      <div class="input-group-custom">
        <i class="fa-solid fa-envelope left-icon"></i>
        <input type="email" name="email" placeholder="Email" required>
      </div>

      <!-- PASSWORD -->
      <div class="input-group-custom">
        <i class="fa-solid fa-lock left-icon"></i>
        <input type="password" id="password" name="password" placeholder="Password" required>
        <i class="fa-solid fa-eye right-icon" id="togglePassword"></i>
      </div>

      <button type="submit" class="btn-login">Login</button>

    </form>

    <a href="#" class="forgot-link">Forgot password?</a>

  </div>
</main>

<footer class="login-footer">
  © <?= date('Y') ?> Al Resalah Consulting & Training
</footer>

<script>
  const togglePassword = document.getElementById('togglePassword');
  const password = document.getElementById('password');

  togglePassword.addEventListener('click', function () {
    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
    password.setAttribute('type', type);

    this.classList.toggle('fa-eye');
    this.classList.toggle('fa-eye-slash');
  });
</script>

</body>
</html>
