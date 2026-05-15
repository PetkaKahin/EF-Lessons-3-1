<?php

declare(strict_types=1);

namespace Infrastructure\Kernel;

use Infrastructure\Config\Config;
use Infrastructure\Config\Globals;
use Symfony\Component\HttpFoundation\Request;

class Application
{
    public function run(): void
    {
        $request = Request::createFromGlobals();
        $config = new Config();

        $router = new Router();
        $registerRoutes = require Globals::ROUTE_PATH;
        $registerRoutes($router);
        $response = $router->dispatch($request)->prepare($request);

        if ($config->get(Globals::DEBUG) === true) {
            $ms = round((microtime(true) - Globals::$appStartedAt) * 1000, 2);

            $response->headers->set(
                Globals::NAME_HEADER_APP_TIME,
                $ms . ' ms'
            );
        }

        $response->send();
    }
}
