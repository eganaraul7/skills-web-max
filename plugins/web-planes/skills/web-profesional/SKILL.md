---
name: web-profesional
description: Construye un proyecto de Raúl vendido en el plan PROFESIONAL (landing, sitio web, e-commerce o sistema de gestión), desde el diseño en Claude Design hasta la revisión en pruebas. Identidad propia con un tono definido, más secciones e imágenes, animación moderada con scroll, contenido optimizado, SEO on-page y accesibilidad AA, y funciones del plan (carrito a WhatsApp, pago con comprobante, roles, reportes con filtros). Úsala cuando Raúl diga "plan profesional", "plan pro", "web-profesional" o pida un proyecto profesional para un cliente.
---

# Plan Profesional: "identidad propia y más contenido"

Una web o un sistema con personalidad: se define un **tono** (agresivo, suave, elegante, juvenil…) y todo el diseño responde a él. Lleva más secciones, más imágenes y una web optimizada.

Los archivos compartidos están en `../../shared/`, relativo a esta SKILL.md.

## Antes de empezar
1. Lee `shared/flujo.md` y sigue su paso 0.
2. Comprueba que el alcance dice **Profesional**. Si dice otro plan, detente.
3. Lee `shared/herramientas.md` y comprueba que tienes las del Básico y las del Profesional.

## Qué incluye este plan

| Aspecto | Nivel Profesional |
|---|---|
| Diseño | **A medida**, con un tono definido por escrito: 3 adjetivos y lo que la marca no es. Paleta y tipografía elegidas para el proyecto. Dirección y revisión con `impeccable` y `taste-skill`; `ui-ux-pro-max` y `unslop` como en el Básico |
| Propuestas en Claude Design | Hasta 3 |
| Animación | Moderada: textos que aparecen al hacer scroll, parallax ligero y hover trabajado (`emil-design-eng`, `animate`). Siempre con `prefers-reduced-motion` |
| Contenido | Más fotos y secciones. Imágenes en AVIF/WebP en varios tamaños con `srcset`; vídeos comprimidos con póster; nada pesa más de lo necesario |
| SEO | On-page completo (jerarquía de títulos, enlaces internos, textos con las palabras que busca el cliente) y analítica tras el consentimiento de cookies |
| Accesibilidad | AA, revisada con `design:accessibility-review` |
| Stack | HTML, CSS y JS, o **Astro / React** si el proyecto lo pide. Con React: `vercel-react-best-practices` y componentes de **Cult UI** adaptados al diseño. Django para la trastienda de webs y PHP para sistemas (ver `shared/stack-hosting.md`) |
| CMS | Solo si se compró como extra ($40 a $60). Ver `shared/cms.md` |
| Escritorio (.exe) | Solo si se compró como extra ($20) |

**Funciones por tipo:** las de la columna **Profesional** de `shared/alcance-por-tipo.md`, que incluye las del Básico. En resumen:
- **Landing:** hasta 8 secciones, formulario que llega al correo y analítica.
- **Sitio web:** galería o blog simple.
- **Web que vende:** catálogo corto de hasta 15 productos, con carrito a WhatsApp.
- **E-commerce:** WhatsApp, más pago manual con comprobante y pasarela si el negocio tiene cuenta; categorías, filtros, cupones, panel de pedidos, correos e inventario básico; 20 productos cargados por Raúl.
- **Sistema:** hasta 6 módulos (nivel Panda Biker), hasta 10 usuarios con roles, reglas de negocio, reportes en PDF por fechas, filtros, Excel, panel con totales y respaldo, y diseño estilizado con la identidad del cliente.
- **Reservas:** formulario que llega al correo o al WhatsApp.
- **Notificaciones:** las de la columna Profesional (panel y correo).

**No incluye:** dirección de arte completa, animaciones avanzadas (secciones fijas, horizontales, canvas, vídeo con scroll), checkout con varios métodos automatizados, importación desde Excel, dashboard tipo CRM, calendario de reservas con disponibilidad, alertas por WhatsApp ni vigilancia de caídas. Si lo piden, aplica el control de alcance.

## Proceso
1. **Diseño:** `shared/flujo.md`, fase 5. Antes de las propuestas, escribe la **ficha de tono** (3 adjetivos, referencias, paleta y tipografía, con su porqué) y que Raúl la confirme. Luego hasta 3 propuestas en Claude Design, con la aprobación por escrito.
2. **Producción:** fase 6, reproduciendo el diseño aprobado.
3. **Siempre:** `shared/seguridad.md`, `shared/legal.md`, `shared/cookies/` y `shared/piso-calidad.md`.
4. **Optimización:** fase 7, con PageSpeed móvil de 90 o más y las imágenes en AVIF/WebP con `srcset`.
5. **Revisión:**
   - todo lo del Básico;
   - pasada de `impeccable`;
   - revisión de accesibilidad AA;
   - SEO on-page (`searchfit-seo:on-page-seo`);
   - en los sistemas y tiendas, prueba de carga (`shared/seguridad.md`, punto 7).

   Luego la fase 8 con 3 rondas de revisión.

## Al terminar cada fase
Dile a Raúl qué quedó listo, qué se comprobó (con cifras: PageSpeed, auditoría, prueba de carga) y qué falta. No digas que algo funciona si no lo probaste.
