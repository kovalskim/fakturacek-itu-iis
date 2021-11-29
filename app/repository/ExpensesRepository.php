<?php

/** Author: Dalibor Kyjovský, Radek Jůzl */

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
        $this->connection->query("DELETE FROM %table WHERE id = %i", $this->table, $id);
    }

    public function updateImg($values, $expenses_id)
    {

        $this->connection->query("UPDATE %table SET `path` = %s WHERE expenses.id = %i", $this->table, $values->path, $expenses_id);
    }

    public function updateExpenseById($id, $values)
    {
        $this->connection->query('UPDATE %table SET %set WHERE id = %i', $this->table, $values, $id);
    }

    public function getPathById($id)
    {
        return $this->connection->query("SELECT path FROM %table WHERE id = %i", $this->table, $id)->fetchField();
    }

    public function getDataForModal($user_id): array
    {
        return $this->connection->query('SELECT id, path FROM expenses WHERE users_id = %i', $user_id)->fetchAll();
    }
}