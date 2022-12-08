<?php

namespace Controller;

class SessionController extends CoreController {

    public function register($arguments = [])
    {
        $this->show('pages/register.twig', $arguments);
    }
}