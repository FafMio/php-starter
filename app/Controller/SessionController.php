<?php

namespace Controller;

use AttributesRouter\Attribute\Route;
use Enum\PasswordResetStatus;
use Model\Manager\UserManager;
use Model\User;
use PHPMailer\PHPMailer\Exception;
use Service\Mailer;
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
                    $arguments['error'][] = 'Les deux mots de passes ne sont pas identiques.';
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

    /**
     * @throws Exception
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/reset-password', name: 'session-resetpassword', methods: ['GET', 'POST'])]
    public function reset($arguments = [])
    {
        $as = new AccountUtils($arguments['session']);
        $um = new UserManager();
        $m = new Mailer();

        if ($as->isConnected()) {
            header('Location: ' . $arguments['router']->generateUrl('session-account'));
        }

        if ($post = $_POST ?? null) {
            $email = $post['email'];

            if ((isset($email) && !empty($email))) {
                if ($um->exist($email)) {
                    $m->sendResetPasswordLink($um->getFromEmail($email), $arguments['router'], $this->twig);
                }

                $arguments['success'][] = 'Si un compte existe avec cette adresse email, vous aller recevoir un mail contenant un lien permanent de changer votre mot de passe.';
                $arguments['success'][] = 'Pensez à vérifier votre boîte de SPAM (courrier indésirable).';
            }
        }

        $this->show('pages/admin/reset.twig', $arguments);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws \Exception
     */
    #[Route('/reset-password/{token}', name: 'session-resetpassword-token', methods: ['GET', 'POST'])]
    public function resetToken($arguments = [])
    {
        $as = new AccountUtils($arguments['session']);
        $um = new UserManager();

        if ($as->isConnected()) {
            header('Location: ' . $arguments['router']->generateUrl('session-account'));
        }

        $token = $arguments['params']['token'];
        if (!empty($token)) {
            $result = $um->isResetPasswordTokenValid($token);

            if ($result == PasswordResetStatus::VALID) {

                if (isset($_POST['submitted'])) {
                    $user = $um->getResetPasswordUser($token);
                    $resetResult = $um->resetPassword($user, [$_POST['new_password'], $_POST['new_password']]);

                    if ($resetResult == 5) {
                        $arguments['success'][] = "Votre mot de passe à été changé avec succès. <br> Vous pouvez dès maintenant vous connecter avec le nouveau mot de passe.";
                        $arguments['hideForm'] = true;
                    } else if ($resetResult == 4) {
                        $arguments['success'][] = "Votre mot de passe à été changé avec succès. <br> Vous pouvez dès maintenant vous connecter avec le nouveau mot de passe.";
                        $arguments['error'][] = "Une erreur est survenue.";
                        $arguments['hideForm'] = true;
                    } else if ($resetResult == 3) {
                        $arguments['error'][] = "Une erreur est survenue.";
                    } else if ($resetResult == 2) {
                        $arguments['error'][] = "Les deux mots de passe ne sont pas identiques";
                    } else if ($resetResult == 1) {
                        $arguments['error'][] = "Veuillez remplir tout les champs.";
                    } else {
                        $arguments['error'][] = "Une erreur est survenue.";
                    }
                    $this->show("pages/admin/account/reset-password.twig", $arguments);
                } else {
                    $this->show("pages/admin/account/reset-password.twig", $arguments);
                }
            } else {
                $arguments['error'][] = "Ce lien n'est plus valide ou n'existe pas.";
                $arguments['hideForm'] = true;
                $this->show("pages/admin/account/reset-password.twig", $arguments);
            }
        }
    }
}