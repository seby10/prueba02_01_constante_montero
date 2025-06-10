<?php

namespace App\Presentation\Controllers;

use App\Data\MySQLDB\Models\User;
use App\Domain\DTOs\Auth\LoginUserDto;
use App\Domain\DTOs\Auth\RegisterUserDto;
use App\Domain\Errors\CustomError;
use App\Domain\Repositories\AuthRepository;
use App\Domain\UseCases\Auth\LoginUserUseCase;
use App\Domain\UseCases\Auth\RegisterUserUseCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthController
{
    public function __construct(
        private AuthRepository $authRepository
    ) {}

    public function registerUser(Request $request, Response $response): Response
    {
        try {
            $body = json_decode($request->getBody()->getContents(), true);

            [$error, $registerUserDto] = RegisterUserDto::create($body ?: []);

            if ($error) {
                return $this->jsonResponse($response, ['error' => $error], 400);
            }

            $registerUseCase = new RegisterUserUseCase($this->authRepository);
            $data = $registerUseCase->execute($registerUserDto);

            return $this->jsonResponse($response, $data);
        } catch (\Exception $error) {
            return $this->handleError($error, $response);
        }
    }

    public function loginUser(Request $request, Response $response): Response
    {
        try {
            $body = json_decode($request->getBody()->getContents(), true);

            [$error, $loginUserDto] = LoginUserDto::create($body ?: []);

            if ($error) {
                return $this->jsonResponse($response, ['error' => $error], 400);
            }

            $loginUseCase = new LoginUserUseCase($this->authRepository);
            $data = $loginUseCase->execute($loginUserDto);

            return $this->jsonResponse($response, $data);
        } catch (\Exception $error) {
            return $this->handleError($error, $response);
        }
    }

    public function getUsers(Request $request, Response $response): Response
    {
        try {
            $user = $request->getAttribute('user');

            if (!$user) {
                error_log("User is NULL in controller");
                return $this->jsonResponse($response, ['error' => 'Unauthorized: User not found in request'], 401);
            }
            
            $users = User::all();

            return $this->jsonResponse($response, [
                'users' => $users->toArray(),
                'authenticated_user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email
                ]
            ]);
        } catch (\Exception $error) {
            error_log("Controller error: " . $error->getMessage());
            return $this->handleError($error, $response);
        }
    }

    private function handleError(\Exception $error, Response $response): Response
    {
        if ($error instanceof CustomError) {
            return $this->jsonResponse($response, ['error' => $error->getErrorMessage()], $error->getStatusCode());
        }

        error_log('Controller error: ' . $error->getMessage());
        return $this->jsonResponse($response, ['error' => 'Internal Server Error'], 500);
    }

    private function jsonResponse(Response $response, array $data, int $status = 200): Response
    {
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}