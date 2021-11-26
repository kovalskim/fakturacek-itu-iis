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

    public function editExpensesByUserId($id, $path, $categories_id, $items, $price)
    {
        $this->connection->query("DELETE FROM %table WHERE `expenses`.`id` = %i", $this->table, $id);
    }

    public function updateImg($values)
    {
        $this->connection->query("UPDATE %table SET %set WHERE id = 37 ", $this->table, (array) $values);
    }

    public function updateExpenseById($id, $values)
    {
        $this->connection->query('UPDATE %table SET %set WHERE id = %i', $this->table, $values, $id);
    }
}