<?php

declare(strict_types=1);

namespace Infrastructure\Http\Middleware;

use Infrastructure\Http\Middleware\Contracts\MiddlewareInterface;
use Infrastructure\Kernel\Container;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class MiddlewarePipeline
{
    public function __construct(
        private readonly Container $container,
        private readonly GlobalMiddlewareRegistry $globalMiddlewareRegistry,
    ) {
    }

    /**
     * @param callable(Request): Response $last
     */
    public function handle(Request $request, callable $last): Response
    {
        return $this->handleStack($request, $this->globalMiddlewareRegistry->all(), $last);
    }

    /**
     * @param list<class-string<MiddlewareInterface>> $middlewares
     * @param callable(Request): Response $last
     */
    public function handleStack(Request $request, array $middlewares, callable $last): Response
    {
        $next = $last;

        foreach (array_reverse($middlewares) as $middleware) {
            $next = function (Request $request) use ($middleware, $next): Response {
                return $this->resolveMiddleware($middleware)->handle($request, $next);
            };
        }

        return $next($request);
    }

    /**
     * @param class-string<MiddlewareInterface> $middleware
     */
    private function resolveMiddleware(string $middleware): MiddlewareInterface
    {
        $instance = $this->container->get($middleware);

        if (!$instance instanceof MiddlewareInterface) {
            throw new RuntimeException(sprintf('Middleware "%s" must implement MiddlewareInterface.', $middleware));
        }

        return $instance;
    }
}
