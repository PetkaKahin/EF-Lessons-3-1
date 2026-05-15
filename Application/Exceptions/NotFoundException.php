<?php

declare(strict_types=1);

namespace Application\Exceptions;

use Symfony\Component\HttpFoundation\Response;

final class NotFoundException extends HttpException
{
    public static function resource(string $name, string $id): self
    {
        return new self(
            sprintf('%s "%s" not found.', $name, $id),
            Response::HTTP_NOT_FOUND,
        );
    }
}
