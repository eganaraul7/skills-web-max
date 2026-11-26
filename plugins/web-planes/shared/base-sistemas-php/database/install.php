<?php
declare(strict_types=1);

/**
 * Instalador por línea de comandos.
 *   php database/install.php            → crea la BD, aplica schema.sql y seed.sql, copia imágenes de ejemplo y crea el admin
 *   php database/install.php --no-seed  → solo esquema + admin (instalación limpia para producción)
 * Credenciales del admin: INSTALL_ADMIN_EMAIL / INSTALL_ADMIN_PASSWORD en config/.env
 * (si la contraseña está vacía se genera una aleatoria y se muestra una sola vez).
 */
if (PHP_SAPI !== 'cli') {
    exit("Ejecuta este script desde la terminal.\n");
}
require dirname(__DIR__) . '/core/bootstrap.php';

$withSeed = !in_array('--no-seed', $argv, true);
$dbName = (string) Env::get('DB_NAME', 'cliente_sistema');
if (!preg_match('/^[A-Za-z0-9_]+$/', $dbName)) {
    exit("DB_NAME solo puede contener letras, números y guion bajo.\n");
}

try {
    SqlScript::createDatabase($dbName);
} catch (PDOException $e) {
    exit('No se pudo conectar a MySQL: ' . $e->getMessage() . "
");
}

$pdo = Db::pdo();
echo "→ Aplicando schema.sql\n";
SqlScript::run($pdo, __DIR__ . '/schema.sql');

foreach (['uploads', 'logs'] as $dir) {
    if (!is_dir(BASE_PATH . '/storage/' . $dir)) {
        mkdir(BASE_PATH . '/storage/' . $dir, 0775, true);
    }
}

if ($withSeed) {
    echo "→ Aplicando seed.sql (datos de ejemplo)\n";
    SqlScript::run($pdo, __DIR__ . '/seed.sql');
    $images = glob(__DIR__ . '/seed-images/*.svg') ?: [];
    foreach ($images as $img) {
        copy($img, BASE_PATH . '/storage/uploads/' . basename($img));
    }
    if ($images) {
        echo '→ ' . count($images) . " imágenes de ejemplo copiadas a storage/uploads\n";
    }
}

$email = mb_strtolower((string) Env::get('INSTALL_ADMIN_EMAIL', 'admin@ejemplo.com'));
$password = (string) Env::get('INSTALL_ADMIN_PASSWORD', '');
$generated = false;
if ($password === '') {
    $password = substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(12))), 0, 14);
    $generated = true;
}
$pdo->prepare('INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)')
    ->execute([Env::get('INSTALL_ADMIN_NAME', 'Administrador'), $email, password_hash($password, PASSWORD_DEFAULT), 'admin']);
echo "→ Administrador creado: {$email}\n";
if ($generated) {
    echo "  Contraseña generada (guárdala, no se volverá a mostrar): {$password}\n";
}
if ($withSeed) {
    $viewerPass = substr(bin2hex(random_bytes(6)), 0, 12);
    $pdo->prepare('INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)')
        ->execute(['Usuario de consulta', 'consulta@ejemplo.com', password_hash($viewerPass, PASSWORD_DEFAULT), 'viewer']);
    echo "→ Usuario de solo lectura: consulta@ejemplo.com / {$viewerPass}\n";
}
echo "✔ Instalación completa.\n";
