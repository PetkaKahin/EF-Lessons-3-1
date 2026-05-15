<?php

declare(strict_types=1);

function dump(mixed $data): void
{
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
}

function dd(mixed $data): void
{
    dump($data);

    exit(1);
}