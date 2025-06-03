<?php

namespace App\Presentation\Middleware;

use App\Config\JwtConfig;
use App\Data\PostgresDB\Models\User;

class AuthMiddleware
{
    public static function validateJWT(): callable
{
    return function ($request) {
        $authHeader = $request->getHeaderLine('Authorization');

        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return new \Nyholm\Psr7\Response(401, ['Content-Type' => 'application/json'], json_encode(['error' => 'Unauthorized: Token not provided']));
        }

        $token = $matches[1];

        try {
            $payload = \App\Config\JwtConfig::validateToken($token);

            if (!$payload || !isset($payload['id'])) {
                return new \Nyholm\Psr7\Response(401, ['Content-Type' => 'application/json'], json_encode(['error' => 'Invalid token']));
            }

            $userId = (int)$payload['id'];
            $user = \App\Data\PostgresDB\Models\User::find($userId);

            if (!$user) {
                error_log("User not found in DB with ID: " . $userId);
                return new \Nyholm\Psr7\Response(401, ['Content-Type' => 'application/json'], json_encode(['error' => 'User not found']));
            }

            // AÑADE el usuario al request
            $request = $request->withAttribute('user', $user);

            error_log("Middleware - User ID: " . $user->id);

            // ✅ Este arreglo es lo que espera tu dispatcher para actualizar el request
            return ['request' => $request];

        } catch (\Exception $e) {
            error_log("Auth middleware error: " . $e->getMessage());
            return new \Nyholm\Psr7\Response(500, ['Content-Type' => 'application/json'], json_encode(['error' => 'Internal Server Error']));
        }
    };
}


    private static function jsonResponse($response, array $data, int $status = 200)
    {
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}
