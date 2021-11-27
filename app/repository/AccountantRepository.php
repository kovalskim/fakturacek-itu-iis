<?php

/** Author: Radek Jůzl, Martin Kovalski */

namespace App\repository;

use Nextras\Dbal\Result\Row;

class AccountantRepository extends AllRepository
{
    private $table = 'users';
    private $table_accountant_permission = 'accountant_permission';

    public function hasAccountantName($users_id): ?Row
    {
        return ($this->connection->query("SELECT us.name, ac.request_status, ac.who, us.avatar_path FROM %table as ac join %table as us on ac.accountant_id = us.id WHERE ac.users_id = %i", $this->table_accountant_permission, $this->table, $users_id)->fetch());
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
            "request_status" => "active",
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
            "request_status" => "active",
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

    public function getAllByUserIdByAccountantId($users_id, $accountant_id): bool
    {
        if(($this->connection->query("SELECT * FROM %table WHERE users_id = %?i and accountant_id = %i and request_status = %s", $this->table_accountant_permission, $users_id, $accountant_id, "active")->fetch()))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function AllAcceptById($id)
    {
        $data = [
            "request_status" => "active",
            "hash" => null,
            "hash_validity" => null
        ];

        $this->connection->query("UPDATE %table SET %set WHERE users_id = %i and who = %s and request_status = %s", $this->table_accountant_permission, $data, $id, "business", "wait");
    }

    public function AllDeclineById($id)
    {
        $this->connection->query('DELETE FROM %table WHERE users_id = %i and who = %s and request_status = %s', $this->table_accountant_permission, $id, "business", "wait");
    }

    public function AllDeleteById($id)
    {
        $this->connection->query('DELETE FROM %table WHERE users_id = %i', $this->table_accountant_permission, $id);
    }

    public function AllAcceptByAccountantId($id)
    {
        $data = [
            "request_status" => "active",
            "hash" => null,
            "hash_validity" => null
        ];

        $this->connection->query("UPDATE %table SET %set WHERE accountant_id = %i and who = %s and request_status = %s", $this->table_accountant_permission, $data, $id, "business", "wait");
    }

    public function getAllClientsByAccountantID($accountant_id): array
    {
        return ($this->connection->query("SELECT * FROM %table as ac join %table as us on ac.users_id = us.id WHERE ac.accountant_id = %i", $this->table_accountant_permission, $this->table, $accountant_id)->fetchAll());
    }
    public function getCountClientsByAccountantID($accountant_id, $status): int
    {
        return $this->connection->query("SELECT * FROM %table as ac join %table as us on ac.users_id = us.id WHERE ac.accountant_id = %i AND request_status = %s", $this->table_accountant_permission, $this->table, $accountant_id, $status)->count();
    }
}