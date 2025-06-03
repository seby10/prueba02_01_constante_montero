<?php

namespace App\Domain\Errors;

class CustomError extends \Exception
{
    public function __construct(
        public readonly int $statusCode,
        public readonly string $errorMessage
    ) {
        parent::__construct($errorMessage);
    }

    public static function badRequest(string $message): self
    {
        return new self(400, $message);
    }

    public static function unauthorized(string $message): self
    {
        return new self(401, $message);
    }

    public static function forbidden(string $message): self
    {
        return new self(403, $message);
    }

    public static function notFound(string $message): self
    {
        return new self(404, $message);
    }

    public static function internalServer(string $message = 'Internal Server Error'): self
    {
        return new self(500, $message);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
}