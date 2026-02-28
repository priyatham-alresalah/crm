<?php
$list = $list ?? [];
$title = $title ?? 'Branches';
$flashError = $_SESSION['form_error'] ?? '';
if ($flashError) { unset($_SESSION['form_error']); }
?>
<?php if ($flashError): ?>
<div class="alert alert-danger"><?= htmlspecialchars($flashError) ?></div>
<?php endif; ?>
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h3 class="card-title mb-0">Branches</h3>
    <a href="<?= base_url('?page=branches/create') ?>" class="btn btn-primary btn-sm">Add Branch</a>
  </div>
  <div class="card-body">
    <?php if (empty($list)): ?>
    <p class="text-muted mb-0 py-4 text-center">No branches. <a href="<?= base_url('?page=branches/create') ?>">Add one</a>.</p>
    <?php else: ?>
    <div class="table-responsive-crm p-0">
      <table class="table table-hover table-striped table-bordered mb-0">
        <thead class="table-light">
          <tr>
            <th>Name</th>
            <th>Code</th>
            <th>Address</th>
            <th>Active</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($list as $row): ?>
          <tr>
            <td><?= htmlspecialchars($row['name'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['code'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['address'] ?? '') ?></td>
            <td><?= !empty($row['is_active']) ? 'Yes' : 'No' ?></td>
            <td>
              <a href="<?= base_url('?page=branches/edit&id=' . urlencode($row['id'] ?? '')) ?>" class="btn btn-sm btn-outline-primary">Edit</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
</div>

