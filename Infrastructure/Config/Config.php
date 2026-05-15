<?php

declare(strict_types=1);

namespace Infrastructure\Config;

final class Config
{
    /**
     * @var array<string, mixed>
     */
    private array $config;

    public function __construct()
    {
        $this->config = require Globals::CONFIG_PATH;
    }

    public function get(string $key): mixed
    {
        return $this->config[$key] ?? null;
    }
}
