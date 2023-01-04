<?php

namespace Controller;

use AttributesRouter\Attribute\Route;
use Enum\PasswordResetStatus;
use Model\Manager\UserManager;
use Model\User;
use PHPMailer\PHPMailer\Exception;
use PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException;
use PragmaRX\Google2FA\Exceptions\InvalidCharactersException;
use PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException;
use Service\DoubleAuthenticationService;
use Service\Mailer;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Util\AccountUtils;
use Util\JsonResponse;

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
        $as = $arguments['account'];
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
        $s = $arguments['session'];
        $as = $arguments['account'];
        $um = new UserManager();
        $das = new DoubleAuthenticationService();

        if ($as->isConnected()) {
            header('Location: ' . $arguments['router']->generateUrl('main-home'));
        }

        if ($post = $_POST ?? null) {
            $email = $post['email'];
            $psw = $post['password'];

            if ((isset($email) && !empty($email)) && (isset($psw) && !empty($psw))) {

                if ($um->exist($email)) {
                    if ($um->checkCredentials($email, $psw)) {
                        $user = $um->getFromEmail($email);
                        if ($user->getGoogleSecret() !== null) {
                            $s->needGoogleauthcheck = true;
                            $s->tempVerif = $user;
                            header('Location: ' . $arguments['router']->generateUrl('session-login-2fa'));
                        } else {
                            $as->login($user);
                            header('Location: ' . $arguments['router']->generateUrl('session-account'));
                        }
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

    #[Route('/login/2fa', name: 'session-login-2fa', methods: ['GET', 'POST'])]
    public function loginTwo($arguments = [])
    {
        $s = $arguments['session'];
        $as = $arguments['account'];
        $um = new UserManager();
        $das = new DoubleAuthenticationService();

//        if ($as->isConnected()) {
//            header('Location: ' . $arguments['router']->generateUrl('main-home'));
//        }

        if (isset($s->tempVerif)) {
            if (isset($s->needGoogleauthcheck) && $s->needGoogleauthcheck === true) {
                if (isset($_POST['submittedCode'])) {
                    $code = $_POST['submittedCode'];
                    if ($das->checkCode($s->tempVerif->getGoogleSecret(), $code)) {
                        $as->login($s->tempVerif);
                        unset($s->needGoogleauthcheck);
                        header('Location: ' . $arguments['router']->generateUrl('session-account'));
                    } else {
                        $arguments['error'][] = "Code invalide, veuillez réessayer.";
                    }
                }
            } else {
                header('Location: ' . $arguments['router']->generateUrl('session-login'));
            }
        } else {
            header('Location: ' . $arguments['router']->generateUrl('session-login'));
        }

        $this->show('pages/admin/login-2fa.twig', $arguments);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/account', name: 'session-account', methods: ['GET'])]
    public function session($arguments = [])
    {
        $s = $arguments['session'];
        $as = $arguments['account'];

        if (!$as->isConnected()) {
            header('Location: ' . $arguments['router']->generateUrl('session-login'));
        }

        $this->show('pages/admin/account/account.twig', $arguments);
    }

    #[Route('/logout', name: 'session-logout', methods: ['GET'])]
    public function logout($arguments = [])
    {
        $as = $arguments['account'];
        $as->logout($arguments['router']->generateUrl('main-home'));
    }

    #[Route('/security/change-password', name: 'session-change-password', methods: ['GET', 'POST'])]
    public function changePassword($arguments = [])
    {
        $as = $arguments['account'];
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
    #[Route('/security/password', name: 'session-resetpassword', methods: ['GET', 'POST'])]
    public function reset($arguments = [])
    {
        $as = $arguments['account'];
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
    #[Route('/security/password/{token}', name: 'session-resetpassword-token', methods: ['GET', 'POST'])]
    public function resetToken($arguments = [])
    {
        $as = $arguments['account'];
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

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/security/2fa', name: 'session-2fa', methods: ['GET', 'POST'])]
    public function doubleAuthentication($arguments = [])
    {
        $s = $arguments['session'];
        $as = $arguments['account'];
        $um = new UserManager();
        $das = new DoubleAuthenticationService();


        if (!$as->isConnected()) {
            header('Location: ' . $arguments['router']->generateUrl('session-login'));
        }

        if (isset($_POST['submitted'])) {
            if (isset($_POST['submittedCode'])) {
                if ($das->checkCode($s->secret, $_POST['submittedCode'])) {
                    $updatedUser = $as->getUser();
                    $updatedUser->setGoogleSecret($s->secret);
                    $result = $um->update($updatedUser);
                    if ($result instanceof User) {
                        $arguments['success'][] = "Code validé avec succès !";
                        $arguments['hideForm'] = true;
                    } else {
                        $arguments['error'][] = "Une erreur c'est produite, veuillez réessayer plus tard.";
                    }
                    unset($s->secret);
                } else {
                    $arguments['error'][] = "Code non valide.";
                }
            }

            if (isset($_POST['delete'])) {
                if ($_POST['delete'] == 'true') {
                    $as->getUser()->setGoogleSecret(null);
                    $result = $um->update($as->getUser());
                    if ($result instanceof User) {
                        $arguments['success'][] = "Double authentification supprimé avec succès.";
                        $arguments['hideForm'] = true;
                    } else {
                        $arguments['error'][] = "Une erreur c'est produite, veuillez réessayer plus tard.";
                    }
                }
            }
        } else {
            if ($as->getUser()->getGoogleSecret() !== null) {
                $arguments['hideForm'] = true;
                $arguments['showDelete'] = true;
            } else {
                try {
                    if (!isset($s->secret)) {
                        $secret = $das->generateNewSecret();
                        $s->secret = $secret;
                    }
                    $arguments['google_secret'] = $das->generateQrCodeImage($as->getUser(), $s->secret);
                    $arguments['secret'] = $s->secret;
                } catch (IncompatibleWithGoogleAuthenticatorException|InvalidCharactersException|SecretKeyTooShortException $e) {
                    dump($e);
                    $arguments['error'][] = "Une erreur est survenue. Veuillez réessayer plus tard.";
                }
            }
        }

        $this->show('pages/admin/account/google-authentication/generate-qrcode.twig', $arguments);
    }

    #[Route('/security/delete-account', name: 'session-logout', methods: ['POST'])]
    public function deleteAccount($arguments = [])
    {
        $as = $arguments['account'];
        $um = new UserManager();
        if($confirmed = $_POST['confirmed']) {
            if($um->removeAllResetToken($as->getUser())) {
                if($um->del($as->getUser())) {
                    new JsonResponse(["message" => "User deleted successfully"], 204);
                }
            } else {
                new JsonResponse(["message" => "Partially deleted."], JsonResponse::HTTP_PARTIAL_CONTENT);
            }
        }
    }
}