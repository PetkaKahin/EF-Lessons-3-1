<?php

declare(strict_types=1);

namespace Application\UseCases\Task;

use Application\Contracts\RepositoryInterface;
use Domain\Task\Task;
use Domain\Task\TaskStatus;

final readonly class UpdateTaskUseCase
{
    /**
     * @param RepositoryInterface<Task> $tasks
     */
    public function __construct(
        private RepositoryInterface $tasks,
    ) {
    }

    public function execute(
        string $id,
        array $data,
    ): Task {
        /** @var Task $task */
        $task = $this->tasks->find($id);

        if (array_key_exists('title', $data)) {
            $task->rename($data['title']);
        }

        if (array_key_exists('description', $data)) {
            $task->changeDescription($data['description']);
        }

        if (array_key_exists('status', $data)) {
            $task->changeStatus(TaskStatus::from($data['status']));
        }

        /** @var Task */
        return $this->tasks->patch($task);
    }
}
