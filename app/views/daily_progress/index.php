<?php
$title = $title ?? 'Daily Progress';
$date = $_GET['date'] ?? date('Y-m-d');
$activities = \DailyProgressController::activities();
$done = $done ?? [];
$targets = $targets ?? [];
?>
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h3 class="card-title mb-0">Daily Progress</h3>
    <a href="<?= base_url('?page=daily_progress/create') ?>" class="btn btn-primary btn-sm">Add Entry</a>
  </div>
  <div class="card-body">
    <div class="mb-3">
      <strong>Date:</strong> <?= htmlspecialchars($date) ?>
    </div>

    <div class="table-responsive-crm p-0">
      <table class="table table-hover table-striped table-bordered mb-0">
        <thead class="table-light">
          <tr>
            <th style="width: 55%;">Activity</th>
            <th style="width: 15%;">Target</th>
            <th style="width: 15%;">Done</th>
            <th style="width: 15%;">Remaining</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($activities as $key => $label):
            $target = $targets[$key]['daily_target'] ?? 0;
            $doneVal = $done[$key] ?? 0;
            $remaining = max(0, $target - $doneVal);
          ?>
            <tr>
              <td><?= htmlspecialchars($label) ?></td>
              <td><?= $target ?></td>
              <td><?= $doneVal ?></td>
              <td><?= $remaining ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

