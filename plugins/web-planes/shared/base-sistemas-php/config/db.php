<?php
declare(strict_types=1);

/**
 * Conexión PDO única. Siempre consultas preparadas (EMULATE_PREPARES desactivado).
 */
final class Db
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo === null) {
            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
                Env::get('DB_HOST', '127.0.0.1'),
                Env::int('DB_PORT', 3306),
                Env::get('DB_NAME', 'cliente_sistema')
            );
            self::$pdo = new PDO($dsn, (string) Env::get('DB_USER', 'root'), (string) Env::get('DB_PASS', ''), [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_STRINGIFY_FETCHES  => false,
            ]);
            self::syncTimezone();
        }
        return self::$pdo;
    }

    /** Alinea la zona horaria de MySQL con la de PHP (CURRENT_TIMESTAMP coherente). */
    public static function syncTimezone(): void
    {
        if (self::$pdo === null) {
            return;
        }
        $offset = (new DateTimeImmutable('now'))->format('P');
        self::$pdo->exec("SET time_zone = '" . $offset . "'");
    }

    public static function reset(): void
    {
        self::$pdo = null;
    }

    /** Ejecuta una consulta preparada y devuelve el statement. */
    public static function run(string $sql, array $params = []): PDOStatement
    {
        $stmt = self::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function one(string $sql, array $params = []): ?array
    {
        $row = self::run($sql, $params)->fetch();
        return $row === false ? null : $row;
    }

    public static function all(string $sql, array $params = []): array
    {
        return self::run($sql, $params)->fetchAll();
    }

    public static function value(string $sql, array $params = []): mixed
    {
        $v = self::run($sql, $params)->fetchColumn();
        return $v === false ? null : $v;
    }

    public static function insert(string $sql, array $params = []): int
    {
        self::run($sql, $params);
        return (int) self::pdo()->lastInsertId();
    }

    /**
     * Ejecuta $fn dentro de una transacción. Reintenta ante deadlock (1213) o lock timeout (1205).
     * @template T
     * @param callable(PDO):T $fn
     * @return T
     */
    public static function transaction(callable $fn, int $retries = 3): mixed
    {
        $pdo = self::pdo();
        for ($attempt = 1; ; $attempt++) {
            $pdo->beginTransaction();
            try {
                $result = $fn($pdo);
                $pdo->commit();
                return $result;
            } catch (Throwable $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                $code = $e instanceof PDOException ? (int) ($e->errorInfo[1] ?? 0) : 0;
                if (in_array($code, [1213, 1205], true) && $attempt < $retries) {
                    usleep(50000 * $attempt);
                    continue;
                }
                throw $e;
            }
        }
    }
}
