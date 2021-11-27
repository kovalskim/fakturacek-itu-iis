<?php

/** Author: Dalibor Kyjovský */

namespace App\repository;

use Nextras\Dbal\Result\Row;

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

    public function updateImg($values, $expenses_id)
    {

        $this->connection->query("UPDATE %table SET `path` = %s WHERE expenses.id = %i", $this->table, $values->path, $expenses_id);
    }

    public function updateExpenseById($id, $values)
    {
        $this->connection->query('UPDATE %table SET %set WHERE id = %i', $this->table, $values, $id);
    }

    public function getLastExpenseId(): ?Row
    {
        return $this->connection->query('SELECT expenses.id FROM expenses ORDER BY expenses.id DESC LIMIT 1;')->fetch();
    }
}