# Alcance por tipo de proyecto y plan

Es la tabla de referencia para el **control de alcance**. Cada plan incluye todo lo del anterior. Lo que no aparece en el plan del cliente **no se construye**: es un extra o un plan superior, y se cotiza con la guía de Raúl (`Cotizaciones/Guia-de-cotizacion.md`).

## Común a todo proyecto

| | Básico | Profesional | Premium |
|---|---|---|---|
| Propuestas de diseño (Claude Design) | Hasta 2 | Hasta 3 | Hasta 3 |
| Rondas de revisión de la web | 2 | 3 | 4 |
| CMS: editor de contenido en tiempo real (webs) | Extra de $30 | Extra de $40 a $60 | Incluido |
| Versión de escritorio (.exe) | Extra de $20 | Extra de $20 | Incluida |

## Landing (una sola página larga)

| Básico | Profesional | Premium |
|---|---|---|
| Hasta 5 secciones, formulario de contacto, botón de WhatsApp, SEO básico | Hasta 8 secciones, formulario que llega al correo, analítica, SEO on-page | Hasta 12 secciones, integraciones (agenda, pixel de anuncios, CRM), velocidad medida antes y después |

## Sitio web (hasta 5 páginas)

| Básico | Profesional | Premium |
|---|---|---|
| 5 páginas, formulario, WhatsApp y mapa | Galería o blog simple | Integraciones, rendimiento medido y capacitación |

Página extra: $30, $40 o $50 según el plan. Una página con función (blog, tienda, reservas) se cotiza como programa extra.

## Landing o sitio que vende (sin ser tienda)

Una web de ventas **no hace lo mismo que un e-commerce**: es una vitrina con pocos productos.

| | Básico | Profesional | Premium |
|---|---|---|---|
| Productos | Se muestran, sin carrito | Catálogo corto: **hasta 15**, con carrito | Catálogo corto: **hasta 30**, con carrito |
| Cómo se compra | Botón de WhatsApp con el mensaje ya escrito ("Hola, me interesa…") | El carrito se envía a WhatsApp con la lista de productos, las cantidades y el total | WhatsApp, o checkout con los datos de pago, la subida del comprobante y aviso por correo |

**Nunca incluye** (eso ya es e-commerce):
- pasarela de pago automática;
- cuentas de cliente;
- cupones;
- inventario sincronizado;
- cálculo de envíos;
- categorías con filtros.

Si el cliente pide alguna, o vende a diario con muchos productos, avisa: "esto ya es un e-commerce y se cotiza como tal".

## E-commerce (la tienda es el producto)

| | Básico | Profesional | Premium |
|---|---|---|---|
| Productos en la tienda | Ilimitados | Ilimitados | Ilimitados |
| Productos que carga Raúl | 10 | 20 | 30 a 40 |
| Cómo se paga | El carrito se envía a WhatsApp con la lista y el total; se cierra por privado (pago móvil) | WhatsApp, **más pago manual con comprobante** (datos de pago, captura y número de referencia) y pasarela (PayPal, Binance…) si el negocio tiene cuenta | Todo lo anterior, más varios métodos de pago con automatización |
| Funciones | Carrito, panel simple para productos y pedidos, se ve bien en celular | Categorías, filtros, cupones, panel de pedidos, correos de confirmación, inventario básico | Inventario sincronizado, reportes de ventas, envíos, SEO de cada producto, **importación desde Excel** |

- El resto de los productos los carga el cliente desde su panel. Si se los pide a Raúl: $5 por cada 10.
- **Importación desde Excel** (solo Premium):
  - cada fila lleva el nombre de la foto o un enlace;
  - las fotos se suben en una carpeta junto con el archivo;
  - después cada producto se edita por separado: datos, fotos, stock y aviso de stock bajo.

## Sistemas de gestión

Base técnica: `base-sistemas-php/`.

| | Básico | Profesional | Premium |
|---|---|---|---|
| Módulos | Inventario, ventas, usuarios y reportes | Hasta 6 (nivel Panda Biker: inventario, compras, ventas, proveedores, usuarios, reportes) | Más de 6, a medida |
| Usuarios | 1 administrador y hasta 2 empleados | Hasta 10, con roles de administrador y empleado | Sin límite, con permisos por usuario |
| Funciones | Crear, ver, editar y borrar; búsqueda; reporte imprimible simple por módulo, **sin filtros** | Reglas de negocio (stock, costos, comprobar stock antes de vender), reportes en PDF por fechas, filtros, exportar a Excel, panel con totales, respaldo | **Dashboard tipo CRM** (gráficas, tendencias, mejores productos y clientes, alertas), historial de quién cambió qué, facturación, importar desde Excel, respaldos automáticos |
| Diseño | Limpio y ordenado, con el logo y los colores del cliente | Estilizado con la identidad del cliente: tipografía, paleta y diseño | Exclusivo, con modo oscuro y detalles muy cuidados |

- Los sistemas **no cobran por internet**: el empleado registra la venta y su forma de pago (efectivo, pago móvil, transferencia, tarjeta).
- **Siempre son web.** La versión de escritorio es un acceso directo `.exe` (ver `escritorio.md`).
- Módulo extra: $100 si es simple; $150 con cálculos o reportes; $200 o más con integraciones o facturación.

## Reservas (restaurante, consultorio, barbería…)

| Básico | Profesional | Premium |
|---|---|---|
| Botón de WhatsApp para reservar | Formulario de reserva que llega al correo o al WhatsApp del negocio, sin control de disponibilidad | Calendario con horarios disponibles, sin dobles reservas, panel para aceptar o cancelar, y **notificaciones push** al dashboard y al WhatsApp con cada reserva nueva |

## Notificaciones

Toda notificación aparece también **en el panel**, con un contador y una lista de pendientes.

### E-commerce y webs que venden

| Aviso | Básico | Profesional | Premium |
|---|---|---|---|
| Nuevo pedido | Contador en el panel (y el mensaje de WhatsApp) | Panel y correo | Panel, correo y WhatsApp |
| Comprobante de pago recibido | — | Panel y correo | Panel, correo y WhatsApp |
| Pedido sin atender tras varias horas | — | Panel | Panel, correo y WhatsApp |
| Stock bajo o producto agotado | Panel | Panel y correo | Panel, correo y WhatsApp |
| Pago fallido | — | Panel y correo | Panel, correo y WhatsApp |
| Fallas (la web no responde, error del sistema) | — | — | Alerta automática al administrador y vigilancia que avisa si la web se cae |

### Sistemas de gestión

| Aviso | Básico | Profesional | Premium |
|---|---|---|---|
| Stock bajo y producto agotado | Panel | Panel y correo | Panel, correo y WhatsApp |
| Compra a proveedor registrada | — | Panel | Panel y correo |
| Intentos de acceso fallidos o usuario bloqueado | Panel | Panel y correo | Panel, correo y WhatsApp |
| Respaldo hecho o fallido | — | Panel | Panel y correo |
| Cuentas por cobrar o pagar vencidas | — | — | Panel, correo y WhatsApp |
| Producto sin movimiento en X días | — | — | Panel |
| Fallas del sistema | — | — | Alerta automática al administrador y vigilancia |

Las claves de correo, WhatsApp Cloud API o push van en `.env`, nunca en el frontend.
