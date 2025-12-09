<?php
// Test de conexión a la base de datos

require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

// Cargar variables de entorno
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "===========================================\n";
echo "Test de Conexión a Base de Datos\n";
echo "===========================================\n\n";

echo "Configuración:\n";
echo "- Host: " . $_ENV['DB_HOST'] . "\n";
echo "- Base de datos: " . $_ENV['DB_NAME'] . "\n";
echo "- Usuario: " . $_ENV['DB_USER'] . "\n";
echo "- Password: " . str_repeat('*', strlen($_ENV['DB_PASS'])) . "\n\n";

try {
    $host = $_ENV['DB_HOST'];
    $dbname = $_ENV['DB_NAME'];
    $username = $_ENV['DB_USER'];
    $password = $_ENV['DB_PASS'];
    $charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';
    
    $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
    
    echo "Intentando conectar...\n";
    
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    echo "✓ Conexión exitosa!\n\n";
    
    // Verificar tablas
    echo "Verificando tablas...\n";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (count($tables) > 0) {
        echo "✓ Tablas encontradas (" . count($tables) . "):\n";
        foreach ($tables as $table) {
            echo "  - $table\n";
        }
    } else {
        echo "⚠ No se encontraron tablas. Necesitas importar database.sql\n";
    }
    
    echo "\n";
    
    // Verificar propiedades
    if (in_array('propiedades', $tables)) {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM propiedades");
        $result = $stmt->fetch();
        echo "✓ Propiedades en BD: " . $result['total'] . "\n";
    }
    
    echo "\n===========================================\n";
    echo "Conexión OK - Todo listo para usar!\n";
    echo "===========================================\n";
    
} catch (PDOException $e) {
    echo "✗ Error de conexión:\n";
    echo "  Mensaje: " . $e->getMessage() . "\n";
    echo "  Código: " . $e->getCode() . "\n\n";
    
    echo "Posibles soluciones:\n";
    echo "1. Verifica que las credenciales en .env sean correctas\n";
    echo "2. Verifica que el servidor MySQL esté accesible\n";
    echo "3. Verifica que la base de datos exista\n";
    echo "4. Verifica los permisos del usuario\n";
    
    exit(1);
}
