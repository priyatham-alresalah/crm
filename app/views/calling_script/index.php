<?php
$byStage = $byStage ?? [];
$stages = ['Intro', 'Follow-up', 'Objection handling', 'Closing'];
$title = $title ?? 'Calling Script';
?>
<div class="card">
  <div class="card-header">
    <h3 class="card-title mb-0">Calling Script</h3>
  </div>
  <div class="card-body">
    <p class="text-muted">Stage-based scripts (read-only).</p>

    <?php foreach ($stages as $stage): ?>
      <?php $items = $byStage[$stage] ?? []; ?>
      <div class="mb-4">
        <h5 class="border-bottom pb-2"><?= htmlspecialchars($stage) ?></h5>
        <?php if (empty($items)): ?>
          <p class="text-muted small">No script for this stage.</p>
        <?php else: ?>
          <div class="list-group">
            <?php foreach ($items as $row): ?>
              <div class="list-group-item">
                <?php if (!empty($row['name'])): ?>
                  <strong><?= htmlspecialchars($row['name']) ?></strong>
                <?php endif; ?>
                <div class="mt-1"><?= nl2br(htmlspecialchars($row['content'] ?? $row['body'] ?? '')) ?></div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>
</div>
