# Versión de escritorio (.exe)

Un programa pequeño con el icono del cliente que abre el sistema en **su propia ventana**, sin la barra del navegador. Usa WebView2, el motor de Edge, que ya viene en Windows 10 y 11.

| Plan | Precio |
|---|---|
| Básico | Extra de $20 |
| Profesional | Extra de $20 |
| Premium | Incluida, entregada con el icono en el escritorio |

## Modo predeterminado: en línea

El sistema vive en el hosting y el `.exe` funciona como un acceso directo que abre su dirección.
- Funciona desde cualquier PC.
- Los datos quedan en el servidor, con sus respaldos.

**Modo local** (solo si Raúl lo pide): el sistema vive en la PC del cliente, con XAMPP.
- Funciona sin internet, pero solo en esa PC, y los respaldos dependen de ella.
- Es lo que hace hoy `CrmLauncher.cs`: enciende MySQL y el servidor PHP al abrirse.

## Archivos de referencia

En esta carpeta:
- `CrmLauncher.cs`: el lanzador del CRM Freelancers, en modo local, con una sola instancia, registro de errores, apertura de enlaces externos en el navegador e icono.
- `build.ps1`: compila con el `csc.exe` que trae Windows (.NET Framework 4); no hace falta instalar nada.
- `crear-accesos.ps1`: crea los accesos directos en el escritorio y en el menú Inicio.

## Cómo adaptarlo a un cliente (modo en línea)

1. Copia los tres archivos a `desktop/` del proyecto.
2. Renombra la clase, el nombre de la ventana, el mutex y la carpeta de datos con el nombre del cliente.
3. En modo en línea:
   - quita `StartServices()` y las constantes de XAMPP;
   - haz que `Url` devuelva la dirección pública del sistema (HTTPS);
   - si no hay internet, muestra un mensaje claro: "Sin conexión. Revisa tu internet y vuelve a abrir".
4. **Enlaces externos** (WhatsApp, correo, otros dominios): se abren en el navegador, no dentro de la ventana. El lanzador ya lo hace.
5. **Icono:** genera un `.ico` desde el logo del cliente, con los tamaños 16, 32, 48 y 256.
6. **Las DLL de WebView2 no van en el repositorio de skills:**
   - `Microsoft.Web.WebView2.Core.dll`;
   - `Microsoft.Web.WebView2.WinForms.dll`;
   - `WebView2Loader.dll`.

   Se toman del paquete oficial `Microsoft.Web.WebView2` de nuget.org, firmado por Microsoft. Pídele permiso a Raúl antes de descargarlo e indícale el nombre, el origen y el tamaño. También se pueden copiar de `Proyectos v2/crm-freelancers/desktop/`, que ya las tiene.
7. Compila con `build.ps1`, prueba el `.exe` y entrégalo junto con `crear-accesos.ps1`.
