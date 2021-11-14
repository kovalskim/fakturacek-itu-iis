<?php

namespace App\PublicModule\model;

use App\PublicModule\repository\UserRepository;
use Exception;
use Nette\Security\Passwords;
use Nette\Utils\DateTime;

class UserManager
{
    /** @var UserRepository */
    private $userRepository;

    /** @var MailSender */
    private $mailSender;

    public function __construct(UserRepository $userRepository, MailSender $mailSender)
    {
        $this->userRepository = $userRepository;
        $this->mailSender = $mailSender;
    }

    /** Author: Radek Jůzl */
    public function registrationFormSucceeded($form, $values)
    {
        $values->password = (new Passwords)->hash($values->password);
        $this->userRepository->insertUser($values);
    }

    /** Author: Radek Jůzl */
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

    /** Author: Martin Kovalski */
    /**
     * @throws Exception
     */
    public function forgottenPasswordFormSucceeded($form, $values)
    {
        /** Check if user exists */
        $row = $this->userRepository->getUserByEmail($values->email);

        if(!$row)
        {
            throw new Exception('Účet s tímto e-mailem nebyl nalezen');
        }

        /** Password not sets, new user has the account */
        if($row->password === NULL)
        {
            throw new Exception('Heslo nebylo nastaveno');
        }

        /** Create hash and expiration date */
        $now = new DateTime();
        $hash_validity = new DateTime('+1 day');
        $hash = (new Passwords)->hash($now . $values->email);

        /** Save to database */
        $this->userRepository->setUserRecoveryCredentials($hash, $hash_validity, $values->email);

        /** Prepare parameters for e-mail */
        $subject = 'Obnova zapomenutého hesla';
        $body = 'forgottenPasswordTemplate.latte';
        $params = [
            'token' => $hash,
            'subject' => $subject
        ];

        /** Send e-mail with next steps */
        $this->mailSender->sendEmail($values->email, $subject, $body, $params);
    }

    /** Author: Martin Kovalski */
    /**
     * @throws Exception
     */
    public function checkToken($token)
    {
        /** Check if token was entered */
        if(!$token)
        {
            throw new Exception('Nebyl zadán token');
        }

        /** Check if token exists */
        $hash_validity = $this->userRepository->getTokenValidity($token);
        if(!$hash_validity)
        {
            throw new Exception('Chybný token');
        }

        /** Check token validity */
        $now = new DateTime();
        if ($hash_validity < $now) {
            throw new Exception('Vypršela platnost tokenu');
        }
    }

    /** Author: Martin Kovalski */
    public function newPasswordFormSucceeded($form, $values)
    {
        /** Delete hash and validity, set new password, update in database */
        $new_password = (new Passwords)->hash($values->password);
        $this->userRepository->setUserNewPassword($values->token, $new_password);
    }
}
