<?php

/** Author: Dalibor Kyjovský */

namespace App\repository;

use Nextras\Dbal\Result\Row;

class StatisticsRepository extends AllRepository
{
    private $expencesTable = "expenses";
    private $invoices_itemsTable = "invoices_items";

    public function getSumExpenses($id)
    {
        return $this->connection->query("SELECT SUM(price) as suma FROM %table WHERE users_id = %i", $this->expencesTable, $id)->fetchField();
    }

    public function getSumRevenues($id)
    {
        return $this->connection->query("SELECT SUM(invoices.suma) as suma FROM invoices WHERE invoices.users_id=%i AND invoices.status='paid';", $id)->fetchField();
    }

    public function getSumInvoices($id)
    {
        return $this->connection->query("SELECT COUNT(invoices.id) as pocet FROM `invoices` WHERE users_id = %i;", $id)->fetchField();
    }

    public function getSumRevenuesLast30day($id)
    {
        return $this->connection->query("SELECT SUM(invoices.suma) as suma FROM invoices WHERE invoices.users_id=%i AND invoices.status='paid' AND invoices.created > current_date - interval 30 day", $id)->fetchField();
    }

    public function getSumExpensesLast30day($id)
    {
        return $this->connection->query("SELECT SUM(price) as suma FROM expenses WHERE users_id = %i AND expenses.datetime > current_date - interval 30 day", $id)->fetchField();
    }


}
