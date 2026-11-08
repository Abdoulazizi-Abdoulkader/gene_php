<#
.SYNOPSIS
    gene - lanceur pour PowerShell (Windows, ou pwsh sous Linux/macOS).
.EXAMPLE
    .\gene.ps1                      # assistant
    .\gene.ps1 generate --profile=boutique
    .\gene.ps1 doctor
.NOTES
    Si PowerShell refuse d'exécuter le script : Unblock-File .\gene.ps1
    ou, pour la session courante : Set-ExecutionPolicy -Scope Process -ExecutionPolicy Bypass
#>
$ErrorActionPreference = 'Stop'
$geneHome = $PSScriptRoot

$php = Get-Command php -ErrorAction SilentlyContinue
if (-not $php) {
    [Console]::Error.WriteLine('[gene] PHP est introuvable. Installez PHP 8.3 ou plus et ajoutez son dossier a la variable PATH.')
    [Console]::Error.WriteLine('[gene] Voir docs/GUIDE.md, section "Installation sous Windows".')
    exit 1
}

if (-not (Test-Path (Join-Path $geneHome 'vendor/autoload.php'))) {
    $composer = Get-Command composer -ErrorAction SilentlyContinue
    if (-not $composer) {
        [Console]::Error.WriteLine("[gene] Dependances absentes et Composer introuvable : installez Composer puis lancez 'composer install' dans $geneHome.")
        exit 1
    }
    $answer = Read-Host '[gene] Dependances absentes. Lancer composer install maintenant ? (o/n)'
    if ($answer -notmatch '^(o|oui|y|yes)$') { exit 1 }
    Push-Location $geneHome
    try {
        & composer install --no-interaction
        if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }
    } finally {
        Pop-Location
    }
}

& $php.Source (Join-Path $geneHome 'bin/gene') @args
exit $LASTEXITCODE
