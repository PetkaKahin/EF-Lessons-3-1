<?php

declare(strict_types=1);

namespace Application\Exceptions;

use Symfony\Component\HttpFoundation\Response;

final class UnauthorizedException extends HttpException
{
    public static function missingAuthorizationHeader(): self
    {
        return new self(
            'Authorization header is required.',
            Response::HTTP_UNAUTHORIZED,
            ['WWW-Authenticate' => 'Bearer'],
        );
    }
}
