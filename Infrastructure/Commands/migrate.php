<?php

declare(strict_types=1);

use Infrastructure\DataBase\MigrationRunner;
use Infrastructure\Kernel\ContainerFactory;

require __DIR__ . '/../../vendor/autoload.php';

$container = (new ContainerFactory())->create();

/** @var MigrationRunner $runner */
$runner = $container->get(MigrationRunner::class);
$runner->run();

echo "done" . PHP_EOL;
