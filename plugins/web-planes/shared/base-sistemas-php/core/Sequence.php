<?php
declare(strict_types=1);

/**
 * Numeración secuencial sin huecos por concurrencia (COT-0001, TCK-000001...).
 * Debe llamarse dentro de una transacción: bloquea la fila del contador hasta el commit.
 */
final class Sequence
{
    public static function next(PDO $pdo, string $name): int
    {
        if (!$pdo->inTransaction()) {
            throw new LogicException('Sequence::next requiere una transacción activa.');
        }
        $pdo->prepare('INSERT IGNORE INTO sequences (name, last_value) VALUES (?, 0)')->execute([$name]);
        $stmt = $pdo->prepare('SELECT last_value FROM sequences WHERE name = ? FOR UPDATE');
        $stmt->execute([$name]);
        $next = (int) $stmt->fetchColumn() + 1;
        $pdo->prepare('UPDATE sequences SET last_value = ? WHERE name = ?')->execute([$next, $name]);
        return $next;
    }

    public static function format(string $prefix, int $number, int $pad = 4): string
    {
        return $prefix . '-' . str_pad((string) $number, $pad, '0', STR_PAD_LEFT);
    }
}
