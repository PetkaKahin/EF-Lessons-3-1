<?php

declare(strict_types=1);

namespace Infrastructure\DataBase;

use Application\Contracts\TransactionManagerInterface;
use PDO;
use Throwable;

final readonly class PdoTransactionManager implements TransactionManagerInterface
{
    public function __construct(
        private PDO $pdo,
    ) {
    }

    public function transactional(callable $callback): mixed
    {
        // На случай, если транзакция уже выполняется, чтобы не создавать новую
        if ($this->pdo->inTransaction()) {
            return $callback();
        }

        $this->pdo->beginTransaction();

        try {
            $result = $callback();
            $this->pdo->commit();

            return $result;
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $exception;
        }
    }
}
