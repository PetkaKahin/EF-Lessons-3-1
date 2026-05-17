<?php

declare(strict_types=1);

namespace Infrastructure\Http\Controllers;

use Infrastructure\Config\Globals;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class WebhookReceiverController
{
    public function __invoke(Request $request): Response
    {
        $payload = $request->toArray();
        $logPath = Globals::WEBHOOK_LOG_PATH;
        $logDirectory = dirname($logPath);

        if (!is_dir($logDirectory) && !mkdir($logDirectory, 0777, true) && !is_dir($logDirectory)) {
            throw new RuntimeException('Failed to create webhook log directory.');
        }

        $line = json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES) . PHP_EOL;

        if (file_put_contents($logPath, $line, FILE_APPEND | LOCK_EX) === false) {
            throw new RuntimeException('Failed to write webhook payload.');
        }

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
