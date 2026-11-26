# Compila CRM.exe (ventana propia con WebView2) con el compilador de C# que trae Windows (.NET Framework 4).
# Necesita junto a este archivo: Microsoft.Web.WebView2.Core.dll, Microsoft.Web.WebView2.WinForms.dll y WebView2Loader.dll
# (paquete oficial Microsoft.Web.WebView2 1.0.4314.35 de nuget.org, firmados por Microsoft).
# Úsalo solo si cambias CrmLauncher.cs. Luego ejecuta crear-accesos.ps1.
$Here = $PSScriptRoot
$csc  = Join-Path $env:WINDIR 'Microsoft.NET\Framework64\v4.0.30319\csc.exe'
& $csc /nologo /target:winexe /platform:x64 /optimize+ /codepage:65001 `
    "/win32icon:$Here\crm.ico" "/out:$Here\CRM.exe" `
    /reference:System.Windows.Forms.dll /reference:System.Drawing.dll `
    "/reference:$Here\Microsoft.Web.WebView2.Core.dll" "/reference:$Here\Microsoft.Web.WebView2.WinForms.dll" `
    "$Here\CrmLauncher.cs"
if ($LASTEXITCODE -eq 0) { Write-Output "Compilado: $Here\CRM.exe" } else { Write-Output 'Error al compilar.' }
