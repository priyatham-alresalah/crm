<?php
$profile = $profile ?? ['name' => '', 'email' => '', 'phone' => ''];
$error = $_SESSION['form_error'] ?? '';
if ($error) {
    unset($_SESSION['form_error']);
}
?>
<div class="card">
  <div class="card-header">
    <h3 class="card-title mb-0">My Profile</h3>
  </div>
  <form method="post" action="<?= base_url('?page=profile') ?>">
    <div class="card-body">
      <?php if ($error): ?>
        <div class="alert alert-info mb-3"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <div class="mb-3">
        <label class="form-label">Name</label>
        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($profile['name'] ?? '') ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($profile['email'] ?? '') ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Phone number</label>
        <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($profile['phone'] ?? '') ?>">
      </div>

      <hr>
      <h5 class="mb-3">Change Password</h5>
      <div class="mb-3">
        <label class="form-label">New password</label>
        <input type="password" name="password" class="form-control" autocomplete="new-password">
      </div>
      <div class="mb-3">
        <label class="form-label">Confirm new password</label>
        <input type="password" name="password_confirm" class="form-control" autocomplete="new-password">
      </div>
      <p class="text-muted small mb-0">Leave password fields blank if you do not want to change it.</p>
    </div>
    <div class="card-footer">
      <button type="submit" class="btn btn-primary">Save changes</button>
    </div>
  </form>
</div>

