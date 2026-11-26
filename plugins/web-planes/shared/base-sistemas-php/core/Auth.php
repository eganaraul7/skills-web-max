<?php
declare(strict_types=1);

/** Autenticación por sesión con roles: admin (todo) y viewer (solo lectura). */
final class Auth
{
    private const MAX_ATTEMPTS = 5;
    private const LOCK_MINUTES = 15;
    private static ?array $user = null;
    private static bool $loaded = false;

    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }
        $secure = str_starts_with((string) config('url'), 'https://');
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        // Cookie propia de ESTE sistema (nombre y ruta únicos): otros sistemas en el mismo dominio no comparten sesión.
        session_name('sid_' . substr(hash('sha256', (string) config('url') . '|' . BASE_PATH), 0, 12));
        $path = rtrim((string) (parse_url((string) config('url'), PHP_URL_PATH) ?: ''), '/') . '/';
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => $path,
            'secure'   => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();

        // Cierre por inactividad.
        $idle = (int) config('session_idle', 120) * 60;
        if (isset($_SESSION['user_id'], $_SESSION['last_seen']) && time() - (int) $_SESSION['last_seen'] > $idle) {
            self::logout();
            session_start();
            flash('info', 'Tu sesión se cerró por inactividad. Inicia sesión de nuevo.');
        }
        $_SESSION['last_seen'] = time();
    }

    /** @return true|string true si inició sesión; mensaje de error en español si no. */
    public static function attempt(string $email, string $password): bool|string
    {
        $email = mb_strtolower(trim($email));
        $since = now()->modify('-' . self::LOCK_MINUTES . ' minutes')->format('Y-m-d H:i:s');
        $attempts = (int) Db::value(
            'SELECT COUNT(*) FROM login_attempts WHERE (email = ? OR ip_address = ?) AND attempted_at >= ?',
            [$email, client_ip(), $since]
        );
        if ($attempts >= self::MAX_ATTEMPTS) {
            return 'Demasiados intentos fallidos. Espera ' . self::LOCK_MINUTES . ' minutos e inténtalo de nuevo.';
        }

        $user = Db::one('SELECT * FROM users WHERE email = ? LIMIT 1', [$email]);
        // password_verify contra un hash ficticio para no revelar si el correo existe por tiempo de respuesta.
        $hash = $user['password_hash'] ?? password_hash(random_token(8), PASSWORD_DEFAULT);
        $valid = password_verify($password, $hash);

        if (!$user || !$valid || !(int) $user['is_active']) {
            Db::run('INSERT INTO login_attempts (email, ip_address, attempted_at) VALUES (?, ?, ?)', [$email, client_ip(), now()->format('Y-m-d H:i:s')]);
            return 'Correo o contraseña incorrectos.';
        }

        if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
            Db::run('UPDATE users SET password_hash = ? WHERE id = ?', [password_hash($password, PASSWORD_DEFAULT), $user['id']]);
        }
        Db::run('DELETE FROM login_attempts WHERE email = ?', [$email]);
        Db::run('UPDATE users SET last_login_at = ? WHERE id = ?', [now()->format('Y-m-d H:i:s'), $user['id']]);

        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['last_seen'] = time();
        unset($_SESSION['_csrf']);
        self::$loaded = false;
        return true;
    }

    public static function user(): ?array
    {
        if (!self::$loaded) {
            self::$loaded = true;
            self::$user = null;
            if (!empty($_SESSION['user_id'])) {
                $u = Db::one('SELECT id, name, email, role, is_active FROM users WHERE id = ?', [(int) $_SESSION['user_id']]);
                if ($u && (int) $u['is_active']) {
                    self::$user = $u;
                } else {
                    unset($_SESSION['user_id']);
                }
            }
        }
        return self::$user;
    }

    public static function id(): ?int
    {
        return self::user() ? (int) self::user()['id'] : null;
    }

    public static function check(): bool
    {
        try {
            return self::user() !== null;
        } catch (Throwable) {
            return false;
        }
    }

    public static function isAdmin(): bool
    {
        return self::check() && self::user()['role'] === 'admin';
    }

    /** Solo los administradores pueden crear, editar o borrar. */
    public static function canWrite(): bool
    {
        return self::isAdmin();
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
        self::$user = null;
        self::$loaded = true;
    }
}
