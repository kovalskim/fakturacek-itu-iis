<?php

/** Author: Dalibor Kyjovský */

namespace App\repository;

class CategoryRepository extends AllRepository
{
    private $table = 'categories';

    public function insertCategoryByUserId($values)
    {
        $this->connection->query("INSERT INTO %table %values", $this->table, $values);
    }

    public function deleteCategoryByUserId($id)
    {
        $this->connection->query("DELETE FROM %table WHERE cat_id  = %i", $this->table, $id);
    }

    public function updateCategoryById($id, $values)
    {
        $this->connection->query('UPDATE %table SET %set WHERE cat_id = %i', $this->table, $values, $id);
    }

    public function selectAllCategoryById($user_id): array
    {
        return $this->connection->query('SELECT cat_id, name FROM %table WHERE users_id = %i', $this->table, $user_id)->fetchPairs("cat_id", "name");
    }

    public function getExpensesCountByCategoryId($id): int
    {
        return $this->connection->query("SELECT * FROM %table WHERE expenses_cat_id = %i" , "expenses", $id)->count();
    }

    public function getCategoryName($name): bool
    {
        if($this->connection->query("SELECT name FROM %table WHERE name = %s", $this->table, $name)->fetchField())
        {
            return true;
        }
        return false;
    }
}