<?php

declare(strict_types=1);

namespace Infrastructure\Kernel;

use Infrastructure\Http\Middleware\Contracts\MiddlewareInterface;
use Infrastructure\Http\Middleware\MiddlewarePipeline;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        private readonly MiddlewarePipeline $middlewarePipeline,
    ) {
    }

    /**
     * @param array{class-string, string} $handler
     * @param list<class-string<MiddlewareInterface>> $middlewares
     */
    public function get(string $path, array $handler, array $middlewares = []): void
    {
        $this->addRoute(Request::METHOD_GET, $path, $handler, $middlewares);
    }

    /**
     * @param array{class-string, string} $handler
     * @param list<class-string<MiddlewareInterface>> $middlewares
     */
    public function post(string $path, array $handler, array $middlewares = []): void
    {
        $this->addRoute(Request::METHOD_POST, $path, $handler, $middlewares);
    }

    /**
     * @param array{class-string, string} $handler
     * @param list<class-string<MiddlewareInterface>> $middlewares
     */
    public function patch(string $path, array $handler, array $middlewares = []): void
    {
        $this->addRoute(Request::METHOD_PATCH, $path, $handler, $middlewares);
    }

    /**
     * @param array{class-string, string} $handler
     * @param list<class-string<MiddlewareInterface>> $middlewares
     */
    public function delete(string $path, array $handler, array $middlewares = []): void
    {
        $this->addRoute(Request::METHOD_DELETE, $path, $handler, $middlewares);
    }

    public function dispatch(Request $request): Response
    {
        foreach ($this->routes as $route) {
            if ($route->matches($request)) {
                return $this->call($route->handler, $route->middlewares, $request);
            }
        }

        return new JsonResponse(['error' => 'Not found.'], Response::HTTP_NOT_FOUND);
    }

    /**
     * @param array{class-string, string} $handler
     * @param list<class-string<MiddlewareInterface>> $middlewares
     */
    private function addRoute(string $method, string $path, array $handler, array $middlewares): void
    {
        $this->routes[] = new Route($method, $path, $handler, $middlewares);
    }

    /**
     * @param array{class-string, string} $handler
     * @param list<class-string<MiddlewareInterface>> $middlewares
     */
    private function call(array $handler, array $middlewares, Request $request): Response
    {
        return $this->middlewarePipeline->handleStack(
            $request,
            $middlewares,
            fn (Request $request): Response => $this->callHandler($handler, $request),
        );
    }

    /**
     * @param array{class-string, string} $handler
     */
    private function callHandler(array $handler, Request $request): Response
    {
        return $this->container->get($handler[0])->{$handler[1]}($request);
    }
}
