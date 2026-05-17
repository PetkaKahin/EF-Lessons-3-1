<?php

declare(strict_types=1);

namespace Infrastructure\Http\Middleware;

use Application\Exceptions\ForbiddenException;
use Application\Exceptions\UnauthorizedException;
use Infrastructure\Config\Config;
use Infrastructure\Config\Globals;
use Infrastructure\Http\Middleware\Contracts\MiddlewareInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly Config $config,
    ) {
    }

    public function handle(Request $request, callable $next): Response
    {
        $authorization = $request->headers->get('Authorization');

        if ($authorization === null || trim($authorization) === '') {
            throw UnauthorizedException::missingAuthorizationHeader();
        }

        if (!preg_match('/^Bearer\s+(.+)$/i', $authorization, $matches)) {
            throw ForbiddenException::invalidToken();
        }

        $token = trim($matches[1]);
        $expectedToken = (string) $this->config->get(Globals::API_TOKEN_NAME);

        if (!hash_equals($expectedToken, $token)) {
            throw ForbiddenException::invalidToken();
        }

        return $next($request);
    }
}
