<?php
declare(strict_types=1);

/** Inicio de sesión, configuración del negocio, usuarios y archivos. El panel de inicio lo define cada sistema en app/. */
final class CoreController
{
    public static function routes(): array
    {
        return [
            ['login', [self::class, 'login'], 'public', ['GET', 'POST']],
            ['logout', [self::class, 'logout'], 'viewer', ['POST']],
            ['settings', [self::class, 'settings'], 'admin'],
            ['settings/save', [self::class, 'saveSettings'], 'admin', ['POST']],
            ['users', [self::class, 'users'], 'admin'],
            ['users/form', [self::class, 'userForm'], 'admin'],
            ['users/save', [self::class, 'saveUser'], 'admin', ['POST']],
            ['users/delete', [self::class, 'deleteUser'], 'admin', ['POST']],
            ['media', static fn() => Upload::serve(query_int('id')), 'public'],
        ];
    }

    public static function login(): void
    {
        if (Auth::check()) {
            redirect('dashboard');
        }
        if (is_post()) {
            $v = Validator::make($_POST, ['email' => ['Correo', 'required|email|max:190'], 'password' => ['Contraseña', 'required|max:200']]);
            if ($v->fails()) {
                with_errors($v->errors(), ['email' => $_POST['email'] ?? '']);
                redirect('login');
            }
            $result = Auth::attempt($v->validated()['email'], (string) $_POST['password']);
            if ($result !== true) {
                with_errors(['password' => $result], ['email' => $v->validated()['email']]);
                redirect('login');
            }
            $intended = (string) ($_SESSION['_intended'] ?? '');
            unset($_SESSION['_intended']);
            // Solo redirige a rutas internas (evita redirecciones abiertas).
            if ($intended !== '' && str_starts_with($intended, '/') && !str_starts_with($intended, '//')) {
                header('Location: ' . $intended, true, 303);
                exit;
            }
            redirect('dashboard');
        }
        View::render('core/login', ['title' => 'Iniciar sesión'], 'public_layout');
    }

    public static function logout(): void
    {
        Auth::logout();
        Auth::startSession();
        flash('success', 'Cerraste sesión correctamente.');
        redirect('login');
    }

    public static function settings(): void
    {
        $zones = array_values(array_filter(DateTimeZone::listIdentifiers(), static fn($z) => str_starts_with($z, 'America/')));
        $rates = Db::all('SELECT r.rate, r.created_at, u.name FROM exchange_rates r LEFT JOIN users u ON u.id = r.created_by ORDER BY r.id DESC LIMIT 10');
        View::render('core/settings', ['title' => 'Configuración del negocio', 's' => Settings::all(), 'zones' => $zones, 'rates' => $rates]);
    }

    public static function saveSettings(): void
    {
        $v = Validator::make($_POST, [
            'business_name'        => ['Nombre del negocio', 'required|max:120'],
            'base_currency'        => ['Moneda principal', 'required|in:USD,VES'],
            'exchange_rate'        => ['Tasa de cambio', 'required|decimal|min_value:0.0001|max_value:9999999'],
            'phone'                => ['Teléfono', 'phone|max:30'],
            'whatsapp'             => ['WhatsApp', 'phone|max:30'],
            'email'                => ['Correo', 'email|max:190'],
            'address'              => ['Dirección', 'max:255'],
            'timezone'             => ['Zona horaria', 'required|max:64'],
        ] + (array) config('settings_fields', []));
        $data = $v->validated();
        if (!$v->fails() && !in_array($data['timezone'], DateTimeZone::listIdentifiers(), true)) {
            $v->addError('timezone', 'Selecciona una zona horaria válida.');
        }
        $logo = $_FILES['logo'] ?? null;
        if ($logo && ($logo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $up = Upload::image($logo, 'Logo de ' . ($data['business_name'] ?? 'el negocio'));
            $up['ok'] ? $data['logo_media_id'] = $up['id'] : $v->addError('logo', $up['error']);
        } elseif (!empty($_POST['remove_logo'])) {
            $data['logo_media_id'] = null;
        }
        if ($v->fails()) {
            with_errors($v->errors(), $_POST);
            flash('error', 'Revisa los campos marcados.');
            redirect('settings');
        }
        if (Money::cmp($data['exchange_rate'], Settings::rate()) !== 0) {
            $data['exchange_rate_updated_at'] = now()->format('Y-m-d H:i:s');
            Db::run('INSERT INTO exchange_rates (rate, created_by) VALUES (?, ?)', [$data['exchange_rate'], Auth::id()]);
        }
        Settings::update($data);
        flash('success', 'Configuración guardada.');
        redirect('settings');
    }

    public static function users(): void
    {
        View::render('core/users', ['title' => 'Usuarios', 'users' => Db::all('SELECT * FROM users ORDER BY name')]);
    }

    public static function userForm(): void
    {
        $id = query_int('id');
        $user = $id ? (Db::one('SELECT id, name, email, role, is_active FROM users WHERE id = ?', [$id]) ?? abort(404)) : [];
        View::render('core/user_form', ['title' => $id ? 'Editar usuario' : 'Nuevo usuario', 'user' => $user, 'id' => $id]);
    }

    public static function saveUser(): void
    {
        $id = query_int('id');
        $v = Validator::make($_POST, [
            'name'      => ['Nombre', 'required|max:120'],
            'email'     => ['Correo', 'required|email|max:190'],
            'role'      => ['Rol', 'required|in:admin,viewer'],
            'is_active' => ['Activo', 'boolean'],
            'password'  => ['Contraseña', ($id ? '' : 'required|') . 'min:10|max:200'],
        ]);
        $data = $v->validated();
        if (!$v->fails() && Db::value('SELECT id FROM users WHERE email = ? AND id <> ?', [$data['email'], $id])) {
            $v->addError('email', 'Ya existe un usuario con ese correo.');
        }
        if ($id === Auth::id() && ($data['role'] !== 'admin' || !$data['is_active'])) {
            $v->addError('role', 'No puedes quitarte a ti mismo el rol de administrador ni desactivarte.');
        }
        if ($v->fails()) {
            with_errors($v->errors(), array_diff_key($_POST, ['password' => 1]));
            redirect('users/form', $id ? ['id' => $id] : []);
        }
        if ($id) {
            Db::run('UPDATE users SET name = ?, email = ?, role = ?, is_active = ? WHERE id = ?', [$data['name'], $data['email'], $data['role'], $data['is_active'], $id]);
            if ($data['password']) {
                Db::run('UPDATE users SET password_hash = ? WHERE id = ?', [password_hash($data['password'], PASSWORD_DEFAULT), $id]);
            }
        } else {
            Db::insert('INSERT INTO users (name, email, password_hash, role, is_active) VALUES (?,?,?,?,?)', [
                $data['name'], $data['email'], password_hash($data['password'], PASSWORD_DEFAULT), $data['role'], $data['is_active'],
            ]);
        }
        flash('success', 'Usuario guardado.');
        redirect('users');
    }

    public static function deleteUser(): void
    {
        $id = query_int('id');
        if ($id === Auth::id()) {
            flash('error', 'No puedes eliminar tu propio usuario.');
        } else {
            Db::run('DELETE FROM users WHERE id = ?', [$id]);
            flash('success', 'Usuario eliminado.');
        }
        redirect('users');
    }
}
