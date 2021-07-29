<?php

namespace Wijourdil\LaravelAccountLock;

use InvalidArgumentException;

/**
 * @throws InvalidArgumentException
 */
function table_name_for_auth_provider(string $provider): string
{
    $config = config("auth.providers.$provider");

    if (empty($config)) {
        throw new InvalidArgumentException("Auth provider '$provider' cannot be found in config('auth.providers')");
    }

    if (isset($config['table'])) {
        return $config['table'];
    } elseif (isset($config['model'])) {
        $class = $config['model'];

        return (new $class())->getTable();
    } else {
        throw new InvalidArgumentException("Auth provider '$provider' does not have table or model defined.");
    }
}
