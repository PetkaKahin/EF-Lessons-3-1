<?php

namespace Infrastructure\Kernel;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Router
{
    /**
     * @var array<string, callable>
     */
    private array $routes = [];

    public function __construct() {

    }

    /**
     * @param callable(Request): Response $handler
     */
    public function get(string $path, callable $handler): void
    {
        $this->addRoute(Request::METHOD_GET, $path, $handler);
    }

    /**
     * @param callable(Request): Response $handler
     */
    public function post(string $path, callable $handler): void
    {
        $this->addRoute(Request::METHOD_POST, $path, $handler);
    }

    /**
     * @param callable(Request): Response $handler
     */
    public function patch(string $path, callable $handler): void
    {
        $this->addRoute(Request::METHOD_PATCH, $path, $handler);
    }

    /**
     * @param callable(Request): Response $handler
     */
    public function delete(string $path, callable $handler): void
    {
        $this->addRoute(Request::METHOD_DELETE, $path, $handler);
    }

    public function dispatch(Request $request): Response
    {
        $key = $this->getRouteKey($request->getMethod(), $request->getPathInfo());
        return $this->routes[$key]($request);
    }

    private function addRoute(string $method, string $path, callable $handler): void
    {
        $key = $this->getRouteKey($method, $path);
        $this->routes[$key] = $handler;
    }

    private function getRouteKey(string $method, string $path): string
    {
        return "{$method} {$path}";
    }
}