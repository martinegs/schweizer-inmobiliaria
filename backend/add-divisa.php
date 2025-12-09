<?php

require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "===========================================\n";
echo "Agregando campo divisa a propiedades\n";
echo "===========================================\n\n";

try {
    $pdo = new PDO(
        "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']};charset={$_ENV['DB_CHARSET']}",
        $_ENV['DB_USER'],
        $_ENV['DB_PASS'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "✓ Conectado a la base de datos\n\n";
    
    // Agregar columna divisa
    echo "Agregando columna divisa...\n";
    $pdo->exec("
        ALTER TABLE propiedades 
        ADD COLUMN divisa ENUM('ARS', 'USD') NOT NULL DEFAULT 'ARS' AFTER precio
    ");
    echo "✓ Columna divisa agregada\n\n";
    
    // Actualizar propiedades existentes con USD para valores altos
    echo "Actualizando propiedades existentes...\n";
    $pdo->exec("
        UPDATE propiedades 
        SET divisa = 'USD' 
        WHERE precio > 10000
    ");
    echo "✓ Propiedades actualizadas\n";
    
    echo "\n===========================================\n";
    echo "Campo divisa agregado exitosamente!\n";
    echo "===========================================\n";
    
} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
