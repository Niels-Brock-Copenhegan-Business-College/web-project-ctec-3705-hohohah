<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle ?? 'Admin') ?> | SCH Admin</title>
  <link rel="stylesheet" href="<?= url('/css/style.css') ?>">
</head>
<body>
<a class="skip-link" href="#admin-content">Skip to main content</a>
<div class="admin-layout">
  <aside class="admin-sidebar" aria-label="Admin navigation">
    <div class="sidebar-logo">
      <span style="background:var(--accent);color:var(--primary);width:28px;height:28px;border-radius:5px;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:0.8rem;flex-shrink:0;" aria-hidden="true">SCH</span>
      Admin Panel
    </div>
    <nav aria-label="Admin menu">
      <div class="sidebar-section">Overview</div>
      <a href="<?= url('/admin/dashboard') ?>" class="<?= ($activePage ?? '') === 'dashboard' ? 'active' : '' ?>" aria-current="<?= ($activePage ?? '') === 'dashboard' ? 'page' : 'false' ?>">
        &#128202; Dashboard
      </a>
      <div class="sidebar-section">Content</div>
      <a href="<?= url('/admin/programmes') ?>" class="<?= ($activePage ?? '') === 'programmes' ? 'active' : '' ?>" aria-current="<?= ($activePage ?? '') === 'programmes' ? 'page' : 'false' ?>">
        &#127979; Programmes
      </a>
      <a href="<?= url('/admin/modules') ?>" class="<?= ($activePage ?? '') === 'modules' ? 'active' : '' ?>" aria-current="<?= ($activePage ?? '') === 'modules' ? 'page' : 'false' ?>">
        &#128218; Modules
      </a>
      <div class="sidebar-section">Students</div>
      <a href="<?= url('/admin/mailing-list') ?>" class="<?= ($activePage ?? '') === 'mailing-list' ? 'active' : '' ?>" aria-current="<?= ($activePage ?? '') === 'mailing-list' ? 'page' : 'false' ?>">
        &#128203; Mailing List
      </a>
      <a href="<?= url('/admin/mailing-list/export') ?>">&#8659; Export CSV</a>
      <div class="sidebar-section">Account</div>
      <a href="<?= url('/') ?>" target="_blank">&#127968; Student Site</a>
      <a href="<?= url('/admin/logout') ?>">&#128275; Logout</a>
    </nav>
  </aside>

  <div class="admin-main" id="admin-content">
    <header class="admin-topbar" role="banner">
      <h1><?= htmlspecialchars($pageTitle ?? 'Admin') ?></h1>
      <div style="display:flex;align-items:center;gap:1rem;">
        <?php if (!empty($flashMessage)): ?>
        <div class="alert alert-<?= htmlspecialchars($flashMessage['type']) ?>" role="alert" style="margin:0;padding:0.5rem 0.9rem;">
          <?= htmlspecialchars($flashMessage['text']) ?>
        </div>
        <?php endif; ?>
        <span style="font-size:0.88rem;color:var(--text-muted);">&#128100; <?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?></span>
        <a href="<?= url('/admin/logout') ?>" class="btn btn-outline btn-sm">Logout</a>
      </div>
    </header>
