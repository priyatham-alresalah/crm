<?php
$list = $list ?? [];
$title = $title ?? 'Interactions';
?>
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h3 class="card-title mb-0">Recent interactions</h3>
    <a href="<?= base_url('?page=interactions/create') ?>" class="btn btn-primary btn-sm">Log interaction</a>
  </div>
  <div class="card-body p-0 table-responsive-crm">
    <table class="table table-hover table-striped table-bordered mb-0">
      <thead class="table-light">
        <tr>
          <th>Date</th>
          <th>Client</th>
          <th>Type</th>
          <th>Stage</th>
          <th>Subject</th>
          <th>Notes</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($list)): ?>
          <tr>
            <td colspan="7" class="text-center text-muted py-4">No interactions yet.</td>
          </tr>
        <?php else: ?>
          <?php foreach ($list as $row): ?>
            <?php
            $clientName = $row['clients']['client_name'] ?? $row['client_name'] ?? '—';
            $clientId = $row['client_id'] ?? '';
            ?>
            <tr>
              <td><?= htmlspecialchars($row['interaction_date'] ?? date('Y-m-d', strtotime($row['created_at'] ?? 'now'))) ?></td>
              <td>
                <a href="<?= base_url('?page=clients&id=' . urlencode($clientId)) ?>"><?= htmlspecialchars($clientName) ?></a>
              </td>
              <td><span class="badge bg-<?= ($row['interaction_type'] ?? '') === 'call' ? 'success' : 'primary' ?>"><?= htmlspecialchars($row['interaction_type'] ?? '') ?></span></td>
              <td><?= htmlspecialchars($row['stage'] ?? '—') ?></td>
              <td><?= htmlspecialchars(mb_substr($row['subject'] ?? '', 0, 30)) ?><?= mb_strlen($row['subject'] ?? '') > 30 ? '…' : '' ?></td>
              <td><?= htmlspecialchars(mb_substr($row['notes'] ?? '', 0, 30)) ?><?= mb_strlen($row['notes'] ?? '') > 30 ? '…' : '' ?></td>
              <td>
                <a href="<?= base_url('?page=clients&id=' . urlencode($clientId)) ?>" class="btn btn-sm btn-outline-primary">View client</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
