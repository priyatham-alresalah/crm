<?php
$users = $users ?? [];
$branches = $branches ?? [];
$mode = $mode ?? ($_GET['mode'] ?? 'user');
$userId = $userId ?? ($_GET['user_id'] ?? '');
$branchId = $branchId ?? ($_GET['branch_id'] ?? '');
$targets = $targets ?? [];
$activities = \DailyTargetController::activities();
$targetYear = $targetYear ?? (int) ($_GET['target_year'] ?? date('Y'));
$targetMonth = $targetMonth ?? (int) ($_GET['target_month'] ?? date('n'));
$monthOptions = $monthOptions ?? [];
?>
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h3 class="card-title mb-0">Daily Targets</h3>
  </div>
  <div class="card-body">
    <form method="post" action="<?= base_url('?page=daily_targets') ?>">
      <div class="row mb-3 align-items-end">
        <div class="col-md-3">
          <label class="form-label">Apply to</label>
          <select name="mode" class="form-select" onchange="this.form.submit()">
            <option value="user" <?= $mode === 'user' ? 'selected' : '' ?>>Single user</option>
            <option value="branch" <?= $mode === 'branch' ? 'selected' : '' ?>>All users in branch</option>
            <option value="all" <?= $mode === 'all' ? 'selected' : '' ?>>All users (company)</option>
          </select>
        </div>

        <div class="col-md-3">
          <label class="form-label">Month</label>
          <select name="target_month" class="form-select" onchange="this.form.submit()">
            <?php foreach ($monthOptions as $m): ?>
              <option value="<?= (int) $m['value'] ?>" <?= $targetMonth === (int) $m['value'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($m['label']) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <input type="hidden" name="target_year" value="<?= (int) $targetYear ?>">
        </div>

        <?php if ($mode !== 'all'): ?>
        <div class="col-md-3">
          <label class="form-label">Branch</label>
          <select name="branch_id" class="form-select" <?= $mode === 'user' ? 'disabled' : '' ?> onchange="this.form.submit()">
            <?php foreach ($branches as $b): ?>
              <option value="<?= htmlspecialchars($b['id'] ?? '') ?>" <?= ($branchId === ($b['id'] ?? '')) ? 'selected' : '' ?>>
                <?= htmlspecialchars($b['name'] ?? '') ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <?php endif; ?>

        <?php if ($mode === 'user'): ?>
        <div class="col-md-3">
          <label class="form-label">User</label>
          <select name="user_id" class="form-select" onchange="this.form.submit()">
            <?php foreach ($users as $u): ?>
              <option value="<?= htmlspecialchars($u['id'] ?? '') ?>" <?= ($userId === ($u['id'] ?? '')) ? 'selected' : '' ?>>
                <?php
                  $name = trim((string) ($u['name'] ?? ''));
                  $role = trim((string) ($u['role'] ?? ''));
                  $label = $name !== '' ? $name : substr((string) ($u['id'] ?? ''), 0, 8);
                  if ($role !== '') {
                      $label .= ' (' . $role . ')';
                  }
                ?>
                <?= htmlspecialchars($label) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <?php endif; ?>
      </div>

      <div class="table-responsive-crm p-0">
        <table class="table table-hover table-striped table-bordered mb-0">
          <thead class="table-light">
            <tr>
              <th style="width: 55%;">Activity</th>
              <th style="width: 20%;">Daily target</th>
              <th style="width: 20%;">Monthly target</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($activities as $key => $label):
              $t = $targets[$key] ?? ['daily_target' => 0, 'monthly_target' => 0];
            ?>
              <tr>
                <td><?= htmlspecialchars($label) ?></td>
                <td>
                  <input type="number" min="0" class="form-control form-control-sm" name="target[<?= htmlspecialchars($key) ?>][daily]" value="<?= (int) $t['daily_target'] ?>">
                </td>
                <td>
                  <input type="number" min="0" class="form-control form-control-sm" name="target[<?= htmlspecialchars($key) ?>][monthly]" value="<?= (int) $t['monthly_target'] ?>" readonly>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div class="mt-3">
        <button type="submit" class="btn btn-primary">Save Targets</button>
      </div>
    </form>
  </div>
</div>

