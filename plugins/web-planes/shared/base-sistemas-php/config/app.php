<?php
declare(strict_types=1);

/**
 * Configuración de la aplicación. Todos los valores sensibles vienen de config/.env.
 */
return [
    'name'             => Env::get('APP_NAME', 'Sistema del cliente'),
    'url'              => rtrim((string) Env::get('APP_URL', 'http://localhost:8080'), '/'),
    'env'              => Env::get('APP_ENV', 'production'),
    'debug'            => Env::bool('APP_DEBUG', false),
    'timezone'         => Env::get('APP_TIMEZONE', 'America/Caracas'),
    'session_idle'     => Env::int('SESSION_IDLE_MINUTES', 120),
    'upload_max_bytes' => Env::int('UPLOAD_MAX_MB', 3) * 1024 * 1024,
    'storage_path'     => BASE_PATH . '/storage',
    'uploads_path'     => BASE_PATH . '/storage/uploads',
    'logs_path'        => BASE_PATH . '/storage/logs',
    // Campos propios de este sistema en «Configuración del negocio»: campo => [etiqueta, reglas]
    'settings_fields' => [
        'allow_negative_stock' => ['Permitir stock negativo', 'boolean'],
    ],
    // Columnas de settings que se editan en otras pantallas del sistema (no en el formulario general)
    'settings_extra_columns' => [],
    // Mostrar la tasa de cambio en la barra superior
    'show_rate' => true,
];
