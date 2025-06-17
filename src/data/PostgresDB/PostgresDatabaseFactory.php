<?php

namespace App\Data\PostgresDB;

use App\Data\Database;
use App\Data\DatabaseFactory;

class PostgresDatabaseFactory implements DatabaseFactory
{
    public static function createDatabase(): Database
    {
        return PostgresDatabase::getInstance();
    }
}