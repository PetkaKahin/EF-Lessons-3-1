<?php

declare(strict_types=1);

namespace Infrastructure\DataBase;

use Infrastructure\Config\Globals;
use PDO;

class MigrationRunner
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    public function run(): void
    {
        $files = glob(Globals::MIGRATIONS_PATH . '/*.sql') ?: [];
        sort($files);

        $hasMigrationsTable = $this->hasMigrationsTable();

        foreach ($files as $file) {
            $name = basename($file);
            $quotedName = $this->pdo->quote($name);

            if ($hasMigrationsTable && $this->pdo->query("SELECT name FROM migrations WHERE name = $quotedName")->fetchColumn()) {
                continue;
            }

            $this->pdo->exec((string) file_get_contents($file));
            $hasMigrationsTable = $this->hasMigrationsTable();

            if ($hasMigrationsTable) {
                $this->pdo->exec("INSERT OR IGNORE INTO migrations (name) VALUES ($quotedName)");
            }
        }
    }

    private function hasMigrationsTable(): bool
    {
        return (bool) $this->pdo
            ->query("SELECT name FROM sqlite_master WHERE type = 'table' AND name = 'migrations'")
            ->fetchColumn();
    }
}
