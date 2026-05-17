<?php

declare(strict_types=1);

namespace Application\UseCases\Webhook;

use Application\Contracts\WebhookAttemptRepositoryInterface;
use Application\Contracts\WebhookClientInterface;
use DateTimeImmutable;
use Domain\Task\Task;

final readonly class SendTaskDoneWebhookUseCase
{
    public function __construct(
        private WebhookClientInterface $client,
        private WebhookAttemptRepositoryInterface $attempts,
        private string $webhookUrl,
    ) {
    }

    public function execute(Task $task): void
    {
        $payload = [
            'taskId' => $task->id->value,
            'status' => $task->status->value,
            'occurredAt' => (new DateTimeImmutable())->format(DateTimeImmutable::ATOM),
        ];

        if (!$this->client->post($this->webhookUrl, $payload)) {
            $this->attempts->save($payload);
        }
    }
}
