<?php

declare(strict_types=1);

namespace Application\Exceptions;

use RuntimeException;

final class IdempotencyKeyAlreadyExistsException extends RuntimeException
{
}
