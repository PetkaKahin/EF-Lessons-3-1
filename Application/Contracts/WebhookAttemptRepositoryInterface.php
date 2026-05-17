<?php

declare(strict_types=1);

namespace Application\Contracts;

interface WebhookAttemptRepositoryInterface
{
    /**
     * @param array<string, mixed> $payload
     */
    public function save(array $payload): void;

    /**
     * @return list<array{id: int, payload: array<string, mixed>, attempts: int}>
     */
    public function findRetryable(int $maxAttempts): array;

    public function increaseAttempts(int $id): void;

    public function delete(int $id): void;
}
