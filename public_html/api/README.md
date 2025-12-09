# Schweizer Inmobiliaria - Backend API

Backend API RESTful para el panel de administración de Schweizer Inmobiliaria.

## Tecnologías
- PHP 7.4+
- Slim Framework 4
- MySQL
- PDO

## Instalación Local

1. Instalar dependencias:
```bash
cd backend
composer install
```

2. Configurar base de datos:
```bash
# Crear base de datos
mysql -u root -p < database.sql
```

3. Configurar variables de entorno:
```bash
cp .env.example .env
# Editar .env con tus credenciales
```

4. Iniciar servidor de desarrollo:
```bash
php -S localhost:8000 -t public
```

## Instalación en Hostinger

1. Subir todos los archivos de `backend/` a tu hosting

2. Crear base de datos MySQL en cPanel y ejecutar `database.sql`

3. Copiar `.env.example` a `.env` y configurar:
```env
DB_HOST=localhost
DB_NAME=tu_base_de_datos
DB_USER=tu_usuario
DB_PASS=tu_contraseña

ADMIN_USERNAME=admin
ADMIN_PASSWORD=tu_password_seguro

JWT_SECRET=genera_un_string_aleatorio_seguro

CORS_ORIGIN=https://schweizerinmobiliaria.com
```

4. Asegurarse que el dominio apunte a la carpeta `backend/public`

5. Dar permisos de escritura a la carpeta `public/uploads`:
```bash
chmod 755 public/uploads
```

## Endpoints API

### Públicos

- `GET /api/propiedades` - Listar propiedades activas
  - Query params: `tipo=venta|alquiler`, `activa=true|false`
- `GET /api/propiedades/{id}` - Obtener una propiedad

### Autenticación

- `POST /api/auth/login` - Login de administrador
- `POST /api/auth/logout` - Cerrar sesión
- `GET /api/auth/check` - Verificar sesión activa

### Admin (requiere autenticación)

- `POST /api/admin/propiedades` - Crear propiedad
- `PUT /api/admin/propiedades/{id}` - Actualizar propiedad
- `DELETE /api/admin/propiedades/{id}` - Eliminar propiedad
- `POST /api/admin/propiedades/{id}/imagenes` - Subir imagen
- `DELETE /api/admin/propiedades/imagenes/{imageId}` - Eliminar imagen

## Estructura de Datos

### Propiedad
```json
{
  "titulo": "string",
  "descripcion": "string",
  "tipo": "venta|alquiler",
  "precio": "decimal",
  "ubicacion": "string",
  "dormitorios": "integer",
  "banos": "integer",
  "superficie": "decimal",
  "destacada": "boolean",
  "activa": "boolean"
}
```

## Seguridad

- Autenticación basada en sesiones PHP
- Timeout de sesión: 30 minutos
- CORS configurado
- Validación de tipos de archivo en uploads
- Límite de tamaño de archivos: 5MB

## Notas

- Las imágenes se guardan en `public/uploads/`
- Las sesiones se manejan con sesiones nativas de PHP
- El archivo `.htaccess` maneja el rewrite de URLs
