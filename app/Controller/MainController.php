<?php

namespace Controller;

use AttributesRouter\Attribute\Route;
use PHPMailer\PHPMailer\Exception;
use Service\Mailer;
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
        $this->show('pages/home.twig', $arguments);
    }
}