<?php

namespace Util;

use Model\User;

class AccountUtils {

    private Session $session;

    public function __construct($session)
    {
        $this->session = $session;
    }

    public function isConnected(): bool {
        if(isset($this->session->loggedIn)) return true;
        else return false;
    }
    
    public function checkConnected($redirect_if_not_logged = '/', $redirect_if_logged = '/prout') {
        if(!$this->isConnected($this->session)) header('Location: ' . $redirect_if_not_logged);
        else {
            if($redirect_if_logged == '/prout') return;
            else header('Location: ' . $redirect_if_logged);
        }
    }

    public function logout() {
        session_destroy();
        header('Location: /login');
    }

    public function login(User $user) {
        $this->session->loggedIn = $user;
    }
}