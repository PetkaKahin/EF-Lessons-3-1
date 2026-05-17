<?php

declare(strict_types=1);

namespace Application\UseCases\Task;

use Application\Contracts\RepositoryInterface;
use Application\UseCases\Webhook\SendTaskDoneWebhookUseCase;
use Domain\Task\Task;
use Domain\Task\TaskStatus;

final readonly class UpdateTaskUseCase
{
    /**
     * @param RepositoryInterface<Task> $tasks
     */
    public function __construct(
        private RepositoryInterface $tasks,
        private SendTaskDoneWebhookUseCase $sendTaskDoneWebhook,
    ) {
    }

    public function execute(
        string $id,
        array $data,
    ): Task {
        /** @var Task $task */
        $task = $this->tasks->find($id);
        $oldStatus = $task->status;

        if (array_key_exists('title', $data)) {
            $task->rename($data['title']);
        }

        if (array_key_exists('description', $data)) {
            $task->changeDescription($data['description']);
        }

        if (array_key_exists('status', $data)) {
            $task->changeStatus(TaskStatus::from($data['status']));
        }

        /** @var Task $task */
        $task = $this->tasks->patch($task);

        if ($oldStatus !== TaskStatus::Done && $task->status === TaskStatus::Done) {
            $this->sendTaskDoneWebhook->execute($task);
        }

        return $task;
    }
}
