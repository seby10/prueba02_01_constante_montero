<?php

namespace App\Data;

use Illuminate\Database\Capsule\Manager as Capsule;

abstract class Database
{
    protected bool $isConnected = false;
    protected Capsule $capsule;

    abstract public function connect(array $options): bool;
    abstract public function getConnection(): Capsule;
    abstract protected function createTables(): void;
}