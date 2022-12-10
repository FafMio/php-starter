<?php

namespace Controller;

use AttributesRouter\Attribute\Route;
use Model\Manager\UserManager;
use Util\AccountUtils;

class SessionController extends CoreController {

    #[Route('/register', name: 'session-register', methods: ['GET', 'POST'])]
    public function register($arguments = [])
    {
        $this->show('pages/admin/register.twig', $arguments);
    }

    #[Route('/login', name: 'session-login', methods: ['GET', 'POST'])]
    public function login($arguments = [])
    {
        $as = new AccountUtils($arguments['session']);
        $um = new UserManager();

        if($as->isConnected()) {
            header('Location: ' . $arguments['router']->generateUrl('main-home'));
        }

        if($post = $_POST ?? null) {
            if($email = $post['email'] && $psw = $post['password']) {

                if($um->exist($email)) {
                    if($um->checkCredentials($email, $psw)) {
                        $as->login($um->getFromEmail($email));
                        header('Location: ' . $arguments['router']->generateUrl('session-account'));
                    } else {
                        dump('id + mdp invalide');
                    }
                } else dump('deso, texiste pas frerot');
            }
        }




        $this->show('pages/admin/login.twig', $arguments);
    }
}