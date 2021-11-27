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

    public function createAdministratorFormSucceeded($form, $values)
    {
        $maybe_user = $this->userRepository->getUserByEmail($values->email);
        if(isset($maybe_user->email))
        {
            throw new Exception('Uživatel s tímto e-mailem již existuje');
        }

        $values->hash = $this->userManager->createHash($values->email);
        $values->hash_validity = $this->userManager->createHashValidity();

        $values->role = 'admin';

        $this->userRepository->insertUser($values);

        /** Prepare parameters for e-mail */
        $subject = 'Vytvoření nového administrátorského účtu';
        $body = 'newAdminAccountTemplate.latte';
        $params = [
            'token' => $values->hash,
            'subject' => $subject
        ];

        /** Send e-mail with next steps */
        $this->mailSender->sendEmail($values->email, $subject, $body, $params);
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

            /** Send e-mail with next steps */
            $this->mailSender->sendEmail($this->userRepository->getUserEmailById($primary), $subject, $body, $params);
        }
    }
}