<?php

declare(strict_types=1);

namespace Application\Exceptions;

use Symfony\Component\HttpFoundation\Response;

final class ForbiddenException extends HttpException
{
    public static function invalidToken(): self
    {
        return new self('Invalid authorization token.', Response::HTTP_FORBIDDEN);
    }
}
