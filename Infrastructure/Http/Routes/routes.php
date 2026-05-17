<?php

declare(strict_types=1);

use Infrastructure\Http\Controllers\EchoController;
use Infrastructure\Http\Controllers\HeadersController;
use Infrastructure\Http\Controllers\HealthController;
use Infrastructure\Http\Controllers\TaskController;
use Infrastructure\Http\Controllers\WebhookReceiverController;
use Infrastructure\Http\Middleware\AuthMiddleware;
use Infrastructure\Kernel\Router;

return static function (
    Router $router,
) {
    $router->get('/health', [HealthController::class, '__invoke']);
    $router->get('/headers', [HeadersController::class, '__invoke']);
    $router->post('/echo', [EchoController::class, '__invoke']);
    $router->post('/webhook-receiver', [WebhookReceiverController::class, '__invoke']);

    $router->get('/tasks', [TaskController::class, 'index']);
    $router->get('/tasks/{id}', [TaskController::class, 'show']);
    $router->post('/tasks', [TaskController::class, 'create'], [AuthMiddleware::class]);
    $router->patch('/tasks/{id}', [TaskController::class, 'update'], [AuthMiddleware::class]);
    $router->delete('/tasks/{id}', [TaskController::class, 'delete'], [AuthMiddleware::class]);
};
