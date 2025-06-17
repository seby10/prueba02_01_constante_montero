<?php

namespace App\Infrastructure\Datasources;

use App\Config\BcryptAdapter;
use App\Data\MySQL\Models\User;
use App\Domain\Datasources\AuthDatasource;
use App\Domain\DTOs\Auth\LoginUserDto;
use App\Domain\DTOs\Auth\RegisterUserDto;
use App\Domain\Entities\UserEntity;
use App\Domain\Errors\CustomError;
use App\Infrastructure\Mappers\UserMapper;
use App\Infrastructure\UnitOfWork\UnitOfWork;

class AuthDatasourceImp implements AuthDatasource
{
    public function __construct(
        private $hashAdapter = null,
        private $compareAdapter = null
    ) {
        $this->hashAdapter = $hashAdapter ?: [BcryptAdapter::class, 'hash'];
        $this->compareAdapter = $compareAdapter ?: [BcryptAdapter::class, 'compare'];
    }

    public function login(LoginUserDto $loginUserDto): UserEntity
    {
        try {
            $user = User::where('email', $loginUserDto->email)->first();
            
            if (!$user) {
                throw CustomError::badRequest('User does not exist');
            }

            $passwordMatch = call_user_func(
                $this->compareAdapter, 
                $loginUserDto->password, 
                $user->password
            );

            if (!$passwordMatch) {
                throw CustomError::badRequest('Invalid password');
            }

            return UserMapper::userEntityFromObject($user);

        } catch (CustomError $error) {
            throw $error;
        } catch (\Exception $error) {
            throw CustomError::internalServer('Login error: ' . $error->getMessage());
        }
    }

    public function register(RegisterUserDto $registerUserDto): UserEntity
    {
        try {
            return UnitOfWork::run(function () use ($registerUserDto) {
                $emailExists = User::where('email', $registerUserDto->email)->first();
                if ($emailExists) {
                    throw CustomError::badRequest('User already exists');
                }

                $hashedPassword = call_user_func($this->hashAdapter, $registerUserDto->password);

                $user = User::create([
                    'name' => $registerUserDto->name,
                    'email' => $registerUserDto->email,
                    'password' => $hashedPassword,
                    'roles' => ['USER_ROLE']
                ]);

                // Aquí podrías agregar más operaciones relacionadas, por ejemplo:
                // Crear perfil, asignar permisos, etc.

                return UserMapper::userEntityFromObject($user);
            });
        } catch (CustomError $error) {
            throw $error;
        } catch (\Exception $error) {
            throw CustomError::internalServer('Registration error: ' . $error->getMessage());
        }
    }
}