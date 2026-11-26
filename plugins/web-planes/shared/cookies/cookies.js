/* Gestor de cookies sin dependencias (web-planes).
 *
 * - Aviso con Aceptar todas / Rechazar / Configurar.
 * - Categorías: necesarias (siempre), analitica y marketing (apagadas por defecto).
 * - Los scripts opcionales se escriben así y solo se activan con consentimiento:
 *     <script type="text/plain" data-cookie="analitica" src="..."></script>
 * - Cualquier enlace con [data-cookie-prefs] reabre el panel ("Preferencias de cookies").
 * - Guarda la elección en localStorage con versión y fecha. Si cambias la lista
 *   de cookies, sube VERSION para volver a preguntar.
 * Configuración opcional: <script>window.COOKIE_CONFIG = { politica: '/cookies/', categorias: ['analitica'] }</script>
 */
(() => {
  const VERSION = 1;
  const KEY = 'cookie_prefs';
  const cfg = Object.assign({ politica: '/cookies/', categorias: ['analitica', 'marketing'] }, window.COOKIE_CONFIG || {});
  const NOMBRES = {
    analitica: ['Analítica', 'Estadísticas anónimas de visitas para mejorar la web.'],
    marketing: ['Marketing', 'Medición y personalización de anuncios.'],
  };

  const read = () => {
    try {
      const v = JSON.parse(localStorage.getItem(KEY));
      return v && v.version === VERSION ? v : null;
    } catch { return null; }
  };

  const activate = (prefs) => {
    document.querySelectorAll('script[type="text/plain"][data-cookie]').forEach((old) => {
      if (!prefs[old.dataset.cookie] || old.dataset.activado) return;
      const s = document.createElement('script');
      [...old.attributes].forEach((at) => { if (at.name !== 'type') s.setAttribute(at.name, at.value); });
      s.text = old.text;
      old.dataset.activado = '1';
      old.after(s);
    });
    document.dispatchEvent(new CustomEvent('cookies:cambio', { detail: prefs }));
  };

  const save = (prefs) => {
    const before = read() || {};
    const data = { version: VERSION, fecha: new Date().toISOString(), ...prefs };
    localStorage.setItem(KEY, JSON.stringify(data));
    // Si se retiró un permiso que ya estaba activo, recargar para que esos scripts dejen de correr.
    if (cfg.categorias.some((c) => before[c] && !data[c])) { location.reload(); return data; }
    activate(data);
    return data;
  };

  let box;
  const close = () => { box?.remove(); box = null; };

  const open = (detalle = false) => {
    close();
    const prev = read() || {};
    box = document.createElement('div');
    box.className = 'ck';
    box.setAttribute('role', 'dialog');
    box.setAttribute('aria-modal', 'false');
    box.setAttribute('aria-labelledby', 'ck-t');
    const filas = cfg.categorias.map((c) => `
      <label class="ck__row"><span><b>${NOMBRES[c][0]}</b><small>${NOMBRES[c][1]}</small></span>
      <input type="checkbox" name="${c}" ${prev[c] ? 'checked' : ''}></label>`).join('');
    box.innerHTML = `
      <p id="ck-t" class="ck__t">Usamos cookies</p>
      <p class="ck__p">Las necesarias hacen que la web funcione. ${cfg.categorias.length ? 'Las demás solo se activan si las aceptas.' : ''} <a href="${cfg.politica}">Política de cookies</a></p>
      <form class="ck__cfg" ${detalle ? '' : 'hidden'}>
        <label class="ck__row"><span><b>Necesarias</b><small>Sesión, carrito y tus preferencias.</small></span>
        <input type="checkbox" checked disabled></label>${filas}
      </form>
      <div class="ck__btns">
        ${cfg.categorias.length ? `<button type="button" data-a="rechazar">Rechazar</button>
        <button type="button" data-a="config">${detalle ? 'Guardar selección' : 'Configurar'}</button>
        <button type="button" data-a="aceptar" class="ck__ok">Aceptar todas</button>`
        : '<button type="button" data-a="aceptar" class="ck__ok">Entendido</button>'}
      </div>`;
    document.body.append(box);
    const form = box.querySelector('form');
    box.addEventListener('click', (e) => {
      const a = e.target.closest('[data-a]')?.dataset.a;
      if (!a) return;
      const all = (v) => Object.fromEntries(cfg.categorias.map((c) => [c, v]));
      if (a === 'aceptar') { save(all(true)); close(); }
      if (a === 'rechazar') { save(all(false)); close(); }
      if (a === 'config') {
        if (form.hidden) { form.hidden = false; e.target.textContent = 'Guardar selección'; form.querySelector('input:not([disabled])')?.focus(); return; }
        save(Object.fromEntries(cfg.categorias.map((c) => [c, form.elements[c].checked]))); close();
      }
    });
    box.querySelector('button')?.focus({ preventScroll: true });
  };

  document.addEventListener('click', (e) => {
    if (e.target.closest('[data-cookie-prefs]')) { e.preventDefault(); open(true); }
  });

  const prefs = read();
  const start = () => (prefs ? activate(prefs) : open(false));
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', start); else start();
  window.CookiePrefs = { open: () => open(true), get: read };
})();
