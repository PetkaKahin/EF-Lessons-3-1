<?php

declare(strict_types=1);

namespace Infrastructure\Http;

use Application\Exceptions\HttpException;
use DomainException;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use TypeError;
use ValueError;

final class ExceptionHandler
{
    public function handle(Throwable $exception): Response
    {
        $status = match (true) {
            $exception instanceof HttpException => $exception->statusCode(),
            $exception instanceof JsonException => Response::HTTP_BAD_REQUEST,
            $exception instanceof InvalidArgumentException,
            $exception instanceof DomainException,
            $exception instanceof TypeError,
            $exception instanceof ValueError => Response::HTTP_UNPROCESSABLE_ENTITY,
            default => Response::HTTP_INTERNAL_SERVER_ERROR,
        };
        $headers = $exception instanceof HttpException ? $exception->headers() : [];

        return new JsonResponse(
            ['error' => $status === Response::HTTP_INTERNAL_SERVER_ERROR ? 'Internal server error' : $exception->getMessage()],
            $status,
            $headers,
        );
    }
}
