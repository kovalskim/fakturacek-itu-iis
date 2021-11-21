<?php

/** Author: Radek Jůzl */

namespace App\PublicModule\repository;

use Nextras\Dbal\Result\Row;

class SettingInvoicesRepository extends AllRepository
{
    private $table = 'setting_invoices';

    public function insertIdBusiness()
    {
        $this->connection->query("INSERT INTO %table %values", $this->table, ['users_id' => $this->connection->getLastInsertedId()]);
    }

    public function selectAll($users_id): ?Row
    {
        return $this->connection->query("SELECT * FROM %table WHERE users_id = %i", $this->table, $users_id)->fetch();
    }

    public function updateSetting($values, $users_id)
    {
        $this->connection->query("UPDATE %table SET %set WHERE users_id = %i", $this->table, (array) $values, $users_id);
    }

    public function getUserLogo($user_id)
    {
        return $this->connection->query("SELECT logo_path FROM %table WHERE users_id = %i", $this->table, $user_id)->fetchField();
    }
}