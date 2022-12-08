<?php

namespace Interface;

interface CrudInterface {
    public function exist(int $id);

    public function get($id);
    public function getAll(int $limit, int $offset, array $data);

    public function add($obj);
    public function del($obj);

    public function update($obj);

}