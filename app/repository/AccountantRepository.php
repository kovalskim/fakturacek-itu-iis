<?php

/** Author: Radek Jůzl */

namespace App\repository;

class AccountantRepository extends AllRepository
{
    private $table = 'users';
    private $table_accountant_permission = 'accountant_permission';

    public function hasAccountantName($users_id)
    {
        return ($this->connection->query("SELECT us.name FROM %table as ac join %table as us on ac.accountant_id = us.id WHERE ac.users_id = %i", $this->table_accountant_permission, $this->table, $users_id)->fetchField());
    }

    public function deleteAccountant($users_id)
    {
        return ($this->connection->query('DELETE FROM %table WHERE users_id = %i', $this->table_accountant_permission, $users_id));
    }

}