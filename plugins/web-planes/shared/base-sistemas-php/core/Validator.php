<?php
declare(strict_types=1);

/**
 * Validación en servidor con mensajes en español.
 *
 * $v = Validator::make($_POST, [
 *   'name'  => ['Nombre', 'required|max:120'],
 *   'price' => ['Precio', 'required|decimal|min_value:0'],
 * ]);
 * if ($v->fails()) { ... $v->errors() ... }  $data = $v->validated();
 *
 * Reglas: required, nullable, max:n, min:n, email, decimal, integer, min_value:x, max_value:x,
 *         date, time, in:a,b,c, phone, url, boolean, slug
 */
final class Validator
{
    private array $errors = [];
    private array $clean = [];

    private function __construct(private readonly array $input, private readonly array $rules)
    {
        $this->run();
    }

    public static function make(array $input, array $rules): self
    {
        return new self($input, $rules);
    }

    public function fails(): bool
    {
        return $this->errors !== [];
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function validated(): array
    {
        return $this->clean;
    }

    public function addError(string $field, string $message): void
    {
        $this->errors[$field] ??= $message;
    }

    private function run(): void
    {
        foreach ($this->rules as $field => [$label, $ruleString]) {
            $rules = $ruleString === '' ? [] : explode('|', $ruleString);
            $raw = $this->input[$field] ?? null;
            $value = is_string($raw) ? trim($raw) : $raw;

            if (in_array('boolean', $rules, true)) {
                $this->clean[$field] = in_array($value, ['1', 1, true, 'on', 'true'], true) ? 1 : 0;
                continue;
            }
            $empty = $value === null || $value === '' || $value === [];
            if ($empty) {
                if (in_array('required', $rules, true)) {
                    $this->errors[$field] = 'El campo «' . $label . '» es obligatorio.';
                }
                $this->clean[$field] = null;
                continue;
            }
            if (!is_string($value) || !mb_check_encoding($value, 'UTF-8')) {
                $this->errors[$field] = 'El campo «' . $label . '» contiene caracteres no válidos.';
                continue;
            }

            foreach ($rules as $rule) {
                [$name, $param] = array_pad(explode(':', $rule, 2), 2, null);
                $error = $this->check($name, $param, $value, $label);
                if ($error !== null) {
                    $this->errors[$field] = $error;
                    break;
                }
                if ($name === 'decimal') {
                    $value = (string) parse_decimal($value);
                } elseif ($name === 'integer') {
                    $value = (string) (int) $value;
                } elseif ($name === 'email') {
                    $value = mb_strtolower($value);
                }
            }
            $this->clean[$field] = $value;
        }
    }

    private function check(string $rule, ?string $param, string $value, string $label): ?string
    {
        switch ($rule) {
            case 'required':
            case 'nullable':
                return null;
            case 'max':
                return mb_strlen($value) > (int) $param ? "«{$label}» admite como máximo {$param} caracteres." : null;
            case 'min':
                return mb_strlen($value) < (int) $param ? "«{$label}» debe tener al menos {$param} caracteres." : null;
            case 'email':
                return filter_var($value, FILTER_VALIDATE_EMAIL) === false ? "«{$label}» debe ser un correo válido (ej.: nombre@dominio.com)." : null;
            case 'decimal':
                return parse_decimal($value) === null ? "«{$label}» debe ser un número (use coma o punto para decimales)." : null;
            case 'integer':
                return preg_match('/^-?\d+$/', $value) ? null : "«{$label}» debe ser un número entero.";
            case 'min_value':
                $n = parse_decimal($value);
                return ($n !== null && Money::cmp($n, $param) < 0) ? "«{$label}» debe ser mayor o igual a {$param}." : null;
            case 'max_value':
                $n = parse_decimal($value);
                return ($n !== null && Money::cmp($n, $param) > 0) ? "«{$label}» debe ser menor o igual a {$param}." : null;
            case 'date':
                $d = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
                return ($d && $d->format('Y-m-d') === $value) ? null : "«{$label}» debe ser una fecha válida.";
            case 'time':
                return preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', substr($value, 0, 5)) ? null : "«{$label}» debe ser una hora válida (HH:MM).";
            case 'in':
                return in_array($value, explode(',', (string) $param), true) ? null : "Selecciona una opción válida en «{$label}».";
            case 'phone':
                return preg_match('/^\+?[\d\s\-().]{7,20}$/', $value) ? null : "«{$label}» debe ser un teléfono válido (ej.: 0414-1234567).";
            case 'url':
                return (filter_var($value, FILTER_VALIDATE_URL) && preg_match('#^https?://#i', $value)) ? null : "«{$label}» debe ser una dirección web que empiece por http:// o https://.";
            case 'slug':
                return preg_match('/^[a-z0-9-]+$/', $value) ? null : "«{$label}» solo admite minúsculas, números y guiones.";
            default:
                throw new LogicException('Regla de validación desconocida: ' . $rule);
        }
    }
}
