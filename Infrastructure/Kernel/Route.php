<?php

declare(strict_types=1);

namespace Infrastructure\Kernel;

use Symfony\Component\HttpFoundation\Request;

final readonly class Route
{
    public function __construct(
        private string $method,
        private string $path,
        public mixed $handler,
    ) {
    }

    public function matches(Request $request): bool
    {
        if (!$request->isMethod($this->method)) {
            return false;
        }

        $routeParts = $this->splitPath($this->path);
        $requestParts = $this->splitPath($request->getPathInfo());

        if (count($routeParts) !== count($requestParts)) {
            return false;
        }

        foreach ($routeParts as $index => $routePart) {
            if (str_starts_with($routePart, '{') && str_ends_with($routePart, '}')) {
                $request->attributes->set(trim($routePart, '{}'), $requestParts[$index]);

                continue;
            }

            if ($routePart !== $requestParts[$index]) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return list<string>
     */
    private function splitPath(string $path): array
    {
        $path = trim(rawurldecode($path), '/');

        return $path === '' ? [] : explode('/', $path);
    }
}
