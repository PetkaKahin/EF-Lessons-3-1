<?php

declare(strict_types=1);

namespace Infrastructure\Http\Client;

use Application\Contracts\WebhookClientInterface;

final class WebhookClient implements WebhookClientInterface
{
    /**
     * @param array<string, mixed> $payload
     */
    public function post(string $url, array $payload): bool
    {
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\n",
                'content' => json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES),
                'ignore_errors' => true,
                'timeout' => 3,
            ],
        ]);

        $response = @file_get_contents($url, false, $context);

        if ($response === false) {
            return false;
        }

        return $this->isSuccessful($http_response_header ?? []);
    }

    /**
     * @param list<string> $headers
     */
    private function isSuccessful(array $headers): bool
    {
        $statusLine = $headers[0] ?? '';

        if (!preg_match('/\s(\d{3})\s/', $statusLine, $matches)) {
            return false;
        }

        $statusCode = (int) $matches[1];

        return $statusCode >= 200 && $statusCode < 300;
    }
}
