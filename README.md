# skills-web-max


Skills para desarrollo de páginas y sistemas básicos, profesionales y premium.
Tres skills de Claude Code para crear **landings, sitios web, e-commerce y sistemas de gestión** según el plan que vendió Raúl:

| Skill | Se llama con | Idea |
|---|---|---|
| `web-basico` | `/web-basico` | Bonito, cuidado y rápido de armar |
| `web-profesional` | `/web-profesional` | Identidad propia, más contenido, más funciones |
| `web-premium` | `/web-premium` | Dirección de arte completa; cada sección y cada frase con propósito |

Cada skill cubre las fases 5 a 8 del manual de trabajo: diseño en **Claude Design**, producción, optimización y revisión en pruebas. La entrega es igual en los tres planes y sale de las plantillas de Raúl.

## Instalar

En cualquier computadora con Claude Code y acceso a este repo privado:

```powershell
claude plugin marketplace add eganaraul7/skills-web-max
```
```powershell
claude plugin install web-planes@skills-web-max
```

Para actualizar después de un cambio en el repo:

```powershell
claude plugin marketplace update skills-web-max
```

El plugin trae configurado el MCP de Playwright. Las demás skills que usa cada plan (Impeccable, Emil Kowalski, Taste, UI UX Pro Max, Unslop, Humanizer, Superpowers…) se instalan aparte: ver `plugins/web-planes/shared/herramientas.md`.

## Usar

En una sesión, escribe `/web-premium` (o el plan que toque) y pasa la carpeta del cliente. La skill lee el anexo de alcance firmado y el briefing, y construye **solo** lo que entra en ese plan.

## Estructura

```
.claude-plugin/marketplace.json
plugins/web-planes/
├── .claude-plugin/plugin.json
├── .mcp.json                     Playwright
├── skills/
│   ├── web-basico/SKILL.md
│   ├── web-profesional/SKILL.md
│   └── web-premium/SKILL.md
└── shared/                       lo común a los tres planes
    ├── flujo.md                  fases 5-8, Claude Design, Git, control de alcance
    ├── alcance-por-tipo.md       qué incluye cada plan en cada tipo de proyecto
    ├── piso-calidad.md           lista de comprobación obligatoria
    ├── seguridad.md              reglas obligatorias (SQL, contraseñas, login, .env, carga)
    ├── legal.md                  privacidad, términos, cookies, consentimiento, pie
    ├── stack-hosting.md          tecnología y hosting por caso
    ├── herramientas.md           skills por plan y cómo instalarlas
    ├── cms.md                    editor de contenido: cómo instalar y conectar cms-web-max
    ├── escritorio.md             versión .exe
    ├── cookies/                  gestor de cookies listo (JS + CSS)
    ├── plantillas/               textos legales y env.example
    ├── scripts/auditoria.py      auditoría de páginas, SEO y archivos
    └── base-sistemas-php/        núcleo seguro de Proyectos v2 para sistemas
```

## Mantener

Si cambia un precio o algo del alcance, se actualizan a la vez:
- `shared/alcance-por-tipo.md`;
- la guía de cotización (`Desarrollador web - WORK/Cotizaciones/Guia-de-cotizacion.md` y su PDF);
- `Plantillas/LEEME.md`.

Después se sube la versión nueva (subiendo `version` en `plugin.json`).

## CMS

- El CMS (`cms-web-max`) vive en su propio repositorio local. Para usar las skills con CMS en otra computadora, esa carpeta también tiene que estar allí.
