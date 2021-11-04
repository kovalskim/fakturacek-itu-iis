<?php

namespace App\PublicModule\model;

use Nette\Security\AuthenticationException;
use Nette\Security\IIdentity;
use Nette\Security\Passwords;
use Nette\Security\SimpleIdentity;
use Nextras\Dbal\Connection;

class Authenticator implements \Nette\Security\Authenticator
{
    /** @var Connection */
    private $connection;

    /** @var Passwords */
    private $passwords;

    public function __construct(Connection $connection, Passwords $passwords)
    {
        $this->connection = $connection;
        $this->passwords = $passwords;
    }

    public function authenticate(string $user, string $password): IIdentity
    {
        $row = $this->connection->query('SELECT * FROM users WHERE email = %s', $user)->fetch();

        if(!$row)
        {
            throw new AuthenticationException();
        }

        if(!($this->passwords->verify($password, $row->password)))
        {
            throw new AuthenticationException();
        }

        return new SimpleIdentity(
            $row->id,
            $row->role
        );
    }
}