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
        $this->connection->query("DELETE FROM %table WHERE `categories`.`id` = %i", $this->table, $id);
    }

    public function editCategoryByUserId($values)
    {
        $this->connection->query("UPDATE %table SET %values", $this->table, $values);
    }


    public function updateCategoryById($id, $values)
    {
        $this->connection->query('UPDATE %table SET %set WHERE id = %i', $this->table, $values, $id);
    }



}