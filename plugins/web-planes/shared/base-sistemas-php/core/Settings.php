<?php
declare(strict_types=1);

/** Configuración del negocio (fila única settings.id = 1). */
final class Settings
{
    private static ?array $cache = null;

    /** Campos comunes a todos los sistemas; cada sistema agrega los suyos en config/app.php ('settings_fields'). */
    public const COMMON = [
        'business_name', 'logo_media_id', 'base_currency', 'exchange_rate', 'exchange_rate_updated_at', 'phone', 'whatsapp',
        'email', 'address', 'timezone',
    ];

    public static function editable(): array
    {
        return array_merge(self::COMMON, array_keys((array) config('settings_fields', [])), (array) config('settings_extra_columns', []));
    }

    public static function all(): array
    {
        if (self::$cache === null) {
            self::$cache = Db::one('SELECT * FROM settings WHERE id = 1') ?? [];
        }
        return self::$cache;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return self::all()[$key] ?? $default;
    }

    public static function rate(): string
    {
        return (string) self::get('exchange_rate', '1');
    }

    public static function update(array $values): void
    {
        $values = array_intersect_key($values, array_flip(self::editable()));
        if ($values === []) {
            return;
        }
        $sets = implode(', ', array_map(static fn(string $k): string => $k . ' = ?', array_keys($values)));
        Db::run('UPDATE settings SET ' . $sets . ' WHERE id = 1', array_values($values));
        self::$cache = null;
    }

    public static function flush(): void
    {
        self::$cache = null;
    }
}
