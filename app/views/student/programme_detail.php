<?php
$pageTitle = $programme['title'];
$metaDescription = mb_substr(strip_tags($programme['description']), 0, 160);

require __DIR__ . '/../partials/header.php';

$levelClass = [
    'Undergraduate' => 'level-ug',
    'Postgraduate'  => 'level-pg'
];

$initials = function (string $name): string {
    $parts = explode(' ', $name);

    return mb_strtoupper(
        mb_substr($parts[0], 0, 1) .
        mb_substr(end($parts), 0, 1)
    );
};
?>

<section class="detail-header" aria-label="Programme header">

  <div class="container">

    <nav class="breadcrumb" aria-label="Breadcrumb">
      <a href="<?= url('/') ?>">Home</a>
      &rsaquo;

      <a href="<?= url('/programmes') ?>">
        Programmes
      </a>

      &rsaquo;

      <span aria-current="page">
        <?= htmlspecialchars($programme['title']) ?>
      </span>
    </nav>

    <span
      class="card-level <?= $levelClass[$programme['level']] ?? 'level-ug' ?>"
      style="margin-bottom:0.75rem;display:inline-block;"
    >
      <?= htmlspecialchars($programme['level']) ?>
    </span>

    <h1>
      <?= htmlspecialchars($programme['title']) ?>
    </h1>

    <p style="opacity:0.9;margin-top:0.5rem;">

      &#128197;
      <?= (int)$programme['duration_years'] ?> year programme

      <?php if ($programme['leader_name']): ?>
      &nbsp;&bull;&nbsp;

      &#128100;
      Led by <?= htmlspecialchars($programme['leader_name']) ?>
      <?php endif; ?>

    </p>

  </div>
</section>

