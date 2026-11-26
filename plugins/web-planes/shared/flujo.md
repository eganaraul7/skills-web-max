# Flujo de trabajo (común a los tres planes)

Cubre las fases 5 a 8 del manual de Raúl: **diseño, producción, optimización y revisión en pruebas**. La entrega (fase 9) y el mantenimiento (fase 10) no están aquí: son iguales en los tres planes y salen de las plantillas de Raúl.

## 0. Antes de empezar: comprobaciones

1. **Carpeta del cliente.** Pide la ruta. Normalmente es `Desarrollador web - WORK/Info Clientes/<cliente>/` o `CLIENTES/2-activos/<cliente>/`.
2. Lee, en este orden:
   - `03-contrato/03-anexo-alcance-cronograma-pagos` (el alcance firmado);
   - el briefing (`04-briefing/`);
   - `02-propuesta/` si hace falta contexto.
3. **El anexo de alcance manda.** Saca de él:
   - tipo de proyecto;
   - plan;
   - número de secciones, páginas, módulos o productos;
   - extras comprados (CMS, escritorio, páginas o módulos extra).
4. **Puertas del manual.** Sin 50 % de anticipo confirmado no hay producción. Si no consta, pregúntale a Raúl antes de seguir.
5. **Plan correcto.** Si el alcance dice otro plan distinto al de esta skill, detente y avisa.
6. Resume en 5 o 6 líneas lo que vas a construir y lo que **no** incluye. Raúl confirma.

## 1. Fase 5 · Diseño en Claude Design

Claude Design reemplaza a Figma y Stitch.

1. **Dirección:** con el briefing, define el tono, la paleta, la tipografía y la estructura de secciones o pantallas. La profundidad depende del plan (ver la SKILL.md de cada uno).
2. **Propuestas en Claude Design:**

   | Plan | Propuestas |
   |---|---|
   | Básico | Hasta 2 |
   | Profesional | Hasta 3 |
   | Premium | Hasta 3 |

   - Cada propuesta lleva escritorio y móvil, con contenido real del cliente (nada de lorem ipsum).
   - Raúl se las pasa al cliente.
3. **Rondas:** el cliente puede pedir cambios. Cada lista de cambios cuenta como una ronda. Una propuesta más allá del límite es un extra (20 % del precio): avisa a Raúl.
4. **Aprobación por escrito** (versión y fecha) = puerta. Sin ella no se pasa al código. Después de aprobar, cualquier cambio es un cambio de alcance y se cotiza.
5. **Traer el diseño al código:**
   - usa la exportación o el traspaso de Claude Design a Claude Code;
   - guarda una copia en `05-diseno/` del cliente.
6. **Al construir se reproduce el diseño aprobado tal cual:** colores, tipografía, espaciados, orden de secciones y textos. No se rediseña en el código. Si algo del diseño no se puede hacer bien (por rendimiento o accesibilidad), se avisa a Raúl antes de cambiarlo.

## 2. Fase 6 · Producción

1. **Plan exacto de archivos → Raúl confirma → se ejecuta** (regla del manual).
2. **Git desde el día 1:**
   ```
   git init
   git branch -M main
   ```
   - Primer commit: `chore: estructura inicial del proyecto`.
   - Ramas `feat/xxx`, con merge a `main`.
   - Prefijos: `feat`, `fix`, `style`, `perf`, `docs` y `chore`.
   - Commits pequeños.
3. **Repo privado en GitHub solo cuando Raúl lo autorice.** Nunca hagas push ni publiques sin su permiso.
4. **Stack y hosting** según `stack-hosting.md`.
5. **Aplica siempre** `seguridad.md`, `legal.md` y `piso-calidad.md`.
6. **Funciones:** solo las que el plan permite según `alcance-por-tipo.md`.
7. **Contenido:**
   - textos y fotos del cliente;
   - si faltan, fotos de bancos libres que correspondan al negocio, con su crédito;
   - nunca imágenes aleatorias ni de relleno.
8. **Avance semanal:** resume a Raúl qué está listo, qué falta y en qué ronda va.

## 3. Fase 7 · Optimización

- **Imágenes:** AVIF/WebP en varios tamaños, carga diferida, ancho y alto fijados (en el Básico al menos WebP y carga diferida).
- **JS y CSS:** sin código muerto y sin librerías que no se usan.
- **Fuentes:** autoalojadas, solo los pesos usados, `font-display: swap`.
- **Velocidad:** PageSpeed móvil de 90 o más. Mídelo y anota la cifra.
- **SEO y accesibilidad:** según el nivel del plan.
- Ejecuta `scripts/auditoria.py` y corrige todo lo que marque.

## 4. Fase 8 · Revisión en pruebas

1. **Despliegue de pruebas:** Netlify o Vercel para la vitrina; PythonAnywhere u otro hosting para la trastienda, en un dominio temporal.
2. **Guía de revisión para el cliente:** qué revisar y cómo mandar **una sola lista** de cambios por ronda.

   | Plan | Rondas de revisión de la web |
   |---|---|
   | Básico | 2 |
   | Profesional | 3 |
   | Premium | 4 |

3. **Pasada completa de QA** (`piso-calidad.md`) antes de cada envío al cliente.
4. **Cierre de la fase:** tag `v1.0.0-rc` y esperar el "Apruebo publicar la web".

## Control de alcance (siempre)

- **Si se pide algo que el plan no incluye, no se construye.** Responde:

  > Esto no está en el plan [X]. Corresponde al plan [Y] o a un extra ([precio de la guía]). Se cotiza aparte.

- **Una landing o un sitio que empieza a ser tienda** (pasarela automática, cuentas, cupones, inventario, envíos, filtros, muchos productos o ventas diarias):

  > Esto ya es un e-commerce y se cotiza como tal.

- **Precios:** nunca los inventes. Usa `Desarrollador web - WORK/Cotizaciones/Guia-de-cotizacion.md`. Si algo no está ahí, pregunta a Raúl.

## Reglas que no se negocian

- Nada se sube a GitHub ni se publica sin autorización de Raúl.
- No simular instalaciones, pruebas ni resultados. Si algo no se pudo comprobar, se dice.
- Sin imágenes rotas, placeholders ni secciones vacías.
- No inventar datos del cliente, testimonios, precios ni términos legales que no estén en las plantillas.
