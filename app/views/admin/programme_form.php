<?php
$isEdit = !empty($programme);
$pageTitle = $isEdit ? 'Edit Programme' : 'Add Programme';
$activePage = 'programmes';
require __DIR__ . '/partials/admin_header.php';
$val = fn(string $k) => htmlspecialchars($old[$k] ?? ($programme[$k] ?? ''));
?>
<div class="admin-content">
  <div style="margin-bottom:1.5rem;">
    <a href="<?= url('/admin/programmes') ?>" class="btn btn-outline btn-sm">&larr; Back to Programmes</a>
  </div>

  <?php if (!empty($errors)): ?>
  <div class="alert alert-error" role="alert">Please correct the errors below.</div>
  <?php endif; ?>

  <div class="table-wrapper" style="padding:2rem;max-width:700px;">
    <form method="POST" action="<?= $isEdit ? url('/admin/programmes/' . $programme['id'] . '/update') : url('/admin/programmes/store') ?>" novalidate>

      <div class="form-group">
        <label for="title">Programme Title <span class="required" aria-label="required">*</span></label>
        <input class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>"
               type="text" id="title" name="title" value="<?= $val('title') ?>" required maxlength="200">
        <?php if (isset($errors['title'])): ?><span class="error-msg" role="alert"><?= htmlspecialchars($errors['title']) ?></span><?php endif; ?>
      </div>

      <div class="form-group">
        <label for="level">Level <span class="required" aria-label="required">*</span></label>
        <select class="form-control <?= isset($errors['level']) ? 'is-invalid' : '' ?>" id="level" name="level" required>
          <option value="">-- Select level --</option>
          <option value="Undergraduate" <?= $val('level') === 'Undergraduate' ? 'selected' : '' ?>>Undergraduate</option>
          <option value="Postgraduate" <?= $val('level') === 'Postgraduate' ? 'selected' : '' ?>>Postgraduate</option>
        </select>
        <?php if (isset($errors['level'])): ?><span class="error-msg" role="alert"><?= htmlspecialchars($errors['level']) ?></span><?php endif; ?>
      </div>

      <div class="form-group">
        <label for="duration_years">Duration (years) <span class="required" aria-label="required">*</span></label>
        <input class="form-control <?= isset($errors['duration_years']) ? 'is-invalid' : '' ?>"
               type="number" id="duration_years" name="duration_years"
               value="<?= $val('duration_years') ?: 3 ?>" min="1" max="6" required>
        <?php if (isset($errors['duration_years'])): ?><span class="error-msg" role="alert"><?= htmlspecialchars($errors['duration_years']) ?></span><?php endif; ?>
      </div>

      <div class="form-group">
        <label for="programme_leader_id">Programme Leader</label>
        <select class="form-control" id="programme_leader_id" name="programme_leader_id">
          <option value="">-- None --</option>
          <?php foreach ($allStaff as $s): ?>
          <option value="<?= $s['id'] ?>" <?= (string)($old['programme_leader_id'] ?? ($programme['programme_leader_id'] ?? '')) === (string)$s['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($s['name']) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label for="description">Description <span class="required" aria-label="required">*</span></label>
        <textarea class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>"
                  id="description" name="description" rows="6" required><?= $val('description') ?></textarea>
        <?php if (isset($errors['description'])): ?><span class="error-msg" role="alert"><?= htmlspecialchars($errors['description']) ?></span><?php endif; ?>
      </div>

      <div class="form-group" style="display:flex;align-items:center;gap:0.75rem;">
        <input type="checkbox" id="published" name="published" value="1"
               <?= (isset($old['published']) || (!isset($old) && !empty($programme['published']))) ? 'checked' : '' ?>
               style="width:18px;height:18px;cursor:pointer;">
        <label for="published" style="margin:0;cursor:pointer;">Published (visible on student site)</label>
      </div>

      <div style="display:flex;gap:1rem;margin-top:1.5rem;">
        <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Save Changes' : 'Create Programme' ?></button>
        <a href="<?= url('/admin/programmes') ?>" class="btn btn-outline">Cancel</a>
      </div>
    </form>
  </div>
</div>
<?php require __DIR__ . '/partials/admin_footer.php'; ?>
