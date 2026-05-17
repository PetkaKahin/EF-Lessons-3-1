<?php

declare(strict_types=1);

namespace Infrastructure\Http\Middleware;

use Infrastructure\Http\Middleware\Contracts\MiddlewareInterface;

final class GlobalMiddlewareRegistry
{
    /**
     * @return list<class-string<MiddlewareInterface>>
     */
    public function all(): array
    {
        return [
            CorsMiddleware::class,
        ];
    }
}
