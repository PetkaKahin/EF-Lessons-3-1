<?php

declare(strict_types=1);

namespace Application\Exceptions;

use Symfony\Component\HttpFoundation\Response;

final class BadRequestException extends HttpException
{
    public static function invalidJson(): self
    {
        return new self('Invalid JSON', Response::HTTP_BAD_REQUEST);
    }
}
