# Reorganiza: backend/ (Laravel + BD), frontend/ (UI), raiz = docs + accesos
$ErrorActionPreference = "Stop"
$root = Split-Path -Parent (Split-Path -Parent $MyInvocation.MyCommand.Path)
Set-Location $root

function Remove-JunctionIfExists([string]$Path) {
    if (Test-Path $Path) {
        $item = Get-Item -LiteralPath $Path -Force
        if ($item.Attributes -band [IO.FileAttributes]::ReparsePoint) {
            cmd /c rmdir "$Path"
        }
    }
}

Remove-JunctionIfExists "$root\backend\app"
Remove-JunctionIfExists "$root\backend\bootstrap"
Remove-JunctionIfExists "$root\backend\config"
Remove-JunctionIfExists "$root\backend\database"
Remove-JunctionIfExists "$root\backend\routes"
Remove-JunctionIfExists "$root\backend\storage"
Remove-JunctionIfExists "$root\frontend\views"
Remove-JunctionIfExists "$root\frontend\css"
Remove-JunctionIfExists "$root\frontend\js"
Remove-JunctionIfExists "$root\frontend\public"

$toBackend = @("app", "bootstrap", "config", "database", "routes", "storage", "tests", "vendor", "artisan", "composer.json", "composer.lock", "phpunit.xml", ".env", ".env.example")
foreach ($name in $toBackend) {
    $src = Join-Path $root $name
    if (Test-Path $src) {
        Move-Item -LiteralPath $src -Destination (Join-Path $root "backend") -Force
    }
}

$toFrontend = @("resources", "public", "package.json", "package-lock.json", "vite.config.js")
foreach ($name in $toFrontend) {
    $src = Join-Path $root $name
    if (Test-Path $src) {
        Move-Item -LiteralPath $src -Destination (Join-Path $root "frontend") -Force
    }
}

Write-Host "Reestructura de carpetas completada."
