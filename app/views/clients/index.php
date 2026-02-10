<?php
$list = $list ?? [];
$title = $title ?? 'Clients';
?>
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h3 class="card-title mb-0">Clients</h3>
    <a href="<?= base_url('?page=clients/create') ?>" class="btn btn-primary btn-sm">Add Client</a>
  </div>
  <div class="card-body p-0 table-responsive-crm">
    <table class="table table-hover table-striped table-bordered mb-0">
      <thead class="table-light">
        <tr>
          <th>Date</th>
          <th>Client Name</th>
          <th>Address</th>
          <th>Email</th>
          <th>Contact Number</th>
          <th>Status</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($list)): ?>
          <tr>
            <td colspan="7" class="text-center text-muted py-4">No clients yet. <a href="<?= base_url('?page=clients/create') ?>">Add one</a>.</td>
          </tr>
        <?php else: ?>
          <?php foreach ($list as $row): ?>
            <tr>
              <td><?= htmlspecialchars(date('Y-m-d', strtotime($row['created_at'] ?? 'now'))) ?></td>
              <td><?= htmlspecialchars($row['client_name'] ?? '') ?></td>
              <td><?= htmlspecialchars($row['address'] ?? '') ?></td>
              <td><?= htmlspecialchars($row['email'] ?? '') ?></td>
              <td><?= ($row['phone'] ?? '') !== '' ? htmlspecialchars($row['phone']) : '—' ?></td>
              <td><span class="badge bg-secondary"><?= htmlspecialchars($row['client_status'] ?? '') ?></span></td>
              <td>
                <a href="<?= base_url('?page=clients&id=' . urlencode($row['id'] ?? '')) ?>" class="btn btn-sm btn-outline-primary">View</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
