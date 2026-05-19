<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle ?? 'Student Course Hub') ?> | University</title>
  <meta name="description" content="<?= htmlspecialchars($metaDescription ?? 'Explore undergraduate and postgraduate degree programmes at our university.') ?>">
  <link rel="stylesheet" href="<?= url('/css/style.css') ?>">
</head>
<body>
<a class="skip-link" href="#main-content">Skip to main content</a>

<header class="site-header" role="banner">
  <nav class="nav-container" aria-label="Main navigation">
    <a class="site-logo" href="<?= url('/') ?>">
      <span class="logo-mark" aria-hidden="true">SCH</span>
      <span class="logo-text">Student Course Hub<span class="logo-sub">University</span></span>
    </a>
    <button class="nav-toggle" aria-controls="nav-menu" aria-expanded="false" aria-label="Toggle navigation">&#9776;</button>
    <ul class="nav-menu" id="nav-menu" role="list">
      <li><a href="<?= url('/') ?>" <?= ($activePage ?? '') === 'home' ? 'class="active" aria-current="page"' : '' ?>>Home</a></li>
      <li><a href="<?= url('/programmes') ?>" <?= ($activePage ?? '') === 'programmes' ? 'class="active" aria-current="page"' : '' ?>>Programmes</a></li>
      <li><a href="<?= url('/programmes?level=Undergraduate') ?>" <?= ($activePage ?? '') === 'ug' ? 'class="active" aria-current="page"' : '' ?>>Undergraduate</a></li>
      <li><a href="<?= url('/programmes?level=Postgraduate') ?>" <?= ($activePage ?? '') === 'pg' ? 'class="active" aria-current="page"' : '' ?>>Postgraduate</a></li>
      <li>
        <button class="btn-admin-trigger" onclick="openAdminModal()" aria-haspopup="dialog" aria-label="Admin login">
          &#128274; Admin
        </button>
      </li>
    </ul>
  </nav>
</header>

<!-- Admin Login Modal -->
<div id="admin-modal" class="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="modal-title" hidden>
  <div class="modal-card">
    <div class="modal-header">
      <h2 id="modal-title" style="font-size:1.1rem;font-weight:700;color:var(--primary);margin:0;">Admin Login</h2>
      <button class="modal-close" onclick="closeAdminModal()" aria-label="Close admin login">&times;</button>
    </div>
    <?php
      $authError = $_SESSION['auth_error'] ?? null;
      $authOld   = $_SESSION['auth_old']   ?? [];
      unset($_SESSION['auth_error'], $_SESSION['auth_old']);
    ?>
    <?php if ($authError): ?>
    <div class="alert alert-error" role="alert" style="margin-bottom:1rem;"><?= htmlspecialchars($authError) ?></div>
    <?php endif; ?>
    <form method="POST" action="<?= url('/admin/login') ?>" novalidate>
      <div class="form-group">
        <label for="modal-username">Username</label>
        <input class="form-control" type="text" id="modal-username" name="username"
               value="<?= htmlspecialchars($authOld['username'] ?? '') ?>"
               required autocomplete="username" <?= $authError ? 'autofocus' : '' ?>>
      </div>
      <div class="form-group">
        <label for="modal-password">Password</label>
        <input class="form-control" type="password" id="modal-password" name="password"
               required autocomplete="current-password">
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%;">Sign In</button>
    </form>
  </div>
</div>

<main id="main-content">
