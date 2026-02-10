<?php
$user = $user ?? null;
$title = $title ?? 'Edit User';
$roles = $roles ?? [];
$error = $error ?? $_SESSION['form_error'] ?? '';
if ($error && isset($_SESSION['form_error'])) {
    unset($_SESSION['form_error']);
}
?>
<div class="card">
  <div class="card-header">
    <h3 class="card-title">Edit User</h3>
  </div>
  <form method="post" action="<?= base_url('?page=users/edit&id=' . urlencode($user['id'] ?? '')) ?>">
    <div class="card-body">
      <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <div class="mb-3">
        <label class="form-label">Name</label>
        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name'] ?? $_POST['name'] ?? '') ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Role</label>
        <select name="role" class="form-select">
          <?php foreach ($roles as $r): ?>
            <option value="<?= htmlspecialchars($r) ?>" <?= ($user['role'] ?? $_POST['role'] ?? 'user') === $r ? 'selected' : '' ?>><?= htmlspecialchars(ucfirst($r)) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-3">
        <div class="form-check">
          <input type="hidden" name="is_active" value="0">
          <input type="checkbox" name="is_active" class="form-check-input" value="1" id="is_active" <?= !empty($user['is_active']) ? 'checked' : '' ?>>
          <label class="form-check-label" for="is_active">Active</label>
        </div>
      </div>
    </div>
    <div class="card-footer">
      <button type="submit" class="btn btn-primary">Save</button>
      <a href="<?= base_url('?page=users') ?>" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>
