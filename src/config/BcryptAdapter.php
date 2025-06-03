<?php
namespace App\Config;
class BcryptAdapter
{
    public static function hash(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, [
           
            'cost' => 10, // You can adjust the cost factor as needed
        ]);
    }

    public static function compare(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}