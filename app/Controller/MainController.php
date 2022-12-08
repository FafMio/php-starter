<?php

namespace Controller;

use AttributesRouter\Attribute\Route;

class MainController extends CoreController {

    #[Route('/', name: 'homepage', methods: ['GET'])]
    public function home($arguments = [])
    {
        $this->show('pages/home.twig', $arguments);
    }


    public function page404() {
        header('HTTP/1.0 404 Not Found');
    }
}