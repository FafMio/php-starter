<?php

namespace Controller;

use AttributesRouter\Attribute\Route;
use Model\Manager\UserManager;
use Model\User;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Util\AccountUtils;

class SessionController extends CoreController
{

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/register', name: 'session-register', methods: ['GET', 'POST'])]
    public function register($arguments = [])
    {
        $as = new AccountUtils($arguments['session']);
        $um = new UserManager();

        if ($as->isConnected()) {
            header('Location: ' . $arguments['router']->generateUrl('main-home'));
        }

        if ($post = $_POST ?? null) {
            $email = $post['email'];
            $fn = $post['firstname'];
            $ln = $post['lastname'];
            $psw = $post['password'];

            if ((isset($email) && !empty($email)) && (isset($fn) && !empty($fn)) && (isset($ln) && !empty($ln)) && (isset($psw) && !empty($psw))) {
                if (!$um->exist($email)) {
//                    $newUser = new User($email, $fn, $ln, $psw);
                    $newUser = new User();
                    $newUser->setEmail($email);
                    $newUser->setFirstname($fn);
                    $newUser->setLastname($ln);
                    $newUser->setPassword($psw);

                    if ($um->register($newUser)) {
                        $arguments['success'] = [
                            'Votre compte à bien été créé. Vous pouvez maintenant <a href="' . $arguments['router']->generateUrl('session-login') . '">vous connecter</a>',
                        ];
                    }
                } else {
                    $arguments['error'] = [
                        'Un compte existe déjà avec cette adresse email. Avez-vous essayé de <a href="' . $arguments['router']->generateUrl('session-login') . '">vous connecter</a>',
                    ];
                }
            } else {
                $arguments['error'] = [
                    'Veuillez remplir tout les champs.',
                ];
            }
        }

        $this->show('pages/admin/register.twig', $arguments);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/login', name: 'session-login', methods: ['GET', 'POST'])]
    public function login($arguments = [])
    {
        $as = new AccountUtils($arguments['session']);
        $um = new UserManager();

        if ($as->isConnected()) {
            header('Location: ' . $arguments['router']->generateUrl('main-home'));
        }

        if ($post = $_POST ?? null) {
            $email = $post['email'];
            $psw = $post['password'];

            if ((isset($email) && !empty($email)) && (isset($psw) && !empty($psw))) {

                if ($um->exist($email)) {
                    if ($um->checkCredentials($email, $psw)) {
                        $as->login($um->getFromEmail($email));
                        header('Location: ' . $arguments['router']->generateUrl('session-account'));
                    } else {
                        $arguments['error'] = [
                            'Identifiants invalides, veuillez les vérifier.',
                        ];
                    }
                } else {
                    $arguments['error'] = [
                        'Identifiants invalides, veuillez les vérifier.',
                    ];
                }
            } else {
                $arguments['error'] = [
                    'Veuillez remplir tout les champs.',
                ];
            }
        }

        $this->show('pages/admin/login.twig', $arguments);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/account', name: 'session-account', methods: ['GET'])]
    public function session($arguments = [])
    {
        $as = new AccountUtils($arguments['session']);
        $um = new UserManager();

        if (!$as->isConnected()) {
            header('Location: ' . $arguments['router']->generateUrl('session-login'));
        }

        $this->show('pages/admin/account/account.twig', $arguments);
    }


    #[Route('/logout', name: 'session-logout', methods: ['GET'])]
    public function logout($arguments = [])
    {
        $as = new AccountUtils($arguments['session']);
        $as->logout($arguments['router']->generateUrl('main-home'));
    }

    #[Route('/change-password', name: 'session-changepassword', methods: ['GET', 'POST'])]
    public function changePassword($arguments = [])
    {
        $as = new AccountUtils($arguments['session']);
        $um = new UserManager();

        if (!$as->isConnected()) {
            header('Location: ' . $arguments['router']->generateUrl('session-login'));
        }

        if ($post = $_POST ?? null) {
            $current = $post['current_password'];
            $new = $post['new_password'];
            $new2 = $post['new2_password'];

            if ((isset($current) && !empty($current)) && (isset($new) && !empty($new)) && (isset($new2) && !empty($new2))) {
                $result = $um->changePassword($current, $as->getUser(), [$new, $new2]);

                if ($result == 0) {
                    $arguments['error'][] = 'Mot de passe actuel incorrect.';
                } elseif ($result == 1) {
                    $arguments['error'][] = 'Veuillez remplir tout les champs.';
                } elseif ($result == 2) {
                    $arguments['error'][] = 'Veuillez remplir tout les champs.';
                } elseif ($result == 5) {
                    $as->logout(null);
                    $arguments['success'][] = 'Votre mot de passe à bien été modifié. Veuillez <a href="' . $arguments['router']->generateUrl('session-login') . '">vous reconnecter</a>';
                } else {
                    $arguments['error'][] = 'Une erreur est survenue, veuillez essayer à nouveau dans quelques instants.';
                }
            } else {
                $arguments['error'][] = 'Veuillez remplir tout les champs.';
            }

        }

        $this->show('pages/admin/account/change-password.twig', $arguments);
    }

    #[Route('/reset-password', name: 'session-resetpassword', methods: ['GET', 'POST'])]
    public function reset($arguments = [])
    {
        $as = new AccountUtils($arguments['session']);

        if ($as->isConnected()) {
            header('Location: ' . $arguments['router']->generateUrl('session-account'));
        }

        if ($post = $_POST ?? null) {
            $email = $post['email'];

            if ((isset($email) && !empty($email))) {
                $arguments['success'][] = 'Si un compte existe avec cette adresse email, vous aller recevoir un mail contenant un lien permanent de changer votre mot de passe.';
            }
        }

        $this->show('pages/admin/reset.twig', $arguments);
    }

    #[Route('/reset-password/{:token}', name: 'session-resetpassword-token', methods: ['GET', 'POST'])]
    public function resetToken($arguments = [])
    {
        $as = new AccountUtils($arguments['session']);

        if ($as->isConnected()) {
            header('Location: ' . $arguments['router']->generateUrl('session-account'));
        }

        if ($post = $_POST ?? null) {
            $email = $post['email'];

            if ((isset($email) && !empty($email))) {
                $arguments['success'][] = 'Si un compte existe avec cette adresse email, vous aller recevoir un mail contenant un lien permanent de changer votre mot de passe.';
            }
        }

        $this->show('pages/admin/reset.twig', $arguments);
    }
}