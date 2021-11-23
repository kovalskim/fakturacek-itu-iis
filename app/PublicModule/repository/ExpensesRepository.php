<?php

/** Author: Dalibor kyjovský */

namespace App\PublicModule\repository;


class ExpensesRepository extends AllRepository
{
    private $table = 'expenses';

    public function insertExpensesByUserId($values)
    {
        $this->connection->query("INSERT INTO %table %values", $this->table, $values);
    }

    public function deleteExpensesByUserId($values)
    {
        $this->connection->query("DELETE FROM %table %values", $this->table, $values);
    }
}