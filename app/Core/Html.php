<?php

declare(strict_types=1);

namespace App\Core;

final class Html
{
    public static function escape(
        string|int|null $value
    ): string {
        return htmlspecialchars(
            (string) ($value ?? ''),
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        );
    }
}