@echo off
title Configuración de Totem - Innovation Day 2025
color 0A

echo.
echo  ██████╗ ██████╗ ███╗   ██╗███████╗██╗ ██████╗
echo ██╔════╝██╔═══██╗████╗  ██║██╔════╝██║██╔════╝
echo ██║     ██║   ██║██╔██╗ ██║█████╗  ██║██║  ███╗
echo ██║     ██║   ██║██║╚██╗██║██╔══╝  ██║██║   ██║
echo ╚██████╗╚██████╔╝██║ ╚████║██║     ██║╚██████╔╝
echo  ╚═════╝ ╚═════╝ ╚═╝  ╚═══╝╚═╝     ╚═╝ ╚═════╝
echo.
echo     CONFIGURADOR AUTOMATICO DE TOTEM
echo     Innovation Day 2025 - Pentafon
echo.
echo ================================================
echo.

REM Verificar si se ejecuta como administrador
net session >nul 2>&1
if %errorLevel% == 0 (
    echo [✓] Ejecutándose como Administrador
) else (
    echo [!] AVISO: Se recomienda ejecutar como Administrador
    echo     para configuraciones avanzadas del sistema
)

echo.
echo [1] Configurando directorio de perfil personalizado...
set TOTEM_PROFILE=%USERPROFILE%\TotemProfile
if not exist "%TOTEM_PROFILE%" (
    mkdir "%TOTEM_PROFILE%"
    echo [✓] Directorio creado: %TOTEM_PROFILE%
) else (
    echo [✓] Directorio ya existe: %TOTEM_PROFILE%
)

echo.
echo [2] Verificando archivos de audio...
if exist "public\audio\audio_1.mp3" (
    echo [✓] Audio principal encontrado
) else (
    echo [!] ERROR: No se encontró audio_1.mp3 en public\audio\
    echo     Asegúrate de tener los archivos de audio en la carpeta correcta
    pause
    exit /b 1
)

echo.
echo [3] Configurando navegador Chrome...
echo.
echo Opciones disponibles:
echo   1. Modo Kiosk (Pantalla completa, sin interfaz)
echo   2. Modo Ventana (Con interfaz, para pruebas)
echo   3. Modo Desarrollo (Con herramientas de desarrollo)
echo.
set /p MODE="Selecciona el modo (1-3): "

set CHROME_PATH=""
REM Buscar Chrome en ubicaciones comunes
for %%i in (
    "%ProgramFiles%\Google\Chrome\Application\chrome.exe"
    "%ProgramFiles(x86)%\Google\Chrome\Application\chrome.exe"
    "%LOCALAPPDATA%\Google\Chrome\Application\chrome.exe"
) do (
    if exist "%%i" set CHROME_PATH="%%i"
)

if %CHROME_PATH%=="" (
    echo [!] ERROR: No se encontró Google Chrome instalado
    echo     Instala Google Chrome desde: https://www.google.com/chrome/
    pause
    exit /b 1
)

echo [✓] Chrome encontrado en: %CHROME_PATH%

REM Configurar flags según el modo seleccionado
set CHROME_FLAGS=--user-data-dir="%TOTEM_PROFILE%" --autoplay-policy=no-user-gesture-required --disable-features=VizDisplayCompositor --disable-web-security --disable-background-timer-throttling --disable-backgrounding-occluded-windows --disable-renderer-backgrounding --disable-extensions --no-first-run --disable-popup-blocking --disable-translate --disable-sync --disable-default-apps

if "%MODE%"=="1" (
    echo [✓] Configurando modo Kiosk
    set CHROME_FLAGS=%CHROME_FLAGS% --kiosk --disable-pinch --overscroll-history-navigation=0
) else if "%MODE%"=="2" (
    echo [✓] Configurando modo Ventana
    set CHROME_FLAGS=%CHROME_FLAGS% --start-maximized
) else if "%MODE%"=="3" (
    echo [✓] Configurando modo Desarrollo
    set CHROME_FLAGS=%CHROME_FLAGS% --auto-open-devtools-for-tabs
) else (
    echo [!] Opción no válida, usando modo Kiosk por defecto
    set CHROME_FLAGS=%CHROME_FLAGS% --kiosk --disable-pinch --overscroll-history-navigation=0
)

