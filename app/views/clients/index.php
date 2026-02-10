<?php
$list = $list ?? [];
$primaryByClient = $primaryByClient ?? [];
$title = $title ?? 'Clients';
?>
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h3 class="card-title mb-0">Clients</h3>
    <a href="<?= base_url('?page=clients/create') ?>" class="btn btn-primary btn-sm">Add Client</a>
  </div>
  <div class="card-body">
    <?php if (empty($list)): ?>
    <p class="text-muted mb-0 py-4 text-center">No clients yet. <a href="<?= base_url('?page=clients/create') ?>">Add one</a>.</p>
    <?php else: ?>
    <div class="table-responsive-crm p-0">
      <table class="table table-hover table-striped table-bordered mb-0">
        <thead class="table-light">
          <tr>
            <th>Client Name</th>
            <th>Primary Contact</th>
            <th>Primary Contact Phone</th>
            <th>Status</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($list as $row):
            $primary = $primaryByClient[$row['id'] ?? ''] ?? null;
          ?>
            <tr>
              <td><?= htmlspecialchars($row['client_name'] ?? '') ?></td>
              <td><?= $primary ? htmlspecialchars($primary['contact_name'] ?? '') : '—' ?></td>
              <td><?= $primary && ($primary['contact_phone'] ?? '') !== '' ? htmlspecialchars($primary['contact_phone']) : '—' ?></td>
              <td><span class="badge bg-secondary"><?= htmlspecialchars($row['client_status'] ?? '') ?></span></td>
              <td>
                <a href="<?= base_url('?page=clients&id=' . urlencode($row['id'] ?? '')) ?>" class="btn btn-sm btn-outline-primary">View</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
</div>
