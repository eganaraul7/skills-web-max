<?php
declare(strict_types=1);

/**
 * Plantillas PHP simples. 'core/login' => core/views/login.php;
 * cualquier otro prefijo => app/views/<nombre>.php.
 */
final class View
{
    public static function path(string $view): string
    {
        [$module, $name] = explode('/', $view, 2);
        $file = $module === 'core'
            ? BASE_PATH . '/core/views/' . $name . '.php'
            : BASE_PATH . '/app/views/' . $name . '.php';
        if (!is_file($file)) {
            throw new RuntimeException('Vista no encontrada: ' . $view);
        }
        return $file;
    }

    public static function partial(string $view, array $data = []): string
    {
        extract($data, EXTR_SKIP);
        ob_start();
        try {
            include self::path($view);
        } catch (Throwable $e) {
            ob_end_clean();
            throw $e;
        }
        return (string) ob_get_clean();
    }

    /**
     * Renderiza una vista dentro de un layout ('layout' panel, 'public_layout' páginas públicas, null sin layout).
     */
    public static function render(string $view, array $data = [], ?string $layout = 'layout'): void
    {
        $content = self::partial($view, $data);
        if ($layout === null) {
            echo $content;
            return;
        }
        echo self::partial('core/' . $layout, $data + ['content' => $content]);
    }
}