echo.
echo [4] Configurando URL del totem...
set /p URL="Ingresa la URL del totem (por defecto: http://localhost:8000): "
if "%URL%"=="" set URL=http://localhost:8000

REM Verificar si la URL termina con /cliente/saludo
echo %URL% | find "/cliente/saludo" >nul
if errorlevel 1 (
    if "%URL:~-1%"=="/" (
        set URL=%URL%cliente/saludo
    ) else (
        set URL=%URL%/cliente/saludo
    )
)

echo [✓] URL configurada: %URL%

echo.
echo [5] Configurando sistema para totem...

REM Configurar plan de energía (requiere admin)
net session >nul 2>&1
if %errorLevel% == 0 (
    echo [✓] Configurando plan de energía de alto rendimiento...
    powercfg /setactive 8c5e7fda-e8bf-4a96-9a85-a6e23a8c635c >nul 2>&1

    REM Desactivar suspensión de pantalla
    powercfg /change monitor-timeout-ac 0 >nul 2>&1
    powercfg /change monitor-timeout-dc 0 >nul 2>&1

    REM Desactivar hibernación
    powercfg /change hibernate-timeout-ac 0 >nul 2>&1
    powercfg /change hibernate-timeout-dc 0 >nul 2>&1

    echo [✓] Sistema configurado para totem
) else (
    echo [!] Configuración de energía omitida (requiere admin)
)

echo.
echo [6] Creando acceso directo...
set SHORTCUT_PATH=%USERPROFILE%\Desktop\Totem_Innovation_Day.lnk
powershell -Command "$WshShell = New-Object -comObject WScript.Shell; $Shortcut = $WshShell.CreateShortcut('%SHORTCUT_PATH%'); $Shortcut.TargetPath = %CHROME_PATH%; $Shortcut.Arguments = '%CHROME_FLAGS% \"%URL%\"'; $Shortcut.WorkingDirectory = '%~dp0'; $Shortcut.IconLocation = %CHROME_PATH%; $Shortcut.Save()"

if exist "%SHORTCUT_PATH%" (
    echo [✓] Acceso directo creado en el escritorio
) else (
    echo [!] Error al crear acceso directo
)

echo.
echo [7] Configuración adicional...
echo.
echo Para optimizar el rendimiento del totem:
echo   • Cierra todas las aplicaciones innecesarias
echo   • Desactiva antivirus temporalmente si es necesario
echo   • Asegúrate de que el audio esté al 100%% y sin silencio
echo   • Usa una conexión de internet estable
echo.

echo ================================================
echo.
echo [✓] CONFIGURACIÓN COMPLETADA EXITOSAMENTE
echo.
echo El totem está listo para usar. Opciones:
echo.
echo   1. Iniciar totem ahora
echo   2. Mostrar comando para uso manual
echo   3. Salir
echo.
set /p OPTION="Selecciona una opción (1-3): "

if "%OPTION%"=="1" (
    echo.
    echo [✓] Iniciando totem...
    echo [i] Presiona Alt+F4 para cerrar el totem
    echo [i] En modo kiosk, presiona Ctrl+Alt+T para abrir Task Manager
    echo.
    timeout /t 3 /nobreak >nul
    start "" %CHROME_PATH% %CHROME_FLAGS% "%URL%"
) else if "%OPTION%"=="2" (
    echo.
    echo ================================================
    echo COMANDO MANUAL:
    echo.
    echo %CHROME_PATH% %CHROME_FLAGS% "%URL%"
    echo.
    echo ================================================
    echo.
    echo Puedes copiar este comando para usar manualmente
    pause
) else (
    echo.
    echo [✓] Configuración guardada. Usa el acceso directo del escritorio
    echo     para iniciar el totem cuando esté listo.
)

echo.
echo ================================================
echo     Innovation Day 2025 - Pentafon
echo      Configuración completada exitosamente
echo ================================================
echo.
pause
