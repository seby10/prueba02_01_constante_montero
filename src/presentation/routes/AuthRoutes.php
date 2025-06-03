<?php

namespace App\Presentation\Routes;

use App\Infrastructure\Datasources\AuthDatasourceImp;
use App\Infrastructure\Repositories\AuthRepositoryImp;
use App\Presentation\Controllers\AuthController;
use App\Presentation\Middleware\AuthMiddleware;
use App\Presentation\Routes\AppRoutes;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;

class AuthRoutes
{
    public static function register(string $prefix = ''): void
    {
        // Instanciar dependencias usando tu estructura de Clean Architecture
        $authDatasource = new AuthDatasourceImp();
        $authRepository = new AuthRepositoryImp($authDatasource);
        $authController = new AuthController($authRepository);

        // Rutas públicas
        AppRoutes::addRoute('POST', $prefix . '/login', function (ServerRequestInterface $request) use ($authController) {
            $response = new Response();
            return $authController->loginUser($request, $response);
        });

        AppRoutes::addRoute('POST', $prefix . '/register', function (ServerRequestInterface $request) use ($authController) {
            $response = new Response();
            return $authController->registerUser($request, $response);
        });

        // Rutas protegidas
        AppRoutes::addRoute('GET', $prefix . '/users', function (ServerRequestInterface $request) use ($authController) {
            $response = new Response();
            return $authController->getUsers($request, $response);
        }, [AuthMiddleware::validateJWT()]);
    }
}
