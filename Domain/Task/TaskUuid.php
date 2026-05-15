<?php

declare(strict_types=1);

namespace Domain\Task;

use InvalidArgumentException;

final readonly class TaskUuid
{
    private const string UUID_PATTERN = '/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/';

    private function __construct(
        public string $value,
    ) {
    }

    public static function fromData(string $value): self
    {
        $value = strtolower(trim($value));

        if (!self::isValid($value)) {
            throw new InvalidArgumentException('Task id must be a UUID.');
        }

        return new self($value);
    }

    private static function isValid(string $value): bool
    {
        return preg_match(self::UUID_PATTERN, $value) === 1;
    }

    public static function generateUuid(): string
    {
        // uniqid() по докам просто для uuid не очень подходит, я попросил нейронку сделать генерацию
        $bytes = random_bytes(16);

        $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40);
        $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4));
    }
}
