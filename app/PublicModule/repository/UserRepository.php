<?php

/** Author: Martin Kovalski, Radek Jůzl */

namespace App\PublicModule\repository;

use Nextras\Dbal\Result\Row;

class UserRepository extends AllRepository
{
    private $table = 'users';
    private $last_password_change_table = 'users_last_password_change';

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

    public function setUserNewPasswordByToken($token, $new_password)
    {
        $data = [
            'hash' => null,
            'hash_validity' => null,
            'password' => $new_password
        ];
        $this->connection->query('UPDATE %table SET %set WHERE hash = %s', $this->table, $data, $token);
    }

    public function getUserEmailById($user_id)
    {
        return $this->connection->query('SELECT email FROM %table WHERE id = %i', $this->table, $user_id)->fetchField();
    }

    public function setUserNewPasswordById($user_id, $new_password)
    {
        $data = [
            'password' => $new_password
        ];
        $this->connection->query('UPDATE %table SET %set WHERE id = %i', $this->table, $data, $user_id);
    }

    public function setUserLastPasswordChange($user_id)
    {
        $this->connection->query('INSERT INTO %table %values', $this->last_password_change_table, ['users_id' => $user_id]);
    }

    public function getUserProfile($user_id): ?Row
    {
        return $this->connection->query("SELECT cin, name, email, phone, account_number, street, city, zip, avatar_path FROM %table WHERE id = %i", $this->table, $user_id)->fetch();
    }

    public function setAccountAsVerified($token)
    {
        $data = [
            'hash' => null,
            'hash_validity' => null,
            'verified' => 1
        ];
        $this->connection->query('UPDATE %table SET %set WHERE hash = %s', $this->table, $data, $token);
    }
}