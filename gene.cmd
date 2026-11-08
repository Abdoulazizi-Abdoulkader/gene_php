@echo off
rem gene - lanceur pour l'invite de commandes Windows (cmd.exe).
rem Usage : gene            (assistant)
rem         gene generate --profile=boutique
rem         gene doctor
setlocal
set "GENE_HOME=%~dp0"

where php >nul 2>nul
if errorlevel 1 (
    echo [gene] PHP est introuvable. Installez PHP 8.3 ou plus et ajoutez son dossier a la variable PATH. 1>&2
    echo [gene] Voir docs\GUIDE.md, section "Installation sous Windows". 1>&2
    exit /b 1
)

if not exist "%GENE_HOME%vendor\autoload.php" (
    where composer >nul 2>nul
    if errorlevel 1 (
        echo [gene] Dependances absentes et Composer introuvable : installez Composer puis lancez "composer install" dans "%GENE_HOME%". 1>&2
        exit /b 1
    )
    choice /c ON /m "[gene] Dependances absentes. Lancer composer install maintenant"
    if errorlevel 2 exit /b 1
    pushd "%GENE_HOME%"
    call composer install --no-interaction
    if errorlevel 1 ( popd & exit /b 1 )
    popd
)

php "%GENE_HOME%bin\gene" %*
exit /b %ERRORLEVEL%
