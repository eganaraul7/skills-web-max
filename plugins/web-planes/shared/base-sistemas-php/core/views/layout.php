<?php
/** Layout del panel de administración. Variables: $title, $content, $scripts (opcional). */
$user = Auth::user();
$current = (string) ($_GET['r'] ?? 'dashboard');
// Menú propio de este sistema (app/nav.php): ['Grupo' => [[ruta, etiqueta, soloAdmin?], ...]]
$nav = require BASE_PATH . '/app/nav.php';
if (Auth::isAdmin()) {
    $nav['Administración'] = [['settings', 'Negocio'], ['users', 'Usuarios']];
} else {
    // Los usuarios de solo lectura no ven accesos a pantallas de escritura.
    foreach ($nav as $group => $items) {
        $nav[$group] = array_values(array_filter($items, static fn($i) => empty($i[2])));
    }
}
// Marca como activa la ruta del menú más específica que coincide con la actual.
$activeRoute = '';
foreach ($nav as $items) {
    foreach ($items as [$route]) {
        if (($current === $route || str_starts_with($current, $route . '/')) && strlen($route) > strlen($activeRoute)) {
            $activeRoute = $route;
        }
    }
}
$isActive = static fn(string $route): bool => $route === $activeRoute;
$logo = media_url(Settings::get('logo_media_id') ? (int) Settings::get('logo_media_id') : null);
?><!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?= e($title ?? '') ?> · <?= e(config('name')) ?></title>
<link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
<meta name="csrf-token" content="<?= e(Csrf::token()) ?>">
</head>
<body>
<a class="skip-link" href="#contenido">Saltar al contenido</a>
<header class="topbar">
  <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="sidebar">
    <span aria-hidden="true">☰</span><span class="sr-only">Abrir menú</span>
  </button>
  <a class="brand" href="<?= e(url('dashboard')) ?>">
    <?php if ($logo): ?><img src="<?= e($logo) ?>" alt="" width="32" height="32"><?php endif; ?>
    <span><?= e(Settings::get('business_name', config('name'))) ?> <small class="app-name">· <?= e(config('name')) ?></small></span>
  </a>
  <div class="topbar-user">
    <?php if (config('show_rate', true)): ?><span class="rate-pill" title="Tasa de cambio vigente">1 US$ = <?= e(Money::format(Settings::rate(), 'VES')) ?></span><?php endif; ?>
    <span class="user-name"><?= e($user['name'] ?? '') ?> <small>(<?= ($user['role'] ?? '') === 'admin' ? 'Administrador' : 'Solo lectura' ?>)</small></span>
    <form method="post" action="<?= e(url('logout')) ?>">
      <?= Csrf::field() ?>
      <button class="btn btn-ghost btn-sm" type="submit">Salir</button>
    </form>
  </div>
</header>
<div class="shell">
  <nav id="sidebar" class="sidebar" aria-label="Navegación principal">
    <?php foreach ($nav as $group => $items): ?>
      <p class="nav-group"><?= e($group) ?></p>
      <ul>
        <?php foreach ($items as [$route, $label]): $active = $isActive($route); ?>
          <li><a href="<?= e(url($route)) ?>"<?= $active ? ' aria-current="page" class="active"' : '' ?>><?= e($label) ?></a></li>
        <?php endforeach; ?>
      </ul>
    <?php endforeach; ?>
  </nav>
  <main id="contenido" class="main" tabindex="-1">
    <?php foreach (pull_flashes() as $f): ?>
      <div class="alert alert-<?= e($f['type']) ?>" role="<?= $f['type'] === 'error' ? 'alert' : 'status' ?>"><?= e($f['message']) ?></div>
    <?php endforeach; ?>
    <?= $content ?>
  </main>
</div>
<script src="<?= e(asset('js/app.js')) ?>"></script>
<?php foreach (($scripts ?? []) as $s): ?>
<script src="<?= e(asset($s)) ?>"></script>
<?php endforeach; ?>
</body>
</html>
