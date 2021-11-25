<?php

/** Author: Dalibor Kyjovský */

namespace App\repository;


class ExpensesRepository extends AllRepository
{
    private $table = 'expenses';

    public function insertExpensesByUserId($values)
    {
        $this->connection->query("INSERT INTO %table %values", $this->table, $values);
    }

    public function deleteExpensesByUserId($id)
    {
        $this->connection->query("DELETE FROM %table WHERE `expenses`.`id` = %i", $this->table, $id);
    }
}