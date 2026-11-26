# Piso de calidad (obligatorio en los tres planes)

El Básico también tiene que ser bueno. Esto no baja nunca, sea cual sea el plan.

## Lista de comprobación antes de cada envío al cliente

### Diseño y contenido
- [ ] Diseño cuidado y coherente: la misma escala de espacios, tipografías y colores en todas las pantallas.
- [ ] Reproduce el diseño aprobado en Claude Design.
- [ ] **Textos sin errores** de ortografía ni de gramática, en el tono del cliente. Pasa los textos por la skill `humanizer`: nada que suene a IA.
- [ ] Sin lorem ipsum, sin "Lorem", sin "TODO", sin secciones vacías.
- [ ] Sin imágenes rotas, de relleno ni que no correspondan al negocio. Toda imagen tiene `alt` con sentido, o `alt=""` si es decorativa.

### Pantallas
- [ ] Se ve perfecto en **celular (375 px), tablet (768 px) y computadora (1440 px)**.
- [ ] **Nada se sale por los lados**: `scrollWidth` igual a `clientWidth` en las tres medidas.
- [ ] Botones y enlaces de al menos 44 × 44 px en el celular.

### Funcionamiento
- [ ] Todos los formularios funcionan de punta a punta: se envían, llegan y muestran un mensaje claro de éxito o de error.
- [ ] Todos los enlaces llevan a donde deben, incluidos WhatsApp, el teléfono y el mapa.
- [ ] **Cero errores en la consola** del navegador y cero peticiones fallidas (404/500).
- [ ] Página 404 propia.

### Velocidad
- [ ] PageSpeed móvil de **90 o más**, medido, no supuesto.

### Seguridad y legal
- [ ] `seguridad.md` cumplido completo.
- [ ] `legal.md` cumplido: privacidad, términos, cookies con preferencias y consentimiento en los formularios.

### SEO mínimo
- [ ] `<title>` y descripción únicos por página.
- [ ] Un solo `<h1>` por página.
- [ ] Canonical.
- [ ] Etiquetas Open Graph con imagen.
- [ ] Favicon, `sitemap.xml` y `robots.txt`.

### Movimiento
- [ ] Toda animación respeta `prefers-reduced-motion`.

## Cómo se comprueba

1. **Script de auditoría:**
   ```powershell
   python <ruta-del-plugin>/shared/scripts/auditoria.py http://localhost:PUERTO
   ```
   Revisa las páginas del sitemap, robots, la 404, las metas SEO, el `<h1>`, el `alt` de las imágenes y que carguen todos los archivos que usa la página.
2. **Playwright** en 375, 768 y 1440 px:
   - captura de cada página;
   - mensajes de la consola;
   - prueba de que nada se sale de la pantalla;
   - envío real de cada formulario.
3. **PageSpeed:** Lighthouse, o PageSpeed Insights una vez que la web esté en el dominio de pruebas.

Si algo no se pudo comprobar (por ejemplo, PageSpeed sin dominio público), se le dice a Raúl, no se da por bueno.
