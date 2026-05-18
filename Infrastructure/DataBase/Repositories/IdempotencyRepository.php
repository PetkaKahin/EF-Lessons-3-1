<?php

declare(strict_types=1);

namespace Infrastructure\DataBase\Repositories;

use Application\Contracts\IdempotencyRepositoryInterface;
use Application\DTO\Idempotency;
use Application\DTO\IdempotencyData;
use Application\Exceptions\IdempotencyKeyAlreadyExistsException;
use PDO;
use PDOException;
use PDOStatement;
use RuntimeException;

class IdempotencyRepository implements IdempotencyRepositoryInterface
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    public function save(Idempotency $item): Idempotency
    {
        $statement = $this->prepare(
            'INSERT INTO idempotency_keys (id, resource_id, request_hash) VALUES (:id, :resource_id, :request_hash)'
        );

        try {
            $statement->execute([
                'id' => $item->id,
                'resource_id' => $item->resourceId,
                'request_hash' => $item->requestHash,
            ]);
        } catch (PDOException $exception) {
            if (($exception->errorInfo[0] ?? null) === '23000') {
                throw new IdempotencyKeyAlreadyExistsException(
                    'Idempotency key already exists.',
                    previous: $exception,
                );
            }

            throw $exception;
        }

        return $item;
    }

    public function find(IdempotencyData $data): ?Idempotency
    {
        $statement = $this->prepare(
            'SELECT * FROM idempotency_keys WHERE id = :id'
        );

        $statement->execute([
            'id' => Idempotency::generateId($data),
        ]);

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        return $this->parseIdempotency($row);
    }

    /**
     * @param array<string, mixed> $row
     */
    private function parseIdempotency(array $row): Idempotency
    {
        return new Idempotency(
            id: (string) $row['id'],
            resourceId: (string) $row['resource_id'],
            requestHash: (string) $row['request_hash'],
        );
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
