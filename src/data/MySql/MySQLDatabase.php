<?php

namespace App\Data\MySQL;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

class MySQLDatabase
{
    private static ?MySQLDatabase $instance = null;
    private bool $isConnected = false;
    private Capsule $capsule;

    private function __construct() {}

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function connect(array $options): bool
    {

        try {
            $this->capsule = new Capsule;
            
            $this->capsule->addConnection([
                'driver' => 'mysql',
                'host' => $options['host'],
                'port' => $options['port'],
                'database' => $options['database'],
                'username' => $options['user'],
                'password' => $options['password'],
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => true,
                'engine' => null,
            ]);

            $this->capsule->setAsGlobal();
            $this->capsule->bootEloquent();

            // Test connection
            $this->capsule->getConnection()->getPdo();

            $this->isConnected = true;
            //echo "MySQL connected successfully\n";
            
            // Create tables if they don't exist
            $this->createTables();
            
            return true;
        } catch (\Exception $e) {
            echo "Error connecting to MySQL: " . $e->getMessage() . "\n";
            throw $e;
        }
    }

    public function getConnection(): Capsule
    {
        if (!$this->isConnected) {
            throw new \Exception('Database not connected. Call connect() first.');
        }
        return $this->capsule;
    }

    private function createTables(): void
    {
        $schema = $this->capsule->schema();
        
        if (!$schema->hasTable('users')) {
            $schema->create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('password');
                $table->string('img')->nullable();
                $table->json('roles');
                $table->timestamps();
            });
            
            //echo "Tabla 'users' creada exitosamente\n";
        } else {
            //echo "Tabla 'users' ya existe\n";
        }
    }
}