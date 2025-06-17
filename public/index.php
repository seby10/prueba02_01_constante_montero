<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';


use App\Config\Environment;
use App\Config\JwtConfig;
use App\Data\MySQL\MySQLDatabaseFactory;

$env = Environment::getInstance();

try {
    $dbFactory = new MySQLDatabaseFactory();

    $database = $dbFactory->createDatabase();
    $database->connect([
        'host' => 'localhost',
        'port' => 3306,
        'database' => 'mydb',
        'user' => 'root',
        'password' => ''
    ]);
} catch (\Exception $e) {
    error_log('Database connection failed: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Failed to connect to database']);
    exit;
}

JwtConfig::init();


header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    $psr17Factory = new \Nyholm\Psr7\Factory\Psr17Factory();
    $creator = new \Nyholm\Psr7Server\ServerRequestCreator(
        $psr17Factory,
        $psr17Factory,
        $psr17Factory,
        $psr17Factory
    );
    
    $request = $creator->fromGlobals();
    
    App\Presentation\Routes\AppRoutes::registerRoutes();
    $response = App\Presentation\Routes\AppRoutes::dispatch($request);
    
    http_response_code($response->getStatusCode());
    foreach ($response->getHeaders() as $name => $values) {
        foreach ($values as $value) {
            header(sprintf('%s: %s', $name, $value), false);
        }
    }
    
    echo $response->getBody();

} catch (Exception $e) {
    error_log('Application error: ' . $e->getMessage());
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Internal Server Error']);
}