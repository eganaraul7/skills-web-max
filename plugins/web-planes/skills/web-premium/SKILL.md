---
name: web-premium
description: Construye un proyecto de Raúl vendido en el plan PREMIUM (landing, sitio web, e-commerce o sistema de gestión), desde el diseño en Claude Design hasta la revisión en pruebas. Dirección de arte completa donde cada sección y cada frase tienen un propósito, animación avanzada (GSAP, ScrollTrigger, Lenis, transiciones entre páginas), fotos y vídeo curados y recursos a medida, SEO y accesibilidad a fondo, velocidad medida, CMS y versión de escritorio incluidos, y las funciones más completas (checkout con varios métodos, dashboard tipo CRM, reservas con calendario). Úsala cuando Raúl diga "plan premium", "web-premium" o pida un proyecto premium para un cliente.
---

# Plan Premium: "dirección de arte completa, cada detalle con propósito"

El nivel de NOX, KAIRO y BRUMA. Antes de diseñar se estudian a fondo la marca, el logo, el público y las referencias. Cada sección y cada frase tienen un propósito, y nada parece genérico ni hecho por IA.

Los archivos compartidos están en `../../shared/`, relativo a esta SKILL.md.

## Antes de empezar
1. Lee `shared/flujo.md` y sigue su paso 0.
2. Comprueba que el alcance dice **Premium**. Si dice otro plan, detente.
3. Lee `shared/herramientas.md` y comprueba que tienes **todas** las herramientas, las de los tres planes.

## Qué incluye este plan

| Aspecto | Nivel Premium |
|---|---|
| Diseño | **Exclusivo.** Estudio de la marca y del público, 2 o 3 referencias de calidad estudiadas en el navegador (se toman como listón, nunca se copian), concepto escrito, sistema de diseño propio y propósito definido para cada sección y cada frase. `impeccable` completo, `taste-skill`, `apple-design`, `ui-ux-pro-max` y `unslop` |
| Propuestas en Claude Design | Hasta 3 |
| Animación | **Avanzada:** GSAP con ScrollTrigger y SplitText, Lenis, secciones fijas y horizontales, revelados con clip-path, transiciones entre páginas, canvas o vídeo controlado por el scroll y microinteracciones. Coreografía con `choreograph`, rendimiento con `animation-performance`, revisión con `review-animations`. Siempre con `prefers-reduced-motion` y una versión estática digna |
| Contenido | Fotos y vídeo **seleccionados uno a uno** y fieles al negocio; recortes y recursos hechos a medida; AVIF/WebP en 3 tamaños con LQIP; vídeo recodificado con ffmpeg. Optimizado al máximo |
| SEO | El más completo: on-page, datos estructurados (schema.org), sitemap, SEO de cada producto y velocidad **medida antes y después con un informe corto** |
| Accesibilidad | AA revisada a fondo: teclado, lector de pantalla, contraste y movimiento reducido |
| Velocidad | 90 o más con las animaciones y el vídeo. Cuesta más trabajo y es parte de lo que se cobra |
| Stack | **Astro / React** en la vitrina (con Cult UI y `vercel-react-best-practices` cuando haya React), Django para la trastienda y PHP o Django para sistemas muy a medida (ver `shared/stack-hosting.md`) |
| CMS | **Incluido.** Ver `shared/cms.md`; mientras esté pendiente, avisa a Raúl al empezar |
| Escritorio (.exe) | **Incluido** en los sistemas, entregado con el icono en el escritorio (`shared/escritorio.md`) |

**Funciones por tipo:** las de la columna **Premium** de `shared/alcance-por-tipo.md`, que incluye las de los otros dos planes. En resumen:
- **Landing:** hasta 12 secciones, con integraciones (agenda, pixel, CRM).
- **Web que vende:** catálogo corto de hasta 30 productos, con WhatsApp o checkout con comprobante y aviso por correo.
- **E-commerce:** todos los métodos de pago con automatización, inventario sincronizado, reportes de ventas, envíos, SEO de cada producto e **importación desde Excel con fotos**; de 30 a 40 productos cargados por Raúl.
- **Sistema:** más de 6 módulos a medida, usuarios sin límite con permisos, **dashboard tipo CRM**, historial de cambios, facturación, importación desde Excel, respaldos automáticos y diseño exclusivo con modo oscuro.
- **Reservas:** calendario con disponibilidad, panel y notificaciones push al dashboard y al WhatsApp.
- **Notificaciones:** todas, incluida la alerta automática de fallas y la vigilancia de caídas.

Aun en Premium, una landing o un sitio **no se convierte en e-commerce** (ver `shared/alcance-por-tipo.md`). Si lo piden, aplica el control de alcance.

## Proceso
1. **Estudio y dirección:**
   - revisa las referencias en el navegador;
   - escribe el concepto: idea, tono, paleta, tipografía, formas y movimiento, con el propósito de cada sección;
   - Raúl lo confirma.
2. **Diseño:** `shared/flujo.md`, fase 5. Hasta 3 propuestas en Claude Design, en escritorio y móvil, que muestren también la intención del movimiento. Aprobación por escrito.
3. **Medición inicial:** si el cliente ya tiene una web, mide su velocidad y su SEO antes de empezar, para el informe de antes y después.
4. **Producción:** fase 6, reproduciendo el diseño aprobado al detalle.
5. **Siempre:** `shared/seguridad.md` (con la prueba de carga), `shared/legal.md`, `shared/cookies/` y `shared/piso-calidad.md`.
6. **Optimización:** fase 7. PageSpeed de 90 o más con las animaciones; nada de saltos de diseño (CLS) por cargas tardías.
7. **Revisión:**
   - todo lo del Profesional;
   - pasada final de `impeccable`;
   - `break-ui` con datos extremos;
   - `review-animations`;
   - auditoría completa del sitio;
   - datos estructurados;
   - Playwright en las tres medidas, también con el movimiento reducido;
   - **informe de velocidad antes y después**.

   Luego la fase 8 con 4 rondas de revisión.

## Al terminar cada fase
Dile a Raúl qué quedó listo, qué se comprobó (con cifras) y qué falta. No digas que algo funciona si no lo probaste.
