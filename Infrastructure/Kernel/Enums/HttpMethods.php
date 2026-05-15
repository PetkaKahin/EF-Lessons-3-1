<?php

declare(strict_types=1);

namespace Infrastructure\Kernel\Enums;

enum HttpMethods: string
{
    case GET = 'GET';
    case POST = 'POST';
    case UPDATE = 'UPDATE';
    case PUT = 'PUT';
    case PATCH = 'PATCH';
    case DELETE = 'DELETE';
    case OPTIONS = 'OPTIONS';
}
