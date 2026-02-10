<?php
$list = $list ?? [];
$title = $title ?? 'Interactions';
?>
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h3 class="card-title mb-0">Recent interactions</h3>
    <a href="<?= base_url('?page=interactions/create') ?>" class="btn btn-primary btn-sm">Log interaction</a>
  </div>
  <div class="card-body p-0">
    <table class="table table-hover table-striped mb-0">
      <thead>
        <tr>
          <th>Date</th>
          <th>Client</th>
          <th>Type</th>
          <th>Status at time</th>
          <th>Notes</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($list)): ?>
          <tr>
            <td colspan="6" class="text-center text-muted py-4">No interactions yet.</td>
          </tr>
        <?php else: ?>
          <?php foreach ($list as $row): ?>
            <?php
            $clientName = $row['clients']['client_name'] ?? $row['client_name'] ?? '—';
            $clientId = $row['client_id'] ?? 0;
            ?>
            <tr>
              <td><?= htmlspecialchars(date('Y-m-d H:i', strtotime($row['created_at'] ?? 'now'))) ?></td>
              <td>
                <a href="<?= base_url('?page=clients&id=' . (int)$clientId) ?>"><?= htmlspecialchars($clientName) ?></a>
              </td>
              <td><span class="badge bg-<?= ($row['type'] ?? '') === 'call' ? 'success' : 'primary' ?>"><?= htmlspecialchars($row['type'] ?? '') ?></span></td>
              <td><?= htmlspecialchars($row['status_at_time'] ?? '—') ?></td>
              <td><?= htmlspecialchars(mb_substr($row['notes'] ?? '', 0, 50)) ?><?= mb_strlen($row['notes'] ?? '') > 50 ? '…' : '' ?></td>
              <td>
                <a href="<?= base_url('?page=clients&id=' . (int)$clientId) ?>" class="btn btn-sm btn-outline-primary">View client</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
