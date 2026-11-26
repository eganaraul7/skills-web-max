# Base para sistemas de gestión en PHP

Núcleo tomado de `Proyectos v2/inventario-pos` (y `Pdf.php` del CRM). Es PHP 8.1 o superior con PDO, sin frameworks ni Composer, para que funcione en un hosting compartido barato con solo subir los archivos e importar el SQL.

## Lo que ya trae resuelto (no rehacerlo)

| Archivo | Qué hace |
|---|---|
| `core/bootstrap.php` | Carga `.env`, la configuración, la zona horaria y el autoload, y convierte los errores en excepciones |
| `core/Auth.php` | Login con `password_hash`, bloqueo tras 5 intentos en 15 min, sesión segura, cierre por inactividad y roles |
| `core/Csrf.php` | Token CSRF en todo POST |
| `core/Router.php` | Rutas `?r=modulo/accion` con acceso público, viewer o admin |
| `core/Crud.php` | CRUD genérico con consultas preparadas; las columnas solo salen de definiciones en el código |
| `core/Validator.php` · `core/Upload.php` | Validación y subidas seguras (tipo real, tamaño, nombre aleatorio, fuera de `public/`) |
| `core/Money.php` · `core/Sequence.php` · `core/Settings.php` | Montos en USD y Bs., numeración y ajustes |
| `core/Pdf.php` | Reportes en PDF sin librerías externas |
| `core/View.php` · `core/helpers.php` | Vistas con escapado (`e()`) |
| `public/index.php` | Único punto de entrada, con las cabeceras de seguridad |
| `database/install.php` | Crea la base, aplica `schema.sql` y `seed.sql` y crea el administrador |

## Lo que se escribe en cada proyecto

- `app/routes.php`, `app/nav.php`, `app/src/` (controladores y servicios) y `app/views/`.
- `database/schema.sql` y `database/seed.sql`.
- `public/assets/`: **diseño nuevo** con la identidad del cliente.
- Copia `config/.env.example` como `config/.env` y rellénalo. El `.env` nunca va a Git.

## Diseño

Las vistas de `core/views/` (login, listados, formularios, layout) **son funcionales, pero feas**: hay que rediseñarlas en cada proyecto según el plan y el diseño aprobado en Claude Design. La única referencia visual válida de Proyectos v2 es el **CRM Freelancers**.

## Reglas de negocio

- Toda lógica de dinero o stock va en servicios **puros y probados**, ejecutados dentro de transacciones.
- Ejemplos en `Proyectos v2/inventario-pos/app/src/`: `StockService` y `SaleService`.
- **Referencia funcional del Profesional:** Panda Biker, que comprueba el stock antes de vender, suma el stock al comprar y sugiere el precio con un +30 %. **No copies su código**: tiene inyección SQL y contraseñas sin cifrar.
