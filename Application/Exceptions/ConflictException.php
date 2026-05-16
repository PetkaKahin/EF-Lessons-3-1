<?php

declare(strict_types=1);

namespace Application\Exceptions;

use Symfony\Component\HttpFoundation\Response;

final class ConflictException extends HttpException
{
    public static function idempotencyKeyBodyMismatch(): self
    {
        return new self(
            'Idempotency-Key was already used with another request body.',
            Response::HTTP_CONFLICT,
        );
    }
}
