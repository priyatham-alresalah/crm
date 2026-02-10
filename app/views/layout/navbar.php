<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
    </li>
  </ul>

  <ul class="navbar-nav ms-auto">
    <li class="nav-item">
      <span class="nav-link text-muted"><?= htmlspecialchars(Auth::name() ?: 'User') ?></span>
    </li>
    <li class="nav-item">
      <span class="nav-link"><span class="badge bg-secondary"><?= htmlspecialchars(Auth::role() ?: '—') ?></span></span>
    </li>
    <li class="nav-item">
      <a href="<?= base_url('logout.php') ?>" class="nav-link">Logout</a>
    </li>
  </ul>
</nav>
