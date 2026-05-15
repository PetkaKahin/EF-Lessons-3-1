<?php

declare(strict_types=1);

namespace Application\UseCases\Task;

use Application\Contracts\RepositoryInterface;
use Domain\Task\Task;

final readonly class GetTaskUseCase
{
    /**
     * @param RepositoryInterface<Task> $tasks
     */
    public function __construct(
        private RepositoryInterface $tasks,
    ) {
    }

    public function execute(string $id): Task
    {
        /** @var Task */
        return $this->tasks->find($id);
    }
}
