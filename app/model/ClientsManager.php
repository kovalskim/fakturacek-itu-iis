<?php

namespace App\model;

/** Author: Martin Kovalski */

use App\repository\ClientRepository;
use Nette\Security\User;

class ClientsManager
{
    /** @var User */
    private $user;

    /** @var ClientRepository */
    private $clientRepository;

    /** @var AresManager */
    private $aresManager;

    public function __construct(User $user, ClientRepository $clientRepository, AresManager $aresManager)
    {
        $this->user = $user;
        $this->clientRepository = $clientRepository;
        $this->aresManager = $aresManager;
    }

    public function editClientsFormValidate($form, $values = null)
    {
        if(!$values)
        {
            $values = $form->getValues();
        }

        if((!(is_numeric($values->zip))) or (strlen($values->zip) != 5))
        {
            $form["zip"]->addError("PSČ má špatný formát.");
        }

        if((!(is_numeric($values->cin))) or (strlen($values->cin) != 8))
        {
            $form["cin"]->addError("Toto IČ neexistuje.");
        }
        else
        {
            if($this->aresManager->verificationCin($values->cin) != 0)
            {
                $form["cin"]->addError("Toto IČ neexistuje.");
            }
        }
    }

    public function editClientsFormSucceeded($form, $values = null)
    {
        $user_id = $this->user->getId();
        if(!$values)
        {
            $values = $form->getValues();
        }

        $values->users_id = $user_id;
        $id = $values->id;

        $this->clientRepository->updateClientById($id, (array)$values);
    }
}