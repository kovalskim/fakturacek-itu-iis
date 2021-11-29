<?php

namespace App\model;

/** Author: Martin Kovalski */

use App\repository\UserRepository;
use Exception;

class AdministratorsManager
{
    /** @var MailSender */
    private $mailSender;

    /** @var UserRepository */
    private $userRepository;

    /** @var UserManager */
    private $userManager;

    public function __construct(MailSender $mailSender, UserRepository $userRepository, UserManager $userManager)
    {
        $this->mailSender = $mailSender;
        $this->userRepository = $userRepository;
        $this->userManager = $userManager;
    }

    /**
     * Create admin form
     * @throws Exception
     */
    public function createAdministratorFormSucceeded($form, $values)
    {
        /** Check if user exists */
        $maybe_user = $this->userRepository->getUserByEmail($values->email);
        if(isset($maybe_user->email))
        {
            throw new Exception('Uživatel s tímto e-mailem již existuje');
        }

        /** Generate hash */
        $values->hash = $this->userManager->createHash($values->email);
        $values->hash_validity = $this->userManager->createHashValidity();

        /** Set role */
        $values->role = 'admin';

        /** Insert into database */
        $this->userRepository->insertUser($values);

        /** Prepare parameters for e-mail */
        $subject = 'Vytvoření nového administrátorského účtu';
        $body = 'newAdminAccountTemplate.latte';
        $params = [
            'token' => $values->hash,
            'subject' => $subject
        ];

        /** Send e-mail with request to create new password */
        $this->mailSender->sendEmail($values->email, $subject, $body, $params);
    }

    /**
     * Function for banning admin
     */
    public function ban($primary)
    {
        $status = $this->userRepository->getUserStatusById($primary);
        /** Check if not banned */
        if($status != 'banned')
        {
            $this->userRepository->updateUserStatus($primary,'banned');

            /** Prepare parameters for e-mail */
            $subject = 'Váš účet byl zablokován';
            $body = 'banAccountTemplate.latte';
            $params = [
                'subject' => $subject
            ];

            /** Send e-mail with notification about ban */
            $this->mailSender->sendEmail($this->userRepository->getUserEmailById($primary), $subject, $body, $params);
        }
    }

    /**
     * Function for allow access to admin
     */
    public function allow($primary)
    {
        $status = $this->userRepository->getUserStatusById($primary);
        if($status == 'banned')
        {
            /** Get origin status back */
            if(!$this->userRepository->isPasswordExists($primary))
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

            /** Send e-mail with message about allow access */
            $this->mailSender->sendEmail($this->userRepository->getUserEmailById($primary), $subject, $body, $params);
        }
    }
}