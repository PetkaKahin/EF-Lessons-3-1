<?php

declare(strict_types=1);

namespace Application\Contracts;

interface TransactionManagerInterface
{
    /**
     * @template TResult
     * @param callable(): TResult $callback
     * @return TResult
     */
    public function transactional(callable $callback): mixed;
}
