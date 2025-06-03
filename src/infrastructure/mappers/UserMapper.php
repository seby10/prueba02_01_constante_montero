<?php

namespace App\Infrastructure\Mappers;

use App\Domain\Entities\UserEntity;
use App\Domain\Errors\CustomError;

class UserMapper
{
    public static function userEntityFromObject($object): UserEntity
    {
        // Si es un modelo de Eloquent, convertir a array
        if (is_object($object) && method_exists($object, 'toArray')) {
            $data = $object->toArray();
        } else {
            $data = (array) $object;
        }

        $id = $data['id'] ?? null;
        $name = $data['name'] ?? null;
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        $roles = $data['roles'] ?? null;
        $img = $data['img'] ?? null;

        if (!$id) {
            throw CustomError::badRequest('Missing ID');
        }
        if (!$name) {
            throw CustomError::badRequest('Missing Name');
        }
        if (!$email) {
            throw CustomError::badRequest('Missing Email');
        }
        if (!$password) {
            throw CustomError::badRequest('Missing Password');
        }
        if (!$roles) {
            throw CustomError::badRequest('Missing Roles');
        }

        // Convertir ID a string para consistencia
        $id = (string) $id;

        // Asegurar que roles sea un array
        if (is_string($roles)) {
            $roles = json_decode($roles, true) ?: ['USER_ROLE'];
        }

        return new UserEntity(
            id: $id,
            name: $name,
            email: $email,
            password: $password,
            roles: $roles,
            img: $img
        );
    }
}