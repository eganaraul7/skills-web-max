# Crea los accesos «CRM» en el Escritorio y en el menú Inicio, apuntando a CRM.exe (con el logo «re»).
# Ejecútalo de nuevo si mueves la carpeta del CRM.
$Here   = $PSScriptRoot
$Exe    = Join-Path $Here 'CRM.exe'
$Shell  = New-Object -ComObject WScript.Shell
$places = @([Environment]::GetFolderPath('Desktop'), [Environment]::GetFolderPath('Programs'))
foreach ($dir in $places) {
    $lnk = $Shell.CreateShortcut((Join-Path $dir 'CRM.lnk'))
    $lnk.TargetPath       = $Exe
    $lnk.Arguments        = ''
    $lnk.WorkingDirectory = $Here
    $lnk.IconLocation     = "$Exe,0"
    $lnk.Description      = 'CRM de Raúl Egaña: clientes, embudo, horas y cobros'
    $lnk.Save()
    Write-Output "Acceso creado: $(Join-Path $dir 'CRM.lnk')"
}
