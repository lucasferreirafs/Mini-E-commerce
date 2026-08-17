<?php

declare(strict_types=1);

use App\Controllers\HomeController;
use App\Core\Router;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$router = new Router();

$homeController = new HomeController();
$router->get(
    '/',
    [$homeController, 'index']
);

$router->dispatch(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI']
);