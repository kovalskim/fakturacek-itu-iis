<?php

/** Author: Radek Jůzl */

namespace App\PublicModule\repository;


class SettingInvoicesRepository extends AllRepository
{
    private $table = 'setting_invoices';

    public function insertIdBusiness()
    {
        $this->connection->query("INSERT INTO %table %values", $this->table, ['users_id' => $this->connection->getLastInsertedId()]);
    }
}