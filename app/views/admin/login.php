<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login | Student Course Hub</title>
  <link rel="stylesheet" href="<?= url('/css/style.css') ?>">
</head>
<body>
<div class="login-page">
  <div class="login-card">
    <div class="login-logo">
      <div class="logo-mark" aria-hidden="true">SCH</div>
      <h1>Admin Login</h1>
      <p style="font-size:0.88rem;color:var(--text-muted);">Student Course Hub</p>
    </div>

    <?php if (!empty($error)): ?>
    <div class="alert alert-error" role="alert"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="<?= url('/admin/login') ?>" novalidate>
      <div class="form-group">
        <label for="username">Username <span class="required" aria-label="required">*</span></label>
        <input class="form-control" type="text" id="username" name="username"
               value="<?= htmlspecialchars($old['username'] ?? '') ?>"
               required autocomplete="username" autofocus>
      </div>
      <div class="form-group">
        <label for="password">Password <span class="required" aria-label="required">*</span></label>
        <input class="form-control" type="password" id="password" name="password"
               required autocomplete="current-password">
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%;margin-top:0.5rem;">Sign In</button>
    </form>
    <p style="margin-top:1.5rem;text-align:center;font-size:0.85rem;">
      <a href="<?= url('/') ?>" style="color:var(--primary);">&larr; Back to student site</a>
    </p>
  </div>
</div>
</body>
</html>
