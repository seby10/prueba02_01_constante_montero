<?php

namespace App\Data\MySQL;

use App\Data\Database;
use App\Data\DatabaseFactory;

class MySQLDatabaseFactory implements DatabaseFactory
{
    public static function createDatabase(): Database
    {
        return MySQLDatabase::getInstance();
    }
}