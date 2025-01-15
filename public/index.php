<?php

use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Application;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('IS_LOCAL', true);
define('PAYCORES_HOST_NAME', IS_LOCAL ? "http://localhost/GE911/" : "https://ge911.com/");
define('PAYCORES_ATTACHMENTS', IS_LOCAL ? "paycores-01" : "ubuntu");

define('FIREBASE_API_KEY', 'AAAAQQhJj7g:APA91bFtb4kjkr30Xxi1SILGPrHvFkRbzRBTvwDHU_0DTU4NSM4eY-R1wbiF34tx1v5YwFI2nrrOPmUrr23aY3v-TSaA6ojGo0drjBuP-_KRxDqb6amf5E3Xqo0QgraqsCKIxYvcZQam');

try {
    /**
     * The FactoryDefault Dependency Injector automatically registers
     * the services that provide a full stack framework.
     */
    $di = new FactoryDefault();

    /**
     * Load configuration file
     */
    $config = include APP_PATH . '/config/config.php';

    // Registrar $config como un servicio compartido
    $di->setShared('config', function () use ($config) {
        return $config;
    });

    /**
     * Handle routes
     */
    include APP_PATH . '/config/router.php';

    /**
     * Read services
     */
    include APP_PATH . '/config/services.php';

    /**
     * Include Autoloader
     */
    include APP_PATH . '/config/loader.php';

    /**
     * Handle the request
     */
    $application = new Application($di);

    // CORRECCIÓN: Usar correctamente $_SERVER["REQUEST_URI"]
    echo str_replace(["\n", "\r", "\t"], '', $application->handle($_SERVER["REQUEST_URI"])->getContent());
} catch (\Exception $e) {
    echo $e->getMessage() . '<br>';
    echo '<pre>' . $e->getTraceAsString() . '</pre>';
}
