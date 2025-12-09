@echo off
echo ====================================
echo Schweizer Inmobiliaria - Setup
echo ====================================
echo.

echo [1/4] Copiando archivo de configuracion...
cd backend
copy .env.example .env
echo.

echo [2/4] Instalando dependencias PHP con Composer...
composer install
echo.

echo [3/4] Configuracion de base de datos
echo IMPORTANTE: Asegurate de haber creado la base de datos primero
echo.
set /p dbhost="Host de MySQL (por defecto: localhost): " || set dbhost=localhost
set /p dbname="Nombre de la base de datos: "
set /p dbuser="Usuario de MySQL: "
set /p dbpass="Password de MySQL: "
set /p adminuser="Usuario admin del panel (por defecto: admin): " || set adminuser=admin
set /p adminpass="Password del admin: "
echo.

echo [4/4] Actualizando archivo .env...
powershell -Command "(gc .env) -replace 'DB_HOST=localhost', 'DB_HOST=%dbhost%' | Out-File -encoding ASCII .env"
powershell -Command "(gc .env) -replace 'DB_NAME=schweizer_inmobiliaria', 'DB_NAME=%dbname%' | Out-File -encoding ASCII .env"
powershell -Command "(gc .env) -replace 'DB_USER=root', 'DB_USER=%dbuser%' | Out-File -encoding ASCII .env"
powershell -Command "(gc .env) -replace 'DB_PASS=', 'DB_PASS=%dbpass%' | Out-File -encoding ASCII .env"
powershell -Command "(gc .env) -replace 'ADMIN_USERNAME=admin', 'ADMIN_USERNAME=%adminuser%' | Out-File -encoding ASCII .env"
powershell -Command "(gc .env) -replace 'ADMIN_PASSWORD=cambiar_esto_en_produccion', 'ADMIN_PASSWORD=%adminpass%' | Out-File -encoding ASCII .env"
echo.

echo ====================================
echo Configuracion completada!
echo ====================================
echo.
echo IMPORTANTE: Ahora debes importar la base de datos:
echo   mysql -u %dbuser% -p %dbname% ^< database.sql
echo.
echo Luego inicia el servidor de desarrollo:
echo   php -S localhost:8000 -t public
echo.
pause
