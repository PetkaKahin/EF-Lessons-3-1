<?php

declare(strict_types=1);

namespace Infrastructure\Config;

/**
 * Класс содержащий все статические переменные/константы приложения
 */
class Globals
{
    // --- CONFIG FILE ---
    public const string API_TOKEN = 'API_TOKEN';
    public const string APP_URL = 'APP_URL';
    public const string DATABASE_PATH = 'DATABASE_PATH';
    public const string DEBUG = 'DEBUG';
    public const string MIGRATIONS_PATH = 'MIGRATIONS_PATH';
    public const string WEBHOOK_LOG_PATH = 'WEBHOOK_LOG_PATH';
    public const string WEBHOOK_URL = 'WEBHOOK_URL';

    // --- CONFIG APP ---
    public const string ROUTE_PATH = __DIR__ . '/../Http/Routes/routes.php';
    public const string CONFIG_PATH = __DIR__ . '/../../config.php';

    // --- DEBUG ---
    public const string NAME_HEADER_APP_TIME = 'X_App_Time';
    public static float $appStartedAt;
}
