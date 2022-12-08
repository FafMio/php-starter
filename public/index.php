<?php

use Controller\MainController;
use Util\AccountUtils;
use Util\Session;
use AttributesRouter\Router;
use Dotenv\Dotenv;

session_start();

require_once __DIR__ . './../vendor/autoload.php';
require_once __DIR__ . './../app/autoloader.php';

$router = null;
try {
    $router = new Router([MainController::class]);
} catch (ReflectionException $e) {
    dump($e);
}
$match = $router->match();

$dotenv = Dotenv::createImmutable(__DIR__ . './../');
$dotenv->load();

if ($match !== false) {
    $params = $match['params'];

    $params['router'] = $router;
    $params['session'] = new Session();
    $params['account'] = new AccountUtils($params['session']);
    $params['env'] = $dotenv;

    $controller = new $match['class']();
    $controller->{$match['method']}($params);
} else {
    $controller = new MainController();
    $controller->page404();
}