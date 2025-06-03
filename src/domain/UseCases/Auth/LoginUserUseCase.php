<?php
namespace App\Domain\UseCases\Auth;

use App\Config\JwtConfig;
use App\Domain\DTOs\Auth\LoginUserDto;
use App\Domain\Errors\CustomError;
use App\Domain\Repositories\AuthRepository;

interface LoginUserUseCaseInterface
{
    public function execute(LoginUserDto $loginUserDto): array;
}

class LoginUserUseCase implements LoginUserUseCaseInterface
{
    public function __construct(
        private AuthRepository $authRepository,
        private $signToken = null
    ) {
        $this->signToken = $signToken ?: [JwtConfig::class, 'generateToken'];
    }

    public function execute(LoginUserDto $loginUserDto): array
    {
        $user = $this->authRepository->login($loginUserDto);
        
        $token = call_user_func($this->signToken, ['id' => $user->id], '3 hours');
        
        if (!$token) {
            throw CustomError::internalServer('Error generating token');
        }

        return [
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ]
        ];
    }
}