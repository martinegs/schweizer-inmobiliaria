#!/bin/bash

echo "===================================="
echo "Schweizer Inmobiliaria - Setup"
echo "===================================="
echo ""

echo "[1/4] Copiando archivo de configuración..."
cd backend
cp .env.example .env
echo ""

echo "[2/4] Instalando dependencias PHP con Composer..."
composer install
echo ""

echo "[3/4] Configuración de base de datos"
echo "IMPORTANTE: Asegúrate de haber creado la base de datos primero"
echo ""
read -p "Host de MySQL (por defecto: localhost): " dbhost
dbhost=${dbhost:-localhost}
read -p "Nombre de la base de datos: " dbname
read -p "Usuario de MySQL: " dbuser
read -sp "Password de MySQL: " dbpass
echo ""
read -p "Usuario admin del panel (por defecto: admin): " adminuser
adminuser=${adminuser:-admin}
read -sp "Password del admin: " adminpass
echo ""

echo "[4/4] Actualizando archivo .env..."
sed -i "s/DB_HOST=localhost/DB_HOST=$dbhost/" .env
sed -i "s/DB_NAME=schweizer_inmobiliaria/DB_NAME=$dbname/" .env
sed -i "s/DB_USER=root/DB_USER=$dbuser/" .env
sed -i "s/DB_PASS=/DB_PASS=$dbpass/" .env
sed -i "s/ADMIN_USERNAME=admin/ADMIN_USERNAME=$adminuser/" .env
sed -i "s/ADMIN_PASSWORD=cambiar_esto_en_produccion/ADMIN_PASSWORD=$adminpass/" .env
echo ""

echo "===================================="
echo "Configuración completada!"
echo "===================================="
echo ""
echo "IMPORTANTE: Ahora debes importar la base de datos:"
echo "  mysql -u $dbuser -p $dbname < database.sql"
echo ""
echo "Luego inicia el servidor de desarrollo:"
echo "  php -S localhost:8000 -t public"
echo ""
