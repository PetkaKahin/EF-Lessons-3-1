<?php

declare(strict_types=1);

namespace Infrastructure\Http\Controllers;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class HealthController
{
    public function __invoke(): Response
    {
        return new JsonResponse([
            'status' => 'ok',
        ], Response::HTTP_OK);
    }
}