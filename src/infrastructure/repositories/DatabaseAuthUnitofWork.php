<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Repositories\AuthUnitOfWork;
use App\Domain\Repositories\AuthRepository;
use Illuminate\Database\Capsule\Manager;

class DatabaseAuthUnitOfWork implements AuthUnitOfWork
{
    private AuthRepository $authRepository;

    public function __construct(
        private Manager $db,
        ?AuthRepository $authRepository = null
    ) {
        $this->authRepository = $authRepository ?? new AuthRepository($db);
    }

    public function beginTransaction(): void
    {
        $this->db->getConnection()->beginTransaction();
    }

    public function commit(): void
    {
        $this->db->getConnection()->commit();
    }

    public function rollback(): void
    {
        $this->db->getConnection()->rollBack();
    }

    public function getAuthRepository(): AuthRepository
    {
        return $this->authRepository;
    }
}