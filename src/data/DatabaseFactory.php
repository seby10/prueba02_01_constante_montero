<?php

namespace App\Data;

interface DatabaseFactory
{
    public static function createDatabase(): Database;
}