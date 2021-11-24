<?php

/** Author: Radek Jůzl */

namespace App\repository;

use Nextras\Dbal\Result\Row;

class TextRepository extends AllRepository
{
    private $table = "texts";

    public function getTextByType($type): ?Row
    {
        return $this->connection->query("SELECT text, img_path FROM %table WHERE type = %s", $this->table, $type)->fetch();
    }

    public function updateTextByType($type, $values)
    {
        $this->connection->query("UPDATE %table SET %set WHERE type = %s", $this->table, $values, $type);
    }

}
