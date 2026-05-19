<?php
$pageTitle = 'Modules';
$activePage = 'modules';
require __DIR__ . '/partials/admin_header.php';
?>
<div class="admin-content">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem;">
    <p style="color:var(--text-muted);"><?= count($modules) ?> module<?= count($modules) !== 1 ? 's' : '' ?> total</p>
    <a href="<?= url('/admin/modules/create') ?>" class="btn btn-primary">&#43; Add Module</a>
  </div>

  <?php if (empty($modules)): ?>
  <div class="empty-state">
    <div class="empty-icon" aria-hidden="true">&#128218;</div>
    <h2>No modules yet</h2>
    <a href="/admin/modules/create" class="btn btn-primary" style="margin-top:1rem;">Add your first module</a>
  </div>
  <?php else: ?>
  <div class="table-wrapper">
    <table aria-label="Modules management table">
      <thead>
        <tr>
          <th scope="col">Title</th>
          <th scope="col">Credits</th>
          <th scope="col">Module Leader</th>
          <th scope="col">Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($modules as $m): ?>
      <tr>
        <td><strong><?= htmlspecialchars($m['title']) ?></strong></td>
        <td><?= (int)$m['credits'] ?></td>
        <td><?= htmlspecialchars($m['leader_name'] ?? '—') ?></td>
        <td>
          <div class="actions-cell">
            <a href="<?= url('/admin/modules/' . $m['id'] . '/edit') ?>" class="btn btn-sm btn-primary" aria-label="Edit <?= htmlspecialchars($m['title']) ?>">Edit</a>
            <form method="POST" action="<?= url('/admin/modules/' . $m['id'] . '/delete') ?>" style="display:inline;"
                  onsubmit="return confirm('Delete \'<?= addslashes($m['title']) ?>\'? This cannot be undone.');">
              <button type="submit" class="btn btn-sm btn-danger" aria-label="Delete <?= htmlspecialchars($m['title']) ?>">Delete</button>
            </form>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
<?php require __DIR__ . '/partials/admin_footer.php'; ?>
