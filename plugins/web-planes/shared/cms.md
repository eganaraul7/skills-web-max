# CMS: editor de contenido en tiempo real

Sirve para que el cliente cambie **textos, imágenes y vídeos** de su web de forma fácil y directa, viendo el resultado al momento, sin llamar a Raúl.

## Cuándo va

| Plan | CMS |
|---|---|
| Básico | Extra de $30 (solo si está en el alcance firmado) |
| Profesional | Extra de $40 a $60, según el contenido |
| Premium | Incluido siempre |
| Sistemas de gestión y e-commerce | Ya traen su panel: no se cobra aparte |

## Estado: PENDIENTE

El CMS oficial se está construyendo como proyecto aparte (`Documents/cms-web-max`), con:
- un panel con muy buen diseño, en modo oscuro;
- un editor visual en tiempo real;
- al menos 22 bloques;
- un SDK que funciona con cualquier tecnología.

**Hasta que Raúl confirme que está listo:**
- **No uses** el Studio viejo de `paginas_webs` como CMS de un cliente.
- **Si el proyecto incluye CMS, avisa a Raúl** al empezar, para decidir cómo se resuelve en ese proyecto.

## Siempre, lleve CMS o no

Deja el contenido **separado del diseño**, para poder conectar el CMS después sin rehacer la web:
- textos, imágenes y vídeos en un solo lugar (un archivo de datos, JSON o modelos de Django), no repartidos dentro del HTML;
- cada sección como un bloque con sus campos (título, texto, imagen, botón);
- imágenes con sus variantes generadas por un script, no a mano.

> Cuando el CMS esté listo, esta página se actualiza con la instalación, la conexión por tipo de stack (HTML, Django, Astro, React, PHP) y la lista de bloques.
