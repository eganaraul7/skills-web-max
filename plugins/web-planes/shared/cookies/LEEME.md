# Gestor de cookies

1. **Copia** `cookies.js` y `cookies.css` al proyecto y **adapta los colores del CSS** al diseño (variables `--ck-*`).
2. **Carga los archivos** en todas las páginas:
   ```html
   <link rel="stylesheet" href="/static/css/cookies.css">
   <script>window.COOKIE_CONFIG = { politica: '/cookies/', categorias: ['analitica'] };</script>
   <script src="/static/js/cookies.js" defer></script>
   ```
   En `categorias` pon solo las que use la web. Si no hay ninguna (`[]`), sale un aviso informativo con un solo botón.
3. **Analítica y pixel**, siempre bloqueados hasta que haya consentimiento:
   ```html
   <script type="text/plain" data-cookie="analitica" src="https://www.googletagmanager.com/gtag/js?id=G-XXXX" async></script>
   <script type="text/plain" data-cookie="analitica">/* configuración de gtag */</script>
   ```
4. **En el pie de página**, siempre:
   ```html
   <a href="/cookies/" data-cookie-prefs>Preferencias de cookies</a>
   ```
5. **Si se añade o se quita una cookie,** sube `VERSION` en `cookies.js` para que se vuelva a preguntar.
6. **Comprueba con Playwright:**
   - el aviso aparece la primera vez;
   - "Rechazar" no carga ningún script opcional;
   - "Configurar" permite elegir por categoría;
   - el enlace del pie vuelve a abrir el panel.
