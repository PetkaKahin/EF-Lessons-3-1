<?php

declare(strict_types=1);

use Application\UseCases\Webhook\RetryWebhookAttemptsUseCase;
use Infrastructure\Kernel\ContainerFactory;

require __DIR__ . '/../../vendor/autoload.php';

$container = (new ContainerFactory())->create();

/** @var RetryWebhookAttemptsUseCase $retryWebhooks */
$retryWebhooks = $container->get(RetryWebhookAttemptsUseCase::class);

do {
    $processed = $retryWebhooks->execute();

    if ($processed > 0) {
        sleep(5);
    }
} while ($processed > 0);
