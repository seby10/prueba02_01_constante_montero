<?php

namespace App\Infrastructure\UnitOfWork;

use Illuminate\Database\Capsule\Manager as Capsule;

class UnitOfWork
{
    public static function run(callable $callback)
    {
        return Capsule::connection()->transaction($callback);
    }
}