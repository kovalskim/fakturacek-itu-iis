<?php

/** Author: Radek Jůzl, Martin Kovalski */

namespace App\repository;


use Nextras\Dbal\Result\Row;

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

    public function deleteClientById($id)
    {
        $this->connection->query("DELETE FROM %table WHERE id = %i", $this->table, $id);
    }

    public function getClientById($id): ?Row
    {
        return $this->connection->query('SELECT * FROM %table WHERE id = %i', $this->table, $id)->fetch();
    }

    public function isExistClient($values): bool
    {
        if($this->connection->query("SELECT id FROM %table WHERE %and", $this->table, $values)->fetchField())
        {
            return true;
        }
        return false;
    }
}