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

    /**
     * @throws Exception
     */
    #[Route('/test', name: 'test', methods: ['GET'])]
    public function session($arguments = [])
    {
        $mailer = new Mailer();

        $mailer->getMailer()->addAddress('fafmio43@gmail.com');

        $mailer->getMailer()->Subject = 'Here is the subject';
        $mailer->getMailer()->Body    = 'This is the HTML message body in bold!';

        try {
            $mailer->getMailer()->send();
            echo 'Message has been sent';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mailer->getMailer()->ErrorInfo}";
        }

    }
}