<nav class="main-header navbar navbar-expand navbar-dark-crm">
  <ul class="navbar-nav">
    <li class="nav-item">
      <a href="<?= base_url() ?>" class="nav-link d-flex align-items-center text-white">
        <img src="<?= base_url('assets/images/logo.png') ?>" alt="Al Resalah" class="navbar-logo me-2">
        <span class="d-none d-md-inline fw-semibold">Al Resalah Consultancies &amp; Training</span>
      </a>
    </li>
  </ul>
  <ul class="navbar-nav ms-auto">
    <li class="nav-item d-flex align-items-center">
      <span class="nav-link py-1 pe-0"><?= htmlspecialchars(Auth::name() ?: 'User') ?></span>
    </li>
    <li class="nav-item d-flex align-items-center">
      <span class="nav-link py-1"><span class="badge bg-light text-dark"><?= htmlspecialchars(Auth::role() ?: '—') ?></span></span>
    </li>
    <li class="nav-item">
      <a href="<?= base_url('logout.php') ?>" class="nav-link">Logout</a>
    </li>
  </ul>
</nav>
