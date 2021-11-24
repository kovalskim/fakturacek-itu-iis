<?php

/** Author: Radek Jůzl */

namespace App\BusinessModule\presenters;

use App\BusinessModule\forms\ClientsFormFactory;
use App\PublicModule\model\DatagridManager;
use App\repository\ClientRepository;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Security\User;
use Nextras\Datagrid\Datagrid;

final class ClientsPresenter extends BasePresenter
{
    /** @var ClientsFormFactory */
    private $clientsFormFactory;

    /** @var DatagridManager */
    private $datagridManager;

    /** @var User */
    public $user;

    /** @var ClientRepository */
    private $clientRepository;

    private $clientTable = 'clients';

    public function __construct(ClientsFormFactory $clientsFormFactory, DatagridManager  $datagridManager, User $user, ClientRepository $clientRepository)
    {
        parent::__construct();
        $this->clientsFormFactory = $clientsFormFactory;
        $this->datagridManager = $datagridManager;
        $this->user = $user;
        $this->clientRepository = $clientRepository;
    }

    public function actionDefault()
    {

    }

    protected function createComponentAddClientForm(): Form
    {
        $form = $this->clientsFormFactory->createClientForm();
        $form->onSuccess[] = [$this, "createAddClientFormSucceeded"];
        return $form;
    }

    /**
     * @throws AbortException
     */
    public function createAddClientFormSucceeded($form, $values)
    {
        $user_id = $this->user->getId();
        $row = ((array) $values) + ['users_id' => $user_id];
        $this->clientRepository->insertClientByUserId($row);

        $this->flashMessage('Klient byl přidán');

        if($this->isAjax())
        {
            $form->reset();
            $this->redrawControl('clientForm');
            $this['datagrid']->redrawControl('rows');
            $this->redrawControl('flashes');
        }
        else
        {
            $this->redirect('this');
        }
    }

    public function createComponentDatagrid(): Datagrid
    {
        $grid = $this->datagridManager->createDatagrid($this->clientTable, $this->getName());

        $grid->addColumn('name', 'Jméno a příjmení')->enableSort(Datagrid::ORDER_ASC);
        $grid->addColumn('cin', 'IČ');
        $grid->addColumn('email', 'E-mail')->enableSort();
        $grid->addColumn('phone', 'Telefon');
        $grid->addColumn('address', 'Adresa');

        return $grid;
    }
}
