<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthController
{
    private $pdo;
    
    public function __construct()
    {
        $this->pdo = new \PDO(
            "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']};charset={$_ENV['DB_CHARSET']}",
            $_ENV['DB_USER'],
            $_ENV['DB_PASS'],
            [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
        );
    }
    
    public function login(Request $request, Response $response)
    {
        $data = json_decode($request->getBody()->getContents(), true);
        
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';
        
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE email = ? AND activo = 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                session_start();
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['nombre'] = $user['nombre'];
                $_SESSION['last_activity'] = time();
                
                $result = [
                    'success' => true,
                    'message' => 'Login exitoso',
                    'user' => [
                        'email' => $user['email'],
                        'nombre' => $user['nombre']
                    ]
                ];
            } else {
                $result = [
                    'success' => false,
                    'message' => 'Credenciales incorrectas'
                ];
                $response = $response->withStatus(401);
            }
        } catch (\PDOException $e) {
            $result = [
                'success' => false,
                'message' => 'Error en el servidor'
            ];
            $response = $response->withStatus(500);
        }
        
        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function logout(Request $request, Response $response)
    {
        session_start();
        session_destroy();
        
        $result = [
            'success' => true,
            'message' => 'Logout exitoso'
        ];
        
        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function checkAuth(Request $request, Response $response)
    {
        session_start();
        
        $isLoggedIn = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
        
        // Verificar timeout de sesión (30 minutos)
        if ($isLoggedIn && isset($_SESSION['last_activity'])) {
            if (time() - $_SESSION['last_activity'] > 1800) {
                session_destroy();
                $isLoggedIn = false;
            } else {
                $_SESSION['last_activity'] = time();
            }
        }
        
        $result = [
            'success' => true,
            'authenticated' => $isLoggedIn,
            'user' => $isLoggedIn ? [
                'email' => $_SESSION['email'],
                'nombre' => $_SESSION['nombre']
            ] : null
        ];
        
        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
