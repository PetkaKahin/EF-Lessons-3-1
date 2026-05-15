<?php

declare(strict_types=1);

namespace Application\Contracts;

/**
 * @template TEntity of object
 */
interface RepositoryInterface
{
    /**
     * @return list<TEntity>
     */
    public function all(): array;

    /**
     * @param array<string, mixed> $filters
     * @return array{items: list<TEntity>, nextCursor: string|null}
     */
    public function paginate(array $filters = [], int $limit = 100, ?string $cursor = null): array;

    /**
     * @param TEntity $item
     * @return TEntity
     */
    public function save(object $item): object;

    /**
     * @param string $id
     * @return TEntity
     */
    public function find(string $id): object;

    /**
     * @param TEntity $item
     * @return TEntity
     */
    public function patch(object $item): object;

    public function delete(string $id): void;
}
