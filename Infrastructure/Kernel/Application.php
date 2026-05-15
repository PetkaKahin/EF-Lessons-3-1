<?php

declare(strict_types=1);

namespace Infrastructure\Kernel;

use Infrastructure\Config\Config;
use Infrastructure\Config\Globals;
use Infrastructure\Http\ExceptionHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Application
{
    public function run(): void
    {
        $request = Request::createFromGlobals();
        $container = (new ContainerFactory())->create();
        /** @var Config $config */
        $config = $container->get(Config::class);

        $router = new Router($container);
        $registerRoutes = require Globals::ROUTE_PATH;
        $registerRoutes($router);
        $response = $this->handle($router, $request)->prepare($request);

        if ($config->get(Globals::DEBUG_NAME) === true) {
            $ms = round((microtime(true) - Globals::$appStartedAt) * 1000, 2);

            $response->headers->set(
                Globals::NAME_HEADER_APP_TIME,
                $ms . ' ms'
            );
        }

        $response->send();
    }

    private function handle(Router $router, Request $request): Response
    {
        try {
            return $router->dispatch($request);
        } catch (Throwable $exception) {
            return (new ExceptionHandler())->handle($exception);
        }
    }
}
