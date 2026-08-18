<?php

declare(strict_types=1);

/**
 * @var string $content
 * @var string $title
 */

use App\Core\View;

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="/css/app.css">

    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>
</head>

<body>
    <div class="container-layout">
        <?php View::component("header"); ?>

        <main>
            <?= $content ?>
        </main>

        <?php View::component("footer"); ?>
    </div>
</body>

</html>