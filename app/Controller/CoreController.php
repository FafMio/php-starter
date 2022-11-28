<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

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

        echo $twig->render($viewName, $viewData);
    }
}