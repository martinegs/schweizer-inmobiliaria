<?php

namespace App\Models;

use App\Database;
use PDO;

class Propiedad
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll($tipo = null, $activa = true)
    {
        $sql = "SELECT p.*, GROUP_CONCAT(i.filename ORDER BY i.orden) as imagenes 
                FROM propiedades p 
                LEFT JOIN imagenes i ON p.id = i.propiedad_id 
                WHERE 1=1";
        
        $params = [];
        
        if ($activa !== null) {
            $sql .= " AND p.activa = :activa";
            $params[':activa'] = $activa ? 1 : 0;
        }
        
        if ($tipo) {
            $sql .= " AND p.tipo = :tipo";
            $params[':tipo'] = $tipo;
        }
        
        $sql .= " GROUP BY p.id ORDER BY p.destacada DESC, p.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        $propiedades = $stmt->fetchAll();
        
        // Convertir imagenes de string a array
        foreach ($propiedades as &$prop) {
            $prop['imagenes'] = $prop['imagenes'] ? explode(',', $prop['imagenes']) : [];
        }
        
        return $propiedades;
    }

    public function getById($id)
    {
        $sql = "SELECT p.*, GROUP_CONCAT(i.filename ORDER BY i.orden) as imagenes 
                FROM propiedades p 
                LEFT JOIN imagenes i ON p.id = i.propiedad_id 
                WHERE p.id = :id 
                GROUP BY p.id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        $propiedad = $stmt->fetch();
        
        if ($propiedad) {
            $propiedad['imagenes'] = $propiedad['imagenes'] ? explode(',', $propiedad['imagenes']) : [];
        }
        
        return $propiedad;
    }

    public function create($data)
    {
        $sql = "INSERT INTO propiedades (titulo, descripcion, tipo, divisa, precio, ubicacion, latitud, longitud, dormitorios, banos, superficie, destacada, activa) 
                VALUES (:titulo, :descripcion, :tipo, :divisa, :precio, :ubicacion, :latitud, :longitud, :dormitorios, :banos, :superficie, :destacada, :activa)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':titulo' => $data['titulo'],
            ':descripcion' => $data['descripcion'] ?? '',
            ':tipo' => $data['tipo'],
            ':divisa' => $data['divisa'] ?? 'ARS',
            ':precio' => $data['precio'],
            ':ubicacion' => $data['ubicacion'],
            ':latitud' => $data['latitud'] ?? null,
            ':longitud' => $data['longitud'] ?? null,
            ':dormitorios' => $data['dormitorios'],
            ':banos' => $data['banos'],
            ':superficie' => $data['superficie'],
            ':destacada' => $data['destacada'] ?? false,
            ':activa' => $data['activa'] ?? true
        ]);
        
        return $this->db->lastInsertId();
    }

    public function update($id, $data)
    {
        $sql = "UPDATE propiedades SET 
                titulo = :titulo, 
                descripcion = :descripcion, 
                tipo = :tipo, 
                divisa = :divisa,
                precio = :precio, 
                ubicacion = :ubicacion, 
                latitud = :latitud,
                longitud = :longitud,
                dormitorios = :dormitorios, 
                banos = :banos, 
                superficie = :superficie, 
                destacada = :destacada, 
                activa = :activa 
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':titulo' => $data['titulo'],
            ':descripcion' => $data['descripcion'] ?? '',
            ':tipo' => $data['tipo'],
            ':divisa' => $data['divisa'] ?? 'ARS',
            ':precio' => $data['precio'],
            ':ubicacion' => $data['ubicacion'],
            ':latitud' => $data['latitud'] ?? null,
            ':longitud' => $data['longitud'] ?? null,
            ':dormitorios' => $data['dormitorios'],
            ':banos' => $data['banos'],
            ':superficie' => $data['superficie'],
            ':destacada' => $data['destacada'] ?? false,
            ':activa' => $data['activa'] ?? true
        ]);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM propiedades WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function addImagen($propiedadId, $filename, $orden = 0)
    {
        $sql = "INSERT INTO imagenes (propiedad_id, filename, orden) VALUES (:propiedad_id, :filename, :orden)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':propiedad_id' => $propiedadId,
            ':filename' => $filename,
            ':orden' => $orden
        ]);
    }

    public function deleteImagen($id)
    {
        $sql = "SELECT filename FROM imagenes WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $imagen = $stmt->fetch();
        
        if ($imagen) {
            $sql = "DELETE FROM imagenes WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $imagen['filename'];
        }
        
        return null;
    }

    public function getImagenesByPropiedadId($propiedadId)
    {
        $sql = "SELECT * FROM imagenes WHERE propiedad_id = :propiedad_id ORDER BY orden";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':propiedad_id' => $propiedadId]);
        return $stmt->fetchAll();
    }
}
