<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>CRM Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/fontawesome-free/css/all.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/bootstrap/css/bootstrap.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/adminlte/dist/css/adminlte.min.css') ?>">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <b>CRM</b> Login
  </div>
  <div class="card">
    <div class="card-body login-card-body">
      <?php $loginError = Session::getFlash('login_error'); if ($loginError): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($loginError) ?></div>
      <?php endif; ?>

      <form action="<?= htmlspecialchars(BASE_URL) ?>" method="post">
        <div class="input-group mb-3">
          <input type="email" name="email" class="form-control" placeholder="Email" required autocomplete="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
          <div class="input-group-text">
            <span class="fas fa-envelope"></span>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" name="password" class="form-control" placeholder="Password" required autocomplete="current-password">
          <div class="input-group-text">
            <span class="fas fa-lock"></span>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <button type="submit" class="btn btn-primary w-100">Sign In</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
</body>
</html>
