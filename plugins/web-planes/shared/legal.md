# Legal (obligatorio en los tres planes)

Así se evitan multas y demandas. Las plantillas de `plantillas/legal/` se rellenan con los datos del cliente. Las variables van entre corchetes: `[NEGOCIO]`, `[RIF]`, `[CORREO]`, etc.

Son plantillas generales, **no asesoría legal**. Un abogado debe revisar la plantilla base una vez. No inventes cláusulas que no estén en las plantillas: si el cliente necesita algo especial, avisa a Raúl.

## Lo que nunca puede faltar

| Proyecto | Documentos obligatorios |
|---|---|
| **Toda web** | Política de privacidad · Términos y condiciones · Política de cookies |
| **Tienda online** (e-commerce, y webs que venden con carrito o checkout) | Lo anterior + **términos de uso y condiciones de compra** + **política de devolución y reembolso** + política de envíos (si hay envíos) |
| **Sistema de gestión** | Términos de uso interno + privacidad de los datos de clientes y empleados (quién ve qué) |

Cada documento tiene su propia página (por ejemplo `/privacidad/`, `/terminos/`, `/cookies/`, `/devoluciones/`) y aparece enlazado en el pie de página.

## Cookies

Implementación lista en `cookies/`, en JS y CSS sin dependencias:

- **Aviso al entrar** con tres botones: **Aceptar todas**, **Rechazar** y **Configurar**.
- **Configurar** abre un panel por categoría:
  - **Necesarias:** siempre activas. Son la sesión, el carrito y las preferencias.
  - **Analítica:** desactivada por defecto.
  - **Marketing** (pixel de anuncios): desactivada por defecto.
- La persona **no está obligada a aceptar o rechazar todo**: puede activar o desactivar cada categoría.
- **Sin consentimiento no se carga** la analítica ni el pixel. Los scripts van como `<script type="text/plain" data-cookie="analitica">` y el gestor los activa solo si se aceptan.
- La elección se guarda (`localStorage`, con fecha y versión) y se puede **cambiar cuando se quiera** desde un enlace fijo del pie: **"Preferencias de cookies"**.
- Si la web no usa analítica ni marketing, se muestra un aviso informativo solo de cookies necesarias, con el mismo enlace al pie.

## Consentimiento en formularios

- Cada formulario que pide datos (contacto, pedido, reserva, registro, boletín) lleva una **casilla sin marcar por defecto**:
  > He leído y acepto la [política de privacidad] y el uso de mis datos para [responder mi mensaje / gestionar mi pedido / mi reserva].
- **Sin marcarla, el formulario no se envía.** Se valida también en el servidor.
- Se guarda **la fecha del consentimiento** junto al registro.
- El boletín lleva un consentimiento aparte, opcional, y un enlace para darse de baja en cada correo.

## Pie del negocio

- **Obligatorio:** nombre del negocio, RIF y contacto (teléfono o WhatsApp y correo; dirección si atiende en persona).
- **Opcional:** redes sociales del cliente, con iconos que enlazan a sus perfiles.
- **Derechos de autor:**
  > © [AÑO] [NEGOCIO]. Todos los derechos reservados.

  El año se calcula solo.
- **Enlaces:** Privacidad · Términos · Cookies · Preferencias de cookies (y Devoluciones en las tiendas).
- **Créditos de fotos de bancos libres,** si la licencia lo pide o como cortesía.

## Plantillas

| Archivo | Para |
|---|---|
| `plantillas/legal/privacidad.md` | Toda web |
| `plantillas/legal/terminos.md` | Toda web |
| `plantillas/legal/cookies.md` | Toda web |
| `plantillas/legal/compra-devoluciones.md` | Tiendas: condiciones de compra, envíos, cambios, devoluciones y reembolsos |
| `plantillas/legal/uso-interno-sistema.md` | Sistemas de gestión |

Cuando el diseño lo permita, las páginas legales usan el mismo diseño del sitio, con un índice y la fecha de la última actualización.
