<?php
$byStage = $byStage ?? [];
$stages = ['Intro', 'Follow-up', 'Objection handling', 'Closing'];
$title = $title ?? 'Calling Script';
?>
<div class="card shadow-sm">
  <div class="card-header bg-white py-3">
    <h3 class="card-title mb-0">Calling Script</h3>
  </div>
  <div class="card-body">
    <p class="text-muted mb-4">Stage-based scripts for calls. Use these when speaking with clients.</p>

    <?php foreach ($stages as $stage): ?>
      <?php $items = $byStage[$stage] ?? []; ?>
      <div class="mb-4">
        <h5 class="text-primary border-bottom pb-2 mb-3"><?= htmlspecialchars($stage) ?></h5>
        <?php if (empty($items)): ?>
          <p class="text-muted small mb-0">No script for this stage. Add rows to the <code>calling_scripts</code> table in Supabase (see <code>database/seeders/001_sample_data.sql</code>).</p>
        <?php else: ?>
          <div class="list-group list-group-flush">
            <?php foreach ($items as $row): ?>
              <?php $scriptTitle = $row['title'] ?? $row['name'] ?? ''; $scriptContent = $row['content'] ?? ''; ?>
              <div class="list-group-item px-0">
                <?php if ($scriptTitle !== ''): ?>
                  <strong class="d-block mb-1"><?= htmlspecialchars($scriptTitle) ?></strong>
                <?php endif; ?>
                <div class="text-secondary"><?= nl2br(htmlspecialchars($scriptContent)) ?></div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>
</div>
