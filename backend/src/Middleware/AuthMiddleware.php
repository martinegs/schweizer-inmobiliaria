<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class AuthMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        session_start();
        
        // Verificar si hay sesión activa
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            $response = new Response();
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'No autorizado'
            ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);
        }
        
        // Renovar tiempo de expiración
        $_SESSION['last_activity'] = time();
        
        return $handler->handle($request);
    }
}
