@echo off
title Schweizer Inmobiliaria - Servidor de Desarrollo

echo ====================================
echo Schweizer Inmobiliaria
echo Iniciando servidores de desarrollo
echo ====================================
echo.

echo Presiona Ctrl+C para detener ambos servidores
echo.

start "Backend PHP" cmd /k "cd backend && php -S localhost:8000 -t public && echo Backend ejecutandose en http://localhost:8000"

timeout /t 2 /nobreak > nul

start "Frontend Vite" cmd /k "npm run dev"

echo.
echo ====================================
echo Servidores iniciados!
echo ====================================
echo.
echo Frontend: http://localhost:5173
echo Backend:  http://localhost:8000
echo Admin:    http://localhost:5173/admin/login
echo.
echo Cierra esta ventana cuando termines de trabajar
echo.
pause
