<?php

namespace Service;

use AttributesRouter\Router;
use Model\Manager\UserManager;
use Model\User;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class Mailer
{
    private PHPMailer $mailer;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->mailer = new PHPMailer(true);

        $this->mailer->isSMTP();
        $this->mailer->Host = $_ENV['SMTP_HOST'];
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = $_ENV['SMTP_USER'];
        $this->mailer->Password = $_ENV['SMTP_PASSWORD'];
        $this->mailer->Port = $_ENV['SMTP_PORT'];
        $this->mailer->CharSet = "UTF-8";

        $this->mailer->setFrom($_ENV['SMTP_DEFAULT_FROM'], 'PHP Starter');
    }

    /**
     * @throws Exception
     */
    public function from(string $emailFrom): void
    {
        $this->mailer->setFrom($emailFrom);
    }

    /**
     * @return PHPMailer
     */
    public function getMailer(): PHPMailer
    {
        return $this->mailer;
    }

    /**
     * @throws Exception
     */
    public function sendResetPasswordLink(User $user, Router $router, Environment $twig): bool
    {
        $um = new UserManager();
        $url = $_ENV['BASE_URI'] . $router->generateUrl('session-resetpassword-token', ['token' => $um->generatePasswordResetToken($user)]);

        $this->mailer->addAddress($user->getEmail());

        $this->mailer->Subject = 'Réinitialisation du mot de passe';
        $this->mailer->isHTML(true);
        try {
            $this->mailer->Body = $twig->render('partials/emails/reset-password.twig', ['resetLink' => $url]);
        } catch (LoaderError|RuntimeError|SyntaxError $e) {
            return false;
        }

        try {
            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$this->mailer->ErrorInfo}";
            return false;
        }
    }

}

/*
 *
 * $mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp.example.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'user@example.com';                     //SMTP username
    $mail->Password   = 'secret';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('from@example.com', 'Mailer');
    $mail->addAddress('joe@example.net', 'Joe User');     //Add a recipient
    $mail->addAddress('ellen@example.com');               //Name is optional
    $mail->addReplyTo('info@example.com', 'Information');
    $mail->addCC('cc@example.com');
    $mail->addBCC('bcc@example.com');

    //Attachments
    $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
    $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = 'Here is the subject';
    $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
 */