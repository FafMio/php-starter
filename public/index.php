<?php

require_once __DIR__ . './../vendor/autoload.php';
require_once __DIR__ . './../app/autoloader.php';

$router = new Router();
$match = $router->get()->match();

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . './../');
$dotenv->load();

session_start();
$session = new Session();
$account = new AccountUtils($session);

if ($match !== false) {
    $routerData = $match['target'];

    $controllerName = $routerData['controller'];
    $methodName = $routerData['method'];
    $arguments = $match['params'];

    $arguments['router'] = $router->get();
    $arguments['session'] = $session;
    $arguments['account'] = $account;
    $arguments['env'] = $dotenv;

    $controller = new $controllerName($arguments);
    $controller->$methodName($arguments);
} else {
    $controller = new MainController();
    $controller->page404();
}