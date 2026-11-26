<?php
declare(strict_types=1);

/** Punto de entrada único (front controller). */
require dirname(__DIR__) . '/core/bootstrap.php';

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');

Auth::startSession();
$_GET = sanitize_utf8($_GET);
$_POST = sanitize_utf8($_POST);

$router = new Router();
$router->addMany(CoreController::routes());
$router->addMany(require BASE_PATH . '/app/routes.php');

$route = (string) ($_GET['r'] ?? '');
if ($route === '') {
    $route = Auth::check() ? 'dashboard' : 'login';
}
if (!preg_match('#^[a-z0-9/_-]{1,80}$#', $route)) {
    $route = '__invalid__';
}

try {
    $router->dispatch($route, $_SERVER['REQUEST_METHOD'] ?? 'GET');
} catch (Throwable $e) {
    log_message('app-error.log', get_class($e) . ': ' . $e->getMessage() . ' en ' . $e->getFile() . ':' . $e->getLine() . "\n" . $e->getTraceAsString());
    if (headers_sent()) {
        echo config('debug') ? '<pre>' . e((string) $e) . '</pre>' : '';
        exit;
    }
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    $message = config('debug') ? $e->getMessage() . ' (' . basename($e->getFile()) . ':' . $e->getLine() . ')' : 'Ocurrió un error inesperado. Ya quedó registrado; inténtalo de nuevo.';
    if (str_contains((string) ($_SERVER['HTTP_ACCEPT'] ?? ''), 'application/json')) {
        json_response(['ok' => false, 'error' => $message], 500);
    }
    abort(500, $message);
}
