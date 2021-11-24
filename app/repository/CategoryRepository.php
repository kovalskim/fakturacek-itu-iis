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

    public function deleteCategoryByUserId($values)
    {
        $this->connection->query("DELETE FROM %table %values");
    }

}