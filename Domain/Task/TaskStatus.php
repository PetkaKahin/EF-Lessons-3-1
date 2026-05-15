<?php

declare(strict_types=1);

namespace Domain\Task;

enum TaskStatus: string
{
    case New        = 'new';
    case InProgress = 'in_progress';
    case Done       = 'done';

    /**
     * Возвращает ранг отсортированных статусов от начальной до финальной стадии
     */
    public function rank(): int
    {
        return match ($this) {
            self::New => 0,
            self::InProgress => 1,
            self::Done => 2,
        };
    }
}
