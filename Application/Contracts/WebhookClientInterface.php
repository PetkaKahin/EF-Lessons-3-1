<?php

declare(strict_types=1);

namespace Application\Contracts;

interface WebhookClientInterface
{
    /**
     * @param array<string, mixed> $payload
     */
    public function post(string $url, array $payload): bool;
}
