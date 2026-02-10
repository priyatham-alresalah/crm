<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?= $title ?? 'CRM' ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap 5 -->
  <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/bootstrap/css/bootstrap.min.css') ?>">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/fontawesome-free/css/all.min.css') ?>">

  <!-- AdminLTE -->
  <link rel="stylesheet" href="<?= base_url('assets/adminlte/dist/css/adminlte.min.css') ?>">
</head>

<body class="hold-transition sidebar-mini">
<div class="wrapper">

  <?php include __DIR__ . '/navbar.php'; ?>
  <?php include __DIR__ . '/sidebar.php'; ?>

  <div class="content-wrapper p-3">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"><?= $title ?? 'CRM' ?></h1>
          </div>
        </div>
      </div>
    </div>
    <section class="content">
      <div class="container-fluid">
        <?= $content ?? '' ?>
      </div>
    </section>
    <?php include __DIR__ . '/footer.php'; ?>
  </div>
</div>

<!-- JS -->
<script src="<?= base_url('assets/adminlte/plugins/jquery/jquery.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/dist/js/adminlte.min.js') ?>"></script>
</body>
</html>
