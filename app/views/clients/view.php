<?php
$client = $client ?? [];
$timeline = $timeline ?? [];
$id = (int) ($client['id'] ?? 0);
?>
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h3 class="card-title mb-0"><?= htmlspecialchars($client['client_name'] ?? 'Client') ?></h3>
    <div>
      <a href="<?= base_url('?page=interactions/create&client_id=' . $id) ?>" class="btn btn-primary btn-sm">Log Interaction</a>
      <a href="<?= base_url('?page=clients') ?>" class="btn btn-secondary btn-sm">Back to list</a>
    </div>
  </div>
  <div class="card-body">
    <dl class="row mb-0">
      <dt class="col-sm-2">Date</dt>
      <dd class="col-sm-10"><?= htmlspecialchars(date('Y-m-d', strtotime($client['created_at'] ?? 'now'))) ?></dd>
      <dt class="col-sm-2">Address</dt>
      <dd class="col-sm-10"><?= htmlspecialchars($client['address'] ?? '—') ?></dd>
      <dt class="col-sm-2">Email</dt>
      <dd class="col-sm-10"><?= htmlspecialchars($client['email'] ?? '—') ?></dd>
      <dt class="col-sm-2">Status</dt>
      <dd class="col-sm-10"><span class="badge bg-secondary"><?= htmlspecialchars($client['status'] ?? '') ?></span></dd>
    </dl>
  </div>
</div>

<div class="card mt-3">
  <div class="card-header">
    <h3 class="card-title mb-0">Interaction timeline</h3>
  </div>
  <div class="card-body">
    <?php if (empty($timeline)): ?>
      <p class="text-muted mb-0">No interactions yet. <a href="<?= base_url('?page=interactions/create&client_id=' . $id) ?>">Log one</a>.</p>
    <?php else: ?>
      <div class="timeline">
        <?php foreach ($timeline as $i): ?>
          <div class="time-label mb-2">
            <span class="bg-info rounded px-2 py-1 text-white">
              <?= htmlspecialchars(date('M j, Y g:i A', strtotime($i['created_at'] ?? 'now'))) ?>
            </span>
          </div>
          <div class="mb-3">
            <span class="badge bg-<?= ($i['type'] ?? '') === 'call' ? 'success' : 'primary' ?>"><?= htmlspecialchars($i['type'] ?? '') ?></span>
            <?php if (!empty($i['status_at_time'])): ?>
              <span class="badge bg-secondary"><?= htmlspecialchars($i['status_at_time']) ?></span>
            <?php endif; ?>
            <p class="mb-0 mt-1"><?= nl2br(htmlspecialchars($i['notes'] ?? '')) ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>
