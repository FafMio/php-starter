<?php

namespace Controller;

use AttributesRouter\Attribute\Route;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class MainController extends CoreController {

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/', name: 'main-home', methods: ['GET'])]
    public function home($arguments = [])
    {
        $session = $arguments['session'];
        $session->account = "coucou";

        $this->show('pages/home.twig', $arguments);
    }


    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/session', name: 'main-home-session', methods: ['GET'])]
    public function session($arguments = [])
    {
        $session = $arguments['session'];

        dump($session->account);

        $this->show('pages/home.twig', $arguments);
    }
}