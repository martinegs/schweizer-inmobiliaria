<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UsuarioController
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
    
    public function getAll(Request $request, Response $response)
    {
        // Verificar que sea super admin (ID 1)
        if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 1) {
            $error = ['success' => false, 'message' => 'Acceso denegado'];
            $response->getBody()->write(json_encode($error));
            return $response->withStatus(403)->withHeader('Content-Type', 'application/json');
        }
        
        try {
            $stmt = $this->pdo->query("
                SELECT id, email, nombre, activo, created_at, updated_at 
                FROM usuarios 
                ORDER BY created_at DESC
            ");
            $usuarios = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            $response->getBody()->write(json_encode($usuarios));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\PDOException $e) {
            $error = ['success' => false, 'message' => 'Error al obtener usuarios'];
            $response->getBody()->write(json_encode($error));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
    
    public function create(Request $request, Response $response)
    {
        // Verificar que sea super admin (ID 1)
        if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 1) {
            $error = ['success' => false, 'message' => 'Acceso denegado'];
            $response->getBody()->write(json_encode($error));
            return $response->withStatus(403)->withHeader('Content-Type', 'application/json');
        }
        
        $data = json_decode($request->getBody()->getContents(), true);
        
        $email = $data['email'] ?? '';
        $nombre = $data['nombre'] ?? '';
        $password = $data['password'] ?? '';
        $activo = $data['activo'] ?? true;
        
        if (empty($email) || empty($nombre) || empty($password)) {
            $error = ['success' => false, 'message' => 'Todos los campos son obligatorios'];
            $response->getBody()->write(json_encode($error));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
        
        try {
            // Verificar si el email ya existe
            $stmt = $this->pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = ['success' => false, 'message' => 'El email ya está registrado'];
                $response->getBody()->write(json_encode($error));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }
            
            // Crear usuario
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $this->pdo->prepare("
                INSERT INTO usuarios (email, nombre, password, activo) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$email, $nombre, $hashedPassword, $activo ? 1 : 0]);
            
            $result = [
                'success' => true,
                'message' => 'Usuario creado exitosamente',
                'id' => $this->pdo->lastInsertId()
            ];
            
            $response->getBody()->write(json_encode($result));
            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
        } catch (\PDOException $e) {
            $error = ['success' => false, 'message' => 'Error al crear usuario'];
            $response->getBody()->write(json_encode($error));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
    
    public function update(Request $request, Response $response, $args)
    {
        // Verificar que sea super admin (ID 1)
        if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 1) {
            $error = ['success' => false, 'message' => 'Acceso denegado'];
            $response->getBody()->write(json_encode($error));
            return $response->withStatus(403)->withHeader('Content-Type', 'application/json');
        }
        
        $id = $args['id'];
        $data = json_decode($request->getBody()->getContents(), true);
        
        try {
            // Verificar si el usuario existe
            $stmt = $this->pdo->prepare("SELECT id FROM usuarios WHERE id = ?");
            $stmt->execute([$id]);
            if (!$stmt->fetch()) {
                $error = ['success' => false, 'message' => 'Usuario no encontrado'];
                $response->getBody()->write(json_encode($error));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }
            
            // Construir query dinámicamente
            $updates = [];
            $params = [];
            
            if (isset($data['nombre'])) {
                $updates[] = "nombre = ?";
                $params[] = $data['nombre'];
            }
            
            if (isset($data['password']) && !empty($data['password'])) {
                $updates[] = "password = ?";
                $params[] = password_hash($data['password'], PASSWORD_BCRYPT);
            }
            
            if (isset($data['activo'])) {
                $updates[] = "activo = ?";
                $params[] = $data['activo'] ? 1 : 0;
            }
            
            if (empty($updates)) {
                $error = ['success' => false, 'message' => 'No hay datos para actualizar'];
                $response->getBody()->write(json_encode($error));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }
            
            $params[] = $id;
            $sql = "UPDATE usuarios SET " . implode(', ', $updates) . " WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            $result = ['success' => true, 'message' => 'Usuario actualizado exitosamente'];
            $response->getBody()->write(json_encode($result));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\PDOException $e) {
            $error = ['success' => false, 'message' => 'Error al actualizar usuario'];
            $response->getBody()->write(json_encode($error));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
    
    public function delete(Request $request, Response $response, $args)
    {
        // Verificar que sea super admin (ID 1)
        if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 1) {
            $error = ['success' => false, 'message' => 'Acceso denegado'];
            $response->getBody()->write(json_encode($error));
            return $response->withStatus(403)->withHeader('Content-Type', 'application/json');
        }
        
        $id = $args['id'];
        
        try {
            // No permitir eliminar al usuario ID 1
            if ($id == 1) {
                $error = ['success' => false, 'message' => 'No se puede eliminar al super administrador'];
                $response->getBody()->write(json_encode($error));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }
            
            // No permitir que el usuario se elimine a sí mismo
            if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $id) {
                $error = ['success' => false, 'message' => 'No puedes eliminarte a ti mismo'];
                $response->getBody()->write(json_encode($error));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }
            
            $stmt = $this->pdo->prepare("DELETE FROM usuarios WHERE id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() === 0) {
                $error = ['success' => false, 'message' => 'Usuario no encontrado'];
                $response->getBody()->write(json_encode($error));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }
            
            $result = ['success' => true, 'message' => 'Usuario eliminado exitosamente'];
            $response->getBody()->write(json_encode($result));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\PDOException $e) {
            $error = ['success' => false, 'message' => 'Error al eliminar usuario'];
            $response->getBody()->write(json_encode($error));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}
