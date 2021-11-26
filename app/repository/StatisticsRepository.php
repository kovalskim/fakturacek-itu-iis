<?php

/** Author: Dalibor Kyjovský */

namespace App\repository;

use Nextras\Dbal\Result\Row;

class StatisticsRepository extends AllRepository
{
    private $expencesTable = "expenses";
    private $invoices_itemsTable = "invoices_items";

    public function getSumExpenses($id): ?Row
    {
        return $this->connection->query("SELECT SUM(price) as suma FROM %table WHERE users_id = %i", $this->expencesTable, $id)->fetch();
    }


    public function getUserProfile($user_id): ?Row
    {
        return $this->connection->query("SELECT cin, name, email, phone, street, city, zip, avatar_path FROM users WHERE id = %i",  $user_id)->fetch();
    }
}
