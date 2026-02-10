<?php
$templates = $templates ?? [];
$clients = $clients ?? [];
$title = $title ?? 'Email Generator';
?>
<div class="card">
  <div class="card-header">
    <h3 class="card-title mb-0">Email Generator</h3>
  </div>
  <div class="card-body">
    <p class="text-muted">Select a template and optional client to fill placeholders. Copy to clipboard (no sending).</p>

    <script>window.EMAIL_TEMPLATES = <?= json_encode(array_column($templates, 'content', 'id')) ?>;</script>
    <div class="row">
      <div class="col-md-6">
        <div class="mb-3">
          <label class="form-label">Template</label>
          <select id="templateSelect" class="form-select">
            <option value="">— Select template —</option>
            <?php foreach ($templates as $t): ?>
              <option value="<?= (int)($t['id'] ?? 0) ?>"><?= htmlspecialchars($t['name'] ?? '') ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Client (for {{client_name}})</label>
          <select id="clientSelect" class="form-select">
            <option value="">— None —</option>
            <?php foreach ($clients as $c): ?>
              <option value="<?= htmlspecialchars($c['client_name'] ?? '', ENT_QUOTES) ?>"><?= htmlspecialchars($c['client_name'] ?? '') ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Your name (for {{your_name}})</label>
          <input type="text" id="yourName" class="form-control" placeholder="Your name">
        </div>
        <button type="button" id="fillBtn" class="btn btn-primary">Fill placeholders</button>
        <button type="button" id="copyBtn" class="btn btn-success">Copy to clipboard</button>
      </div>
    </div>

    <div class="mt-4">
      <label class="form-label">Preview / Content</label>
      <textarea id="previewContent" class="form-control" rows="12" placeholder="Select a template and click Fill placeholders."></textarea>
    </div>
  </div>
</div>

<script>
(function() {
  var templateSelect = document.getElementById('templateSelect');
  var clientSelect = document.getElementById('clientSelect');
  var yourName = document.getElementById('yourName');
  var preview = document.getElementById('previewContent');
  var fillBtn = document.getElementById('fillBtn');
  var copyBtn = document.getElementById('copyBtn');

  function getTemplateContent() {
    var id = templateSelect.value;
    return (window.EMAIL_TEMPLATES && window.EMAIL_TEMPLATES[id]) ? window.EMAIL_TEMPLATES[id] : '';
  }

  fillBtn.addEventListener('click', function() {
    var content = getTemplateContent();
    var clientName = clientSelect.value;
    var name = yourName.value.trim();
    content = content.replace(/\{\{client_name\}\}/g, clientName || '{{client_name}}');
    content = content.replace(/\{\{your_name\}\}/g, name || '{{your_name}}');
    preview.value = content;
  });

  templateSelect.addEventListener('change', function() {
    preview.value = getTemplateContent();
  });

  copyBtn.addEventListener('click', function() {
    preview.select();
    document.execCommand('copy');
    copyBtn.textContent = 'Copied!';
    setTimeout(function() { copyBtn.textContent = 'Copy to clipboard'; }, 2000);
  });
})();
</script>
