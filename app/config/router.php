<?php

use Phalcon\Mvc\Router;

// Crea una instancia del enrutador
$router = $di->getRouter();

// Define las rutas
$router->add(
    '/',
    [
        'controller' => 'index',
        'action'     => 'index',
    ]
);

$router->add(
    '/about',
    [
        'controller' => 'index',
        'action'     => 'about',
    ]
);

// Aquí no pasas parámetros a `handle()`
$router->handle($_SERVER['REQUEST_URI']);
