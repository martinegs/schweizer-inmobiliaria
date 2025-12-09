<?php
// Migrar base de datos

require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

// Cargar variables de entorno
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "===========================================\n";
echo "Migrando Base de Datos\n";
echo "===========================================\n\n";

try {
    $host = $_ENV['DB_HOST'];
    $dbname = $_ENV['DB_NAME'];
    $username = $_ENV['DB_USER'];
    $password = $_ENV['DB_PASS'];
    $charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';
    
    $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
    
    echo "Conectando a la base de datos...\n";
    
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    echo "✓ Conectado\n\n";
    
    // Leer el archivo SQL
    $sqlFile = __DIR__ . '/database.sql';
    
    if (!file_exists($sqlFile)) {
        throw new Exception("No se encuentra el archivo database.sql");
    }
    
    echo "Leyendo database.sql...\n";
    $sql = file_get_contents($sqlFile);
    
    // Eliminar las líneas de CREATE DATABASE y USE que no son necesarias
    $sql = preg_replace('/CREATE DATABASE.*?;/i', '', $sql);
    $sql = preg_replace('/USE\s+\w+;/i', '', $sql);
    
    // Dividir por statement (usando punto y coma como delimitador)
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        function($stmt) {
            return !empty($stmt) && !preg_match('/^--/', $stmt);
        }
    );
    
    echo "Ejecutando " . count($statements) . " statements...\n\n";
    
    $executed = 0;
    foreach ($statements as $statement) {
        if (empty(trim($statement))) continue;
        
        try {
            $pdo->exec($statement);
            $executed++;
            
            // Mostrar progreso
            if (preg_match('/CREATE TABLE.*`(\w+)`/i', $statement, $matches)) {
                echo "✓ Tabla creada: {$matches[1]}\n";
            } elseif (preg_match('/INSERT INTO\s+(\w+)/i', $statement, $matches)) {
                echo "✓ Datos insertados en: {$matches[1]}\n";
            }
        } catch (PDOException $e) {
            // Ignorar errores de "tabla ya existe"
            if (strpos($e->getMessage(), 'already exists') === false) {
                echo "⚠ Error: " . $e->getMessage() . "\n";
            }
        }
    }
    
    echo "\n===========================================\n";
    echo "Migración completada!\n";
    echo "Statements ejecutados: $executed\n";
    echo "===========================================\n\n";
    
    // Verificar las tablas creadas
    echo "Verificando tablas creadas...\n";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($tables as $table) {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM `$table`");
        $count = $stmt->fetch()['count'];
        echo "  ✓ $table ($count registros)\n";
    }
    
    echo "\n¡Base de datos lista para usar!\n";
    
} catch (PDOException $e) {
    echo "✗ Error de base de datos:\n";
    echo "  " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "✗ Error:\n";
    echo "  " . $e->getMessage() . "\n";
    exit(1);
}
