<?php

namespace App\PublicModule\repository;

class TextRepository extends AllRepository
{
    private $table = "texts";

    public function getTextByType($type)
    {
        return $this->connection->query("SELECT text FROM %table WHERE type = %s", $this->table, $type)->fetchField();
    }
}
