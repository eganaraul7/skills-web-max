# Seguridad (regla obligatoria en los tres planes)

Vale para el Básico, el Profesional y el Premium por igual. Si un punto no se cumple, **el proyecto no se entrega**.

## 1. Base de datos: nada de inyección SQL
- Todas las consultas, **siempre preparadas** con parámetros:
  - Django: el ORM, o `cursor.execute(sql, params)`;
  - PHP: PDO con `EMULATE_PREPARES=false`.
- Nunca se mete texto del usuario dentro de la consulta: ni concatenando, ni con f-strings, ni con interpolación.
- Nombres de tabla o columna dinámicos: solo de una lista fija definida en el código.
- El usuario de la base de datos de producción tiene solo los permisos que necesita.

> Ejemplo de lo que **nunca** se hace (estaba en Panda Biker):
> `"SELECT * FROM usuarios where usuario='$usuario' and clave='$contraseña'"`.
> Con eso, cualquiera entra escribiendo `' OR '1'='1` en el campo de usuario.

## 2. Contraseñas siempre cifradas
- **Django:** el sistema de usuarios de Django (PBKDF2 o Argon2).
- **PHP:** `password_hash` / `password_verify`, con rehash automático.
- **Nunca** se guardan tal cual, ni cifradas de forma reversible, ni con MD5 o SHA1.
- **Contraseñas iniciales:**
  - se generan al instalar y se muestran una sola vez, o se piden por variable de entorno;
  - nunca quedan escritas en el código ni en el repositorio.

## 3. Login protegido
- **Bloqueo** tras 5 intentos fallidos en 15 minutos, por cuenta y por IP.
- **Sesión:**
  - se renueva al entrar (`session_regenerate_id(true)` o lo que hace Django);
  - se cierra sola por inactividad;
  - **cookie** `HttpOnly`, `SameSite=Lax` y `Secure` cuando hay HTTPS.
- **Mensaje de error genérico** ("usuario o contraseña incorrectos"): no revela si el usuario existe.
- **Cada página del panel comprueba la sesión y el rol en el servidor**, no solo ocultando botones.

## 4. Formularios protegidos
- **CSRF:** token en todo POST, también en los `fetch` con la cabecera `X-CSRF-Token`.
- **XSS:**
  - toda salida escapada (`{{ }}` en Django; `htmlspecialchars` en PHP);
  - el HTML enriquecido se sanea con una lista de etiquetas permitidas;
  - el JSON metido dentro del HTML va escapado (`json_script` en Django; `JSON_HEX_TAG` en PHP).
- **Validación en el servidor**, aunque también se valide en el navegador.
- **Formularios públicos:** trampa antispam (honeypot) y límite de envíos por IP.

## 5. Subidas de archivos validadas
- Se comprueba el **tipo real** con `finfo` / Pillow, no la extensión.
- **Tamaño máximo.**
- Nombre aleatorio.
- Guardado **fuera** de la carpeta pública y del código, y servido con `X-Content-Type-Options: nosniff`.
- Los comprobantes de pago solo los ve el personal autorizado.

## 6. Claves secretas nunca en el frontend
- Contraseñas de base de datos, claves de APIs, tokens de pago, WhatsApp, correo o mapas privados van **siempre en `.env`**, en el servidor.
- `.env` está en `.gitignore`. Solo se versiona un `.env.example` **sin valores reales**.
- **Si una función necesita una clave, la llamada se hace desde la trastienda.** El navegador nunca ve la clave.
- Antes de cada commit, revisa que no se cuelen secretos:
  ```
  git grep -nE "(api[_-]?key|secret|password|token)\s*[:=]"
  ```
- Si una clave se filtró alguna vez a Git, **se cambia** (rotar), no basta con borrarla.

## 7. Que no se caiga con muchos usuarios a la vez
- **Límite de peticiones** por IP en el login, en los formularios y en la API.
- **Índices** en las columnas que se buscan o filtran.
- **Paginación** en todos los listados; nada de cargar miles de filas.
- **Transacciones** en las operaciones críticas (vender, descontar stock, confirmar pedidos): dos ventas simultáneas nunca descuadran el stock.
- Archivos estáticos con caché y comprimidos.
- **Prueba de carga antes de entregar:** simula al menos 50 usuarios a la vez sobre el login y las páginas principales, y anota el resultado. Por ejemplo:
  ```
  python -m locust --headless -u 50 -r 10 -t 1m
  ```

> **Siendo honesto con el cliente:** cuánto aguanta también depende del hosting que pague. La skill garantiza que el código no sea el punto débil.

## 8. Cabeceras y configuración de producción
- `DEBUG` apagado en producción.
- `ALLOWED_HOSTS` / `APP_URL` correctos.
- **HTTPS obligatorio:** redirección y HSTS.
- **Cabeceras:**
  - `X-Content-Type-Options: nosniff`;
  - `X-Frame-Options: DENY` (salvo el editor del CMS);
  - `Referrer-Policy: strict-origin-when-cross-origin`;
  - `Content-Security-Policy` razonable.
- **Errores sin detalles técnicos para el visitante.** El detalle va al registro (log).
- **Respaldo de la base de datos** antes de cada cambio en producción.

## Base ya resuelta

- **Sistemas en PHP:** parte de `base-sistemas-php/`, el núcleo de Proyectos v2. Ya trae resueltos los puntos 1 a 5.
- **Django:** usa sus protecciones estándar (CSRF, ORM, autenticación, escapado) y añade el bloqueo de intentos y el límite de peticiones.
