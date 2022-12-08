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
        $this->show('404.html.twig');
    }
}