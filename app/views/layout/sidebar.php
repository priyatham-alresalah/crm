<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <a href="<?= base_url() ?>" class="brand-link">
    <span class="brand-text fw-bold ms-3">CRM</span>
  </a>

  <div class="sidebar">
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column">

        <?php $page = $_GET['page'] ?? 'dashboard'; ?>
        <li class="nav-item">
          <a href="<?= base_url() ?>" class="nav-link <?= $page === 'dashboard' ? 'active' : '' ?>">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?= base_url('?page=clients') ?>" class="nav-link <?= strpos($page, 'clients') === 0 ? 'active' : '' ?>">
            <i class="nav-icon fas fa-users"></i>
            <p>Clients</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?= base_url('?page=interactions') ?>" class="nav-link <?= strpos($page, 'interactions') === 0 ? 'active' : '' ?>">
            <i class="nav-icon fas fa-phone-alt"></i>
            <p>Interactions</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?= base_url('?page=email_generator') ?>" class="nav-link <?= $page === 'email_generator' ? 'active' : '' ?>">
            <i class="nav-icon fas fa-envelope"></i>
            <p>Email Generator</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?= base_url('?page=calling_script') ?>" class="nav-link <?= $page === 'calling_script' ? 'active' : '' ?>">
            <i class="nav-icon fas fa-file-alt"></i>
            <p>Calling Script</p>
          </a>
        </li>

        <?php if (Auth::can('reports')): ?>
        <li class="nav-item">
          <a href="<?= base_url('?page=reports') ?>" class="nav-link <?= ($_GET['page'] ?? '') === 'reports' ? 'active' : '' ?>">
            <i class="nav-icon fas fa-chart-bar"></i>
            <p>Reports</p>
          </a>
        </li>
        <?php endif; ?>

      </ul>
    </nav>
  </div>
</aside>
