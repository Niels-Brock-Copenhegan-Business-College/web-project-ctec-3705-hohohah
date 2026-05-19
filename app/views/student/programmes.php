<?php
$pageTitle = 'Browse Programmes';
$activePage = 'programmes';
require __DIR__ . '/../partials/header.php';

$levelClass = ['Undergraduate' => 'level-ug', 'Postgraduate' => 'level-pg'];
?>

<div class="container">
  <h1 class="page-title">Browse Programmes</h1>
  <p class="page-subtitle">
    <?= count($programmes) ?> programme<?= count($programmes) !== 1 ? 's' : '' ?> found
    <?= $search ? 'for "<strong>' . htmlspecialchars($search) . '</strong>"' : '' ?>
    <?= $levelFilter ? 'at <strong>' . htmlspecialchars($levelFilter) . '</strong> level' : '' ?>
  </p>

  <section class="search-bar" aria-label="Search and filter programmes">
    <form method="GET" action="<?= url('/programmes') ?>" role="search">
      <div class="search-group">
        <label for="search-input">Search</label>
        <input id="search-input" type="search" name="search" placeholder="Programme title or keyword..."
               value="<?= htmlspecialchars($search) ?>" aria-label="Search programmes by keyword">
      </div>
      <div class="search-group" style="max-width:200px;">
        <label for="level-select">Level</label>
        <select id="level-select" name="level" aria-label="Filter by level">
          <option value="">All levels</option>
          <option value="Undergraduate" <?= $levelFilter === 'Undergraduate' ? 'selected' : '' ?>>Undergraduate</option>
          <option value="Postgraduate" <?= $levelFilter === 'Postgraduate' ? 'selected' : '' ?>>Postgraduate</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Search</button>
      <?php if ($search || $levelFilter): ?>
      <a href="<?= url('/programmes') ?>" class="btn btn-outline">Clear</a>
      <?php endif; ?>
    </form>
  </section>

  <?php if (empty($programmes)): ?>
  <div class="empty-state" role="status" aria-live="polite">
    <div class="empty-icon" aria-hidden="true">&#128269;</div>
    <h2>No programmes found</h2>
    <p>Try different search terms or <a href="<?= url('/programmes') ?>">browse all programmes</a>.</p>
  </div>
  <?php else: ?>
  <ul class="programme-grid" role="list" aria-live="polite" style="list-style:none;padding:0;margin:0;">
    <?php foreach ($programmes as $p): ?>
    <li>
      <article class="programme-card" aria-labelledby="p-<?= $p['id'] ?>">
        <div class="card-image" aria-hidden="true">
          <?php if ($p['image']): ?>
            <img src="<?= url('/images/' . htmlspecialchars($p['image'])) ?>" alt="">
          <?php else: ?>&#127979;<?php endif; ?>
        </div>
        <div class="card-body">
          <span class="card-level <?= $levelClass[$p['level']] ?? 'level-ug' ?>"><?= htmlspecialchars($p['level']) ?></span>
          <h2 class="card-title" id="p-<?= $p['id'] ?>"><?= htmlspecialchars($p['title']) ?></h2>
          <p class="card-desc"><?= htmlspecialchars(mb_substr($p['description'], 0, 130)) ?>...</p>
          <div class="card-meta">
            <span>&#128197; <?= (int)$p['duration_years'] ?> year<?= $p['duration_years'] > 1 ? 's' : '' ?></span>
            <?php if ($p['leader_name']): ?>
            <span>&#128100; <?= htmlspecialchars($p['leader_name']) ?></span>
            <?php endif; ?>
          </div>
        </div>
        <div class="card-footer">
          <a href="<?= url('/programmes/' . $p['id']) ?>" class="btn btn-primary btn-sm" aria-label="View details for <?= htmlspecialchars($p['title']) ?>">View Details &rarr;</a>
        </div>
      </article>
    </li>
    <?php endforeach; ?>
  </ul>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
