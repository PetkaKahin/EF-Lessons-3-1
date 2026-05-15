<?php

declare(strict_types=1);

return [
    'APP_URL' => 'http://localhost:5173',
    'API_TOKEN' => '123',

    'DATABASE_PATH' => __DIR__ . '/var/app.sqlite',
    'MIGRATIONS_PATH' => __DIR__ . '/Infrastructure/Database/migrations',
    'WEBHOOK_URL' => 'http://nginx/webhook-receiver',
    'WEBHOOK_LOG_PATH' => __DIR__ . '/var/webhook.log',

    'DEBUG' => true,
];