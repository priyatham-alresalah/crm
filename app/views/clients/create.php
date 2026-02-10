<?php
$title = $title ?? 'Add Client';
$statuses = $statuses ?? [];
$error = $_SESSION['form_error'] ?? '';
if ($error) {
    unset($_SESSION['form_error']);
}
?>
<div class="card">
  <div class="card-header">
    <h3 class="card-title">Add Client</h3>
  </div>
  <form method="post" action="<?= base_url('?page=clients/create') ?>">
    <div class="card-body">
      <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <div class="mb-3">
        <label class="form-label">Client Name <span class="text-danger">*</span></label>
        <input type="text" name="client_name" class="form-control" required value="<?= htmlspecialchars($_POST['client_name'] ?? '') ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Address</label>
        <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($_POST['address'] ?? '') ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
          <?php foreach ($statuses as $s): ?>
            <option value="<?= htmlspecialchars($s) ?>" <?= ($_POST['status'] ?? '') === $s ? 'selected' : '' ?>><?= htmlspecialchars($s) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="card-footer">
      <button type="submit" class="btn btn-primary">Save Client</button>
      <a href="<?= base_url('?page=clients') ?>" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>
