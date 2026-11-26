<?php
declare(strict_types=1);

/**
 * Enrutador por nombre (?r=modulo/accion).
 * Niveles de acceso: public | viewer (sesión iniciada) | admin (escritura).
 * Toda petición POST valida el token CSRF antes de llegar al controlador.
 */
final class Router
{
    /** @var array<string, array{handler: callable, access: string, methods: string[]}> */
    private array $routes = [];

    public function add(string $name, callable|array $handler, string $access = 'admin', array $methods = ['GET']): void
    {
        $this->routes[$name] = ['handler' => $handler, 'access' => $access, 'methods' => $methods];
    }

    /** @param array<int, array> $routes [nombre, handler, acceso?, métodos?] */
    public function addMany(array $routes): void
    {
        foreach ($routes as $r) {
            $this->add($r[0], $r[1], $r[2] ?? 'admin', $r[3] ?? ['GET']);
        }
    }

    public function has(string $name): bool
    {
        return isset($this->routes[$name]);
    }

    public function dispatch(string $name, string $method): void
    {
        $route = $this->routes[$name] ?? null;
        if ($route === null) {
            abort(404, 'La dirección que buscas no existe.');
        }
        if (!in_array($method, $route['methods'], true)) {
            abort(405, 'Esta acción no admite el método ' . $method . '.');
        }
        if ($method === 'POST' && !Csrf::verify()) {
            if (str_contains((string) ($_SERVER['HTTP_ACCEPT'] ?? ''), 'application/json')) {
                json_response(['ok' => false, 'error' => 'El formulario expiró. Recarga la página e inténtalo de nuevo.'], 419);
            }
            abort(419, 'Por seguridad, recarga la página e inténtalo de nuevo.');
        }
        if ($route['access'] !== 'public') {
            if (!Auth::check()) {
                if ($method === 'GET') {
                    $_SESSION['_intended'] = $_SERVER['REQUEST_URI'] ?? '';
                }
                redirect('login');
            }
            if ($route['access'] === 'admin' && !Auth::isAdmin()) {
                abort(403, 'Tu usuario es de solo lectura. Pide a un administrador que haga este cambio.');
            }
        }
        ($route['handler'])();
    }
}
