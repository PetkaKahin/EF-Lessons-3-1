<?php

declare(strict_types=1);

namespace Application\UseCases\Task;

use Application\Contracts\RepositoryInterface;
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
    ) {
    }

    public function execute(string $title, ?string $description = null, ?string $status = null): Task
    {
        $task = Task::create(
            id: TaskUuid::fromData(TaskUuid::generateUuid()),
            title: $title,
            createdAt: new DateTimeImmutable(),
            description: $description,
            status: TaskStatus::from($status ?? TaskStatus::New->value),
        );

        /** @var Task */
        return $this->tasks->save($task);
    }
}
