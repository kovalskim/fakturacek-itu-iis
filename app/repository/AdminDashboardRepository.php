<?php

/** Author: Martin Kovalski */

namespace App\repository;

class AdminDashboardRepository extends AllRepository
{
    private $table_users = 'users';
    private $table_invoices = 'invoices';

    public function getAdminCount(): int
    {
        return $this->connection->query('SELECT * FROM %table WHERE role = %s', $this->table_users, 'admin')->count();
    }

    public function getBusinessCount(): int
    {
        return $this->connection->query('SELECT * FROM %table WHERE role = %s', $this->table_users, 'business')->count();
    }

    public function getAccountantCount(): int
    {
        return $this->connection->query('SELECT * FROM %table WHERE role = %s', $this->table_users, 'accountant')->count();

    }

    public function getInvoicesCount(): int
    {
        return $this->connection->query('SELECT * FROM %table', $this->table_invoices)->count();
    }
}