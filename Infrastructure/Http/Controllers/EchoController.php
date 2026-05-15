<?php

declare(strict_types=1);

namespace Infrastructure\Http\Controllers;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EchoController
{
    public function __invoke(Request $request): Response
    {
        return new JsonResponse([
            ...$request->toArray()
        ], Response::HTTP_OK);
    }
}