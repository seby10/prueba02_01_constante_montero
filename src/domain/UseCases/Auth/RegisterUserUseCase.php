<?php
namespace App\Domain\UseCases\Auth;

use App\Config\JwtConfig;
use App\Domain\DTOs\Auth\RegisterUserDto;
use App\Domain\Errors\CustomError;
use App\Domain\Repositories\AuthRepository;
use App\Domain\Repositories\AuthUnitOfWork;

interface RegisterUserUseCaseInterface
{
    public function execute(RegisterUserDto $registerUserDto): array;
}

// class RegisterUserUseCase implements RegisterUserUseCaseInterface
// {
//     public function __construct(
//         private AuthRepository $authRepository,
//         private $signToken = null
//     ) {
//         $this->signToken = $signToken ?: [JwtConfig::class, 'generateToken'];
//     }

//     public function execute(RegisterUserDto $registerUserDto): array
//     {
//         $user = $this->authRepository->register($registerUserDto);
        
//         $token = call_user_func($this->signToken, ['id' => $user->id], '3 hours');
        
//         if (!$token) {
//             throw CustomError::internalServer('Error generating token');
//         }

//         return [
//             'token' => $token,
//             'user' => [
//                 'id' => $user->id,
//                 'name' => $user->name,
//                 'email' => $user->email
//             ]
//         ];
//     }
// }

class RegisterUserUseCase
{
    public function __construct(
        private AuthUnitOfWork $unitOfWork,
        private $signToken = null
    ) {
        $this->signToken = $signToken ?: [JwtConfig::class, 'generateToken'];
    }

    public function execute(RegisterUserDto $dto): array
    {
        try {
            $this->unitOfWork->beginTransaction();
            
            $authRepository = $this->unitOfWork->getAuthRepository();
            
            // if ($authRepository->findByEmail($dto->email)) {
            //     throw new \Exception('User already exists');
            // }

            $createdUser = $authRepository->register($dto);
            
            $this->unitOfWork->commit();

            $token = call_user_func($this->signToken, ['id' => $createdUser->id], '3 hours');

            return [
                'token' => $token,
                'user' => [
                    'id' => $createdUser->id,
                    'name' => $createdUser->name,
                    'email' => $createdUser->email
                ]
            ];

        } catch (\Exception $e) {
            $this->unitOfWork->rollback();
            throw $e;
        }
    }
}