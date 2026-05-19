</main>

<footer class="site-footer" role="contentinfo">
  <p>&copy; <?= date('Y') ?> University Student Course Hub. All rights reserved.</p>
</footer>

<script>
(function () {
  var toggle = document.querySelector('.nav-toggle');
  var menu = document.getElementById('nav-menu');
  if (toggle && menu) {
    toggle.addEventListener('click', function () {
      var open = menu.classList.toggle('open');
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  }
})();

function openAdminModal() {
  var modal = document.getElementById('admin-modal');
  modal.removeAttribute('hidden');
  document.body.style.overflow = 'hidden';
  var first = modal.querySelector('input, button');
  if (first) first.focus();
}

function closeAdminModal() {
  var modal = document.getElementById('admin-modal');
  modal.setAttribute('hidden', '');
  document.body.style.overflow = '';
}

// Close on backdrop click
document.addEventListener('DOMContentLoaded', function () {
  var modal = document.getElementById('admin-modal');
  if (modal) {
    modal.addEventListener('click', function (e) {
      if (e.target === modal) closeAdminModal();
    });
  }
  // Auto-open if there was a login error (session flagged it)
  <?php if (!empty($_SESSION['open_admin_modal'] ?? null)): unset($_SESSION['open_admin_modal']); ?>
  openAdminModal();
  <?php endif; ?>
});

// Close on Escape key
document.addEventListener('keydown', function (e) {
  if (e.key === 'Escape') closeAdminModal();
});
</script>
</body>
</html>
