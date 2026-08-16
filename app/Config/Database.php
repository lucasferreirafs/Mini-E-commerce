<?php

declare(strict_types=1);

namespace App\Config;

use App\Core\Env;

final class Database
{
    /**
     * @return array{
     *     driver: string,
     *     host: string,
     *     port: int,
     *     database: string,
     *     username: string,
     *     password: string,
     *     sslmode: string
     * }
     */
    public static function get(): array
    {
        return [
            'driver' => Env::required('DB_CONNECTION'),
            'host' => Env::required('DB_HOST'),
            'port' => Env::int('DB_PORT', 5432),
            'database' => Env::required('DB_DATABASE'),
            'username' => Env::required('DB_USERNAME'),
            'password' => Env::required('DB_PASSWORD'),
            'sslmode' => Env::get('DB_SSLMODE', 'require'),
        ];
    }
}