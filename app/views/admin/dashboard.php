<?php require __DIR__ . '/partials/admin_header.php'; ?>

<div class="admin-content">
  <div class="stats-grid" role="list" aria-label="Dashboard statistics overview">
    <div class="stat-card" role="listitem">
      <div class="stat-number"><?= $stats['total_programmes'] ?></div>
      <div class="stat-label">Total Programmes</div>
    </div>

    <div class="stat-card" role="listitem">
      <div class="stat-number"><?= $stats['published_programmes'] ?></div>
      <div class="stat-label">Published</div>
    </div>

    <div class="stat-card" role="listitem">
      <div class="stat-number"><?= $stats['total_modules'] ?></div>
      <div class="stat-label">Modules</div>
    </div>

    <div class="stat-card" role="listitem">
      <div class="stat-number"><?= $stats['total_registrations'] ?></div>
      <div class="stat-label">Interest Registrations</div>
    </div>
  </div>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;flex-wrap:wrap;">
    
    <section aria-labelledby="quick-actions-heading">
      <h2 id="quick-actions-heading"
          style="font-size:1.1rem;font-weight:700;color:var(--primary);margin-bottom:1rem;">
          Quick Actions
      </h2>

      <div style="display:flex;flex-direction:column;gap:0.75rem;">
        <a href="<?= url('/admin/programmes/create') ?>" class="btn btn-primary">
          &#43; Add New Programme
        </a>

        <a href="<?= url('/admin/modules/create') ?>" class="btn btn-outline">
          &#43; Add New Module
        </a>

        <a href="<?= url('/admin/mailing-list') ?>" class="btn btn-outline">
          &#128203; View Mailing List
        </a>

        <a href="<?= url('/admin/mailing-list/export') ?>" class="btn btn-success">
          &#8659; Export CSV
        </a>
      </div>
    </section>

    <section aria-labelledby="recent-heading">
      <h2 id="recent-heading"
          style="font-size:1.1rem;font-weight:700;color:var(--primary);margin-bottom:1rem;">
          Recent Registrations
      </h2>

      <?php if (empty($recentRegistrations)): ?>

      <p style="color:var(--text-muted);font-size:0.9rem;">
        No registrations available yet.
      </p>

      <?php else: ?>

      <div class="table-wrapper">
        <table aria-label="Recent interest registrations">
          <thead>
            <tr>
              <th>Name</th>
              <th>Programme</th>
              <th>Date</th>
            </tr>
          </thead>

          <tbody>
          <?php foreach ($recentRegistrations as $r): ?>
            <tr>
              <td><?= htmlspecialchars($r['first_name'] . ' ' . $r['last_name']) ?></td>
              <td><?= htmlspecialchars($r['programme_title']) ?></td>
              <td><?= htmlspecialchars(date('d M Y', strtotime($r['registered_at']))) ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <a href="<?= url('/admin/mailing-list') ?>"
         style="font-size:0.85rem;color:var(--primary);margin-top:0.75rem;display:inline-block;">
         View all &rarr;
      </a>

      <?php endif; ?>
    </section>
  </div>
</div>

<?php require __DIR__ . '/partials/admin_footer.php'; ?>
