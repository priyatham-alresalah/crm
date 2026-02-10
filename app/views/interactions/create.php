<?php
$clients = $clients ?? [];
$clientId = isset($_GET['client_id']) ? trim((string) $_GET['client_id']) : trim((string) ($_POST['client_id'] ?? ''));
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
            <option value="<?= htmlspecialchars($c['id'] ?? '', ENT_QUOTES) ?>" <?= $clientId === ($c['id'] ?? '') ? 'selected' : '' ?>><?= htmlspecialchars($c['client_name'] ?? '') ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Type</label>
        <select name="interaction_type" class="form-select">
          <option value="email" <?= ($_POST['interaction_type'] ?? '') === 'email' ? 'selected' : '' ?>>Email</option>
          <option value="call" <?= ($_POST['interaction_type'] ?? '') === 'call' ? 'selected' : '' ?>>Call</option>
          <option value="meeting" <?= ($_POST['interaction_type'] ?? '') === 'meeting' ? 'selected' : '' ?>>Meeting</option>
          <option value="whatsapp" <?= ($_POST['interaction_type'] ?? '') === 'whatsapp' ? 'selected' : '' ?>>WhatsApp</option>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Stage</label>
        <select name="stage" class="form-select">
          <option value="intro" <?= ($_POST['stage'] ?? '') === 'intro' ? 'selected' : '' ?>>Intro</option>
          <option value="followup" <?= ($_POST['stage'] ?? '') === 'followup' ? 'selected' : '' ?>>Follow-up</option>
          <option value="closing" <?= ($_POST['stage'] ?? '') === 'closing' ? 'selected' : '' ?>>Closing</option>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Interaction date <span class="text-danger">*</span></label>
        <input type="date" name="interaction_date" class="form-control" required value="<?= htmlspecialchars($_POST['interaction_date'] ?? date('Y-m-d')) ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Subject</label>
        <input type="text" name="subject" class="form-control" value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>">
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
