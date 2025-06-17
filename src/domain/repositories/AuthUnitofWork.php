<?php

namespace App\Domain\Repositories;

interface AuthUnitOfWork
{
    public function beginTransaction(): void;
    public function commit(): void;
    public function rollback(): void;
    public function getAuthRepository(): AuthRepository;
}