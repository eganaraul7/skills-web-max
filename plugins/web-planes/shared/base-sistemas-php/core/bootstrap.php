<?php
declare(strict_types=1);

/**
 * Arranque común para web, CLI (cron, instalador) y pruebas.
 */
define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/core/Env.php';
Env::load(BASE_PATH . '/config/.env');

$GLOBALS['__config'] = require BASE_PATH . '/config/app.php';
require BASE_PATH . '/config/db.php';
require BASE_PATH . '/core/helpers.php';

// Autoload: clases en core/ y app/src/ (un archivo por clase, mismo nombre).
spl_autoload_register(static function (string $class): void {
    static $map = null;
    if ($map === null) {
        $map = [];
        $files = array_merge(glob(BASE_PATH . '/core/*.php') ?: [], glob(BASE_PATH . '/app/src/*.php') ?: []);
        foreach ($files as $file) {
            $map[basename($file, '.php')] = $file;
        }
    }
    if (isset($map[$class])) {
        require_once $map[$class];
    }
});

date_default_timezone_set((string) config('timezone'));

error_reporting(E_ALL);
ini_set('display_errors', config('debug') ? '1' : '0');
ini_set('log_errors', '1');
ini_set('error_log', config('logs_path') . '/php-error.log');

// Convierte warnings/notices en excepciones: nada pasa en silencio.
set_error_handler(static function (int $severity, string $message, string $file, int $line): bool {
    if (!(error_reporting() & $severity)) {
        return false;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
});

// La zona horaria configurada en el panel tiene prioridad sobre la del .env.
if (PHP_SAPI !== 'cli' || getenv('APP_LOAD_SETTINGS') === '1') {
    try {
        $tz = Settings::get('timezone');
        if (is_string($tz) && in_array($tz, DateTimeZone::listIdentifiers(), true)) {
            date_default_timezone_set($tz);
            Db::syncTimezone();
        }
    } catch (Throwable $e) {
        // Antes de instalar la base de datos todavía no hay configuración.
    }
}
