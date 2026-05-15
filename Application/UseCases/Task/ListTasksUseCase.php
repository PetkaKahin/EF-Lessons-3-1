<?php

declare(strict_types=1);

namespace Application\UseCases\Task;

use Application\Contracts\RepositoryInterface;
use Domain\Task\Task;
use Domain\Task\TaskStatus;

final readonly class ListTasksUseCase
{
    /**
     * @param RepositoryInterface<Task> $tasks
     */
    public function __construct(
        private RepositoryInterface $tasks,
    ) {
    }

    /**
     * @return array{items: list<Task>, nextCursor: string|null}
     */
    public function execute(?string $status = null, int $limit = 100, ?string $cursor = null): array
    {
        return $this->tasks->paginate(
            filters: [
                'status' => $status !== null ? TaskStatus::from($status) : null,
            ],
            limit: $limit,
            cursor: $cursor,
        );
    }
}
