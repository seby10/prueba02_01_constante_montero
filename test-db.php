<?php
require __DIR__ . '/vendor/autoload.php';

use App\Config\Environment;

// Cargar variables de entorno
$env = Environment::getInstance();

try {
    // Configuración de PostgreSQL
    $host = $env->get('POSTGRES_HOST');
    $port = $env->get('POSTGRES_PORT');
    $dbname = $env->get('POSTGRES_DB_NAME');
    $user = $env->get('POSTGRES_USER');
    $password = $env->get('POSTGRES_PASSWORD');

    // Intentar conexión
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "✅ ¡Conexión exitosa a PostgreSQL!";
    
    // Opcional: Ejecutar una consulta de prueba
    $stmt = $pdo->query("SELECT 1");
    $result = $stmt->fetch();
    echo "\nConsulta de prueba: " . ($result ? "Funciona" : "Error");

} catch (PDOException $e) {
    echo "❌ Error de conexión a PostgreSQL: " . $e->getMessage();
}