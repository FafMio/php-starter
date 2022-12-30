<?php

namespace Service;

use JetBrains\PhpStorm\Pure;
use Model\User;
use PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException;
use PragmaRX\Google2FA\Exceptions\InvalidCharactersException;
use PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException;
use PragmaRX\Google2FA\Google2FA;

class DoubleAuthenticationService
{

    private Google2FA $google2FA;

    #[Pure]
    public function __construct()
    {
        $this->google2FA = new Google2FA();
    }

    /**
     * Generate new secret?
     * It needs to be saved for the user.
     *
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws SecretKeyTooShortException
     * @throws InvalidCharactersException
     */
    public function generateNewSecret(): string
    {
        return $this->google2FA->generateSecretKey();
    }

    /**
     * Generate the QRCode Image Src for the user.
     * User has to scan this code into the Google Auth App
     *
     * @param User $user
     * @param string $secret
     * @return string
     */
    public function generateQrCodeImage(User $user, string $secret): string
    {
        $text = $this->google2FA->getQRCodeUrl(
            $_ENV['APP_NAME'],
            $user->getConcatName(),
            $secret
        );

        return "https://chart.googleapis.com/chart?cht=qr&chs=300x300&chl=$text";
    }

    /**
     * Check if the submitted code is correct.
     *
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws SecretKeyTooShortException
     * @throws InvalidCharactersException
     */
    public function checkCode(string $secret, string $submittedCode): bool
    {
        return ($this->google2FA->verifyKey($secret, $submittedCode));
    }

    /**
     * @return Google2FA
     */
    public function getGoogle2FA(): Google2FA
    {
        return $this->google2FA;
    }

    /**
     * @param Google2FA $google2FA
     */
    public function setGoogle2FA(Google2FA $google2FA): void
    {
        $this->google2FA = $google2FA;
    }

}