<?php

/** Author: Radek Jůzl */

namespace App\PublicModule\model;

use App\repository\UserRepository;
use Nette\Security\User;

class EditProfile
{
    /** @var UserRepository */
    private $userRepository;

    /** @var User*/
    public $user;

    /** @var MailSender */
    private $mailSender;

    /** @var UserManager */
    private $userManager;

    public function __construct(UserRepository $userRepository, User $user, MailSender $mailSender, UserManager $userManager)
    {
        $this->userRepository = $userRepository;
        $this->user = $user;
        $this->mailSender = $mailSender;
        $this->userManager = $userManager;
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

    public function editProfileFormSucceeded($form, $values): int
    {
        $new_email = 0;
        $id = $this->user->getId();
        if($values->email != $this->userRepository->getUserEmailById($id))
        {
            $new_email = 1;
            $values->hash = $this->userManager->createHash($values->email);
            $values->hash_validity = $this->userManager->createHashValidity();
            $this->userRepository->updateUserVerified($id, 0);

            $subject = 'Ověření e-mailové adresy';
            $body = 'verificationAccountTemplate.latte';
            $params = [
                'token' => $values->hash,
                'subject' => $subject
            ];

            /** Send e-mail with next steps */
            $this->mailSender->sendEmail($values->email, $subject, $body, $params);
        }
        $this->userRepository->updateProfile($this->user->getId(), $values);
        return $new_email;
    }


}