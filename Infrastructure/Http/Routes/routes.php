<?php

declare(strict_types=1);

use Infrastructure\Http\Controllers\EchoController;
use Infrastructure\Http\Controllers\HeadersController;
use Infrastructure\Http\Controllers\HealthController;
use Infrastructure\Kernel\Router;

return static function (
    Router $router,
) {
    $router->get('/health', new HealthController());
    $router->get('/headers', new HeadersController());
    $router->post('/echo', new EchoController());
};