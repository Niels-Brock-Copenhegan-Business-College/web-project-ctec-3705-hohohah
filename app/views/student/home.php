<?php
$pageTitle = 'Home';
$activePage = 'home';
$metaDescription = 'Explore undergraduate and postgraduate degree programmes. Find your perfect course and register your interest today.';
require __DIR__ . '/../partials/header.php';

$levelIcons = ['Undergraduate' => '&#127979;', 'Postgraduate' => '&#127891;'];
$levelClass = ['Undergraduate' => 'level-ug', 'Postgraduate' => 'level-pg'];
?>

<section class="hero" aria-labelledby="hero-heading">
  <h1 id="hero-heading">Find Your Perfect Degree Programme</h1>
  <p>Explore our undergraduate and postgraduate courses, discover modules and faculty, and register your interest in seconds.</p>
  <form action="<?= url('/programmes') ?>" method="GET" role="search" aria-label="Quick programme search">
    <div style="display:flex;gap:0.75rem;justify-content:center;flex-wrap:wrap;max-width:600px;margin:0 auto;">
      <div style="flex:1;min-width:200px;position:relative;">
        <label for="hero-search" class="sr-only" style="position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0,0,0,0);">Search programmes</label>
        <input id="hero-search" type="search" name="search" placeholder="e.g. Cyber Security, Data..." aria-label="Search programmes"
          style="width:100%;padding:0.7rem 1rem;border:none;border-radius:8px;font-size:1rem;">
      </div>
      <button type="submit" class="btn btn-accent" style="padding:0.7rem 1.5rem;">Search Programmes</button>
    </div>
  </form>
</section>

<div class="container">
  <section aria-labelledby="featured-heading">
    <h2 id="featured-heading" class="page-title">Available Programmes</h2>
    <p class="page-subtitle">Showing <?= count($programmes) ?> published programme<?= count($programmes) !== 1 ? 's' : '' ?></p>

    <div style="display:flex;gap:0.75rem;flex-wrap:wrap;margin-bottom:1.5rem;" role="group" aria-label="Filter by level">
      <a href="<?= url('/programmes') ?>" class="btn btn-outline btn-sm <?= !($activeLevel ?? '') ? 'btn-primary' : '' ?>">All</a>
      <a href="<?= url('/programmes?level=Undergraduate') ?>" class="btn btn-sm <?= ($activeLevel ?? '') === 'Undergraduate' ? 'btn-primary' : 'btn-outline' ?>">Undergraduate</a>
      <a href="<?= url('/programmes?level=Postgraduate') ?>" class="btn btn-sm <?= ($activeLevel ?? '') === 'Postgraduate' ? 'btn-primary' : 'btn-outline' ?>">Postgraduate</a>
    </div>

    <?php if (empty($programmes)): ?>
    <div class="empty-state" role="status">
      <div class="empty-icon" aria-hidden="true">&#128269;</div>
      <h3>No programmes found</h3>
      <p>Try adjusting your search or check back later.</p>
    </div>
    <?php else: ?>
    <ul class="programme-grid" role="list" style="list-style:none;padding:0;margin:0;">
      <?php foreach ($programmes as $p): ?>
      <li>
        <article class="programme-card" aria-labelledby="prog-<?= $p['id'] ?>">
          <div class="card-image" aria-hidden="true">
            <?php if ($p['image']): ?>
              <img src="<?= url('/images/' . htmlspecialchars($p['image'])) ?>" alt="">
            <?php else: ?>&#127979;<?php endif; ?>
          </div>
          <div class="card-body">
            <span class="card-level <?= $levelClass[$p['level']] ?? 'level-ug' ?>"><?= htmlspecialchars($p['level']) ?></span>
            <h3 class="card-title" id="prog-<?= $p['id'] ?>"><?= htmlspecialchars($p['title']) ?></h3>
            <p class="card-desc"><?= htmlspecialchars(mb_substr($p['description'], 0, 120)) ?>...</p>
            <div class="card-meta">
              <span>&#128197; <?= (int)$p['duration_years'] ?> year<?= $p['duration_years'] > 1 ? 's' : '' ?></span>
              <?php if ($p['leader_name']): ?>
              <span>&#128100; <?= htmlspecialchars($p['leader_name']) ?></span>
              <?php endif; ?>
            </div>
          </div>
          <div class="card-footer">
            <a href="<?= url('/programmes/' . $p['id']) ?>" class="btn btn-primary btn-sm" aria-label="View details for <?= htmlspecialchars($p['title']) ?>">View Details</a>
          </div>
        </article>
      </li>
      <?php endforeach; ?>
    </ul>
    <?php endif; ?>
  </section>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
