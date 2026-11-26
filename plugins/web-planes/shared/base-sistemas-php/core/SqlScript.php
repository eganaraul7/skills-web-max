<?php
declare(strict_types=1);

/** Ejecuta archivos .sql sentencia por sentencia (respeta comillas y comentarios --). */
final class SqlScript
{
    public static function split(string $sql): array
    {
        $stmts = [];
        $buf = '';
        $len = strlen($sql);
        $quote = null;
        for ($i = 0; $i < $len; $i++) {
            $c = $sql[$i];
            if ($quote === null && $c === '-' && ($sql[$i + 1] ?? '') === '-') {
                $nl = strpos($sql, "\n", $i);
                $i = $nl === false ? $len : $nl;
                $buf .= "\n";
                continue;
            }
            if ($quote !== null) {
                $buf .= $c;
                if ($c === '\\') {
                    $buf .= $sql[++$i] ?? '';
                } elseif ($c === $quote) {
                    if (($sql[$i + 1] ?? '') === $quote) {
                        $buf .= $sql[++$i];
                    } else {
                        $quote = null;
                    }
                }
                continue;
            }
            if ($c === "'" || $c === '"' || $c === '`') {
                $quote = $c;
            }
            if ($c === ';') {
                if (trim($buf) !== '') {
                    $stmts[] = trim($buf);
                }
                $buf = '';
                continue;
            }
            $buf .= $c;
        }
        if (trim($buf) !== '') {
            $stmts[] = trim($buf);
        }
        return $stmts;
    }

    public static function run(PDO $pdo, string $file): void
    {
        foreach (self::split((string) file_get_contents($file)) as $stmt) {
            $pdo->exec($stmt);
        }
    }

    /** Crea la base de datos si no existe (nombre validado: solo letras, números y _). */
    public static function createDatabase(string $name): void
    {
        if (!preg_match('/^[A-Za-z0-9_]+$/', $name)) {
            throw new InvalidArgumentException('Nombre de base de datos no válido.');
        }
        $server = new PDO(
            sprintf('mysql:host=%s;port=%d;charset=utf8mb4', Env::get('DB_HOST', '127.0.0.1'), Env::int('DB_PORT', 3306)),
            (string) Env::get('DB_USER', 'root'),
            (string) Env::get('DB_PASS', ''),
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        $server->exec("CREATE DATABASE IF NOT EXISTS `{$name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    }
}
