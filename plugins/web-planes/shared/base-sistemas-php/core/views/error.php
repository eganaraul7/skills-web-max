<section class="card empty-state" aria-labelledby="error-title">
  <p class="error-code" aria-hidden="true"><?= e($status) ?></p>
  <h1 id="error-title"><?= e($title) ?></h1>
  <?php if ($message !== ''): ?><p><?= e($message) ?></p><?php endif; ?>
  <p><a class="btn btn-primary" href="<?= e(url(Auth::check() ? 'dashboard' : '')) ?>">Volver al inicio</a></p>
</section>
