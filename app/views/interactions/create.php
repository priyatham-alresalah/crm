<?php
$clients = $clients ?? [];
$statuses = $statuses ?? [];
$clientId = isset($_GET['client_id']) ? (int) $_GET['client_id'] : (int) ($_POST['client_id'] ?? 0);
$error = $_SESSION['form_error'] ?? '';
if ($error) {
    unset($_SESSION['form_error']);
}
?>
<div class="card">
  <div class="card-header">
    <h3 class="card-title">Log interaction</h3>
  </div>
  <form method="post" action="<?= base_url('?page=interactions/create') ?>">
    <div class="card-body">
      <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <div class="mb-3">
        <label class="form-label">Client <span class="text-danger">*</span></label>
        <select name="client_id" class="form-select" required>
          <option value="">— Select client —</option>
          <?php foreach ($clients as $c): ?>
            <option value="<?= (int)($c['id'] ?? 0) ?>" <?= $clientId === (int)($c['id'] ?? 0) ? 'selected' : '' ?>><?= htmlspecialchars($c['client_name'] ?? '') ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Type</label>
        <select name="type" class="form-select">
          <option value="email" <?= ($_POST['type'] ?? '') === 'email' ? 'selected' : '' ?>>Email</option>
          <option value="call" <?= ($_POST['type'] ?? '') === 'call' ? 'selected' : '' ?>>Call</option>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Status at time of interaction</label>
        <select name="status_at_time" class="form-select">
          <option value="">— Optional —</option>
          <?php foreach ($statuses as $s): ?>
            <option value="<?= htmlspecialchars($s) ?>"><?= htmlspecialchars($s) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Notes</label>
        <textarea name="notes" class="form-control" rows="4"><?= htmlspecialchars($_POST['notes'] ?? '') ?></textarea>
      </div>
    </div>
    <div class="card-footer">
      <button type="submit" class="btn btn-primary">Save</button>
      <a href="<?= base_url('?page=interactions') ?>" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>
