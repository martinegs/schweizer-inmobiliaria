<?php

require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "===========================================\n";
echo "Creando tabla de usuarios y usuario admin\n";
echo "===========================================\n\n";

try {
    $pdo = new PDO(
        "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']};charset={$_ENV['DB_CHARSET']}",
        $_ENV['DB_USER'],
        $_ENV['DB_PASS'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "✓ Conectado a la base de datos\n\n";
    
    // Crear tabla usuarios
    echo "Creando tabla usuarios...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS usuarios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            nombre VARCHAR(255),
            activo BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_email (email),
            INDEX idx_activo (activo)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ Tabla usuarios creada\n\n";
    
    // Crear usuario
    echo "Creando usuario admin...\n";
    $email = 'gerenciageneral@schweizerinmobiliaria.com';
    $password = password_hash('cochiteamo', PASSWORD_BCRYPT);
    $nombre = 'Gerencia General';
    
    $stmt = $pdo->prepare("
        INSERT INTO usuarios (email, password, nombre) 
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE 
        password = VALUES(password),
        nombre = VALUES(nombre)
    ");
    
    $stmt->execute([$email, $password, $nombre]);
    
    echo "✓ Usuario creado exitosamente\n";
    echo "  Email: $email\n";
    echo "  Contraseña: cochiteamo\n";
    
    echo "\n===========================================\n";
    echo "Usuario admin listo para usar!\n";
    echo "===========================================\n";
    
} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
