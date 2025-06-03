<?php

namespace App\Config;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtConfig
{
    private static string $jwtSeed;

    public static function init(): void
    {
        self::$jwtSeed = Environment::getInstance()->get('JWT_SEED');
    }

    public static function generateToken(array $payload, string $duration = '2 hours'): ?string
    {
        try {
            $now = time();
            $exp = strtotime("+{$duration}", $now);

            $tokenPayload = array_merge($payload, [
                'iat' => $now,
                'exp' => $exp
            ]);

            return JWT::encode($tokenPayload, self::$jwtSeed, 'HS256');
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function validateToken(string $token): ?array
    {
        try {
            $decoded = JWT::decode($token, new Key(self::$jwtSeed, 'HS256'));
            return (array) $decoded;
        } catch (\Exception $e) {
            return null;
        }
    }
}
