<?php

namespace Manager;

use Interface\CrudInterface;
use Model\User;
use Util\Database;
use PDO;

class UserManager extends Database implements CrudInterface
{

    public function exist($email): bool
    {
        return (bool) $this->sql("SELECT * FROM administrators AS a WHERE a.email=:email", ['email' => $email])->fetch();
    }

    public function get($id): ?User
    {
        return $this->sql("SELECT * FROM administrators WHERE id_Administrator=:id", ['id' => $id], [PDO::FETCH_CLASS, 'User'])->fetch();
    }

    public function getAll(int $limit, int $offset, array $data): ?array
    {
        return $this->sql("SELECT * FROM administrators WHERE 1", [], [PDO::FETCH_CLASS, 'User'])->fetchAll();
    }

    public function add($obj): ?User
    {
        $this->sql(
            "INSERT INTO administrators (email, forname, surname, password) VALUES(:email, :forname, :surname, :password)",
            [
                'email' => $obj->getEmail(),
                'forname' => $obj->getForname(),
                'surname' => $obj->getSurname(),
                'password' => $obj->getPassword(),
            ]
        );
        $obj->setidAdministrator($this->getDatabase()->lastInsertId());
        return $obj;
    }

    public function del($obj): bool
    {
        if ($this->sql("DELETE FROM administrators WHERE id_Administrator=:id", ['id' => $obj->getidAdministrator()])->fetch()) return true;
        else return false;
    }

    public function update($f_admin): bool
    {
        if ($this->sql(
            "UPDATE administrators SET :email, :forname, :surname, :password WHERE id_Administrator=:id",
            [
                'id' => $f_admin->getidAdministrator(),
                'email' => $f_admin->getEmail(),
                'forname' => $f_admin->getForname(),
                'surname' => $f_admin->getSurname(),
                'password' => $f_admin->getPassword(),
            ]
        )->fetch()) return true;
        else return false;
    }

    public function checkCredentials($f_email, $f_password): bool
    {
        $userExist = $this->sql("SELECT a.email FROM administrators AS a WHERE a.email=:email LIMIT 1", ['email' => $f_email])->fetch();

        if (!empty($userExist)) {
            $data = $this->sql("SELECT a.email, a.password FROM administrators AS a WHERE a.email=:email LIMIT 1", ['email' => $f_email])->fetch();
            if (password_verify($f_password, $data['password'])) {
                return true;
            } else return false;
        } else return false;
    }

    public function getFromEmail($f_email): ?User
    {
        return $this->sql("SELECT * FROM administrators WHERE email=:email", ['email' => $f_email], [PDO::FETCH_CLASS, 'User'])->fetch();
    }

    public function changePassword($f_old, $f_admin, $f_new = []): int
    { //$this->sql("SELECT password FROM administrators WHERE email=:email LIMIT 1", ['email' => $f_admin->getPassword()])->fetch()['password']
        if (password_verify($f_old, $f_admin->getPassword())) {
            if (isset($f_new[0]) && isset($f_new[1])) {
                if ($f_new[0] == $f_new[1]) {
                    if ($f_old !== $f_new) {
                        if ($this->sql("UPDATE administrators SET password=:new WHERE email=:email", ['email' => $f_admin->getEmail(), 'new' => password_hash($f_new[1], PASSWORD_DEFAULT)])) return 5;
                        else return 4;
                    } else return 3;
                } else return 2;
            } else return 1;
        } else return 0;
    }

    public function register($f_admin): int
    {
        if (!$this->exist($f_admin->getEmail())) {
            if($this->sql("INSERT INTO administrators (email, forname, surname, password) VALUES (:email, :forname, :surname, :password)", ['email' => $f_admin->getEmail(), 'forname' => $f_admin->getForname(), 'surname' => $f_admin->getSurname(), 'password' => password_hash($f_admin->getPassword(), PASSWORD_DEFAULT)])) return 2;
            else return 1; //? L'utilisateur n'a pas pu être inséré dans la BDD
        } else return 0; //? Le mail existe déjà en bdd
    }
}
