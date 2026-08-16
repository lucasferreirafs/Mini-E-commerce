<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

final class View
{
    public static function render(
        string $view,
        array $data = [],
        string $layout = 'main'
    ): void {
        $root = dirname(__DIR__, 2);

        $viewFile = $root . '/views/' . $view . '.php';
        $layoutFile = $root . '/views/layouts/' . $layout . '.php';

        if (!is_file($viewFile)) {
            throw new RuntimeException(
                sprintf('View "%s" não encontrada.', $view)
            );
        }

        if (!is_file($layoutFile)) {
            throw new RuntimeException(
                sprintf('Layout "%s" não encontrado.', $layout)
            );
        }

        extract($data, EXTR_SKIP);

        ob_start();

        require $viewFile;

        $content = ob_get_clean();

        require $layoutFile;
    }

    public static function component(
        string $component,
        array $data = []
    ): void {
        $projectRoot = dirname(__DIR__, 2);

        $componentFile = sprintf(
            '%s/views/components/%s.php',
            $projectRoot,
            $component
        );

        if (!is_file($componentFile)) {
            throw new RuntimeException(
                sprintf(
                    'Componente "%s" não encontrado.',
                    $component
                )
            );
        }

        extract($data, EXTR_SKIP);

        require $componentFile;
    }
}