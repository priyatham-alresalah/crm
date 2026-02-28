<aside class="main-sidebar sidebar-dark-crm elevation-4">
  <a href="<?= base_url() ?>" class="brand-link">
    <span class="brand-text fw-bold">CRM</span>
    <small class="d-block text-white-50" style="font-size: 0.7rem;">Client Relationship Management</small>
  </a>
  <div class="sidebar">
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" role="menu">

        <?php $page = $_GET['page'] ?? 'dashboard'; ?>

        <li class="nav-header text-uppercase">Main</li>
        <li class="nav-item">
          <a href="<?= base_url() ?>" class="nav-link <?= $page === 'dashboard' ? 'active' : '' ?>">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?= base_url('?page=profile') ?>" class="nav-link <?= $page === 'profile' ? 'active' : '' ?>">
            <i class="nav-icon fas fa-user"></i>
            <p>My Profile</p>
          </a>
        </li>

        <?php if (Auth::isAdmin()): ?>
        <li class="nav-header text-uppercase">Admin</li>
        <li class="nav-item">
          <a href="<?= base_url('?page=users') ?>" class="nav-link <?= strpos($page, 'users') === 0 ? 'active' : '' ?>">
            <i class="nav-icon fas fa-user-cog"></i>
            <p>Users</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?= base_url('?page=branches') ?>" class="nav-link <?= strpos($page, 'branches') === 0 ? 'active' : '' ?>">
            <i class="nav-icon fas fa-code-branch"></i>
            <p>Branches</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?= base_url('?page=daily_targets') ?>" class="nav-link <?= $page === 'daily_targets' ? 'active' : '' ?>">
            <i class="nav-icon fas fa-bullseye"></i>
            <p>Daily Targets</p>
          </a>
        </li>
        <?php endif; ?>

        <li class="nav-header text-uppercase">Clients &amp; Activity</li>
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

        <li class="nav-header text-uppercase">Tools</li>
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
        <li class="nav-item">
          <a href="<?= base_url('?page=daily_progress') ?>" class="nav-link <?= $page === 'daily_progress' ? 'active' : '' ?>">
            <i class="nav-icon fas fa-list-check"></i>
            <p>Daily Progress</p>
          </a>
        </li>

        <?php if (Auth::can('reports')): ?>
        <li class="nav-header text-uppercase">Reports</li>
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
