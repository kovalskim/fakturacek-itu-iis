<?php

/** Author: Radek Jůzl */

namespace App\repository;


class ClientRepository extends AllRepository
{
    private $table = 'clients';

    public function insertClientByUserId($values)
    {
        $this->connection->query("INSERT INTO %table %values", $this->table, $values);
    }

    public function updateClientById($id, $values)
    {
        $this->connection->query('UPDATE %table SET %set WHERE id = %i', $this->table, $values, $id);
    }
}