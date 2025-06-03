<?php

namespace App\Domain\Datasources;

use App\Domain\DTOs\Auth\LoginUserDto;
use App\Domain\DTOs\Auth\RegisterUserDto;
use App\Domain\Entities\UserEntity;

interface AuthDatasource
{
    public function register(RegisterUserDto $registerUserDto): UserEntity;
    public function login(LoginUserDto $loginUserDto): UserEntity;
}
