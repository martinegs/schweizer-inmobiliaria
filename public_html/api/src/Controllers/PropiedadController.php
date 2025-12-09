<?php

namespace App\Controllers;

use App\Models\Propiedad;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class PropiedadController
{
    private $model;

    public function __construct()
    {
        $this->model = new Propiedad();
    }

    public function getAll(Request $request, Response $response)
    {
        $params = $request->getQueryParams();
        $tipo = $params['tipo'] ?? null;
        $activa = isset($params['activa']) ? (bool)$params['activa'] : true;
        
        $propiedades = $this->model->getAll($tipo, $activa);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $propiedades
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getById(Request $request, Response $response, $args)
    {
        $id = $args['id'];
        $propiedad = $this->model->getById($id);
        
        if (!$propiedad) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Propiedad no encontrada'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $propiedad
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function create(Request $request, Response $response)
    {
        $data = json_decode($request->getBody()->getContents(), true);
        
        // Validación básica
        $required = ['titulo', 'tipo', 'precio', 'ubicacion', 'dormitorios', 'banos', 'superficie'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => "El campo $field es requerido"
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
        }
        
        $id = $this->model->create($data);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Propiedad creada exitosamente',
            'data' => ['id' => $id]
        ]));
        
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function update(Request $request, Response $response, $args)
    {
        $id = $args['id'];
        $data = json_decode($request->getBody()->getContents(), true);
        
        $success = $this->model->update($id, $data);
        
        if (!$success) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al actualizar la propiedad'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Propiedad actualizada exitosamente'
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function delete(Request $request, Response $response, $args)
    {
        $id = $args['id'];
        
        // Obtener imágenes antes de borrar
        $imagenes = $this->model->getImagenesByPropiedadId($id);
        
        $success = $this->model->delete($id);
        
        if (!$success) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al eliminar la propiedad'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
        
        // Eliminar archivos de imágenes
        foreach ($imagenes as $imagen) {
            $filePath = __DIR__ . '/../../public/uploads/' . $imagen['filename'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Propiedad eliminada exitosamente'
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function uploadImage(Request $request, Response $response, $args)
    {
        $id = $args['id'];
        
        // Verificar que la propiedad existe
        $propiedad = $this->model->getById($id);
        if (!$propiedad) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Propiedad no encontrada'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
        
        $uploadedFiles = $request->getUploadedFiles();
        
        if (!isset($uploadedFiles['imagen'])) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'No se recibió ninguna imagen'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        
        $uploadedFile = $uploadedFiles['imagen'];
        
        if ($uploadedFile->getError() !== UPLOAD_ERR_OK) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al subir el archivo'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        
        // Validar tipo de archivo
        $allowedExtensions = explode(',', $_ENV['ALLOWED_EXTENSIONS'] ?? 'jpg,jpeg,png,webp');
        $filename = $uploadedFile->getClientFilename();
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (!in_array($extension, $allowedExtensions)) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Tipo de archivo no permitido'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        
        // Validar tamaño
        $maxSize = $_ENV['UPLOAD_MAX_SIZE'] ?? 5242880; // 5MB por defecto
        if ($uploadedFile->getSize() > $maxSize) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'El archivo es demasiado grande'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        
        // Generar nombre único
        $newFilename = uniqid('prop_' . $id . '_') . '.' . $extension;
        $uploadPath = __DIR__ . '/../../public/uploads/' . $newFilename;
        
        // Mover archivo
        $uploadedFile->moveTo($uploadPath);
        
        // Guardar en BD
        $orden = count($propiedad['imagenes']);
        $this->model->addImagen($id, $newFilename, $orden);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Imagen subida exitosamente',
            'data' => ['filename' => $newFilename]
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function deleteImage(Request $request, Response $response, $args)
    {
        $imageId = $args['imageId'];
        
        $filename = $this->model->deleteImagen($imageId);
        
        if (!$filename) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Imagen no encontrada'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
        
        // Eliminar archivo físico
        $filePath = __DIR__ . '/../../public/uploads/' . $filename;
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Imagen eliminada exitosamente'
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }
}
