#!/bin/bash

echo "===================================="
echo "Schweizer Inmobiliaria"
echo "Iniciando servidores de desarrollo"
echo "===================================="
echo ""

# Función para matar procesos al salir
cleanup() {
    echo ""
    echo "Deteniendo servidores..."
    kill $BACKEND_PID $FRONTEND_PID 2>/dev/null
    exit 0
}

trap cleanup INT TERM

# Iniciar backend en background
cd backend
php -S localhost:8000 -t public &
BACKEND_PID=$!
cd ..

sleep 2

# Iniciar frontend en background
npm run dev &
FRONTEND_PID=$!

echo ""
echo "===================================="
echo "Servidores iniciados!"
echo "===================================="
echo ""
echo "Frontend: http://localhost:5173"
echo "Backend:  http://localhost:8000"
echo "Admin:    http://localhost:5173/admin/login"
echo ""
echo "Presiona Ctrl+C para detener ambos servidores"
echo ""

# Esperar a que el usuario presione Ctrl+C
wait
