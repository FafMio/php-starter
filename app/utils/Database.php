<?php

namespace App\Util;

use PDO;

abstract class Database {
    protected PDO $database;
    private string $dbname;
    private string $hostname;

    public function __construct()
    {
        $this->hostname = $_ENV['DB_HOST'];
        $this->dbname = $_ENV['DB_NAME'];

        $this->database = new PDO(
            'mysql:host='.$this->hostname.';dbname='.$this->dbname.'',
             $_ENV["DB_USERNAME"],
             $_ENV["DB_PASSWORD"],
             array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8', PDO::ATTR_ERRMODE => 'true', PDO::ERRMODE_EXCEPTION => 'true'));
    }

    public function sql($f_sql = "", $f_prep = [], $f_fetchmode = null) {
        $req = $this->getDatabase()->prepare($f_sql);
        if(!is_null($f_fetchmode)) $req->setFetchMode($f_fetchmode[0], $f_fetchmode[1]);
        $res = $req->execute($f_prep);
        
        return $req;
    }

    /**
     * Get the value of hostname
     */ 
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * Set the value of hostname
     *
     * @return  self
     */ 
    public function setHostname($hostname)
    {
        $this->hostname = $hostname;

        return $this;
    }

    /**
     * Get the value of dbname
     */ 
    public function getDbname()
    {
        return $this->dbname;
    }

    /**
     * Set the value of dbname
     *
     * @return  self
     */ 
    public function setDbname($dbname)
    {
        $this->dbname = $dbname;

        return $this;
    }

    /**
     * Get the value of database
     */ 
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * Set the value of database
     *
     * @return  self
     */ 
    public function setDatabase($database)
    {
        $this->database = $database;

        return $this;
    }
}