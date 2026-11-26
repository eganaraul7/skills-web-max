<?php
declare(strict_types=1);

/** Lee un valor de config/app.php con notación de puntos ("mail.host"). */
function config(string $key, mixed $default = null): mixed
{
    $value = $GLOBALS['__config'] ?? [];
    foreach (explode('.', $key) as $part) {
        if (!is_array($value) || !array_key_exists($part, $value)) {
            return $default;
        }
        $value = $value[$part];
    }
    return $value;
}

/** Escape de salida HTML. Usar SIEMPRE al imprimir datos. */
function e(mixed $value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function url(string $route = '', array $params = []): string
{
    $query = $route === '' ? $params : ['r' => $route] + $params;
    $qs = http_build_query($query, '', '&', PHP_QUERY_RFC3986);
    return config('url') . '/' . ($qs === '' ? '' : '?' . $qs);
}

function asset(string $path): string
{
    $file = BASE_PATH . '/public/assets/' . ltrim($path, '/');
    $version = is_file($file) ? (string) filemtime($file) : '1';
    return config('url') . '/assets/' . ltrim($path, '/') . '?v=' . $version;
}

function media_url(?int $mediaId): ?string
{
    return $mediaId ? url('media', ['id' => $mediaId]) : null;
}

function redirect(string $route = '', array $params = []): never
{
    header('Location: ' . url($route, $params), true, 303);
    exit;
}

function redirect_back(string $fallbackRoute = 'dashboard'): never
{
    $ref = $_SERVER['HTTP_REFERER'] ?? '';
    if ($ref !== '' && str_starts_with($ref, config('url'))) {
        header('Location: ' . $ref, true, 303);
        exit;
    }
    redirect($fallbackRoute);
}

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function input(string $key, mixed $default = ''): mixed
{
    $value = $_POST[$key] ?? $_GET[$key] ?? $default;
    return is_string($value) ? trim($value) : $value;
}

function query_int(string $key, int $default = 0): int
{
    $v = $_GET[$key] ?? $_POST[$key] ?? null;
    return (is_string($v) && ctype_digit($v)) ? (int) $v : $default;
}

function flash(string $type, string $message): void
{
    $_SESSION['_flash'][] = ['type' => $type, 'message' => $message];
}

function pull_flashes(): array
{
    $f = $_SESSION['_flash'] ?? [];
    unset($_SESSION['_flash']);
    return $f;
}

/** Guarda errores de validación y datos enviados para repintar el formulario. */
function with_errors(array $errors, array $old): void
{
    $_SESSION['_errors'] = $errors;
    unset($old['_csrf']);
    $_SESSION['_old'] = $old;
}

function form_errors(): array
{
    static $errors = null;
    if ($errors === null) {
        $errors = $_SESSION['_errors'] ?? [];
        unset($_SESSION['_errors']);
    }
    return $errors;
}

function old(string $key, mixed $default = ''): mixed
{
    static $old = null;
    if ($old === null) {
        $old = $_SESSION['_old'] ?? [];
        unset($_SESSION['_old']);
    }
    return $old[$key] ?? $default;
}

function field_error(string $field): string
{
    $errors = form_errors();
    if (!isset($errors[$field])) {
        return '';
    }
    return '<p class="field-error" id="err-' . e($field) . '">' . e($errors[$field]) . '</p>';
}

/** Atributos ARIA para un campo con error. */
function aria_error(string $field): string
{
    return isset(form_errors()[$field]) ? ' aria-invalid="true" aria-describedby="err-' . e($field) . '"' : '';
}

function json_response(mixed $data, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function abort(int $status, string $message = ''): never
{
    http_response_code($status);
    $titles = [403 => 'Acceso denegado', 404 => 'Página no encontrada', 405 => 'Método no permitido', 419 => 'La sesión del formulario expiró', 500 => 'Error interno'];
    $title = $titles[$status] ?? 'Error';
    View::render('core/error', ['title' => $title, 'status' => $status, 'message' => $message], Auth::check() ? 'layout' : 'public_layout');
    exit;
}

/** Sustituye secuencias UTF-8 inválidas en datos de entrada (evita fallos al guardar o codificar JSON). */
function sanitize_utf8(mixed $value): mixed
{
    if (is_array($value)) {
        return array_map('sanitize_utf8', $value);
    }
    return is_string($value) && !mb_check_encoding($value, 'UTF-8') ? mb_convert_encoding($value, 'UTF-8', 'UTF-8') : $value;
}

/** JSON para guardar en BD: nunca devuelve false; los bytes UTF-8 inválidos se sustituyen. */
function json_store(mixed $value): string
{
    return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE | JSON_THROW_ON_ERROR);
}

function random_token(int $bytes = 32): string
{
    return bin2hex(random_bytes($bytes));
}

function now(): DateTimeImmutable
{
    return new DateTimeImmutable('now');
}

function today(): string
{
    return now()->format('Y-m-d');
}

/** Normaliza decimales escritos como "1.234,56", "1234,56" o "1234.56". Devuelve null si no es numérico. */
function parse_decimal(mixed $value): ?string
{
    if (is_int($value) || is_float($value)) {
        return Money::n($value);
    }
    $s = str_replace([' ', "\u{00A0}"], '', trim((string) $value));
    if ($s === '') {
        return null;
    }
    if (str_contains($s, '.') && str_contains($s, ',')) {
        $s = str_replace('.', '', $s);
        $s = str_replace(',', '.', $s);
    } elseif (str_contains($s, ',')) {
        $s = str_replace(',', '.', $s);
    }
    return preg_match('/^-?\d+(\.\d+)?$/', $s) ? $s : null;
}

function money(mixed $amount, string $currency = 'USD'): string
{
    return Money::format($amount, $currency);
}

/** Muestra un monto en USD y su equivalente en Bs. con la tasa dada (o la actual). */
function money_both(mixed $usd, ?string $rate = null): string
{
    $rate ??= Settings::rate();
    return Money::format($usd, 'USD') . ' · ' . Money::format(Money::toVes((string) $usd, $rate), 'VES');
}

/** Valor decimal de la BD para un <input>: "1500.000" → "1500", "2.500" → "2.5". */
function num_input(mixed $value): string
{
    $s = (string) ($value ?? '');
    if (str_contains($s, '.')) {
        $s = rtrim(rtrim($s, '0'), '.');
    }
    return $s;
}

function qty(mixed $value): string
{
    $s = rtrim(rtrim(number_format((float) $value, 3, ',', '.'), '0'), ',');
    return $s === '' || $s === '-' ? '0' : $s;
}

const ES_DAYS = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
const ES_MONTHS = [1 => 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];

function date_es(string|DateTimeInterface|null $date, bool $withDay = false): string
{
    if ($date === null || $date === '') {
        return '';
    }
    $d = $date instanceof DateTimeInterface ? $date : new DateTimeImmutable($date);
    $txt = (int) $d->format('j') . ' de ' . ES_MONTHS[(int) $d->format('n')] . ' de ' . $d->format('Y');
    return $withDay ? ES_DAYS[(int) $d->format('w')] . ', ' . $txt : $txt;
}

function datetime_es(string|DateTimeInterface|null $date): string
{
    if ($date === null || $date === '') {
        return '';
    }
    $d = $date instanceof DateTimeInterface ? $date : new DateTimeImmutable($date);
    return date_es($d) . ', ' . $d->format('g:i a');
}

function time_es(string $time): string
{
    return (new DateTimeImmutable('2000-01-01 ' . $time))->format('g:i a');
}

/** Convierte un teléfono venezolano (0414-1234567) a formato internacional para wa.me. */
function phone_international(?string $phone): string
{
    $digits = preg_replace('/\D+/', '', (string) $phone) ?? '';
    if ($digits === '') {
        return '';
    }
    if (strlen($digits) === 11 && $digits[0] === '0') {
        return '58' . substr($digits, 1);
    }
    if (strlen($digits) === 10 && $digits[0] === '4') {
        return '58' . $digits;
    }
    return $digits;
}

function whatsapp_link(?string $phone, string $text): string
{
    $number = phone_international($phone);
    return 'https://wa.me/' . $number . '?text=' . rawurlencode($text);
}

function slugify(string $text): string
{
    $text = mb_strtolower(trim($text), 'UTF-8');
    $text = strtr($text, ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u', 'ñ' => 'n']);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? '';
    return trim($text, '-') ?: 'item';
}

function client_ip(): string
{
    return substr((string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'), 0, 45);
}

function log_message(string $file, string $message): void
{
    $dir = config('logs_path');
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
    file_put_contents($dir . '/' . $file, '[' . now()->format('Y-m-d H:i:s') . '] ' . $message . PHP_EOL, FILE_APPEND | LOCK_EX);
}

function selected(mixed $a, mixed $b): string
{
    return (string) $a === (string) $b ? ' selected' : '';
}

function checked(mixed $value): string
{
    return $value ? ' checked' : '';
}
