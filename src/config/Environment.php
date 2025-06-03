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
            'MONGO_URL' => $this->getEnv('MONGO_DB_URL'),
            'MONGO_DB_NAME' => $this->getEnv('MONGO_DB_NAME'),
            'JWT_SEED' => $this->getEnv('JWT_SEED'),
            'POSTGRES_DB_NAME' => $this->getEnv('POSTGRES_DB_NAME'),
            'POSTGRES_HOST' => $this->getEnv('POSTGRES_HOST'),
            'POSTGRES_PORT' => $this->getEnv('POSTGRES_PORT', 5432),
            'POSTGRES_USER' => $this->getEnv('POSTGRES_USER'),
            'POSTGRES_PASSWORD' => $this->getEnv('POSTGRES_PASSWORD'),
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
        
        if ($value === null) {
            throw new \Exception("Environment variable {$key} is required");
        }
        
        return $value;
    }
}