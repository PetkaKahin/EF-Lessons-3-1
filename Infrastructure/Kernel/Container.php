<?php

declare(strict_types=1);

namespace Infrastructure\Kernel;

use RuntimeException;

final class Container
{
    /**
     * @var array<string, callable(self): object>
     */
    private array $factories = [];

    /**
     * @var array<string, object>
     */
    private array $instances = [];

    /**
     * @param callable(self): object $factory
     */
    public function set(string $id, callable $factory): void
    {
        $this->factories[$id] = $factory;
    }

    public function instance(string $id, object $instance): void
    {
        $this->instances[$id] = $instance;
    }

    /**
     * @template T of object
     * @param class-string<T> $id
     * @return T
     */
    public function get(string $id): object
    {
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        if (!isset($this->factories[$id])) {
            throw new RuntimeException(sprintf('Service "%s" is not registered.', $id));
        }

        $this->instances[$id] = $this->factories[$id]($this);

        return $this->instances[$id];
    }
}
