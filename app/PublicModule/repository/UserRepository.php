<?php

namespace App\PublicModule\repository;

use Nextras\Dbal\Result\Row;

class UserRepository extends AllRepository
{
    private $table = 'users';

    public function getUserByEmail($email): ?Row
    {
        return $this->connection->query('SELECT * FROM %table WHERE email = %s', $this->table, $email)->fetch();
    }

    public function getUserByCin($cin): ?Row
    {
        return $this->connection->query('SELECT * FROM %table WHERE cin = %s', $this->table, $cin)->fetch();
    }

    public function insertUser($values)
    {
        $this->connection->query('INSERT INTO %table %values',$this->table, (array) $values);
    }

}