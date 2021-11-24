<?php

/** Author: Martin Kovalski */

namespace App\model;

use App\repository\UserRepository;
use Exception;
use Nette\Security\AuthenticationException;
use Nette\Security\IIdentity;
use Nette\Security\Passwords;
use Nette\Security\SimpleIdentity;

class Authenticator implements \Nette\Security\Authenticator
{
    /** @var Passwords */
    private $passwords;

    /** @var UserRepository */
    private $userRepository;

    public function __construct(Passwords $passwords, UserRepository $userRepository)
    {
        $this->passwords = $passwords;
        $this->userRepository = $userRepository;
    }

    /**
     * @throws Exception
     */
    public function authenticate(string $user, string $password): IIdentity
    {
        $row = $this->userRepository->getUserByEmail($user);

        if(!$row)
        {
            throw new AuthenticationException('Uživatel s tím e-mailem neexistuje');
        }

        if(!($this->passwords->verify($password, $row->password)))
        {
            throw new AuthenticationException('Špatně zadané heslo');
        }

        /** E-mail verification */
        if($row->verified == 0)
        {
            throw new Exception('E-mail ještě nebyl ověřen');
        }

        return new SimpleIdentity(
            $row->id,
            $row->role
        );
    }
}