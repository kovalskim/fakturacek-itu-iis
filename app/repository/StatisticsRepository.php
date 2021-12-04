<?php

/** Author: Dalibor Kyjovský */

namespace App\repository;

class StatisticsRepository extends AllRepository
{
    private $expencesTable = "expenses";

    public function getSumExpenses($id)
    {
        return $this->connection->query("SELECT SUM(price) as suma FROM `expenses` WHERE expenses.users_id = %i", $id)->fetchField();
    }

    public function getSumRevenues($id)
    {
        return $this->connection->query("SELECT SUM(invoices.suma) as suma FROM invoices WHERE invoices.users_id=%i AND invoices.status='paid';", $id)->fetchField();
    }


    public function getExpensesPerYear($id)
    {
        return $this->connection->query("SELECT SUM(expenses.price) as `expenses`, YEAR(datetime) as `date`
        FROM expenses 
        WHERE expenses.users_id = %i
        GROUP BY YEAR(expenses.datetime)
        Order BY expenses.datetime;", $id)->fetchAll();
    }

    public function getRevenuesPerYear($id)
    {
        return $this->connection->query("SELECT SUM(invoices.suma) as `revenues`, YEAR(invoices.created) as `date` 
        FROM invoices 
        WHERE invoices.users_id = %i AND invoices.status = 'paid' 
        GROUP BY YEAR(invoices.created) 
        Order BY invoices.created
        LIMIT 5", $id)->fetchAll();
    }

    public function getExpensesPerMonth($id)
    {
        return $this->connection->query("SELECT SUM(expenses.price) as `expenses`, monthname(datetime) as `date` 
        FROM expenses WHERE expenses.users_id = %i AND YEAR(expenses.datetime) = YEAR(CURDATE())
        GROUP BY MONTH(expenses.datetime) 
        Order BY expenses.datetime
        LIMIT 5
        ", $id)->fetchAll();
    }

    public function getRevenuesPerMonth($id)
    {
        return $this->connection->query("SELECT SUM(invoices.suma) as `revenues`, monthname(invoices.created) as `date` 
        FROM invoices WHERE invoices.users_id = %i 
        GROUP BY MONTH(invoices.created) 
        Order BY invoices.created
        LIMIT 5
        ", $id)->fetchAll();
    }

    public function getExpensesPerTMonths($id)
    {
        return $this->connection->query("SELECT SUM(expenses.price) as `expenses`, monthname(datetime) as `date` FROM expenses WHERE expenses.users_id = %i GROUP BY MONTH(expenses.datetime) Order BY expenses.datetime
        ", $id)->fetchAll();
    }

    public function getRevenuesPerTMonths($id)
    {
        return $this->connection->query("SELECT SUM(invoices.suma) as `revenues`, monthname(invoices.created) as `date` FROM invoices WHERE invoices.users_id = %i GROUP BY MONTH(invoices.created) Order BY invoices.created
        ", $id)->fetchAll();
    }






}
