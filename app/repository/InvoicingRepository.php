<?php

/** Author: Martin Kovalski, Radek Jůzl */

namespace App\repository;

use Nette\Utils\DateTime;
use Nextras\Dbal\Result\Row;

class InvoicingRepository extends AllRepository
{
    private $table = 'invoices';
    private $table_items = 'invoices_items';
    public $table_clients = 'clients';

    public function updateInvoiceStatus($id, $status)
    {
        $this->connection->query('UPDATE %table SET %set WHERE id = %i', $this->table, ['status' => $status], $id);
    }

    public function getInvoiceByIdAndUserId($id, $user_id): ?Row
    {
        return $this->connection->query('SELECT * FROM %table WHERE id = %?i AND users_id = %i', $this->table, $id, $user_id)->fetch();
    }

    public function getInvoiceDataById($id): ?Row
    {
        return $this->connection->query('SELECT * FROM %table WHERE id = %i', $this->table, $id)->fetch();
    }

    public function getInvoiceItemsById($id): array
    {
        return $this->connection->query('SELECT * FROM %table WHERE invoices_id = %i', $this->table_items, $id)->fetchAll();
    }

    public function getResultsByString($string): array
    {
        return $this->connection->query('SELECT * FROM %table WHERE name LIKE %s OR name LIKE %s OR email LIKE %s OR cin LIKE %s LIMIT 5', $this->table_clients, $string . '%', '% ' . $string . '%', $string . '%', $string . '%')->fetchAll();
    }

    public function getLastVsByUserId($user_id)
    {
       return $this->connection->query('SELECT variable_symbol FROM %table WHERE users_id = %i ORDER BY id DESC LIMIT 1', $this->table, $user_id)->fetchField();
    }

    public function insertInvoice($values)
    {
        $this->connection->query("INSERT INTO %table %values", $this->table, $values);
    }

    public function lasIdInvoice()
    {
        return $this->connection->getLastInsertedId();
    }

    public function insertItemInvoice($values)
    {
        $this->connection->query("INSERT INTO %table %values[]", $this->table_items, $values);
    }

    public function updateSuma($suma, $id)
    {
        $this->connection->query("UPDATE %table SET %set WHERE id = %i",  $this->table, ["suma" =>  $suma], $id);
    }

    public function getUnpaidInvoicesByUserId($user_id): array
    {
        return $this->connection->query('SELECT * FROM invoices WHERE users_id = %i AND status = "unpaid" AND due_date >= %dt', $user_id, new DateTime())->fetchAll();
    }

    public function getAfterDueDateInvoicesByUserId($user_id): array
    {
        return $this->connection->query('SELECT * FROM invoices WHERE users_id = %i AND status = "unpaid" AND due_date < %dt', $user_id, new DateTime())->fetchAll();
    }

    public function updateAfterDueDateInvoicesByUserId($user_id)
    {
        $this->connection->query("UPDATE %table SET %set WHERE id = %i and due_date < %dt",  $this->table, ["after_due_date" =>  1], $user_id, new DateTime());
    }
}