<?php

declare(strict_types=1);

namespace Infrastructure\Http\Middleware;

use Infrastructure\Config\Config;
use Infrastructure\Config\Globals;
use Infrastructure\Http\Middleware\Contracts\MiddlewareInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CorsMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly Config $config,
    ) {
    }

    public function handle(Request $request, callable $next): Response
    {
        if ($request->isMethod(Request::METHOD_OPTIONS)) {
            return $this->addCorsHeaders(new Response('', Response::HTTP_NO_CONTENT));
        }

        return $this->addCorsHeaders($next($request));
    }

    private function addCorsHeaders(Response $response): Response
    {
        $response->headers->set('Access-Control-Allow-Origin', (string) $this->config->get(Globals::CORS_ALLOWED_ORIGIN_NAME));
        $response->headers->set('Access-Control-Allow-Methods', (string) $this->config->get(Globals::CORS_ALLOWED_METHODS_NAME));
        $response->headers->set('Access-Control-Allow-Headers', (string) $this->config->get(Globals::CORS_ALLOWED_HEADERS_NAME));

        return $response;
    }
}
