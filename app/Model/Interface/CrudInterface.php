<?php

namespace Model\Interface;

use Model\User;

interface CrudInterface {
    public function exist(string $email);

    public function get(string $email): ?User;
    public function getAll(int $limit, int $offset, array $data): mixed;

    public function add(User $obj): ?User;
    public function del(User $obj): bool;

    public function update(User $obj): ?User;

}