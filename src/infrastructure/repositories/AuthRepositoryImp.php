<?php

namespace App\Infrastructure\Repositories;

use App\Domain\DTOs\Auth\LoginUserDto;
use App\Domain\DTOs\Auth\RegisterUserDto;
use App\Domain\Entities\UserEntity;
use App\Domain\Repositories\AuthRepository;
use App\Domain\Datasources\AuthDatasource;

class AuthRepositoryImp implements AuthRepository
{
    public function __construct(
        private AuthDatasource $authDatasource
    ) {}

    public function login(LoginUserDto $loginUserDto): UserEntity
    {
        return $this->authDatasource->login($loginUserDto);
    }

    public function register(RegisterUserDto $registerUserDto): UserEntity
    {
        return $this->authDatasource->register($registerUserDto);
    }
}