<?php

namespace App\Domain\DTOs\Auth;

use App\Config\Validators;


class RegisterUserDto
{
    private function __construct(
        public string $name,
        public string $email,
        public string $password
    ) {}

    public static function create(array $data): array
    {
        $name = $data['name'] ?? null;
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$name || !$email || !$password) {
            return ['Missing required fields', null];
        }

        if (!Validators::isEmail($email)) {
            return ['Invalid email', null];
        }

        if (strlen($password) < 6) {
            return ['Password must be at least 6 characters long', null];
        }

        return [null, new self($name, $email, $password)];
    }
}

