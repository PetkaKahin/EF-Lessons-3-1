<?php

declare(strict_types=1);

namespace Application\UseCases\Task;

use Application\Contracts\IdempotencyRepositoryInterface;
use Application\Contracts\RepositoryInterface;
use Application\DTO\Idempotency;
use Application\DTO\IdempotencyData;
use Application\Exceptions\ConflictException;
use DateTimeImmutable;
use Domain\Task\Task;
use Domain\Task\TaskStatus;
use Domain\Task\TaskUuid;

final readonly class CreateTaskUseCase
{
    /**
     * @param RepositoryInterface<Task> $tasks
     */
    public function __construct(
        private RepositoryInterface $tasks,
        private IdempotencyRepositoryInterface $idempotency,
    ) {
    }

    public function execute(
        string $title,
        IdempotencyData $data,
        ?string $description = null,
        ?string $status = null,
    ): Task
    {
        $requestHash = $this->makeRequestHash($title, $description, $status);

        $alreadyCreatedTask = $this->findTaskCreatedBySameIdempotencyRequest($data, $requestHash);

        if ($alreadyCreatedTask !== null) {
            return $alreadyCreatedTask;
        }

        $task = Task::create(
            id: TaskUuid::fromData(TaskUuid::generateUuid()),
            title: $title,
            createdAt: new DateTimeImmutable(),
            description: $description,
            status: TaskStatus::from($status ?? TaskStatus::New->value),
        );

        /** @var Task $task */
        $task = $this->tasks->save($task);

        $this->saveIdempotencyResult($data, $task, $requestHash);

        return $task;
    }

    private function findTaskCreatedBySameIdempotencyRequest(IdempotencyData $data, string $requestHash): ?Task
    {
        if ($data->key === null) {
            return null;
        }

        $idempotency = $this->idempotency->find($data);

        if ($idempotency === null) {
            return null;
        }

        if ($idempotency->requestHash !== $requestHash) {
            throw ConflictException::idempotencyKeyBodyMismatch();
        }

        /** @var Task */
        return $this->tasks->find($idempotency->resourceId);
    }

    private function saveIdempotencyResult(IdempotencyData $data, Task $task, string $requestHash): void
    {
        if ($data->key === null) {
            return;
        }

        $this->idempotency->save(Idempotency::create(
            data: $data,
            resourceId: $task->id->value,
            requestHash: $requestHash,
        ));
    }

    private function makeRequestHash(string $title, ?string $description, ?string $status): string
    {
        $payload = [
            'title' => $title,
            'description' => $description,
            'status' => $status ?? TaskStatus::New->value,
        ];

        return hash('sha256', (string) json_encode($payload));
    }
}
