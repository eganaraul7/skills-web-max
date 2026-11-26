---
name: web-basico
description: Construye un proyecto de Raúl vendido en el plan BÁSICO (landing, sitio web, e-commerce o sistema de gestión), desde el diseño en Claude Design hasta la revisión en pruebas. Diseño bonito y cuidado sobre una estructura base con el logo, los colores y la tipografía del cliente; funciones mínimas del plan; seguridad, lo legal y el piso de calidad completos. Úsala cuando Raúl diga "plan básico", "web-basico" o pida hacer un proyecto básico para un cliente.
---

# Plan Básico: "bonito, cuidado y rápido de armar"

Una web o un sistema bien hecho sobre una estructura base. Se ve bonito y profesional, pero sin exclusividad.

Los archivos compartidos están en `../../shared/`, relativo a esta SKILL.md. Léelos cuando el paso lo indique.

## Antes de empezar
1. Lee `shared/flujo.md` y sigue su paso 0: carpeta del cliente, anexo de alcance, anticipo y resumen confirmado por Raúl.
2. Comprueba que el alcance dice **Básico**. Si dice otro plan, detente.
3. Lee `shared/herramientas.md` y comprueba que tienes las del Básico.

## Qué incluye este plan

| Aspecto | Nivel Básico |
|---|---|
| Diseño | Estructura base limpia con el logo, los colores y la tipografía del cliente. Bonito y coherente, sin dirección de arte. Usa `ui-ux-pro-max` para elegir una paleta y una pareja tipográfica adecuadas al rubro, y `unslop` para que no parezca hecho por IA |
| Propuestas en Claude Design | Hasta 2 |
| Animación | Solo transiciones suaves en CSS: hover, foco y aparición simple. Nada de scroll avanzado ni librerías de animación |
| Contenido | Fotos de bancos libres acordes al negocio, en WebP con carga diferida. Textos pasados por `humanizer` |
| SEO | Básico: título y descripción, Open Graph, sitemap, robots y un `<h1>` por página |
| Accesibilidad | De base: contraste legible, `alt`, foco visible y etiquetas en los formularios |
| Stack | HTML, CSS y JS. Django solo si es e-commerce; PHP (`shared/base-sistemas-php/`) si es un sistema |
| CMS | Solo si se compró como extra ($30). Ver `shared/cms.md` |
| Escritorio (.exe) | Solo si se compró como extra ($20). Ver `shared/escritorio.md` |

**Funciones por tipo:** las de la columna **Básico** de `shared/alcance-por-tipo.md`. En resumen:
- **Landing:** hasta 5 secciones, formulario y WhatsApp.
- **Sitio web:** 5 páginas, formulario, WhatsApp y mapa.
- **Web que vende:** botón de WhatsApp con el mensaje ya escrito, sin carrito.
- **E-commerce:** carrito que envía el pedido a WhatsApp con la lista y el total; 10 productos cargados por Raúl.
- **Sistema:** inventario, ventas, usuarios y reportes simples sin filtros; 1 administrador y 2 empleados.
- **Reservas:** botón de WhatsApp.

**No incluye:** backend para reservas, carrito en una web que no es tienda, pasarela, comprobante de pago, roles, filtros en reportes, animaciones con scroll ni CMS (salvo que se haya comprado). Si lo piden, aplica el control de alcance de `shared/flujo.md`.

## Proceso
1. **Diseño:** `shared/flujo.md`, fase 5. Hasta 2 propuestas en Claude Design, aprobación por escrito y traspaso al código.
2. **Producción:** fase 6. Plan de archivos, confirmación de Raúl, Git, y construir reproduciendo el diseño aprobado.
3. **Siempre:** `shared/seguridad.md` (completo, también en el Básico), `shared/legal.md` (privacidad, términos, cookies con preferencias, consentimiento en los formularios y pie del negocio) y la implementación de `shared/cookies/`.
4. **Optimización:** fase 7, con PageSpeed móvil de 90 o más.
5. **Revisión:** `shared/piso-calidad.md` completo, con `shared/scripts/auditoria.py`, Playwright en 375, 768 y 1440 px y `web-design-guidelines`. Luego la fase 8 con 2 rondas de revisión.

## Al terminar cada fase
Dile a Raúl qué quedó listo, qué se comprobó (con cifras: PageSpeed, auditoría, consola) y qué falta. No digas que algo funciona si no lo probaste.
