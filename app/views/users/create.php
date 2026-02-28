<?php
$title = $title ?? 'Add User';
$roles = $roles ?? [];
$branches = $branches ?? [];
$error = $error ?? $_SESSION['form_error'] ?? '';
if ($error && isset($_SESSION['form_error'])) {
    unset($_SESSION['form_error']);
}
?>
<div class="card">
  <div class="card-header">
    <h3 class="card-title">Add User</h3>
  </div>
  <form method="post" action="<?= base_url('?page=users/create') ?>">
    <div class="card-body">
      <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <div class="mb-3">
        <label class="form-label">Email <span class="text-danger">*</span></label>
        <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Password <span class="text-danger">*</span></label>
        <input type="password" name="password" class="form-control" required minlength="6">
      </div>

      <div class="mb-3">
        <label class="form-label">Name</label>
        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" placeholder="Display name">
      </div>

      <div class="mb-3">
        <label class="form-label">Phone number</label>
        <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Branch <span class="text-danger">*</span></label>
        <select name="branch_id" class="form-select" required>
          <option value="">Select branch</option>
          <?php foreach ($branches as $b): ?>
            <option value="<?= htmlspecialchars($b['id'] ?? '') ?>" <?= ($_POST['branch_id'] ?? '') === ($b['id'] ?? '') ? 'selected' : '' ?>>
              <?= htmlspecialchars($b['name'] ?? '') ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Role</label>
        <select name="role" class="form-select">
          <?php foreach ($roles as $r): ?>
            <option value="<?= htmlspecialchars($r) ?>" <?= ($_POST['role'] ?? 'user') === $r ? 'selected' : '' ?>><?= htmlspecialchars(ucfirst($r)) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="card-footer">
      <button type="submit" class="btn btn-primary">Create User</button>
      <a href="<?= base_url('?page=users') ?>" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>
