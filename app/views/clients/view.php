<?php
$client = $client ?? [];
$timeline = $timeline ?? [];
$contacts = $contacts ?? [];
$id = $client['id'] ?? '';
$primaryContact = null;
foreach ($contacts as $c) {
    if (!empty($c['is_primary'])) {
        $primaryContact = $c;
        break;
    }
}
$flashError = $_SESSION['form_error'] ?? '';
if ($flashError) { unset($_SESSION['form_error']); }
?>
<?php if ($flashError): ?>
<div class="alert alert-danger"><?= htmlspecialchars($flashError) ?></div>
<?php endif; ?>
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h3 class="card-title mb-0"><?= htmlspecialchars($client['client_name'] ?? 'Client') ?></h3>
    <div>
      <a href="<?= base_url('?page=interactions/create&client_id=' . urlencode($id)) ?>" class="btn btn-primary btn-sm">Log Interaction</a>
      <a href="<?= base_url('?page=clients') ?>" class="btn btn-secondary btn-sm">Back to list</a>
    </div>
  </div>
  <div class="card-body">
    <dl class="row mb-0">
      <dt class="col-sm-2">Date</dt>
      <dd class="col-sm-10"><?= htmlspecialchars(date('Y-m-d', strtotime($client['created_at'] ?? 'now'))) ?></dd>
      <dt class="col-sm-2">Address</dt>
      <dd class="col-sm-10"><?= htmlspecialchars($client['address'] ?? '—') ?></dd>
      <?php if ($primaryContact): ?>
      <dt class="col-sm-2">Primary contact</dt>
      <dd class="col-sm-10"><?= htmlspecialchars($primaryContact['contact_name'] ?? '') ?><?= ($primaryContact['designation'] ?? '') !== '' ? ' (' . htmlspecialchars($primaryContact['designation']) . ')' : '' ?></dd>
      <dt class="col-sm-2">Contact email</dt>
      <dd class="col-sm-10"><?= ($primaryContact['contact_email'] ?? '') !== '' ? htmlspecialchars($primaryContact['contact_email']) : '—' ?></dd>
      <dt class="col-sm-2">Contact phone</dt>
      <dd class="col-sm-10"><?= ($primaryContact['contact_phone'] ?? '') !== '' ? htmlspecialchars($primaryContact['contact_phone']) : '—' ?></dd>
      <?php endif; ?>
      <dt class="col-sm-2">Status</dt>
      <dd class="col-sm-10"><span class="badge bg-secondary"><?= htmlspecialchars($client['client_status'] ?? '') ?></span></dd>
    </dl>
  </div>
</div>

<div class="card mt-3">
  <div class="card-header">
    <h3 class="card-title mb-0">Contacts</h3>
  </div>
  <div class="card-body">
    <?php if (empty($contacts)): ?>
    <p class="text-muted mb-0">No contacts yet. Add one below.</p>
    <?php else: ?>
    <ul class="list-group list-group-flush">
      <?php foreach ($contacts as $c): ?>
      <li class="list-group-item d-flex justify-content-between align-items-center">
        <div>
          <?= htmlspecialchars($c['contact_name'] ?? '') ?>
          <?php if (!empty($c['is_primary'])): ?>
          <span class="badge bg-primary ms-1">Primary</span>
          <?php endif; ?>
          <?php if (($c['designation'] ?? '') !== ''): ?>
          <span class="text-muted small">— <?= htmlspecialchars($c['designation']) ?></span>
          <?php endif; ?>
          <?php if (($c['contact_email'] ?? '') !== ''): ?>
          <span class="text-muted small d-block"><?= htmlspecialchars($c['contact_email']) ?></span>
          <?php endif; ?>
          <?php if (($c['contact_phone'] ?? '') !== ''): ?>
          <span class="text-muted small d-block"><?= htmlspecialchars($c['contact_phone']) ?></span>
          <?php endif; ?>
        </div>
        <?php if (empty($c['is_primary'])): ?>
        <a href="<?= base_url('?page=clients/contact_primary&client_id=' . urlencode($id) . '&id=' . urlencode($c['id'] ?? '')) ?>" class="btn btn-sm btn-outline-primary">Set as primary</a>
        <?php endif; ?>
      </li>
      <?php endforeach; ?>
    </ul>
    <?php endif; ?>

    <hr class="my-3">
    <h5 class="mb-2">Add contact</h5>
    <form method="post" action="<?= base_url('?page=clients/contact_add') ?>" class="row g-2">
      <input type="hidden" name="client_id" value="<?= htmlspecialchars($id) ?>">
      <div class="col-md-3">
        <input type="text" name="contact_name" class="form-control form-control-sm" placeholder="Name" required>
      </div>
      <div class="col-md-2">
        <input type="email" name="contact_email" class="form-control form-control-sm" placeholder="Email">
      </div>
      <div class="col-md-2">
        <input type="text" name="contact_phone" class="form-control form-control-sm" placeholder="Phone">
      </div>
      <div class="col-md-2">
        <input type="text" name="designation" class="form-control form-control-sm" placeholder="Designation">
      </div>
      <div class="col-md-2">
        <div class="form-check">
          <input type="checkbox" name="is_primary" value="1" class="form-check-input" id="new_primary">
          <label class="form-check-label small" for="new_primary">Primary</label>
        </div>
      </div>
      <div class="col-md-1">
        <button type="submit" class="btn btn-primary btn-sm">Add</button>
      </div>
    </form>
  </div>
</div>

<div class="card mt-3">
  <div class="card-header">
    <h3 class="card-title mb-0">Interaction timeline</h3>
  </div>
  <div class="card-body">
    <?php if (empty($timeline)): ?>
      <p class="text-muted mb-0">No interactions yet. <a href="<?= base_url('?page=interactions/create&client_id=' . urlencode($id)) ?>">Log one</a>.</p>
    <?php else: ?>
      <div class="timeline">
        <?php foreach ($timeline as $i): ?>
          <div class="time-label mb-2">
            <span class="bg-info rounded px-2 py-1 text-white">
              <?= htmlspecialchars(date('M j, Y', strtotime($i['interaction_date'] ?? $i['created_at'] ?? 'now'))) ?>
            </span>
          </div>
          <div class="mb-3">
            <span class="badge bg-<?= ($i['interaction_type'] ?? '') === 'call' ? 'success' : 'primary' ?>"><?= htmlspecialchars($i['interaction_type'] ?? '') ?></span>
            <?php if (!empty($i['stage'])): ?>
              <span class="badge bg-secondary"><?= htmlspecialchars($i['stage']) ?></span>
            <?php endif; ?>
            <?php if (!empty($i['subject'])): ?>
              <strong><?= htmlspecialchars($i['subject']) ?></strong>
            <?php endif; ?>
            <p class="mb-0 mt-1"><?= nl2br(htmlspecialchars($i['notes'] ?? '')) ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>
