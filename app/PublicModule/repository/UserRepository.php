<?php

namespace App\PublicModule\repository;

use Nextras\Dbal\Result\Row;

class UserRepository extends AllRepository
{
    private $table = 'user';

    public function getUserByEmail($email): ?Row
    {
        return $this->connection->query('SELECT * FROM %table WHERE email = %s', $this->table, $email)->fetch();
    }

    public function getUserByCin($cin): ?Row
    {
        return $this->connection->query('SELECT * FROM %table WHERE cin = %s', $this->table, $cin)->fetch();
    }

    public function insertUser($cin, $name, $email, $phone, $password, $role, $street, $city, $zip)
    {
        $this->connection->query('INSERT INTO %table', [
            'cin' => $cin,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'password' => $password,
            'role' => $role,
            'street' => $street,
            'city' => $city,
            'zip' => $zip,
            ]);
        //TODO: Udelat select zda tam je? nebo vypise insert nejakou chybu?
        return true;
    }

}