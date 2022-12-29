<?php

namespace Controller;

use AttributesRouter\Router;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

class CoreController
{
    public Environment $twig;

    public function __construct()
    {
        $loader = new FilesystemLoader(__DIR__ . '/../Views');
        $this->twig = new Environment($loader);

        $this->twig->addFunction(new TwigFunction('dump', function (mixed $var, mixed ...$vars) {
            dump($var, $vars);
        }));

        $this->twig->addFunction(new TwigFunction('route', function (Router $router, string $route, $parameters = []) {
            return $router->generateUrl($route, $parameters);
        }));
    }


    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function show($viewName, $viewData = [])
    {
        echo $this->twig->render($viewName, $viewData);
    }

    /**
     * @param array $arguments
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function page404(array $arguments = [])
    {
        header('HTTP/1.0 404 Not Found');
        $this->show('404.twig', $arguments);
    }


}