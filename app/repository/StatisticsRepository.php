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

    public function getSumRevenues($id): ?Row
    {
        return $this->connection->query("SELECT SUM(invoices_items.suma) as suma FROM invoices INNER JOIN invoices_items ON invoices.id = invoices_items.invoices_id WHERE invoices.users_id=%i;
        ", $id)->fetch();
    }

    public function getSumInvoices($id): ?Row
    {
        return $this->connection->query("SELECT COUNT(invoices.id) as pocet FROM `invoices` WHERE users_id = %i;", $id)->fetch();
    }


}
