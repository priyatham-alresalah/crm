<?php
$branch = $branch ?? null;
$title = $title ?? 'Edit Branch';
$error = $error ?? $_SESSION['form_error'] ?? '';
if ($error && isset($_SESSION['form_error'])) {
    unset($_SESSION['form_error']);
}
?>
<div class="card">
  <div class="card-header">
    <h3 class="card-title">Edit Branch</h3>
  </div>
  <form method="post" action="<?= base_url('?page=branches/edit&id=' . urlencode($branch['id'] ?? '')) ?>">
    <div class="card-body">
      <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <div class="mb-3">
        <label class="form-label">Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($branch['name'] ?? $_POST['name'] ?? '') ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Code</label>
        <input type="text" name="code" class="form-control" value="<?= htmlspecialchars($branch['code'] ?? $_POST['code'] ?? '') ?>" placeholder="Short code (optional)">
      </div>

      <div class="mb-3">
        <label class="form-label">Address</label>
        <textarea name="address" class="form-control" rows="2"><?= htmlspecialchars($branch['address'] ?? $_POST['address'] ?? '') ?></textarea>
      </div>

      <div class="mb-3">
        <div class="form-check">
          <input type="hidden" name="is_active" value="0">
          <input type="checkbox" name="is_active" class="form-check-input" value="1" id="is_active" <?= !empty($branch['is_active']) ? 'checked' : '' ?>>
          <label class="form-check-label" for="is_active">Active</label>
        </div>
      </div>
    </div>
    <div class="card-footer">
      <button type="submit" class="btn btn-primary">Save</button>
      <a href="<?= base_url('?page=branches') ?>" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>

