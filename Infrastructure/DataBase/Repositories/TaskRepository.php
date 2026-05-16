<?php

declare(strict_types=1);

namespace Infrastructure\DataBase\Repositories;

use Application\Contracts\RepositoryInterface;
use Application\Exceptions\NotFoundException;
use DateTimeImmutable;
use Domain\Task\Task;
use Domain\Task\TaskStatus;
use Domain\Task\TaskUuid;
use InvalidArgumentException;
use PDO;
use PDOStatement;
use RuntimeException;

/**
 * @implements RepositoryInterface<Task>
 */
class TaskRepository implements RepositoryInterface
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    /**
     * @return list<Task>
     */
    public function all(): array
    {
        $statement = $this->pdo->query(
            'SELECT * FROM tasks ORDER BY createdAt ASC, id ASC'
        );

        if ($statement === false) {
            throw new RuntimeException('Failed to load tasks.');
        }

        $tasks = [];

        while (($row = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            $tasks[] = $this->parseTask($row);
        }

        return $tasks;
    }

    /**
     * @return array{items: list<Task>, nextCursor: string|null}
     */
    public function paginate(array $filters = [], int $limit = 100, ?string $cursor = null): array
    {
        $limit = max(1, min(100, $limit));
        $status = $filters['status'] ?? null;
        $where = [];
        $params = [];

        if ($status !== null && $status !== '') {
            $where[] = 'status = :status';
            $params['status'] = $status instanceof TaskStatus ? $status->value : (string) $status;
        }

        if ($cursor !== null && $cursor !== '') {
            $where[] = 'id > :cursor';
            $params['cursor'] = $cursor;
        }

        $query = 'SELECT * FROM tasks';

        if ($where !== []) {
            $query .= ' WHERE ' . implode(' AND ', $where);
        }

        $query .= ' ORDER BY id ASC LIMIT :limit';
        $statement = $this->prepare($query);

        foreach ($params as $name => $value) {
            $statement->bindValue(':' . $name, $value);
        }

        $statement->bindValue(':limit', $limit + 1, PDO::PARAM_INT);
        $statement->execute();

        $tasks = [];

        while (($row = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            $tasks[] = $this->parseTask($row);
        }

        $hasNextPage = count($tasks) > $limit;

        if ($hasNextPage) {
            array_pop($tasks);
        }

        return [
            'items' => $tasks,
            'nextCursor' => $hasNextPage && $tasks !== []
                ? $tasks[array_key_last($tasks)]->id->value
                : null,
        ];
    }

    public function save(object $item): Task
    {
        $task = $this->assertTask($item);

        $statement = $this->prepare(
            'INSERT INTO tasks (id, title, description, status, createdAt) VALUES (:id, :title, :description, :status, :createdAt)'
        );

        $statement->execute($task->toArray());

        return $task;
    }

    public function find(string $id): Task
    {
        $statement = $this->prepare(
            'SELECT * FROM tasks WHERE id = :id'
        );

        $statement->execute([
            'id' => TaskUuid::fromData($id)->value,
        ]);

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            throw NotFoundException::resource('Task', $id);
        }

        return $this->parseTask($row);
    }

    public function patch(object $item): Task
    {
        $task = $this->assertTask($item);

        $statement = $this->prepare(
            'UPDATE tasks SET title = :title, description = :description, status = :status, createdAt = :createdAt WHERE id = :id'
        );

        $statement->execute($task->toArray());

        return $task;
    }

    public function delete(string $id): void
    {
        $statement = $this->prepare('DELETE FROM tasks WHERE id = :id');
        $statement->execute([
            'id' => TaskUuid::fromData($id)->value,
        ]);

        if ($statement->rowCount() === 0) {
            throw NotFoundException::resource('Task', $id);
        }
    }

    /**
     * @param array<string, mixed> $row
     */
    private function parseTask(array $row): Task
    {
        return Task::create(
            id: TaskUuid::fromData((string) $row['id']),
            title: (string) $row['title'],
            createdAt: new DateTimeImmutable((string) $row['createdAt']),
            description: $row['description'] !== null ? (string) $row['description'] : null,
            status: TaskStatus::from((string) $row['status']),
        );
    }

    private function assertTask(object $item): Task
    {
        if (!$item instanceof Task) {
            throw new InvalidArgumentException(sprintf(
                'Expected instance of %s.',
                Task::class,
            ));
        }

        return $item;
    }

    private function prepare(string $query): PDOStatement
    {
        $statement = $this->pdo->prepare($query);

        if ($statement === false) {
            throw new RuntimeException('Failed to prepare database query.');
        }

        return $statement;
    }
}
