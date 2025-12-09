<?php

require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "===========================================\n";
echo "Agregando coordenadas de ubicación\n";
echo "===========================================\n\n";

try {
    $pdo = new PDO(
        "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']};charset={$_ENV['DB_CHARSET']}",
        $_ENV['DB_USER'],
        $_ENV['DB_PASS'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "✓ Conectado a la base de datos\n\n";
    
    // Agregar columnas de latitud y longitud
    echo "Agregando columnas de coordenadas...\n";
    $pdo->exec("
        ALTER TABLE propiedades 
        ADD COLUMN latitud DECIMAL(10, 8) NULL AFTER ubicacion,
        ADD COLUMN longitud DECIMAL(11, 8) NULL AFTER latitud
    ");
    echo "✓ Columnas latitud y longitud agregadas\n\n";
    
    echo "\n===========================================\n";
    echo "Campos de coordenadas agregados!\n";
    echo "===========================================\n";
    
} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
