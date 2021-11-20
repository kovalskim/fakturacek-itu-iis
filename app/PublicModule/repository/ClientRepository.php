<?php

/** Author: Radek Jůzl */

namespace App\PublicModule\repository;


class ClientRepository extends AllRepository
{
    private $table = 'clients';

    public function insertClientByUserId($values)
    {
        $this->connection->query("INSERT INTO %table %values", $this->table, $values);
    }
}