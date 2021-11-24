<?php

/** Author: Martin Kovalski */

namespace App\repository;

use Nextras\Dbal\Connection;

class AllRepository
{
    /** @var $connection */
    public $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getAllTable($table): array
    {
        return $this->connection->query('SELECT * FROM %table', $table)->fetchAll();
    }
}