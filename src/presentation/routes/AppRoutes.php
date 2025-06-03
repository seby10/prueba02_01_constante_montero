<?php

namespace App\Presentation\Routes;

use App\Presentation\Routes\AuthRoutes;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;

class AppRoutes
{
    private static array $routes = [];

    public static function registerRoutes(): void
    {
        AuthRoutes::register('/api/auth');
    }

    public static function addRoute(string $method, string $path, callable $handler, array $middleware = []): void
    {
        self::$routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler,
            'middleware' => $middleware
        ];
    }

    public static function dispatch(ServerRequestInterface $request): Response
    {
        $method = $request->getMethod();
        $uri = $request->getUri()->getPath();

        foreach (self::$routes as $route) {
            if ($route['method'] === $method && self::matchPath($route['path'], $uri)) {

                foreach ($route['middleware'] as $middleware) {
                    $middlewareResult = $middleware($request);
                    if ($middlewareResult instanceof Response) {
                        return $middlewareResult;
                    }
                    if (is_array($middlewareResult)) {

                        $request = $middlewareResult['request'];
                    }
                }
                return call_user_func($route['handler'], $request);
            }
        }

        // Ruta no encontrada
        return new Response(
            404,
            ['Content-Type' => 'application/json'],
            json_encode(['error' => 'Route not found'])
        );
    }

    private static function matchPath(string $routePath, string $requestUri): bool
    {
        // Simple matching - puedes expandir esto para parámetros dinámicos
        return $routePath === $requestUri;
    }

}
