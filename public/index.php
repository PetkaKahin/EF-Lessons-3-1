<?php

declare(strict_types=1);

use Infrastructure\Config\Globals;
use Infrastructure\Kernel\Application;

require_once dirname(__DIR__) . '/vendor/autoload.php';

Globals::$appStartedAt = microtime(true);

new Application()->run();
