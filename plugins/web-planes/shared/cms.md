# CMS: editor de contenido en tiempo real (cms-web-max)

Con el CMS, el cliente cambia **textos, fotos y vídeos** de su web real de forma fácil y directa, sin llamar a Raúl:
- entra a `/cms/`, hace clic en una sección, escribe sobre los textos y cambia las imágenes;
- ve el borrador en celular, tableta o computadora;
- publica cuando quiere y puede volver a cualquier versión.

## Cuándo va

| Plan | CMS |
|---|---|
| Básico | Extra de $30 (solo si está en el alcance firmado) |
| Profesional | Extra de $40 a $60, según el contenido |
| Premium | **Incluido siempre** |
| Sistemas de gestión y e-commerce | Ya traen su propio panel: no se cobra aparte |

## Dónde está

- **Repositorio del CMS:** `C:\Users\raule\Documents\cms-web-max`.
- **Guía completa:** `cms/README.md` dentro de ese repositorio. **Léela antes de instalarlo**: manda sobre este resumen si algo cambió.
- **Si esa carpeta no existe en esta computadora, pide a Raúl el repositorio.** No uses el Studio viejo de `paginas_webs`.

## Cómo se instala en la web del cliente

Requiere Python 3.11 o superior. **El CMS corre con Django**, así que una web con CMS necesita un hosting con Python (ver más abajo).

```powershell
# desde el repositorio del CMS
python scripts/instalar.py C:/ruta/a/la-web --nombre "Nombre del negocio"
# desde la carpeta de la web
pip install -r cms/requirements.txt
python cms/manage.py migrate
python cms/manage.py createcachetable
python cms/manage.py crear_admin      # Raúl crea el administrador; el cliente queda como editor
```

El instalador hace esto:
- copia el núcleo en `cms/`;
- crea `cms/.env` con una clave nueva (**fuera de Git**);
- crea `cms.config.json`.

Para actualizar el CMS se vuelve a ejecutar el instalador y luego `migrate`. Nunca toca `.env`, `data/`, `media/` ni `content/`.

## Cómo se conecta, por tecnología

| Web | Cómo |
|---|---|
| HTML, CSS y JS | `<div data-cms-page>`, más `cms.css` y `cms.global.js`. Servida por el CMS, llega ya pintada (bueno para el SEO) |
| Django | `cms.core` en `INSTALLED_APPS`, `path("cms/", include("cms.core.urls"))` y las etiquetas `{% cms_head %}`, `{% cms_render %}` y `{% cms_scripts %}`. El diseño de cada bloque se cambia en `templates/cms/blocks/<tipo>.html` |
| PHP | `require 'cms/integrations/php/cms.php'`, más `cms_head()`, `cms_body()` y `cms_scripts()` |
| Astro | `readHead()` y `readBody()` de `cms/integrations/astro/cms.mjs`; el deploy hook va en `webhookUrl` |
| React | `createClient().page()` del SDK; se marca `data-block-id` y `data-cms-field` y se llama a `enableVisualEditing()` |

Los ejemplos completos están en `examples/html-vanilla` y `examples/django-site` del repositorio del CMS.

## Adaptarlo a cada proyecto (sin tocar el núcleo)

- **Colores, tipografías y logo:** desde el panel.
- **Qué bloques puede usar cada sitio:** en `cms.config.json`.
- **Bloques propios:** un `.json` por bloque en `cms-blocks/`, con su plantilla o su renderer en la web.
- **Los 23 bloques base:**
  - Encabezado, Pie, Barra de aviso, Portada, Portada dividida.
  - Banda de logos, Servicios, Imagen y texto, Cifras, Pasos, Historia.
  - Galería, Vídeo, Testimonios, Equipo, Precios, Tabla comparativa.
  - Tarjetas destacadas, Preguntas frecuentes, Llamado a la acción, Contacto, Mapa y horarios, Texto libre.

**El diseño lo pone la web, no el CMS.** Cada bloque se pinta con el diseño aprobado en Claude Design. Nunca se deja el aspecto por defecto de los bloques en un proyecto Profesional o Premium.

## Hosting

El CMS va **dentro de la web** del cliente: no hay un servidor aparte.

- **PythonAnywhere** (predeterminado): web y CMS juntos.
- **VPS:** con gunicorn y disco persistente para `cms/data` y `cms/media`.
- **Netlify o Vercel:** sirven solo la parte estática; el CMS tiene que correr en otro servidor (p. ej. `cms.cliente.com`).

**Ojo:** una web Básica sin trastienda que **compra el CMS** pasa a necesitar hosting con Python. Hay que decírselo al cliente en la cotización con su costo anual (ver `stack-hosting.md`).

## Siempre, lleve CMS o no

Deja el contenido **separado del diseño** (cada sección como un bloque con sus campos), para poder conectar el CMS después sin rehacer la web.

## Comprobaciones al entregar con CMS

- [ ] `cms/.env` fuera de Git, con `DJANGO_DEBUG=0`, `ALLOWED_HOSTS` y `CSRF_TRUSTED_ORIGINS` del dominio real.
- [ ] El cliente entra como **editor**, no como administrador.
- [ ] Probado con Playwright:
  - editar un texto y una imagen;
  - publicar;
  - ver el cambio en la web;
  - volver a la versión anterior.
- [ ] Los bloques usados se ven con el diseño del proyecto en celular, tableta y computadora.
