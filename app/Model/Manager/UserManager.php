<?php

namespace Model\Manager;

use Model\Interface\CrudInterface;
use Model\User;
use Util\Database;
use PDO;

class UserManager extends Database implements CrudInterface
{

    public function exist(string $obj): bool
    {
        return (bool) $this->sql("SELECT * FROM user AS a WHERE a.email=:email", ['email' => $obj])->fetch();
    }

    public function get(string $obj): ?User
    {
        return $this->sql("SELECT * EXCEPT(password) FROM user WHERE id=:id", ['id' => $obj], [PDO::FETCH_CLASS, 'User'])->fetch();
    }

    public function getAll(int $limit, int $offset, array $data): ?array
    {
        return $this->sql("SELECT * EXCEPT(password) FROM user WHERE 1", [], [PDO::FETCH_CLASS, 'User'])->fetchAll();
    }

    public function add(User $obj): ?User
    {
        $this->sql(
            "INSERT INTO user (email, firstname, lastname, password) VALUES(:email, :firstname, :lastname, :password)",
            [
                'email' => $obj->getEmail(),
                'firstname' => $obj->getFirstname(),
                'lastname' => $obj->getLastname(),
                'password' => $obj->getPassword(),
            ]
        );
        $obj->setIdUser($this->getDatabase()->lastInsertId());
        return $obj;
    }

    public function del(User $obj): bool
    {
        if ($this->sql("DELETE FROM user WHERE id=:id", ['id' => $obj->getIdUser()])->fetch()) return true;
        else return false;
    }

    public function update(User $obj): ?User
    {
        if ($this->sql(
            "UPDATE user SET :email, :firstname, :lastname WHERE id=:id",
            [
                'id' => $obj->getIdUser(),
                'email' => $obj->getEmail(),
                'firstname' => $obj->getFirstname(),
                'lastname' => $obj->getLastname(),
            ]
        )->fetch()) return $obj;
        else return null;
    }

    public function checkCredentials(string $f_email, string $f_password): bool
    {
        $userExist = $this->sql("SELECT a.email FROM user AS a WHERE a.email=:email LIMIT 1", ['email' => $f_email])->fetch();

        if (!empty($userExist)) {
            $data = $this->sql("SELECT a.email, a.password FROM user AS a WHERE a.email=:email LIMIT 1", ['email' => $f_email])->fetch();
            if (password_verify($f_password, $data['password'])) {
                return true;
            } else return false;
        } else return false;
    }

    public function getFromEmail(string $f_email): User|bool
    {
        return $this->sql("SELECT * FROM user WHERE email=:email", ['email' => $f_email], [PDO::FETCH_CLASS, User::class])->fetch();
    }

    public function changePassword(string $f_old, User $f_admin, $f_new = []): int
    { //$this->sql("SELECT password FROM user WHERE email=:email LIMIT 1", ['email' => $f_admin->getPassword()])->fetch()['password']
        if (password_verify($f_old, $f_admin->getPassword())) {
            if (isset($f_new[0]) && isset($f_new[1])) {
                if ($f_new[0] == $f_new[1]) {
                    if ($f_old !== $f_new) {
                        if ($this->sql("UPDATE user SET password=:new WHERE email=:email", ['email' => $f_admin->getEmail(), 'new' => password_hash($f_new[1], PASSWORD_DEFAULT)])) return 5; // Ca marche !
                        else return 4; // Y'a un pépin
                    } else return 3;
                } else return 2; // Les deux nouveaux ne sont pas pareils
            } else return 1; // Il manque les 2 nouveaux
        } else return 0; // Mauvais mot de passe actuel
    }

    public function register($f_admin): int
    {
        if (!$this->exist($f_admin->getEmail())) {
            if($this->sql("INSERT INTO user (email, firstname, lastname, password) VALUES (:email, :firstname, :lastname, :password)", ['email' => $f_admin->getEmail(), 'firstname' => $f_admin->getFirstname(), 'lastname' => $f_admin->getLastname(), 'password' => password_hash($f_admin->getPassword(), PASSWORD_DEFAULT)])) return 2;
            else return 1; //? L'utilisateur n'a pas pu être inséré dans la BDD
        } else return 0; //? Le mail existe déjà en bdd
    }
}
