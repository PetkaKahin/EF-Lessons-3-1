<?php

declare(strict_types=1);

namespace Application\UseCases\Task;

use Application\Contracts\RepositoryInterface;
use Domain\Task\Task;

final readonly class DeleteTaskUseCase
{
    /**
     * @param RepositoryInterface<Task> $tasks
     */
    public function __construct(
        private RepositoryInterface $tasks,
    ) {
    }

    public function execute(string $id): void
    {
        $this->tasks->delete($id);
    }
}
