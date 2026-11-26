# Herramientas (skills y MCP) por plan

Cada plan usa las del anterior y suma las suyas.

| | Básico | Profesional | Premium |
|---|---|---|---|
| **Proceso** | `superpowers:brainstorming`, `superpowers:writing-plans`, `superpowers:verification-before-completion` | — | `superpowers:systematic-debugging` cuando algo falla |
| **Diseño** | `frontend-design`, `ui-ux-pro-max` (paletas, tipografías y estilos por rubro), `unslop` (que no parezca hecho por IA) | `impeccable` (dirección y revisión), `taste-skill:design-taste-frontend` | `impeccable` completo (dirección, revisión final y sistema de diseño), `apple-design` |
| **Textos** | `humanizer` (que los textos no suenen a IA) | — | — |
| **Movimiento** | — | `emil-design-eng`, `animate` (animación moderada) | `choreograph`, `animation-performance`, `gestures`, `review-animations`, `improve-animations` |
| **Componentes** | — | **Cult UI** y `vercel-react-best-practices` (solo si el proyecto usa React) | Lo mismo, con más componentes a medida |
| **Revisión** | `web-design-guidelines`, **Playwright** (celular, tablet y computadora) y `shared/scripts/auditoria.py` | `design:accessibility-review` y SEO on-page (`searchfit-seo:on-page-seo`) | `break-ui` (datos extremos), auditoría completa del sitio, datos estructurados (`searchfit-seo:schema-markup`) y velocidad medida antes y después |

Playwright y el script de auditoría van en los tres planes porque el piso de calidad hay que **comprobarlo**, no suponerlo.

## Paso de preparación (al empezar cada proyecto)

1. **Revisa qué skills hay disponibles** en la sesión (la lista de skills del sistema).
2. **Playwright ya viene con este plugin** (`.mcp.json`).
3. **Si falta alguna del plan, díselo a Raúl** con su origen y no sigas como si estuviera.

   | Skill | Origen |
   |---|---|
   | superpowers | Plugin oficial `superpowers` |
   | frontend-design | Plugin oficial `frontend-design` |
   | impeccable | `github.com/pbakaus/impeccable` |
   | emil-design-eng y las de animación | `github.com/emilkowalski/skills` |
   | taste-skill | `github.com/leonxlnx/taste-skill` |
   | ui-ux-pro-max | `github.com/nextlevelbuilder/ui-ux-pro-max-skill` |
   | unslop | `github.com/heydamir/unslop` |
   | humanizer | `github.com/blader/humanizer` |
   | vercel-react-best-practices y web-design-guidelines | `github.com/vercel-labs/agent-skills` |
   | Cult UI | Componentes React por proyecto, desde `cult-ui.com` (no es una skill) |

4. **Instalación:**
   - Si Raúl aprueba instalar una, clona el repo en una carpeta temporal, **léela antes de instalarla** y cópiala a `~/.claude/skills/<nombre>/`.
   - No ejecutes scripts de un repo sin haberlos leído.
