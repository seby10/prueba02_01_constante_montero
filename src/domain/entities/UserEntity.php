<?php
namespace App\Domain\Entities;

class UserEntity
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
        public string $password,
        public array $roles,
        public ?string $img = null
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'roles' => $this->roles,
            'img' => $this->img
        ];
    }
}