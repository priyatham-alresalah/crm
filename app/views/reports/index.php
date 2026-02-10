<?php
$clientsByStatus = $clientsByStatus ?? [];
$interactionsPerUser = $interactionsPerUser ?? [];
$followUpsInRange = $followUpsInRange ?? 0;
$dateFrom = $dateFrom ?? date('Y-m-01');
$dateTo = $dateTo ?? date('Y-m-d');
$title = $title ?? 'Reports';
?>
<div class="card">
  <div class="card-header">
    <h3 class="card-title mb-0">Reports</h3>
  </div>
  <div class="card-body">
    <form method="get" action="<?= base_url('?page=reports') ?>" class="row g-3 mb-4">
      <input type="hidden" name="page" value="reports">
      <div class="col-auto">
        <label class="form-label">Date from</label>
        <input type="date" name="date_from" class="form-control" value="<?= htmlspecialchars($dateFrom) ?>">
      </div>
      <div class="col-auto">
        <label class="form-label">Date to</label>
        <input type="date" name="date_to" class="form-control" value="<?= htmlspecialchars($dateTo) ?>">
      </div>
      <div class="col-auto d-flex align-items-end">
        <button type="submit" class="btn btn-primary">Filter</button>
      </div>
    </form>

    <div class="row">
      <div class="col-md-6">
        <div class="card card-outline card-primary">
          <div class="card-header">
            <h3 class="card-title">Clients by status</h3>
          </div>
          <div class="card-body p-0">
            <table class="table table-sm mb-0">
              <thead>
                <tr>
                  <th>Status</th>
                  <th class="text-end">Count</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($clientsByStatus as $status => $count): ?>
                  <tr>
                    <td><?= htmlspecialchars($status) ?></td>
                    <td class="text-end"><?= (int) $count ?></td>
                  </tr>
                <?php endforeach; ?>
                <?php if (empty($clientsByStatus)): ?>
                  <tr><td colspan="2" class="text-muted text-center">No data</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card card-outline card-success">
          <div class="card-header">
            <h3 class="card-title">Follow-ups in date range</h3>
          </div>
          <div class="card-body">
            <p class="mb-0"><strong><?= (int) $followUpsInRange ?></strong> interactions between <?= htmlspecialchars($dateFrom) ?> and <?= htmlspecialchars($dateTo) ?></p>
          </div>
        </div>
        <div class="card card-outline card-info mt-3">
          <div class="card-header">
            <h3 class="card-title">Interactions per user</h3>
          </div>
          <div class="card-body p-0">
            <table class="table table-sm mb-0">
              <thead>
                <tr>
                  <th>User ID</th>
                  <th class="text-end">Count</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($interactionsPerUser as $uid => $count): ?>
                  <tr>
                    <td><?= htmlspecialchars($uid === 'unknown' ? '—' : $uid) ?></td>
                    <td class="text-end"><?= (int) $count ?></td>
                  </tr>
                <?php endforeach; ?>
                <?php if (empty($interactionsPerUser)): ?>
                  <tr><td colspan="2" class="text-muted text-center">No data</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
