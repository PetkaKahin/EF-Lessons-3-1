<?php

declare(strict_types=1);

namespace Infrastructure\DataBase\Repositories;

use Application\Contracts\WebhookAttemptRepositoryInterface;
use PDO;
use PDOStatement;
use RuntimeException;

final readonly class WebhookAttemptRepository implements WebhookAttemptRepositoryInterface
{
    public function __construct(
        private PDO $pdo,
    ) {
    }

    public function save(array $payload): void
    {
        $statement = $this->prepare(
            'INSERT INTO webhook_attempts (payload_json, attempts) VALUES (:payload_json, 0)'
        );

        $statement->execute([
            'payload_json' => json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES),
        ]);
    }

    public function findRetryable(int $maxAttempts): array
    {
        $statement = $this->prepare(
            'SELECT * FROM webhook_attempts WHERE attempts < :max_attempts ORDER BY id ASC'
        );

        $statement->bindValue(':max_attempts', $maxAttempts, PDO::PARAM_INT);
        $statement->execute();

        $attempts = [];

        while (($row = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            $attempts[] = [
                'id' => (int) $row['id'],
                'payload' => json_decode((string) $row['payload_json'], true, flags: JSON_THROW_ON_ERROR),
                'attempts' => (int) $row['attempts'],
            ];
        }

        return $attempts;
    }

    public function increaseAttempts(int $id): void
    {
        $statement = $this->prepare(
            'UPDATE webhook_attempts SET attempts = attempts + 1 WHERE id = :id'
        );

        $statement->execute(['id' => $id]);
    }

    public function delete(int $id): void
    {
        $statement = $this->prepare('DELETE FROM webhook_attempts WHERE id = :id');
        $statement->execute(['id' => $id]);
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