<div class="container">

  <?php if ($flash = $flashMessage ?? null): ?>
  <div
    class="alert alert-<?= htmlspecialchars($flash['type']) ?>"
    role="alert"
    aria-live="assertive"
  >
    <?= htmlspecialchars($flash['text']) ?>
  </div>
  <?php endif; ?>

  <div class="detail-grid">

    <!-- Main content -->
    <div>

      <section
        class="detail-section"
        aria-labelledby="about-heading"
      >
        <h2 id="about-heading">
          About This Programme
        </h2>

        <p>
          <?= nl2br(htmlspecialchars($programme['description'])) ?>
        </p>
      </section>

      <!-- Modules by year -->
      <section
        class="detail-section"
        aria-labelledby="modules-heading"
      >

        <h2 id="modules-heading">
          Modules
        </h2>

        <?php if (empty($modulesByYear)): ?>

        <p class="text-muted">
          No modules listed for this programme yet.
        </p>

        <?php else: ?>

        <div
          class="year-tabs"
          role="tablist"
          aria-label="Select year of study"
        >

          <?php $first = true; ?>

          <?php foreach ($modulesByYear as $year => $mods): ?>

          <button
            class="year-tab <?= $first ? 'active' : '' ?>"
            role="tab"
            id="tab-year-<?= $year ?>"
            aria-selected="<?= $first ? 'true' : 'false' ?>"
            aria-controls="panel-year-<?= $year ?>"
            onclick="showYear(<?= $year ?>)"
            data-year="<?= $year ?>"
          >
            Year <?= $year ?>
          </button>

          <?php $first = false; ?>

          <?php endforeach; ?>

        </div>

        <?php $first = true; ?>

        <?php foreach ($modulesByYear as $year => $mods): ?>

        <div
          class="modules-year <?= $first ? 'active' : '' ?>"
          id="panel-year-<?= $year ?>"
          role="tabpanel"
          aria-labelledby="tab-year-<?= $year ?>"
        >

          <?php foreach ($mods as $module): ?>

          <div class="module-item">

            <div class="module-icon" aria-hidden="true">
              &#128218;
            </div>

            <div class="module-info">

              <div class="module-title">
                <?= htmlspecialchars($module['title']) ?>
              </div>

              <?php if ($module['leader_name']): ?>
              <div class="module-leader">
                &#128100;
                <?= htmlspecialchars($module['leader_name']) ?>
              </div>
              <?php endif; ?>

              <?php if ($module['description']): ?>
              <div
                style="font-size:0.85rem;color:var(--text-muted);margin-top:0.3rem;"
              >
                <?= htmlspecialchars(mb_substr($module['description'], 0, 120)) ?>...
              </div>
              <?php endif; ?>

              <span class="module-credits">
                <?= (int)$module['credits'] ?> credits
              </span>

            </div>
          </div>

          <?php endforeach; ?>

        </div>

        <?php $first = false; ?>

        <?php endforeach; ?>

        <?php endif; ?>
      </section>

      <!-- Staff -->
      <?php if (!empty($staff)): ?>

      <section
        class="detail-section"
        aria-labelledby="staff-heading"
      >

        <h2 id="staff-heading">
          Programme Staff
        </h2>

        <?php foreach ($staff as $member): ?>

        <div class="staff-card">

          <div class="staff-avatar" aria-hidden="true">

            <?php if ($member['photo']): ?>

            <img
              src="<?= url('/images/' . htmlspecialchars($member['photo'])) ?>"
              alt=""
            >

            <?php else: ?>

            <?= $initials($member['name']) ?>

            <?php endif; ?>

          </div>

          <div class="staff-info">

            <div class="staff-name">
              <?= htmlspecialchars($member['name']) ?>
            </div>

            <div class="staff-role">
              <?= htmlspecialchars($member['role']) ?>
            </div>

            <?php if ($member['bio']): ?>

            <div class="staff-bio">
              <?= htmlspecialchars(mb_substr($member['bio'], 0, 180)) ?>...
            </div>

            <?php endif; ?>

            <?php if ($member['email']): ?>

            <div style="margin-top:0.4rem;">

              <a
                href="mailto:<?= htmlspecialchars($member['email']) ?>"
                style="font-size:0.85rem;color:var(--primary);"
              >
                &#9993;
                <?= htmlspecialchars($member['email']) ?>
              </a>

            </div>

            <?php endif; ?>

          </div>
        </div>

        <?php endforeach; ?>

      </section>

      <?php endif; ?>

    </div>

    <!-- Sidebar -->
    <aside
      class="detail-sidebar"
      aria-label="Register your interest"
    >

      <div class="interest-box">

        <h3>
          &#128203; Register Your Interest
        </h3>

        <?php if ($alreadyRegistered ?? false): ?>

        <div class="alert alert-success" role="status">
          You have already registered interest in this programme.
        </div>

        <form
          method="POST"
          action="<?= url('/programmes/' . $programme['id'] . '/withdraw') ?>"
        >

          <input
            type="hidden"
            name="programme_id"
            value="<?= $programme['id'] ?>"
          >

          <p
            style="font-size:0.88rem;color:var(--text-muted);margin-bottom:1rem;"
          >
            Changed your mind?
          </p>

          <div class="form-group">

            <label for="withdraw-email">
              Your email address
              <span class="required" aria-label="required">*</span>
            </label>

            <input
              class="form-control"
              type="email"
              id="withdraw-email"
              name="email"
              required
              autocomplete="email"
            >

          </div>

          <button
            type="submit"
            class="btn btn-outline btn-sm"
            style="width:100%;"
          >
            Withdraw Interest
          </button>

        </form>

        <?php else: ?>

        <p
          style="font-size:0.88rem;color:var(--text-muted);margin-bottom:1rem;"
        >
          Interested in
          <strong><?= htmlspecialchars($programme['title']) ?></strong>?

          Let us keep you updated on open days and application deadlines.
        </p>

        <form
          method="POST"
          action="<?= url('/programmes/' . $programme['id'] . '/interest') ?>"
          novalidate
          aria-label="Register your interest"
        >

          <?php if (!empty($errors ?? [])): ?>

          <div class="alert alert-error" role="alert">
            Please correct the errors below.
          </div>

          <?php endif; ?>

          <div class="form-group">

            <label for="first_name">
              First name
              <span class="required" aria-label="required">*</span>
            </label>

            <input
              class="form-control <?= isset($errors['first_name']) ? 'is-invalid' : '' ?>"
              type="text"
              id="first_name"
              name="first_name"
              value="<?= htmlspecialchars($old['first_name'] ?? '') ?>"
              required
              autocomplete="given-name"
              aria-describedby="<?= isset($errors['first_name']) ? 'err-first' : '' ?>"
            >

            <?php if (isset($errors['first_name'])): ?>

            <span
              class="error-msg"
              id="err-first"
              role="alert"
            >
              <?= htmlspecialchars($errors['first_name']) ?>
            </span>

            <?php endif; ?>

          </div>

          <div class="form-group">

            <label for="last_name">
              Last name
              <span class="required" aria-label="required">*</span>
            </label>

            <input
              class="form-control <?= isset($errors['last_name']) ? 'is-invalid' : '' ?>"
              type="text"
              id="last_name"
              name="last_name"
              value="<?= htmlspecialchars($old['last_name'] ?? '') ?>"
              required
              autocomplete="family-name"
              aria-describedby="<?= isset($errors['last_name']) ? 'err-last' : '' ?>"
            >

            <?php if (isset($errors['last_name'])): ?>

            <span
              class="error-msg"
              id="err-last"
              role="alert"
            >
              <?= htmlspecialchars($errors['last_name']) ?>
            </span>

            <?php endif; ?>

          </div>

          <div class="form-group">

            <label for="email">
              Email address
              <span class="required" aria-label="required">*</span>
            </label>

            <input
              class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
              type="email"
              id="email"
              name="email"
              value="<?= htmlspecialchars($old['email'] ?? '') ?>"
              required
              autocomplete="email"
              aria-describedby="email-hint <?= isset($errors['email']) ? 'err-email' : '' ?>"
            >

            <span class="form-hint" id="email-hint">
              We will only use this to send you programme updates.
            </span>

            <?php if (isset($errors['email'])): ?>

            <span
              class="error-msg"
              id="err-email"
              role="alert"
            >
              <?= htmlspecialchars($errors['email']) ?>
            </span>

            <?php endif; ?>

          </div>

          <div class="form-group">

            <label for="phone">
              Phone number (optional)
            </label>

            <input
              class="form-control"
              type="tel"
              id="phone"
              name="phone"
              value="<?= htmlspecialchars($old['phone'] ?? '') ?>"
              autocomplete="tel"
            >

          </div>

          <div class="form-group">

            <label for="message">
              Message (optional)
            </label>

            <textarea
              class="form-control"
              id="message"
              name="message"
              rows="3"
            ><?= htmlspecialchars($old['message'] ?? '') ?></textarea>

          </div>

          <button
            type="submit"
            class="btn btn-primary"
            style="width:100%;"
          >
            Register Interest
          </button>

        </form>

        <?php endif; ?>

      </div>

      <div style="margin-top:1rem;text-align:center;">

        <a
          href="<?= url('/programmes') ?>"
          class="btn btn-outline btn-sm"
          style="width:100%;"
        >
          &larr; All Programmes
        </a>

      </div>

    </aside>

  </div>
</div>

<script>
function showYear(year) {

  document.querySelectorAll('.year-tab').forEach(function(tab) {

    var active = parseInt(tab.dataset.year) === year;

    tab.classList.toggle('active', active);

    tab.setAttribute(
      'aria-selected',
      active ? 'true' : 'false'
    );
  });

  document.querySelectorAll('.modules-year').forEach(function(panel) {

    panel.classList.toggle(
      'active',
      panel.id === 'panel-year-' + year
    );
  });
}
</script>

<?php require __DIR__ . '/../partials/footer.php'; ?>
