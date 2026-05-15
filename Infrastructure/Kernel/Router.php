<?php

declare(strict_types=1);

namespace Infrastructure\Kernel;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Router
{
    /**
     * @var list<Route>
     */
    private array $routes = [];

    public function __construct(
        private readonly Container $container,
    ) {
    }

    /**
     * @param callable(Request): Response|array{class-string, string} $handler
     */
    public function get(string $path, mixed $handler): void
    {
        $this->addRoute(Request::METHOD_GET, $path, $handler);
    }

    /**
     * @param callable(Request): Response|array{class-string, string} $handler
     */
    public function post(string $path, mixed $handler): void
    {
        $this->addRoute(Request::METHOD_POST, $path, $handler);
    }

    /**
     * @param callable(Request): Response|array{class-string, string} $handler
     */
    public function patch(string $path, mixed $handler): void
    {
        $this->addRoute(Request::METHOD_PATCH, $path, $handler);
    }

    /**
     * @param callable(Request): Response|array{class-string, string} $handler
     */
    public function delete(string $path, mixed $handler): void
    {
        $this->addRoute(Request::METHOD_DELETE, $path, $handler);
    }

    public function dispatch(Request $request): Response
    {
        foreach ($this->routes as $route) {
            if ($route->matches($request)) {
                return $this->call($route->handler, $request);
            }
        }

        return new Response('Not found.', Response::HTTP_NOT_FOUND);
    }

    private function addRoute(string $method, string $path, mixed $handler): void
    {
        $this->routes[] = new Route($method, $path, $handler);
    }

    private function call(mixed $handler, Request $request): Response
    {
        if (is_array($handler)) {
            $controller = $this->container->get($handler[0]);

            return $controller->{$handler[1]}($request);
        }

        return $handler($request);
    }
}
