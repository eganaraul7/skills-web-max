<?php
/** Layout para páginas públicas y de acceso (login, reservas, cotización compartida). */
$logo = null;
$businessName = config('name');
try {
    $businessName = Settings::get('business_name', config('name'));
    $logo = media_url(Settings::get('logo_media_id') ? (int) Settings::get('logo_media_id') : null);
} catch (Throwable) {
}
?><!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php if (!empty($noindex) || !isset($noindex)): ?><meta name="robots" content="noindex"><?php endif; ?>
<title><?= e($title ?? '') ?> · <?= e($businessName) ?></title>
<?php if (!empty($description)): ?><meta name="description" content="<?= e($description) ?>"><?php endif; ?>
<link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
<meta name="csrf-token" content="<?= e(Csrf::token()) ?>">
</head>
<body class="public">
<a class="skip-link" href="#contenido">Saltar al contenido</a>
<header class="public-header">
  <div class="public-brand">
    <?php if ($logo): ?><img src="<?= e($logo) ?>" alt="Logo de <?= e($businessName) ?>" width="40" height="40"><?php endif; ?>
    <span><?= e($businessName) ?></span>
  </div>
</header>
<main id="contenido" class="public-main" tabindex="-1">
  <?php foreach (pull_flashes() as $f): ?>
    <div class="alert alert-<?= e($f['type']) ?>" role="<?= $f['type'] === 'error' ? 'alert' : 'status' ?>"><?= e($f['message']) ?></div>
  <?php endforeach; ?>
  <?= $content ?>
</main>
<script src="<?= e(asset('js/app.js')) ?>"></script>
<?php foreach (($scripts ?? []) as $s): ?>
<script src="<?= e(asset($s)) ?>"></script>
<?php endforeach; ?>
</body>
</html>
