<?php

declare(strict_types=1);

namespace Application\Exceptions;

use RuntimeException;

abstract class HttpException extends RuntimeException
{
    public function __construct(
        string $message,
        private readonly int $statusCode,
    ) {
        parent::__construct($message);
    }

    public function statusCode(): int
    {
        return $this->statusCode;
    }
}
