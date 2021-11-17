<?php

/** Author: Radek Jůzl */

namespace App\PublicModule\model;

use App\PublicModule\repository\UserRepository;
use Nette\Security\User;

class EditProfile
{
    /** @var UserRepository */
    private $userRepository;

    /** @var User*/
    public $user;

    public function __construct(UserRepository $userRepository, User $user)
    {
        $this->userRepository = $userRepository;
        $this->user = $user;
    }

    public function editProfileFormValidate($form, $values)
    {
        if($this->userRepository->getUserByEmail($values->email))
        {
            if($this->userRepository->getUserEmailById($this->user->getId()) != $values->email)
            {
                $form["email"]->addError("Tento email se už používá.");
            }

        }
    }

    public function editProfileFormSucceeded($form, $values)
    {
       $this->userRepository->updateProfile($this->user->getId(), $values);
    }


}