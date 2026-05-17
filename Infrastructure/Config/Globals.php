<?php

declare(strict_types=1);

namespace Infrastructure\Config;

/**
 * Класс содержащий все статические переменные/константы приложения
 */
class Globals
{
    // --- CONFIG FILE ---
    public const string API_TOKEN_NAME = 'API_TOKEN';
    public const string APP_URL_NAME = 'APP_URL';
    public const string CORS_ALLOWED_HEADERS_NAME = 'CORS_ALLOWED_HEADERS';
    public const string CORS_ALLOWED_METHODS_NAME = 'CORS_ALLOWED_METHODS';
    public const string CORS_ALLOWED_ORIGIN_NAME = 'CORS_ALLOWED_ORIGIN';
    public const string DATABASE_PATH_NAME = 'DATABASE_PATH';
    public const string DEBUG_NAME = 'DEBUG';
    public const string MIGRATIONS_PATH_NAME = 'MIGRATIONS_PATH';
    public const string WEBHOOK_LOG_PATH_NAME = 'WEBHOOK_LOG_PATH';
    public const string WEBHOOK_URL_NAME = 'WEBHOOK_URL';

    // --- CONFIG APP ---
    public const string ROUTE_PATH = __DIR__ . '/../Http/Routes/routes.php';
    public const string CONFIG_PATH = __DIR__ . '/../../config.php';
    public const string MIGRATIONS_PATH = __DIR__ . '/../DataBase/Migrations';

    // --- DEBUG ---
    public const string NAME_HEADER_APP_TIME = 'X_App_Time';
    public static float $appStartedAt;
}
