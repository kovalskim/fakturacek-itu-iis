<?php

/** Author: Martin Kovalski, Radek Jůzl */

namespace App\repository;

use Nextras\Dbal\Result\Row;

class UserRepository extends AllRepository
{
    private $table = 'users';
    private $last_password_change_table = 'users_last_password_change';
    private $users_last_login = "users_last_login";

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
            'password' => $new_password,
            'status' => 'active'
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

    public function deleteUserLastPasswordChange($user_id)
    {
        $this->connection->query('DELETE FROM %table WHERE users_id = %i', $this->last_password_change_table, $user_id);
    }

    public function userIdByToken($token)
    {
        return $this->connection->query('SELECT id FROM %table WHERE hash = %s', $this->table, $token)->fetchField();
    }

    public function getUserProfile($user_id): ?Row
    {
        return $this->connection->query("SELECT cin, name, email, phone, street, city, zip, avatar_path FROM %table WHERE id = %i", $this->table, $user_id)->fetch();
    }

    public function setAccountAsVerified($token)
    {
        $data = [
            'hash' => null,
            'hash_validity' => null,
            'status' => 'active'
        ];
        $this->connection->query('UPDATE %table SET %set WHERE hash = %s', $this->table, $data, $token);
    }

    public function updateProfile($user_id, $values)
    {
        $this->connection->query("UPDATE %table SET %set WHERE id = %i", $this->table, (array) $values, $user_id);
    }

    public function getUserAvatar($user_id)
    {
        return $this->connection->query("SELECT avatar_path FROM %table WHERE id = %i", $this->table, $user_id)->fetchField();
    }

    public function updateUserStatus($user_id, $status)
    {
        $this->connection->query("UPDATE %table SET status = %s WHERE id = %i", $this->table, $status, $user_id);
    }

    public function insertLastLoginById($user_id)
    {
        $this->connection->query("INSERT INTO %table %values", $this->users_last_login, ['users_id' => $user_id]);
    }

    public function deleteLastLoginById($user_id)
    {
        $this->connection->query("DELETE FROM %table WHERE users_id = %i", $this->users_last_login, $user_id);
    }

    public function isPasswordExists($user_id): bool
    {
        if($this->connection->query('SELECT password FROM %table WHERE id = %i', $this->table, $user_id)->fetchField())
        {
            return true;
        }
        return false;
    }

    public function getUserStatusById($id)
    {
        return $this->connection->query('SELECT status FROM %table WHERE id = %i', $this->table, $id)->fetchField();
    }

    public function isLastLogin($user_id): bool
    {
        if($this->connection->query("SELECT timestamp FROM %table WHERE id = %i", $this->users_last_login, $user_id)->fetchField())
        {
            return true;
        }
        return false;
    }
}