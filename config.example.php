<?php

declare(strict_types=1);

return [
    'APP_URL' => 'http://localhost:5173',
    'API_TOKEN' => '123',
    'CORS_ALLOWED_ORIGIN' => 'http://localhost:5173',
    'CORS_ALLOWED_METHODS' => 'GET, POST, PATCH, DELETE, OPTIONS',
    'CORS_ALLOWED_HEADERS' => 'Content-Type, Authorization, Idempotency-Key',

    'DATABASE_PATH' => __DIR__ . '/db.sqlite',
    'MIGRATIONS_PATH' => __DIR__ . '/Infrastructure/Database/migrations',
    'WEBHOOK_URL' => 'http://nginx/webhook-receiver',

    'DEBUG' => true,
];
