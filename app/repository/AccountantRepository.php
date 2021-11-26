<?php

/** Author: Radek Jůzl */

namespace App\repository;

use Nextras\Dbal\Result\Row;

class AccountantRepository extends AllRepository
{
    private $table = 'users';
    private $table_accountant_permission = 'accountant_permission';

    public function hasAccountantName($users_id): ?Row
    {
        return ($this->connection->query("SELECT us.name, ac.status, ac.who FROM %table as ac join %table as us on ac.accountant_id = us.id WHERE ac.users_id = %i", $this->table_accountant_permission, $this->table, $users_id)->fetch());
    }

    public function deleteAccountant($users_id)
    {
        $this->connection->query('DELETE FROM %table WHERE users_id = %i', $this->table_accountant_permission, $users_id);
    }

    public function addConnectionUser($values)
    {
        $this->connection->query("INSERT INTO %table %values", $this->table_accountant_permission, $values);
    }

    public function updateStatus($token)
    {
        $data = [
            "status" => "active",
            "hash" => null,
            "hash_validity" => null,
        ];

        $this->connection->query("UPDATE %table SET %set WHERE hash = %s", $this->table_accountant_permission, $data, $token);
    }

    public function getTokenValidity($token)
    {
        return $this->connection->query('SELECT hash_validity FROM %table WHERE hash = %s', $this->table_accountant_permission, $token)->fetchField();
    }

    public function updateStatusById($id)
    {
        $data = [
            "status" => "active",
            "hash" => null,
            "hash_validity" => null
        ];

        $this->connection->query("UPDATE %table SET %set WHERE users_id = %i", $this->table_accountant_permission, $data, $id);
    }

    public function isExistUser($users_id): bool
    {
        if(($this->connection->query("SELECT * FROM %table WHERE users_id = %i", $this->table_accountant_permission, $users_id)->fetch()))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

}