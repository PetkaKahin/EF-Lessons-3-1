<?php

declare(strict_types=1);

namespace Infrastructure\Http\Middleware\Contracts;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface MiddlewareInterface
{
    public function handle(Request $request, callable $next): Response;
}
