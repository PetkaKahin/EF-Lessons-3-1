<?php

declare(strict_types=1);

namespace Infrastructure\Http\Controllers;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HeadersController
{
    public function __invoke(Request $request): Response
    {
        $result = [
            'User-Agent' => $request->headers->get('user-agent'),
            'Accept' => $request->headers->get('accept'),
        ];

        $authorization = $request->headers->get('authorization');

        if (!empty($authorization)) {
            $result['Authorization'] = $authorization;
        }

        return new JsonResponse($result, Response::HTTP_OK);
    }
}