<?php

declare(strict_types=1);

namespace App\Core;

use App\Config\Database;
use PDO;

final class DatabaseConnection
{
    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $config = Database::get();

        $dsn = sprintf(
            '%s:host=%s;port=%d;dbname=%s;sslmode=%s',
            $config['driver'],
            $config['host'],
            $config['port'],
            $config['database'],
            $config['sslmode']
        );

        self::$connection = new PDO(
            $dsn,
            $config['username'],
            $config['password'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_STRINGIFY_FETCHES => false,
            ]
        );

        return self::$connection;
    }

    private function __construct()
    {
    }
}