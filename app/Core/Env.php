<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

final class Env
{
    public static function get(
        string $key,
        mixed $default = null
    ): mixed {
        return $_ENV[$key] ?? $_SERVER[$key] ?? $default;
    }

    public static function required(string $key): string
    {
        $value = self::get($key);

        if (!is_string($value) || trim($value) === '') {
            throw new RuntimeException(
                sprintf('A variável de ambiente "%s" é obrigatória.', $key)
            );
        }

        return $value;
    }

    public static function int(
        string $key,
        int $default
    ): int {
        $value = self::get($key);

        if ($value === null) {
            return $default;
        }

        if (filter_var($value, FILTER_VALIDATE_INT) === false) {
            throw new RuntimeException(
                sprintf('A variável "%s" precisa ser um número inteiro.', $key)
            );
        }

        return (int) $value;
    }

    public static function bool(
        string $key,
        bool $default = false
    ): bool {
        $value = self::get($key);

        if ($value === null) {
            return $default;
        }

        return filter_var(
            $value,
            FILTER_VALIDATE_BOOLEAN
        );
    }
}
