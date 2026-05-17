<?php

declare(strict_types=1);

namespace Application\UseCases\Webhook;

use Application\Contracts\WebhookAttemptRepositoryInterface;
use Application\Contracts\WebhookClientInterface;

final readonly class RetryWebhookAttemptsUseCase
{
    public function __construct(
        private WebhookClientInterface $client,
        private WebhookAttemptRepositoryInterface $attempts,
        private string $webhookUrl,
    ) {
    }

    public function execute(int $maxAttempts = 3): int
    {
        $processed = 0;

        foreach ($this->attempts->findRetryable($maxAttempts) as $attempt) {
            $processed++;

            if ($this->client->post($this->webhookUrl, $attempt['payload'])) {
                $this->attempts->delete($attempt['id']);
                continue;
            }

            $this->attempts->increaseAttempts($attempt['id']);
        }

        return $processed;
    }
}
