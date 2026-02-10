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
  <div class="card-header d-flex justify-content-between align-items-center">
    <h3 class="card-title mb-0">Client Contacts</h3>
    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addContactModal">Add Contact</button>
  </div>
  <div class="card-body p-0">
    <?php if (empty($contacts)): ?>
    <p class="text-muted mb-0 p-3">No contacts yet. Click &quot;Add Contact&quot; to add one.</p>
    <?php else: ?>
    <div class="table-responsive-crm">
      <table class="table table-hover table-bordered mb-0">
        <thead class="table-light">
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Designation</th>
            <th>Primary</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($contacts as $c): ?>
          <tr>
            <td><?= htmlspecialchars($c['contact_name'] ?? '') ?></td>
            <td><?= ($c['contact_email'] ?? '') !== '' ? htmlspecialchars($c['contact_email']) : '—' ?></td>
            <td><?= ($c['contact_phone'] ?? '') !== '' ? htmlspecialchars($c['contact_phone']) : '—' ?></td>
            <td><?= ($c['designation'] ?? '') !== '' ? htmlspecialchars($c['designation']) : '—' ?></td>
            <td><?php if (!empty($c['is_primary'])): ?><span class="badge bg-primary">Primary</span><?php else: ?>—<?php endif; ?></td>
            <td>
              <button type="button" class="btn btn-sm btn-outline-secondary edit-contact" data-id="<?= htmlspecialchars($c['id'] ?? '', ENT_QUOTES) ?>" data-name="<?= htmlspecialchars($c['contact_name'] ?? '', ENT_QUOTES) ?>" data-email="<?= htmlspecialchars($c['contact_email'] ?? '', ENT_QUOTES) ?>" data-phone="<?= htmlspecialchars($c['contact_phone'] ?? '', ENT_QUOTES) ?>" data-designation="<?= htmlspecialchars($c['designation'] ?? '', ENT_QUOTES) ?>" data-primary="<?= !empty($c['is_primary']) ? '1' : '0' ?>">Edit</button>
              <?php if (empty($c['is_primary'])): ?>
              <a href="<?= base_url('?page=client_contacts/primary&client_id=' . urlencode($id) . '&id=' . urlencode($c['id'] ?? '')) ?>" class="btn btn-sm btn-outline-primary">Set primary</a>
              <?php endif; ?>
              <a href="<?= base_url('?page=client_contacts/delete&id=' . urlencode($c['id'] ?? '')) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this contact?');">Delete</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- Add Contact Modal -->
<div class="modal fade" id="addContactModal" tabindex="-1" aria-labelledby="addContactModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" action="<?= base_url('?page=client_contacts/store') ?>">
        <input type="hidden" name="client_id" value="<?= htmlspecialchars($id) ?>">
        <div class="modal-header">
          <h5 class="modal-title" id="addContactModalLabel">Add Contact</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Name <span class="text-danger">*</span></label>
            <input type="text" name="contact_name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="contact_email" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="text" name="contact_phone" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Designation</label>
            <input type="text" name="designation" class="form-control">
          </div>
          <div class="form-check">
            <input type="checkbox" name="is_primary" value="1" class="form-check-input" id="add_is_primary">
            <label class="form-check-label" for="add_is_primary">Primary contact</label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Contact Modal -->
<div class="modal fade" id="editContactModal" tabindex="-1" aria-labelledby="editContactModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" action="<?= base_url('?page=client_contacts/update') ?>" id="editContactForm">
        <input type="hidden" name="id" id="edit_contact_id" value="">
        <div class="modal-header">
          <h5 class="modal-title" id="editContactModalLabel">Edit Contact</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Name <span class="text-danger">*</span></label>
            <input type="text" name="contact_name" id="edit_contact_name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="contact_email" id="edit_contact_email" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="text" name="contact_phone" id="edit_contact_phone" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Designation</label>
            <input type="text" name="designation" id="edit_contact_designation" class="form-control">
          </div>
          <div class="form-check">
            <input type="checkbox" name="is_primary" value="1" class="form-check-input" id="edit_is_primary">
            <label class="form-check-label" for="edit_is_primary">Primary contact</label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
(function() {
  var editModalEl = document.getElementById('editContactModal');
  document.querySelectorAll('.edit-contact').forEach(function(btn) {
    btn.addEventListener('click', function() {
      document.getElementById('edit_contact_id').value = this.dataset.id || '';
      document.getElementById('edit_contact_name').value = this.dataset.name || '';
      document.getElementById('edit_contact_email').value = this.dataset.email || '';
      document.getElementById('edit_contact_phone').value = this.dataset.phone || '';
      document.getElementById('edit_contact_designation').value = this.dataset.designation || '';
      document.getElementById('edit_is_primary').checked = this.dataset.primary === '1';
      var modal = new bootstrap.Modal(editModalEl);
      modal.show();
    });
  });
})();
</script>

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
