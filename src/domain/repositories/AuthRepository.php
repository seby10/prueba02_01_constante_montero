<?php

namespace App\Domain\Repositories;

use App\Domain\DTOs\Auth\LoginUserDto;
use App\Domain\DTOs\Auth\RegisterUserDto;
use App\Domain\Entities\UserEntity;

interface AuthRepository
{
    public function register(RegisterUserDto $registerUserDto): UserEntity;
    public function login(LoginUserDto $loginUserDto): UserEntity;
}