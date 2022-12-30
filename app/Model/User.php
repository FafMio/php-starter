<?php

namespace Model;

class User
{

    private ?string $id;
    private string $email;
    private string $firstname;
    private string $lastname;
    private string $password;
    private ?string $google_secret = null;

    public function __construct()
    {
        $get_arguments = func_get_args();
        $number_of_arguments = func_num_args();

        if (method_exists($this, $method_name = '__construct' . $number_of_arguments)) {
            call_user_func_array(array($this, $method_name), $get_arguments);
        }
    }

    public function __construct5($id, $email, $firstname, $lastname, $password)
    {
        $this->id = $id;
        $this->email = $email;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->password = $password;
    }

    public function __construct6($id, $email, $firstname, $lastname, $password, $google_secret)
    {
        $this->id = $id;
        $this->email = $email;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->password = $password;
        $this->google_secret = $google_secret;
    }

    public function __construct4($email, $firstname, $lastname, $password)
    {
        $this->id = null;
        $this->email = $email;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->password = $password;
    }

    public function __construct0()
    {

    }

    public function getConcatName()
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    /**
     * @return mixed
     */
    public function getIdUser()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setIdUser($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param mixed $firstname
     */
    public function setFirstname($firstname): void
    {
        $this->firstname = $firstname;
    }

    /**
     * @return mixed
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param mixed $lastname
     */
    public function setLastname($lastname): void
    {
        $this->lastname = $lastname;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password): void
    {
        $this->password = $password;
    }

    /**
     * @return string|null
     */
    public function getGoogleSecret(): ?string
    {
        return $this->google_secret;
    }

    /**
     * @param string|null $google_secret
     */
    public function setGoogleSecret(?string $google_secret): void
    {
        $this->google_secret = $google_secret;
    }
}
