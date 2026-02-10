<?php
$list = $list ?? [];
$title = $title ?? 'Users';
$flashError = $_SESSION['form_error'] ?? '';
if ($flashError) { unset($_SESSION['form_error']); }
?>
<?php if ($flashError): ?>
<div class="alert alert-danger"><?= htmlspecialchars($flashError) ?></div>
<?php endif; ?>
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h3 class="card-title mb-0">Users</h3>
    <a href="<?= base_url('?page=users/create') ?>" class="btn btn-primary btn-sm">Add User</a>
  </div>
  <div class="card-body">
    <?php if (empty($list)): ?>
    <p class="text-muted mb-0 py-4 text-center">No users. <a href="<?= base_url('?page=users/create') ?>">Add one</a>.</p>
    <?php else: ?>
    <div class="table-responsive-crm p-0">
      <table class="table table-hover table-striped table-bordered mb-0">
        <thead class="table-light">
          <tr><th>Name</th><th>Role</th><th>Active</th><th></th></tr>
        </thead>
        <tbody>
          <?php foreach ($list as $row): ?>
          <tr>
            <td><?= htmlspecialchars($row['name'] ?? '') ?></td>
            <td><span class="badge bg-secondary"><?= htmlspecialchars($row['role'] ?? '') ?></span></td>
            <td><?= !empty($row['is_active']) ? 'Yes' : 'No' ?></td>
            <td>
              <a href="<?= base_url('?page=users/edit&id=' . urlencode($row['id'] ?? '')) ?>" class="btn btn-sm btn-outline-primary">Edit</a>
              <?php if (Auth::id() !== ($row['id'] ?? '') && !empty($row['is_active'])): ?>
              <a href="<?= base_url('?page=users/delete&id=' . urlencode($row['id'] ?? '')) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Deactivate this user?');">Deactivate</a>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
</div>
