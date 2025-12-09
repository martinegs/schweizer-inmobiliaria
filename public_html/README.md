# Estructura de Producción

Esta carpeta contiene la estructura lista para desplegar en el servidor.

## Contenido

- `index.html` - Aplicación Vue compilada
- `assets/` - Archivos estáticos (JS, CSS, imágenes)
- `.htaccess` - Configuración para Vue Router
- `api/` - Backend PHP con Slim Framework

## Despliegue en Hostinger

### 1. Clonar el repositorio en el servidor

```bash
ssh usuario@tudominio.com
cd ~
git clone https://github.com/martinegs/schweizer-inmobiliaria.git
```

### 2. Copiar archivos a public_html

```bash
# Respaldar public_html actual (si existe)
mv public_html public_html.backup

# Copiar la carpeta public_html del repo
cp -r schweizer-inmobiliaria/public_html .
```

### 3. Configurar el backend

```bash
cd public_html/api

# Crear archivo .env
cp .env.example .env
nano .env
```

Configurar las variables:
```env
DB_HOST=193.203.175.60
DB_NAME=u941565543_schweizer
DB_USER=u941565543_admin
DB_PASS=tu_contraseña
DB_CHARSET=utf8mb4
```

### 4. Instalar dependencias

```bash
composer install --no-dev --optimize-autoloader
```

### 5. Configurar permisos

```bash
chmod 755 public/uploads
chmod 644 .env
cd ..
chmod 755 api
```

### 6. Verificar

Visita: `https://tudominio.com`

## Actualizar el sitio

Cuando hagas cambios, solo necesitas:

```bash
cd ~/schweizer-inmobiliaria
git pull origin main
cp -r public_html/* ~/public_html/
```

Si actualizas el frontend, recompila localmente:
```bash
npm run build
```

Y haz commit + push de los nuevos archivos en `dist/`.
