<?php

namespace App\Config;

use Dotenv\Dotenv;

class Environment
{
    private static ?Environment $instance = null;
    private array $config = [];

    private function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();
        
        $this->config = [
            'PORT' => $this->getEnv('PORT', 3100),
            'JWT_SEED' => $this->getEnv('JWT_SEED'),
            'MYSQL_DB_NAME' => $this->getEnv('MYSQL_DB_NAME'),
            'MYSQL_HOST' => $this->getEnv('MYSQL_HOST'),
            'MYSQL_PORT' => $this->getEnv('MYSQL_PORT', 3306),
            'MYSQL_USER' => $this->getEnv('MYSQL_USER'),
            'MYSQL_PASSWORD' => $this->getEnv('MYSQL_PASSWORD'),
        ];
    }
    
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function get(string $key): mixed
    {
        return $this->config[$key] ?? null;
    }

    private function getEnv(string $key, mixed $default = null): mixed
    {
        $value = $_ENV[$key] ?? $default;
        
        if ($value === null && $key !== 'MYSQL_PASSWORD') {
            throw new \Exception("Environment variable {$key} is required");
        }
        
        return $value;
    }
}