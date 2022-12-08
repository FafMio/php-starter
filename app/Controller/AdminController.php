<?php

namespace App\Controller;

use App\Controller\CoreController;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class AdminController extends CoreController {

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function home($arguments = [])
    {
        $this->show('pages/admin/home.twig', $arguments);
    }
}