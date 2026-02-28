<?php
$title = $title ?? 'Add Daily Progress';
$error = $error ?? $_SESSION['form_error'] ?? '';
if ($error && isset($_SESSION['form_error'])) {
    unset($_SESSION['form_error']);
}
$defaultDate = $_POST['progress_date'] ?? date('Y-m-d');
$activities = \DailyProgressController::activities();
?>
<div class="card">
  <div class="card-header">
    <h3 class="card-title">Add Daily Progress</h3>
  </div>
  <form method="post" action="<?= base_url('?page=daily_progress/create') ?>">
    <div class="card-body">
      <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <div class="mb-3">
        <label class="form-label">Date</label>
        <input type="date" name="progress_date" class="form-control" value="<?= htmlspecialchars($defaultDate) ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Activities</label>
        <div class="table-responsive-crm">
          <table class="table table-sm table-bordered mb-0">
            <thead class="table-light">
              <tr>
                <th style="width: 55%;">Activity</th>
                <th style="width: 20%;">Count</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($activities as $key => $label): ?>
                <tr>
                  <td><?= htmlspecialchars($label) ?></td>
                  <td>
                    <input type="number" min="0" class="form-control form-control-sm" name="activity[<?= htmlspecialchars($key) ?>]" value="<?= htmlspecialchars($_POST['activity'][$key] ?? '0') ?>">
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Summary / Notes</label>
        <textarea name="summary" class="form-control" rows="4" placeholder="Any additional notes..."><?= htmlspecialchars($_POST['summary'] ?? '') ?></textarea>
      </div>
    </div>
    <div class="card-footer">
      <button type="submit" class="btn btn-primary">Save</button>
      <a href="<?= base_url('?page=daily_progress') ?>" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>

