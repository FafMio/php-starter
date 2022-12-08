<?php

namespace App\Controller;

use AttributesRouter\Router;
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

        $twig->addFunction(new TwigFunction('route', function (Router $router, string $route, $parameters = []) {
           return $router->generateUrl($route, $parameters);
        }));

        echo $twig->render($viewName, $viewData);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function page404() {
        $this->show('404.html.twig');
    }


}