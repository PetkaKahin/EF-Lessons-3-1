<?php

declare(strict_types=1);

namespace Application\Exceptions;

use Symfony\Component\HttpFoundation\Response;

final class ValidationException extends HttpException
{
    public static function message(string $message): self
    {
        return new self($message, Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
