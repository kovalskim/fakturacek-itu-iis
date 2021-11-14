<?php

/** Author: Martin Kovalski, Radek Jůzl */

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

    public function setUserRecoveryCredentials($hash, $hash_validity, $email)
    {
        $data = [
            'hash' => $hash,
            'hash_validity' => $hash_validity
        ];
        $this->connection->query('UPDATE %table SET %set WHERE email = %s', $this->table, $data, $email);
    }

    public function getTokenValidity($token)
    {
        return $this->connection->query('SELECT hash_validity FROM %table WHERE hash = %s', $this->table, $token)->fetchField();
    }

    public function setUserNewPassword($token, $new_password)
    {
        $data = [
            'hash' => null,
            'hash_validity' => null,
            'password' => $new_password
        ];
        $this->connection->query('UPDATE %table SET %set WHERE hash = %s', $this->table, $data, $token);
    }
}