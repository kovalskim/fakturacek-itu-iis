<?php

namespace App\PublicModule\model;

use App\PublicModule\repository\UserRepository;
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

    public function authenticate(string $user, string $password): IIdentity
    {
        $row = $this->userRepository->getUserByEmail($user);

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