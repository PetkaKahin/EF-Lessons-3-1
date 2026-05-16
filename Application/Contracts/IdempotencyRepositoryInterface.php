<?php

declare(strict_types=1);

namespace Application\Contracts;

use Application\DTO\Idempotency;
use Application\DTO\IdempotencyData;

interface IdempotencyRepositoryInterface
{
    public function save(Idempotency $item): Idempotency;

    public function find(IdempotencyData $data): ?Idempotency;
}
