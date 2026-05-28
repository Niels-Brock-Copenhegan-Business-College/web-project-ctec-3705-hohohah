<?php
$isEdit = !empty($module);
$pageTitle = $isEdit ? 'Edit Module' : 'Add Module';
$activePage = 'modules';

require __DIR__ . '/partials/admin_header.php';

$val = fn(string $k) => htmlspecialchars($old[$k] ?? ($module[$k] ?? ''));
$formAction = $isEdit
    ? url('/admin/modules/' . $module['id'] . '/update')
    : url('/admin/modules/store');
?>

<div class="admin-content">

  <div style="margin-bottom:1.5rem;">
    <a href="<?= url('/admin/modules') ?>" class="btn btn-outline btn-sm">
      &larr; Back to Modules
    </a>
  </div>

  <?php if (!empty($errors)): ?>
  <div class="alert alert-error" role="alert">
    Please correct the errors below.
  </div>
  <?php endif; ?>

  <div class="table-wrapper" style="padding:2rem;max-width:700px;">

    <form method="POST" action="<?= $formAction ?>" novalidate>

      <div class="form-group">
        <label for="title">
          Module Title
          <span class="required" aria-label="required">*</span>
        </label>

        <input
          class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>"
          type="text"
          id="title"
          name="title"
          value="<?= $val('title') ?>"
          required
          maxlength="200"
        >

        <?php if (isset($errors['title'])): ?>
        <span class="error-msg" role="alert">
          <?= htmlspecialchars($errors['title']) ?>
        </span>
        <?php endif; ?>
      </div>

      <div class="form-group">
        <label for="credits">
          Credits
          <span class="required" aria-label="required">*</span>
        </label>

        <input
          class="form-control <?= isset($errors['credits']) ? 'is-invalid' : '' ?>"
          type="number"
          id="credits"
          name="credits"
          value="<?= $val('credits') ?: 20 ?>"
          min="1"
          max="120"
          required
        >

        <?php if (isset($errors['credits'])): ?>
        <span class="error-msg" role="alert">
          <?= htmlspecialchars($errors['credits']) ?>
        </span>
        <?php endif; ?>
      </div>

      <div class="form-group">
        <label for="module_leader_id">Module Leader</label>

        <select
          class="form-control"
          id="module_leader_id"
          name="module_leader_id"
        >
          <option value="">-- None --</option>

          <?php foreach ($allStaff as $s): ?>
          <option
            value="<?= $s['id'] ?>"
            <?= (string)($old['module_leader_id'] ?? ($module['module_leader_id'] ?? '')) === (string)$s['id'] ? 'selected' : '' ?>
          >
            <?= htmlspecialchars($s['name']) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label for="description">Description</label>

        <textarea
          class="form-control"
          id="description"
          name="description"
          rows="5"
        ><?= $val('description') ?></textarea>
      </div>

      <div style="display:flex;gap:1rem;margin-top:1.5rem;">

        <button type="submit" class="btn btn-primary">
          <?= $isEdit ? 'Save Changes' : 'Create Module' ?>
        </button>

        <a href="<?= url('/admin/modules') ?>" class="btn btn-outline">
          Cancel
        </a>

      </div>

    </form>
  </div>
</div>

<?php require __DIR__ . '/partials/admin_footer.php'; ?>
