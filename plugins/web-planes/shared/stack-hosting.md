# Stack y hosting

## Las dos partes de una web
- **Vitrina:** lo que se ve. Son archivos que van tal cual al navegador.
- **Trastienda (backend):** un programa siempre encendido, con su base de datos. Guarda los pedidos, las reservas y los usuarios, envía correos y es lo que hace funcionar un panel.

**Regla:** si el alcance no necesita trastienda, **no se pone**. Sale más rápido, más barato de alojar y casi sin mantenimiento.
- Los formularios sin trastienda funcionan con un servicio gratuito como **Web3Forms**. Su clave de acceso es pública por diseño; aun así va en la configuración, no repetida por el código.
- Las reservas y los pedidos simples van por WhatsApp.

## Stack por plan

| Pieza | Básico | Profesional | Premium |
|---|---|---|---|
| Vitrina | HTML, CSS y JS | HTML, CSS y JS, o **Astro / React** si el proyecto lo pide | **Astro / React**, más GSAP, ScrollTrigger y Lenis |
| Trastienda de webs | Python + **Django** | Django | Django |
| Sistemas de gestión | **PHP** (núcleo de `base-sistemas-php/`) | PHP | PHP, o Django si es muy a medida |
| Base de datos | MySQL / MariaDB | MySQL / MariaDB | MySQL / MariaDB |

**Cómo elegir:**
- **Astro** para webs de mucho contenido: carga muy rápido y es buena para Google.
- **React** para partes muy interactivas (configuradores, filtros, carritos, dashboards), como "islas" dentro de la página. Con React se pueden usar componentes de **Cult UI** (`npx shadcn@latest add @cult-ui/<componente>`), adaptados al diseño aprobado.
- **HTML simple** si no hace falta ninguno de los dos.
- **PHP solo para sistemas de gestión** del tipo Panda Biker. Las webs con trastienda van en Django.

**Convenciones de Django:**
- `.env` con python-dotenv.
- MySQL con PyMySQL, y SQLite solo en local.
- Panel con `django.contrib.admin` personalizado.
- Precios siempre recalculados en el servidor.
- Tests de las vistas y de la lógica de dinero.

## Hosting

Lo paga el cliente, directo al proveedor y **a su nombre**. Se decide con él.

| Caso | Opción | Costo aproximado | Notas |
|---|---|---|---|
| Solo vitrina | Netlify, Vercel o Cloudflare Pages | Gratis | Solo se paga el dominio |
| Django | **PythonAnywhere (predeterminada)** | Unos 10 USD/mes (plan Developer; el de 5 USD fue reemplazado en 2026) | Precio fijo, MySQL incluido, sin servidor que administrar. El gratuito no admite dominio propio |
| Django | Hostinger VPS (KVM 1) | Unos 6,50 USD/mes con contrato de 24 meses (renueva a unos 12) | **Sube al renovar**; un VPS puede alojar las webs de varios clientes; el servidor hay que administrarlo (actualizaciones, seguridad, respaldos) |
| Django | Railway o Render | 5 a 15 USD/mes o más | Cobran según el uso; la base de datos se paga aparte |
| Sistemas PHP | Hostinger, plan compartido | Unos 3 USD/mes | PHP, MySQL, SSL y correo incluidos |
| Dominio .com | Cualquier registrador | 10 a 15 USD/año | A nombre del cliente |

- La cotización siempre dice el **costo anual estimado** de tener la web en línea (por ejemplo, con PythonAnywhere: "unos 130 a 135 USD al año entre hosting y dominio"; una web solo estática: solo el dominio), para que no haya sorpresas.
- **Los precios cambian:** confírmalos en la web del proveedor antes de cotizar y dile a Raúl de qué fecha son.
