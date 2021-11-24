<?php

/** Author: Martin Kovalski */

namespace App\repository;

use Nextras\Dbal\Result\Row;

class InvoicingRepository extends AllRepository
{
    private $table = 'invoices';
    private $table_items = 'invoices_items';

    public function updateInvoiceStatus($id, $status)
    {
        $this->connection->query('UPDATE %table SET %set WHERE id = %i', $this->table, ['status' => $status], $id);
    }

    public function getInvoiceByIdAndUserId($id, $user_id): ?Row
    {
        return $this->connection->query('SELECT * FROM %table WHERE id = %i AND users_id = %i', $this->table, $id, $user_id)->fetch();
    }

    public function getInvoiceItemsById($id): array
    {
        return $this->connection->query('SELECT * FROM %table WHERE invoices_id = %i', $this->table_items, $id)->fetchAll();
    }
}