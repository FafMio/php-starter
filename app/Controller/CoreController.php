<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

class CoreController
{

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function show($viewName, $viewData = [])
    {
        $loader = new FilesystemLoader(__DIR__ . './../Views');
        $twig = new Environment($loader);

        $twig->addFunction(new TwigFunction('dump', function (mixed $var) {
            dump($var);
        }));

        $twig->addFunction(new TwigFunction('route', function (AltoRouter $router, string $route, $parameters = []) {
           return $router->generate($route, $parameters);
        }));


//        $path = new TwigFunction('path', function (string $routeId, array $parameters) {
//
//        });
//        $twig->addFunction('path', $path);

        echo $twig->render($viewName, $viewData);
    }


}