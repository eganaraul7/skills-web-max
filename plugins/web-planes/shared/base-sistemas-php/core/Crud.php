<?php
declare(strict_types=1);

/**
 * CRUD declarativo para catálogos sencillos (clientes, servicios, mesas, categorías...).
 * Los nombres de tabla y columnas salen SOLO de la definición en código, nunca de la petición;
 * los valores siempre van como parámetros preparados.
 *
 * Definición:
 * [
 *   'route' => 'clients', 'table' => 'clients', 'title' => 'Clientes', 'singular' => 'cliente',
 *   'order' => 'name ASC', 'search' => ['name', 'email'], 'empty' => 'Mensaje de estado vacío',
 *   'fields' => [
 *     'name' => ['label' => 'Nombre', 'type' => 'text', 'rules' => 'required|max:120', 'list' => true],
 *     'zone_id' => ['label' => 'Zona', 'type' => 'select', 'options' => fn() => [id => nombre], 'rules' => 'required'],
 *     'services' => ['label' => 'Servicios', 'type' => 'pivot', 'pivot' => ['booking_staff_services', 'staff_id', 'service_id'], 'options' => fn() => [...]],
 *   ],
 *   'beforeDelete' => fn(int $id): ?string => null, // mensaje si no se puede borrar
 *   'parent' => ['route' => 'bookings/settings', 'label' => 'Configuración de reservas'],
 * ]
 * Tipos: text, email, tel, textarea, decimal, integer, select, checkbox, time, pivot
 */
final class Crud
{
    public static function routes(callable $definition): array
    {
        $route = $definition()['route'];
        return [
            [$route, static fn() => self::index($definition()), 'viewer'],
            [$route . '/form', static fn() => self::form($definition()), 'admin'],
            [$route . '/save', static fn() => self::save($definition()), 'admin', ['POST']],
            [$route . '/delete', static fn() => self::delete($definition()), 'admin', ['POST']],
        ];
    }

    public static function options(array $field): array
    {
        $o = $field['options'] ?? [];
        return is_callable($o) ? $o() : $o;
    }

    private static function index(array $d): void
    {
        $q = trim((string) ($_GET['q'] ?? ''));
        $page = max(1, query_int('page', 1));
        $perPage = 25;
        $where = '';
        $params = [];
        if ($q !== '' && !empty($d['search'])) {
            $where = ' WHERE ' . implode(' OR ', array_map(static fn($c) => $c . ' LIKE ?', $d['search']));
            $params = array_fill(0, count($d['search']), '%' . $q . '%');
        }
        $total = (int) Db::value('SELECT COUNT(*) FROM ' . $d['table'] . $where, $params);
        $rows = Db::all(
            'SELECT * FROM ' . $d['table'] . $where . ' ORDER BY ' . ($d['order'] ?? 'id DESC') . ' LIMIT ' . $perPage . ' OFFSET ' . (($page - 1) * $perPage),
            $params
        );
        View::render('core/crud_list', [
            'title' => $d['title'], 'd' => $d, 'rows' => $rows, 'q' => $q,
            'page' => $page, 'pages' => max(1, (int) ceil($total / $perPage)), 'total' => $total,
        ]);
    }

    private static function form(array $d): void
    {
        $id = query_int('id');
        $row = [];
        if ($id) {
            $row = Db::one('SELECT * FROM ' . $d['table'] . ' WHERE id = ?', [$id]) ?? abort(404, 'El registro no existe.');
            foreach ($d['fields'] as $name => $f) {
                if (($f['type'] ?? '') === 'pivot') {
                    [$table, $local, $foreign] = $f['pivot'];
                    $row[$name] = array_map('strval', Db::run("SELECT {$foreign} FROM {$table} WHERE {$local} = ?", [$id])->fetchAll(PDO::FETCH_COLUMN));
                }
            }
        }
        View::render('core/crud_form', [
            'title' => ($id ? 'Editar ' : 'Nuevo ') . $d['singular'], 'd' => $d, 'row' => $row, 'id' => $id,
        ]);
    }

