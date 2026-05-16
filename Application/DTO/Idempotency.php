<?php

declare(strict_types=1);

namespace Application\DTO;

final readonly class Idempotency
{
    public function __construct(
        public string $id,
        public string $resourceId,
        public string $requestHash,
    ) {}

    public static function create(IdempotencyData $data, string $resourceId, string $requestHash): self
    {
        return new self(
          self::generateId($data),
          $resourceId,
          $requestHash,
        );
    }

    public static function generateId(IdempotencyData $data): string
    {
        return hash('sha256', $data->operation . ':' . $data->key);
    }
}
