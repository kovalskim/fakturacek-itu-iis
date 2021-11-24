<?php

namespace App\model;

use App\repository\ClientRepository;
use Nette\Security\User;

/** Author: Martin Kovalski */

class ClientsManager
{
    /** @var User */
    private $user;

    /** @var ClientRepository */
    private $clientRepository;

    public function __construct(User $user, ClientRepository $clientRepository)
    {
        $this->user = $user;
        $this->clientRepository = $clientRepository;
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