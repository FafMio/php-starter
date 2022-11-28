<?php

class MainController extends CoreController {

    public function home($arguments = [])
    {
        $this->show('pages/home.html.twig', $arguments);
    }


    public function page404() {
        $this->show('404.html.twig');
    }
}