<?php

namespace App\model;

use App\repository\SettingInvoicesRepository;
use App\repository\UserRepository;
use Exception;
use Nette\Security\AuthenticationException;
use Nette\Security\Passwords;
use Nette\Security\User;
use Nette\Utils\DateTime;

class UserManager
{
    /** @var UserRepository */
    private $userRepository;

    /** @var MailSender */
    private $mailSender;

    /** @var User */
    private $user;

    /** @var Authenticator */
    private $authenticator;

    /** @var SettingInvoicesRepository */
    private $settingInvoicesRepository;

    /** @var AresManager */
    private $aresManager;

    public function __construct(UserRepository $userRepository, MailSender $mailSender, User $user, Authenticator $authenticator, SettingInvoicesRepository $settingInvoicesRepository, AresManager $aresManager)
    {
        $this->userRepository = $userRepository;
        $this->mailSender = $mailSender;
        $this->user = $user;
        $this->authenticator = $authenticator;
        $this->settingInvoicesRepository = $settingInvoicesRepository;
        $this->aresManager = $aresManager;
    }

    /** Author: Martin Kovalski */
    public function createHash($email): string
    {
        return (new Passwords)->hash(new DateTime() . $email);
    }

    /** Author: Martin Kovalski */
    public function createHashValidity(): DateTime
    {
        return new DateTime('+1 day');
    }

    /** Author: Radek Jůzl, Martin Kovalski */
    public function registrationFormSucceeded($form, $values)
    {
        $values->password = (new Passwords)->hash($values->password);
        $values->hash = $this->createHash($values->email);
        $values->hash_validity = $this->createHashValidity();
        $this->userRepository->insertUser($values);
        if($values->role == "business")
        {
            $this->settingInvoicesRepository->insertIdBusiness();
        }

        /** Prepare parameters for e-mail */
        $subject = 'Ověření e-mailové adresy';
        $body = 'verificationAccountTemplate.latte';
        $params = [
            'token' => $values->hash,
            'subject' => $subject
        ];

        /** Send e-mail with next steps */
        $this->mailSender->sendEmail($values->email, $subject, $body, $params);
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

        if($this->aresManager->verificationCin($cin) != 0)
        {
            $form["cin"]->addError("Toto IČ neexistuje.");
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
        $hash_validity = $this->createHashValidity();
        $hash = $this->createHash($values->email);

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

    /** Author: Martin Kovalski, Radek Jůzl */
    public function newPasswordFormSucceeded($form, $values)
    {
        /** Delete hash and validity, set new password, update in database */
        $new_password = (new Passwords)->hash($values->password);
        $user_id = $this->userRepository->userIdByToken($values->token);
        $this->userRepository->setUserNewPasswordByToken($values->token, $new_password);
        $this->userRepository->deleteUserLastPasswordChange($user_id);
        $this->userRepository->setUserLastPasswordChange($user_id);
    }

    /** Author: Martin Kovalski */
    public function changePasswordFormSucceeded($form, $values)
    {
        $user_id = $this->user->getId();

        /** Get user´s e-mail */
        $email = $this->userRepository->getUserEmailById($user_id);

        /** Autenticate user */
        try
        {
            $this->authenticator->authenticate($email, $values->old_password);
        }
        catch (AuthenticationException $e)
        {
            throw new Exception('Zadané heslo není správné');
        }

        /** Create hash from new password */
        $new_password = (new Passwords)->hash($values->password);

        /** Update password in database */
        $this->userRepository->setUserNewPasswordById($user_id, $new_password);

        /** Save date of last password change */
        $this->userRepository->deleteUserLastPasswordChange($user_id);
        $this->userRepository->setUserLastPasswordChange($user_id);
    }

    public function ban($primary)
    {
        $status = $this->userRepository->getUserStatusById($primary);
        if($status != 'banned')
        {
            $this->userRepository->updateUserStatus($primary,'banned');

            /** Prepare parameters for e-mail */
            $subject = 'Váš účet byl zablokován';
            $body = 'banAccountTemplate.latte';
            $params = [
                'subject' => $subject
            ];

            /** Send e-mail with next steps */
            $this->mailSender->sendEmail($this->userRepository->getUserEmailById($primary), $subject, $body, $params);
        }
    }

    public function allow($primary)
    {
        $status = $this->userRepository->getUserStatusById($primary);
        if($status == 'banned')
        {
            if(!$this->userRepository->isLastLogin($primary))
            {
                $this->userRepository->updateUserStatus($primary, 'new');
            }
            else
            {
                $this->userRepository->updateUserStatus($primary, 'active');
            }

            /** Prepare parameters for e-mail */
            $subject = 'Váš účet byl odblokován';
            $body = 'allowAccountTemplate.latte';
            $params = [
                'subject' => $subject
            ];

            /** Send e-mail with next steps */
            $this->mailSender->sendEmail($this->userRepository->getUserEmailById($primary), $subject, $body, $params);
        }
    }

    public function resetPassword($primary)
    {
        $email = $this->userRepository->getUserEmailById($primary);
        $hash_validity = $this->createHashValidity();
        $hash = $this->createHash($email);

        /** Save to database */
        $this->userRepository->setUserRecoveryCredentials($hash, $hash_validity, $email);

        /** Prepare parameters for e-mail */
        $subject = 'Reset hesla';
        $body = 'forgottenPasswordTemplate.latte';
        $params = [
            'token' => $hash,
            'subject' => $subject
        ];

        /** Send e-mail with next steps */
        $this->mailSender->sendEmail($email, $subject, $body, $params);
    }
}
