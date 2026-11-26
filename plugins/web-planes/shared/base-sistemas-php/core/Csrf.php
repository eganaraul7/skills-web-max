<?php
declare(strict_types=1);

/** Tokens CSRF sincronizados con la sesión (formularios y peticiones fetch). */
final class Csrf
{
    public static function token(): string
    {
        if (empty($_SESSION['_csrf']) || !is_string($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = random_token(32);
        }
        return $_SESSION['_csrf'];
    }

    public static function field(): string
    {
        return '<input type="hidden" name="_csrf" value="' . e(self::token()) . '">';
    }

    public static function verify(): bool
    {
        $sent = $_POST['_csrf'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
        $expected = $_SESSION['_csrf'] ?? '';
        return is_string($sent) && is_string($expected) && $expected !== '' && hash_equals($expected, $sent);
    }
}
