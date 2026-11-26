<?php
declare(strict_types=1);

/**
 * Aritmética monetaria exacta con bcmath (sin errores de coma flotante).
 * Todos los valores entran y salen como string numérico.
 */
final class Money
{
    public const SCALE = 8;

    public static function n(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '0';
        }
        if (is_float($value)) {
            $value = sprintf('%.' . self::SCALE . 'F', $value);
        }
        $value = (string) $value;
        if (!preg_match('/^-?\d+(\.\d+)?$/', $value)) {
            throw new InvalidArgumentException('Valor numérico no válido: ' . $value);
        }
        return bcadd($value, '0', self::SCALE);
    }

    public static function add(mixed ...$values): string
    {
        $sum = '0';
        foreach ($values as $v) {
            $sum = bcadd($sum, self::n($v), self::SCALE);
        }
        return $sum;
    }

    public static function sub(mixed $a, mixed $b): string
    {
        return bcsub(self::n($a), self::n($b), self::SCALE);
    }

    public static function mul(mixed $a, mixed $b): string
    {
        return bcmul(self::n($a), self::n($b), self::SCALE);
    }

    public static function div(mixed $a, mixed $b): string
    {
        if (bccomp(self::n($b), '0', self::SCALE) === 0) {
            throw new InvalidArgumentException('División entre cero.');
        }
        return bcdiv(self::n($a), self::n($b), self::SCALE);
    }

    /** Redondeo "half up" (alejándose de cero) a $places decimales. */
    public static function round(mixed $value, int $places = 2): string
    {
        $v = self::n($value);
        $negative = bccomp($v, '0', self::SCALE) < 0;
        $abs = $negative ? substr($v, 1) : $v;
        $half = '0.' . str_repeat('0', $places) . '5';
        $rounded = bcadd($abs, $half, $places);
        if (bccomp($rounded, '0', $places) === 0) {
            return $rounded;
        }
        return ($negative ? '-' : '') . $rounded;
    }

    public static function cmp(mixed $a, mixed $b): int
    {
        return bccomp(self::n($a), self::n($b), self::SCALE);
    }

    public static function isZero(mixed $a): bool
    {
        return self::cmp($a, '0') === 0;
    }

    public static function percent(mixed $amount, mixed $pct): string
    {
        return self::div(self::mul($amount, $pct), '100');
    }

    public static function toVes(mixed $usd, mixed $rate): string
    {
        return self::round(self::mul($usd, $rate), 2);
    }

    public static function toUsd(mixed $ves, mixed $rate): string
    {
        return self::round(self::div($ves, $rate), 2);
    }

    public static function format(mixed $amount, string $currency = 'USD'): string
    {
        $rounded = (float) self::round($amount ?? '0', 2);
        $txt = number_format(abs($rounded), 2, ',', '.');
        $sign = $rounded < 0 ? '-' : '';
        return $currency === 'VES' ? $sign . 'Bs. ' . $txt : $sign . '$ ' . $txt;
    }
}
