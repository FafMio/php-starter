<?php

namespace Util;

class Session
{
    private array $attributs = [];
    private ?int $sleepingtime = null;

    public function __construct()
    {
        if (array_key_exists('serialized', $_SESSION)) {
            $session = unserialize($_SESSION['serialized']);
            $this->attributs = $session->attributs;
            $this->sleepingtime = $session->sleepingtime;
        }
    }

    public function __get($name)
    {
        return $this->attributs[$name];
    }

    public function __set($name, $value)
    {
        $this->attributs[$name] = $value;
    }

    public function __destruct()
    {
        $_SESSION['serialized'] = serialize($this);
    }

    public function __sleep()
    {
        $this->sleepingtime = time();
        return ['attributs', 'sleepingtime'];
    }

    public function __wakeup()
    {
        $this->sleepingtime = time() - $this->sleepingtime;
    }

    public function __isset($name)
    {
        return isset($this->attributs[$name]);
    }

    public function __unset($name)
    {
        unset($this->attributs[$name]);
    }

    public function getTime(): ?int
    {
        return $this->sleepingtime;
    }
}
