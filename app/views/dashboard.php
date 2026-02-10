<?php
$title = $title ?? 'Dashboard';
$totalClients = $totalClients ?? 0;
$totalInteractions = $totalInteractions ?? 0;
$followUpsToday = $followUpsToday ?? 0;
$statusCounts = $statusCounts ?? [];
$statuses = ['new', 'contacted', 'converted', 'lost'];
?>
<div class="row">
  <div class="col-lg-3 col-6">
    <div class="small-box bg-info">
      <div class="inner">
        <h3><?= (int) $totalClients ?></h3>
        <p>Total Clients</p>
      </div>
      <div class="icon">
        <i class="fas fa-users"></i>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-6">
    <div class="small-box bg-success">
      <div class="inner">
        <h3><?= (int) $totalInteractions ?></h3>
        <p>Total Interactions</p>
      </div>
      <div class="icon">
        <i class="fas fa-comments"></i>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-6">
    <div class="small-box bg-warning">
      <div class="inner">
        <h3><?= (int) $followUpsToday ?></h3>
        <p>Follow-ups Today</p>
      </div>
      <div class="icon">
        <i class="fas fa-calendar-day"></i>
      </div>
    </div>
  </div>
</div>

<div class="row mt-3">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Clients by Status</h3>
      </div>
      <div class="card-body">
        <div class="row">
          <?php foreach ($statuses as $status): ?>
            <div class="col-md-4 col-lg-2 mb-2">
              <div class="info-box">
                <span class="info-box-icon bg-secondary"><i class="fas fa-tag"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text"><?= htmlspecialchars(ucfirst($status)) ?></span>
                  <span class="info-box-number"><?= (int) ($statusCounts[$status] ?? 0) ?></span>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</div>
