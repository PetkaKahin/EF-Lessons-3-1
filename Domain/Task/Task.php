<?php

declare(strict_types=1);

namespace Domain\Task;

use DateTimeImmutable;
use DomainException;
use InvalidArgumentException;

final class Task
{
    public function __construct(
        public readonly TaskUuid          $id,
        public private(set) string        $title,
        public private(set) ?string       $description,
        public private(set) TaskStatus    $status,
        public readonly DateTimeImmutable $createdAt,
    ) {
        self::validateTitle($title);
    }

    public static function create(
        TaskUuid          $id,
        string            $title,
        DateTimeImmutable $createdAt,
        ?string           $description = null,
        TaskStatus        $status = TaskStatus::New,
    ): self {
        return new self(
            id: $id,
            title: $title,
            description: $description,
            status: $status,
            createdAt: $createdAt,
        );
    }

    public function rename(string $title): void
    {
        self::validateTitle($title);

        $this->title = $title;
    }

    public function changeDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function changeStatus(TaskStatus $status): void
    {
        if ($this->status->rank() > $status->rank()) {
            throw new DomainException(sprintf(
                'The task cannot be changed %s->%s',
                $this->status->name,
                $status->name,
            ));
        }

        $this->status = $status;
    }

    /**
     * @return array{id: string, title: string, description: string|null, status: string, createdAt: string}
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id->value,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status->value,
            'createdAt' => $this->createdAt->format(DateTimeImmutable::ATOM),
        ];
    }

    private static function validateTitle(string $title): void
    {
        if (trim($title) === '') {
            throw new InvalidArgumentException('Title is required.');
        }
    }
}
