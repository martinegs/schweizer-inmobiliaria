#!/bin/bash

# Mover archivos de dist/ a la raíz de public_html
echo "Moviendo archivos de dist/ a public_html..."

# Copiar todo de dist/ al directorio actual (public_html)
cp -r dist/* .

# Limpiar archivos innecesarios
echo "Limpiando archivos innecesarios..."
rm -rf src/
rm -rf node_modules/
rm -rf .git/
rm -f package.json
rm -f package-lock.json
rm -f vite.config.js
rm -f jsconfig.json
rm -f README.md
rm -f .gitignore
rm -f deploy.sh
rm -f .deployment

echo "Deployment completado!"
