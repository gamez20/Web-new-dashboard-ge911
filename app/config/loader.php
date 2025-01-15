<?php

use Phalcon\Loader;

$loader = new Loader();

$loader->registerNamespaces(
    [
        'App\Controllers' => APP_PATH . '/controllers/',
        'App\Models'      => APP_PATH . '/models/',
    ]
);

$loader->register();
