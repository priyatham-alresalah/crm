<?php
$templates = $templates ?? [];
$categories = $categories ?? ['All', 'Intro', 'Follow-up', 'Reminder', 'Closure'];
$title = $title ?? 'Email Generator';
$templatesJson = json_encode($templates);
?>
<div class="card">
  <div class="card-header">
    <h3 class="card-title mb-0">Email Generator</h3>
  </div>
  <div class="card-body">
    <p class="text-muted mb-3">Select a category and template. Edit subject/body if needed. Copy to clipboard (no sending). Placeholders: <code>{{client_name}}</code>, <code>{{contact_name}}</code> left for future linking.</p>

    <div class="row g-3 mb-3">
      <div class="col-md-3">
        <label class="form-label">Category</label>
        <select id="categorySelect" class="form-select">
          <?php foreach ($categories as $cat): ?>
            <option value="<?= htmlspecialchars($cat, ENT_QUOTES) ?>"><?= htmlspecialchars($cat) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-5">
        <label class="form-label">Template Name</label>
        <select id="templateSelect" class="form-select">
          <option value="">— Select template —</option>
          <?php foreach ($templates as $t): ?>
            <option value="<?= (int) ($t['id'] ?? 0) ?>" data-category="<?= htmlspecialchars((string) ($t['category'] ?? ''), ENT_QUOTES) ?>"><?= htmlspecialchars($t['name'] ?? '') ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <div class="mb-3">
      <label class="form-label">Subject</label>
      <input type="text" id="subjectInput" class="form-control" placeholder="Email subject">
    </div>

    <div class="mb-3">
      <label class="form-label">Body</label>
      <textarea id="bodyInput" class="form-control" rows="14" placeholder="Select a template to fill subject and body."></textarea>
    </div>

    <button type="button" id="copyBtn" class="btn btn-success">Copy Email</button>
  </div>
</div>

<div id="emailToast" class="toast align-items-center text-bg-success border-0 position-fixed bottom-0 end-0 m-3" role="alert" aria-live="assertive" aria-atomic="true" style="z-index: 1090;">
  <div class="d-flex">
    <div class="toast-body">Email copied to clipboard.</div>
    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
  </div>
</div>

<script>
(function() {
  var CATEGORY_ALL = 'All';
  var categorySelect = document.getElementById('categorySelect');
  var templateSelect = document.getElementById('templateSelect');
  var subjectInput = document.getElementById('subjectInput');
  var bodyInput = document.getElementById('bodyInput');
  var copyBtn = document.getElementById('copyBtn');
  var toastEl = document.getElementById('emailToast');

  var templates = <?= $templatesJson ?>;

  function getTemplatesByCategory() {
    var cat = categorySelect.value;
    if (cat === CATEGORY_ALL) return templates;
    return templates.filter(function(t) { return (t.category || '') === cat; });
  }

  function refreshTemplateOptions() {
    var list = getTemplatesByCategory();
    var current = templateSelect.value;
    templateSelect.innerHTML = '<option value="">— Select template —</option>';
    list.forEach(function(t) {
      var opt = document.createElement('option');
      opt.value = t.id;
      opt.textContent = t.name || ('Template ' + t.id);
      opt.dataset.subject = t.subject || '';
      opt.dataset.body = t.body || '';
      templateSelect.appendChild(opt);
    });
    if (current && list.some(function(t) { return String(t.id) === current; })) {
      templateSelect.value = current;
    } else {
      subjectInput.value = '';
      bodyInput.value = '';
    }
  }

  function fillFromTemplate() {
    var opt = templateSelect.options[templateSelect.selectedIndex];
    if (!opt || !opt.value) {
      subjectInput.value = '';
      bodyInput.value = '';
      return;
    }
    subjectInput.value = opt.dataset.subject || '';
    bodyInput.value = opt.dataset.body || '';
  }

  categorySelect.addEventListener('change', function() {
    refreshTemplateOptions();
    fillFromTemplate();
  });

  templateSelect.addEventListener('change', fillFromTemplate);

  copyBtn.addEventListener('click', function() {
    var subject = subjectInput.value.trim();
    var body = bodyInput.value.trim();
    var text = subject ? (subject + '\n\n' + body) : body;
    if (!text) {
      if (typeof bootstrap !== 'undefined' && toastEl) {
        var t = new bootstrap.Toast(toastEl);
        toastEl.querySelector('.toast-body').textContent = 'Nothing to copy. Select a template first.';
        toastEl.classList.remove('text-bg-success');
        toastEl.classList.add('text-bg-warning');
        t.show();
        setTimeout(function() { toastEl.classList.remove('text-bg-warning'); toastEl.classList.add('text-bg-success'); toastEl.querySelector('.toast-body').textContent = 'Email copied to clipboard.'; }, 0);
      }
      return;
    }
    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(text).then(function() {
        if (toastEl && typeof bootstrap !== 'undefined') {
          var toast = new bootstrap.Toast(toastEl);
          toastEl.querySelector('.toast-body').textContent = 'Email copied to clipboard.';
          toast.show();
        } else {
          copyBtn.textContent = 'Copied!';
          setTimeout(function() { copyBtn.textContent = 'Copy Email'; }, 2000);
        }
      });
    } else {
      var ta = document.createElement('textarea');
      ta.value = text;
      ta.style.position = 'fixed';
      ta.style.opacity = '0';
      document.body.appendChild(ta);
      ta.select();
      document.execCommand('copy');
      document.body.removeChild(ta);
      if (toastEl && typeof bootstrap !== 'undefined') {
        var t = new bootstrap.Toast(toastEl);
        toastEl.querySelector('.toast-body').textContent = 'Email copied to clipboard.';
        t.show();
      } else {
        copyBtn.textContent = 'Copied!';
        setTimeout(function() { copyBtn.textContent = 'Copy Email'; }, 2000);
      }
    }
  });

  refreshTemplateOptions();
})();
</script>
