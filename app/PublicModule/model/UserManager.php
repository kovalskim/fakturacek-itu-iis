<?php

namespace App\PublicModule\model;

use App\PublicModule\repository\UserRepository;
use Exception;
use Nette\Security\Passwords;

class UserManager
{
    /** @var UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function registrationFormSucceeded($form, $values)
    {
        $values->password = (new Passwords)->hash($values->password);
        $this->userRepository->insertUser($values);
    }

    public function registrationFormValidate($form, $values)
    {
        $email = $values->email;
        $cin = $values->cin;

        if($this->userRepository->getUserByEmail($email))
        {
            $form["email"]->addError("Tento email se už používá.");
        }
        if($this->userRepository->getUserByCin($cin))
        {
            $form["cin"]->addError("Toto IČ se už používá.");
        }
    }
}
