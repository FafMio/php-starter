<?php

namespace Util;

use Model\Manager\UserManager;
use Model\User;

class AccountUtils
{

    private Session $session;

    public function __construct($session)
    {
        $this->session = $session;

        if ($this->session->getTime() >= 3600) {
            $this->logout();
        }
    }

    public function isConnected(): bool
    {
        if (isset($this->session->loggedIn)) return true;
        else return false;
    }

    public function checkConnected($redirect_if_not_logged = '/', $redirect_if_logged = '/prout')
    {
        if (!$this->isConnected($this->session)) header('Location: ' . $redirect_if_not_logged);
        else {
            if ($redirect_if_logged == '/prout') return;
            else header('Location: ' . $redirect_if_logged);
        }
    }

    public function logout(string $fallback_route = "/login")
    {
        session_destroy();
        header('Location: ' . $fallback_route);
    }

    public function login(User $user)
    {
        $this->session->loggedIn = $user;
    }

    public function getUser(): ?User
    {
        return $this->session->loggedIn;
    }

    public function deleteAccount()
    {
        $um = new UserManager();
        $um->removeAllResetToken($this->getUser());
        if ($um->del($this->getUser())) {
            $this->logout();
        }
    }
}