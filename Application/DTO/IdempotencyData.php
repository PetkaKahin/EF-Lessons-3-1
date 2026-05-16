<?php

declare(strict_types=1);

namespace Application\DTO;

final readonly class IdempotencyData
{
    public function __construct(
        public ?string $key,
        public string  $operation,
    )
    {}
}