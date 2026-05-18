<?php

declare(strict_types=1);

namespace Application\UseCases\Task;

use Application\Contracts\IdempotencyRepositoryInterface;
use Application\Contracts\RepositoryInterface;
use Application\Contracts\TransactionManagerInterface;
use Application\DTO\Idempotency;
use Application\DTO\IdempotencyData;
use Application\Exceptions\ConflictException;
use Application\Exceptions\IdempotencyKeyAlreadyExistsException;
use DateTimeImmutable;
use Domain\Task\Task;
use Domain\Task\TaskStatus;
use Domain\Task\TaskUuid;
use RuntimeException;

final readonly class CreateTaskUseCase
{
    /**
     * @param RepositoryInterface<Task> $tasks
     */
    public function __construct(
        private RepositoryInterface $tasks,
        private IdempotencyRepositoryInterface $idempotency,
        private TransactionManagerInterface $transactionManager,
    ) {
    }

    public function execute(
        string $title,
        IdempotencyData $idempotencyData,
        ?string $description = null,
        ?string $status = null,
        array $requestBody = [],
    ): Task
    {
        $requestHash = $this->makeRequestBodyHash($requestBody ?: [
            'title' => $title,
            'description' => $description,
            'status' => $status ?? TaskStatus::New->value,
        ]);

        if ($idempotencyData->key === null) {
            return $this->createTask($title, $description, $status);
        }

        try {
            return $this->transactionManager->transactional(
                fn (): Task => $this->createOrReturnExistingTask(
                    $idempotencyData,
                    $requestHash,
                    $title,
                    $description,
                    $status,
                ),
            );
        } catch (IdempotencyKeyAlreadyExistsException) {
            return $this->findTaskCreatedBySameIdempotencyRequest($idempotencyData, $requestHash)
                ?? throw new RuntimeException('Failed to load idempotency result.');
        }
    }

    private function createOrReturnExistingTask(
        IdempotencyData $idempotencyData,
        string $requestHash,
        string $title,
        ?string $description,
        ?string $status,
    ): Task {
        $alreadyCreatedTask = $this->findTaskCreatedBySameIdempotencyRequest($idempotencyData, $requestHash);

        if ($alreadyCreatedTask !== null) {
            return $alreadyCreatedTask;
        }

        $task = $this->createTask($title, $description, $status);
        $this->saveIdempotencyResult($idempotencyData, $task, $requestHash);

        return $task;
    }

    private function createTask(string $title, ?string $description, ?string $status): Task
    {
        $task = Task::create(
            id: TaskUuid::fromData(TaskUuid::generateUuid()),
            title: $title,
            createdAt: new DateTimeImmutable(),
            description: $description,
            status: TaskStatus::from($status ?? TaskStatus::New->value),
        );

        /** @var Task $task */
        $task = $this->tasks->save($task);

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

    /**
     * @param array<mixed> $requestBody
     */
    private function makeRequestBodyHash(array $requestBody): string
    {
        $requestBodyWithStableKeyOrder = $this->sortObjectKeysRecursively($requestBody);
        $json = json_encode($requestBodyWithStableKeyOrder, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        if ($json === false) {
            throw new RuntimeException('Failed to encode request body for idempotency hash.');
        }

        return hash('sha256', $json);
    }

    private function sortObjectKeysRecursively(mixed $value): mixed
    {
        if (!is_array($value)) {
            return $value;
        }

        if (array_is_list($value)) {
            return array_map(fn (mixed $item): mixed => $this->sortObjectKeysRecursively($item), $value);
        }

        // Сортируем, чтобы порядок полей в JSON не влиял на хэш
        ksort($value);

        foreach ($value as $key => $item) {
            $value[$key] = $this->sortObjectKeysRecursively($item);
        }

        return $value;
    }
}
