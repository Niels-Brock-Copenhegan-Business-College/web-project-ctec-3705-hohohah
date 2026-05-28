<?php
$pageTitle = 'Programmes';
$activePage = 'programmes';

require __DIR__ . '/partials/admin_header.php';

$programmeCount = count($programmes);
?>

<div class="admin-content">

  <div
    style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem;"
  >

    <p style="color:var(--text-muted);">
      <?= $programmeCount ?> programme<?= $programmeCount !== 1 ? 's' : '' ?> total
    </p>

    <a
      href="<?= url('/admin/programmes/create') ?>"
      class="btn btn-primary"
    >
      &#43; Add Programme
    </a>

  </div>

  <?php if (empty($programmes)): ?>

  <div class="empty-state">

    <div class="empty-icon" aria-hidden="true">
      &#127979;
    </div>

    <h2>No programmes yet</h2>

    <a
      href="/admin/programmes/create"
      class="btn btn-primary"
      style="margin-top:1rem;"
    >
      Add your first programme
    </a>

  </div>

  <?php else: ?>

  <div class="table-wrapper">

    <table aria-label="Programmes management table">

      <thead>
        <tr>
          <th scope="col">Title</th>
          <th scope="col">Level</th>
          <th scope="col">Duration</th>
          <th scope="col">Status</th>
          <th scope="col">Leader</th>
          <th scope="col">Actions</th>
        </tr>
      </thead>

      <tbody>

        <?php foreach ($programmes as $p): ?>
        <tr>

          <td>
            <strong>
              <?= htmlspecialchars($p['title']) ?>
            </strong>
          </td>

          <td>
            <?= htmlspecialchars($p['level']) ?>
          </td>

          <td>
            <?= (int)$p['duration_years'] ?> yr
          </td>

          <td>
            <span class="badge <?= $p['published'] ? 'badge-published' : 'badge-draft' ?>">
              <?= $p['published'] ? 'Published' : 'Draft' ?>
            </span>
          </td>

          <td>
            <?= htmlspecialchars($p['leader_name'] ?? '—') ?>
          </td>

          <td>
            <div class="actions-cell">

              <a
                href="<?= url('/programmes/' . $p['id']) ?>"
                class="btn btn-sm btn-outline"
                target="_blank"
                aria-label="View <?= htmlspecialchars($p['title']) ?> on student site"
              >
                View
              </a>

              <a
                href="<?= url('/admin/programmes/' . $p['id'] . '/edit') ?>"
                class="btn btn-sm btn-primary"
                aria-label="Edit <?= htmlspecialchars($p['title']) ?>"
              >
                Edit
              </a>

              <form
                method="POST"
                action="<?= url('/admin/programmes/' . $p['id'] . '/toggle') ?>"
                style="display:inline;"
              >
                <button
                  type="submit"
                  class="btn btn-sm btn-outline"
                  aria-label="<?= $p['published'] ? 'Unpublish' : 'Publish' ?> <?= htmlspecialchars($p['title']) ?>"
                >
                  <?= $p['published'] ? 'Unpublish' : 'Publish' ?>
                </button>
              </form>

              <form
                method="POST"
                action="<?= url('/admin/programmes/' . $p['id'] . '/delete') ?>"
                style="display:inline;"
                onsubmit="return confirm('Delete \'<?= addslashes($p['title']) ?>\'? This cannot be undone.');"
              >
                <button
                  type="submit"
                  class="btn btn-sm btn-danger"
                  aria-label="Delete <?= htmlspecialchars($p['title']) ?>"
                >
                  Delete
                </button>
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
