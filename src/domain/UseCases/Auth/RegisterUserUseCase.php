<?php
namespace App\Domain\UseCases\Auth;

use App\Config\JwtConfig;
use App\Domain\DTOs\Auth\RegisterUserDto;
use App\Domain\Errors\CustomError;
use App\Domain\Repositories\AuthRepository;

interface RegisterUserUseCaseInterface
{
    public function execute(RegisterUserDto $registerUserDto): array;
}

class RegisterUserUseCase implements RegisterUserUseCaseInterface
{
    public function __construct(
        private AuthRepository $authRepository,
        private $signToken = null
    ) {
        $this->signToken = $signToken ?: [JwtConfig::class, 'generateToken'];
    }

    public function execute(RegisterUserDto $registerUserDto): array
    {
        $user = $this->authRepository->register($registerUserDto);
        
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
