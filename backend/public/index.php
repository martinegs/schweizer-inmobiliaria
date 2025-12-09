<?php

use App\Controllers\AuthController;
use App\Controllers\PropiedadController;
use App\Controllers\UsuarioController;
use App\Middleware\AuthMiddleware;
use App\Middleware\CorsMiddleware;
use Slim\Factory\AppFactory;
use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

// Cargar variables de entorno
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Configurar sesiones
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Lax');

// Crear app Slim
$app = AppFactory::create();

// Middleware global
$app->add(new CorsMiddleware());

// Middleware para errores
$app->addErrorMiddleware(true, true, true);

// Rutas públicas
$app->get('/api/propiedades', [PropiedadController::class, 'getAll']);
$app->get('/api/propiedades/{id}', [PropiedadController::class, 'getById']);

// Rutas de autenticación
$app->post('/api/auth/login', [AuthController::class, 'login']);
$app->post('/api/auth/logout', [AuthController::class, 'logout']);
$app->get('/api/auth/check', [AuthController::class, 'checkAuth']);

// Rutas protegidas (requieren autenticación)
$app->group('/api/admin', function ($group) {
    $group->post('/propiedades', [PropiedadController::class, 'create']);
    $group->put('/propiedades/{id}', [PropiedadController::class, 'update']);
    $group->delete('/propiedades/{id}', [PropiedadController::class, 'delete']);
    $group->post('/propiedades/{id}/imagenes', [PropiedadController::class, 'uploadImage']);
    $group->delete('/propiedades/imagenes/{imageId}', [PropiedadController::class, 'deleteImage']);
    
    // Rutas de usuarios
    $group->get('/usuarios', [UsuarioController::class, 'getAll']);
    $group->post('/usuarios', [UsuarioController::class, 'create']);
    $group->put('/usuarios/{id}', [UsuarioController::class, 'update']);
    $group->delete('/usuarios/{id}', [UsuarioController::class, 'delete']);
})->add(new AuthMiddleware());

// Manejar OPTIONS para CORS
$app->options('/{routes:.+}', function ($request, $response) {
    return $response;
});

$app->run();
