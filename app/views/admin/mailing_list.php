<?php
$pageTitle = 'Mailing List';
$activePage = 'mailing-list';
require __DIR__ . '/partials/admin_header.php';

$registrationCount = count($registrations);
?>

<div class="admin-content">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem;">
    
    <p style="color:var(--text-muted);">
      <?= $registrationCount ?> registration<?= $registrationCount !== 1 ? 's' : '' ?> total
    </p>

    <div style="display:flex;gap:0.75rem;flex-wrap:wrap;">
      
      <form method="GET" action="<?= url('/admin/mailing-list') ?>" style="display:flex;gap:0.5rem;align-items:center;">
        <label for="filter-prog" style="font-size:0.88rem;white-space:nowrap;">
          Filter by programme:
        </label>

        <select
          id="filter-prog"
          name="programme_id"
          class="form-control"
          style="width:auto;min-width:180px;"
          onchange="this.form.submit()"
        >
          <option value="">All programmes</option>

          <?php foreach ($allProgrammes as $p): ?>
          <option
            value="<?= $p['id'] ?>"
            <?= (string)($filterProgrammeId ?? '') === (string)$p['id'] ? 'selected' : '' ?>
          >
            <?= htmlspecialchars($p['title']) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </form>

      <a
        href="<?= url('/admin/mailing-list/export') . ($filterProgrammeId ? '?programme_id=' . (int)$filterProgrammeId : '') ?>"
        class="btn btn-success"
      >
        &#8659; Export CSV
      </a>
    </div>
  </div>

  <?php if (empty($registrations)): ?>
  
  <div class="empty-state">
    <div class="empty-icon" aria-hidden="true">&#128203;</div>

    <h2>No registrations yet</h2>

    <p>
      Students can register their interest from each programme page.
    </p>
  </div>

  <?php else: ?>

  <div class="table-wrapper">
    <table aria-label="Student interest registrations">
      
      <thead>
        <tr>
          <th scope="col">Name</th>
          <th scope="col">Email</th>
          <th scope="col">Phone</th>
          <th scope="col">Programme</th>
          <th scope="col">Date</th>
          <th scope="col">Actions</th>
        </tr>
      </thead>

      <tbody>
        <?php foreach ($registrations as $r): ?>
        <tr>

          <td>
            <?= htmlspecialchars($r['first_name'] . ' ' . $r['last_name']) ?>
          </td>

          <td>
            <a href="mailto:<?= htmlspecialchars($r['email']) ?>">
              <?= htmlspecialchars($r['email']) ?>
            </a>
          </td>

          <td>
            <?= htmlspecialchars($r['phone'] ?? '—') ?>
          </td>

          <td>
            <?= htmlspecialchars($r['programme_title']) ?>
          </td>

          <td>
            <?= date('d M Y', strtotime($r['registered_at'])) ?>
          </td>

          <td>
            <form
              method="POST"
              action="<?= url('/admin/mailing-list/' . (int)$r['id'] . '/delete') ?>"
              style="display:inline;"
              onsubmit="return confirm('Remove this registration?');"
            >
              <button
                type="submit"
                class="btn btn-sm btn-danger"
                aria-label="Remove registration for <?= htmlspecialchars($r['email']) ?>"
              >
                Remove
              </button>
            </form>
          </td>

        </tr>
        <?php endforeach; ?>
      </tbody>

    </table>
  </div>

  <?php endif; ?>
</div>

<?php require __DIR__ . '/partials/admin_footer.php'; ?>
