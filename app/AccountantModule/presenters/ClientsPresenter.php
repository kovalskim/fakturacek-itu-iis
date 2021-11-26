<?php

/** Author: Radek Jůzl */

namespace App\AccountantModule\presenters;

use App\forms\ClientsAccountantFormFactory;
use App\model\DatagridManager;
use App\model\UserManager;
use App\repository\AccountantRepository;
use Exception;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Security\User;
use Nextras\Datagrid\Datagrid;

final class ClientsPresenter extends BasePresenter
{
    /** @var ClientsAccountantFormFactory */
    private $clientsAccountantFormFactory;

    /** @var DatagridManager */
    private $datagridManager;

    /** @var UserManager */
    private $userManager;

    /** @var User */
    public $user;

    /** @var AccountantRepository */
    private $accountantRepository;

    private $table = 'users';

    public function __construct(ClientsAccountantFormFactory $clientsAccountantFormFactory, DatagridManager $datagridManager, UserManager $userManager, User $user, AccountantRepository $accountantRepository)
    {
        parent::__construct();
        $this->clientsAccountantFormFactory = $clientsAccountantFormFactory;
        $this->datagridManager = $datagridManager;
        $this->userManager = $userManager;
        $this->user = $user;
        $this->accountantRepository = $accountantRepository;
    }

    public function actionDefault()
    {

    }

    protected function createComponentClientConnectionForm(): Form
    {
        $form = $this->clientsAccountantFormFactory->createConnectionForm();
        //$form->onValidate[] = [$this, "clientConnectionFormValidate"];
        $form->onSuccess[] = [$this, "clientConnectionFormSucceeded"];
        return $form;
    }

    /**
     * @throws AbortException
     */
    public function clientConnectionFormSucceeded($form, $values)
    {
        try
        {
            $this->userManager->addClientAccountant($values->email, $this->user->getId(), "accountant");
            $this->flashMessage("Žádost o přidaní byla odeslána", 'success');
            if($this->isAjax())
            {
                $form->reset();
                $this->redrawControl('clientConnectionForm');
                $this['datagrid']->redrawControl('rows');
                $this->redrawControl('flashes');
            }
            else
            {
                $this->redirect('this');
            }
        }
        catch (Exception $e)
        {
            $this->flashMessage($e->getMessage(), 'danger');
            if($this->isAjax())
            {
                $this->redrawControl('clientConnectionForm');
                $this->redrawControl('flashes');
            }
            else
            {
                $this->redirect('this');
            }
        }

    }

    public function createComponentDatagrid(): Datagrid
    {
        $grid = $this->datagridManager->createDatagrid($this->table, $this->getName());

        $grid->addColumn('name', 'Jméno a příjmení')->enableSort(Datagrid::ORDER_ASC);
        $grid->addColumn('cin', 'IČ');
        $grid->addColumn('vat', 'DIČ');
        $grid->addColumn('email', 'E-mail')->enableSort();
        $grid->addColumn('phone', 'Telefon');
        $grid->addColumn('street', 'Ulice a č.p.');
        $grid->addColumn('city', 'Město');
        $grid->addColumn('zip', 'PSČ');
        $grid->addColumn('status', 'Status')->enableSort();

        return $grid;
    }

    /**
     * @throws AbortException
     */
    public function actionAddConnection($token)
    {
        try
        {
            $this->userManager->checkToken($token, "accountant_permission");
        }
        catch (Exception $e)
        {
            $this->flashMessage($e->getMessage(), 'danger');
            $this->redirect(':Accountant:Clients:default');
        }

        $this->accountantRepository->updateStatus($token);
        $this->flashMessage("Přidán klient", 'success');
        $this->redirect(':Accountant:Clients:default');
    }
}
