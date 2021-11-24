<?php

/** Author: Martin Kovalski */

namespace App\repository;

class InvoicingRepository extends AllRepository
{
    private $table = 'invoices';

    public function updateInvoiceStatus($id, $status)
    {
        $this->connection->query('UPDATE %table SET %set WHERE id = %i', $this->table, ['status' => $status], $id);
    }
}