    private static function save(array $d): void
    {
        $id = query_int('id');
        $rules = [];
        foreach ($d['fields'] as $name => $f) {
            $type = $f['type'] ?? 'text';
            if ($type === 'pivot') {
                continue;
            }
            $r = $f['rules'] ?? '';
            if ($type === 'checkbox') {
                $r = 'boolean';
            } elseif ($type === 'email') {
                $r .= '|email';
            } elseif ($type === 'decimal') {
                $r .= '|decimal';
            } elseif ($type === 'integer') {
                $r .= '|integer';
            } elseif ($type === 'time') {
                $r .= '|time';
            } elseif ($type === 'tel') {
                $r .= '|phone';
            }
            $rules[$name] = [$f['label'], trim($r, '|')];
        }
        $v = Validator::make($_POST, $rules);
        $data = $v->validated();
        foreach ($d['fields'] as $name => $f) {
            if (($f['type'] ?? '') === 'select' && $data[$name] !== null && !array_key_exists($data[$name], self::options($f))) {
                $v->addError($name, 'Selecciona una opción válida en «' . $f['label'] . '».');
            }
        }
        if (isset($d['validate'])) {
            foreach (($d['validate'])($data, $id) as $field => $msg) {
                $v->addError($field, $msg);
            }
        }
        if ($v->fails()) {
            with_errors($v->errors(), $_POST);
            flash('error', 'Revisa los campos marcados.');
            redirect($d['route'] . '/form', $id ? ['id' => $id] : []);
        }

        $pivots = [];
        foreach ($d['fields'] as $name => $f) {
            if (($f['type'] ?? '') === 'pivot') {
                $allowed = array_map('strval', array_keys(self::options($f)));
                $pivots[$name] = array_values(array_intersect(array_map('strval', (array) ($_POST[$name] ?? [])), $allowed));
            }
        }

        Db::transaction(static function (PDO $pdo) use ($d, &$id, $data, $pivots): void {
            $cols = array_keys($data);
            if ($id) {
                $set = implode(', ', array_map(static fn($c) => $c . ' = ?', $cols));
                $pdo->prepare('UPDATE ' . $d['table'] . ' SET ' . $set . ' WHERE id = ?')->execute([...array_values($data), $id]);
            } else {
                $ph = implode(', ', array_fill(0, count($cols), '?'));
                $pdo->prepare('INSERT INTO ' . $d['table'] . ' (' . implode(', ', $cols) . ') VALUES (' . $ph . ')')->execute(array_values($data));
                $id = (int) $pdo->lastInsertId();
            }
            foreach ($pivots as $name => $values) {
                [$table, $local, $foreign] = $d['fields'][$name]['pivot'];
                $pdo->prepare("DELETE FROM {$table} WHERE {$local} = ?")->execute([$id]);
                $ins = $pdo->prepare("INSERT INTO {$table} ({$local}, {$foreign}) VALUES (?, ?)");
                foreach ($values as $value) {
                    $ins->execute([$id, (int) $value]);
                }
            }
        });
        flash('success', ucfirst($d['singular']) . ' guardado correctamente.');
        redirect($d['route']);
    }

    private static function delete(array $d): void
    {
        $id = query_int('id');
        if (isset($d['beforeDelete']) && ($msg = ($d['beforeDelete'])($id)) !== null) {
            flash('error', $msg);
            redirect($d['route']);
        }
        try {
            Db::run('DELETE FROM ' . $d['table'] . ' WHERE id = ?', [$id]);
            flash('success', ucfirst($d['singular']) . ' eliminado.');
        } catch (PDOException $e) {
            if (($e->errorInfo[1] ?? 0) === 1451) {
                flash('error', 'No se puede eliminar: este ' . $d['singular'] . ' está en uso. Puedes desactivarlo en su lugar.');
            } else {
                throw $e;
            }
        }
        redirect($d['route']);
    }

    /** Texto para mostrar una celda en el listado. */
    public static function display(array $field, mixed $value): string
    {
        $type = $field['type'] ?? 'text';
        if (isset($field['format'])) {
            return ($field['format'])($value);
        }
        return match ($type) {
            'checkbox' => $value ? 'Sí' : 'No',
            'select'   => (string) (self::options($field)[$value] ?? '—'),
            'decimal'  => $value === null ? '—' : number_format((float) $value, 2, ',', '.'),
            'time'     => $value ? time_es((string) $value) : '—',
            default    => ($value === null || $value === '') ? '—' : mb_strimwidth((string) $value, 0, 80, '…'),
        };
    }
}
