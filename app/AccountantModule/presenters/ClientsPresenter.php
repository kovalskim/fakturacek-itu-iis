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
use Nette\Forms\Container;
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

    private $table = 'accountant_permission';

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
        $grid->addColumn('request_status', 'Status')->enableSort();

        $grid->setFilterFormFactory([$this, 'datagridFilterFormFactory']);
        $grid->setChangeStatusCallback([$this, 'ChangeRequestStatus']);

        $grid->addGlobalAction('accept', 'Příjmout žádosti', function (array $ids, Datagrid $grid) {
            foreach ($ids as $id)
            {
                $this->accountantRepository->AllAcceptById($id);
            }
            $this->flashMessage('Žádosti byly přijaty', 'success');
            $this->redrawControl('flashes');
            $grid->redrawControl('rows');
        });

        $grid->addGlobalAction('decline', 'Odmítnout žádosti', function (array $ids, Datagrid $grid) {
            foreach ($ids as $id) {

                $this->accountantRepository->AllDeclineById($id);
            }
            $this->flashMessage('Žádosti byly odmítnuty', 'success');
            $this->redrawControl('flashes');
            $grid->redrawControl('rows');
        });

        $grid->addGlobalAction('delete', 'Odstranit klienty', function (array $ids, Datagrid $grid) {
            foreach ($ids as $id) {

                $this->accountantRepository->AllDeleteById($id);
            }
            $this->flashMessage('Klienti byly odstraněni', 'success');
            $this->redrawControl('flashes');
            $grid->redrawControl('rows');
        });

        return $grid;
    }

    public function datagridFilterFormFactory(): Container
    {
        $form = new Container();

        $form->addText('name')
            ->setHtmlAttribute('placeholder', 'Jméno a příjmení');

        $form->addText('cin')
            ->setHtmlAttribute('placeholder', 'IČ');

        $form->addText('vat')
            ->setHtmlAttribute('placeholder', 'DIČ');

        $form->addText('email')
            ->setHtmlAttribute('placeholder', 'E-mail');

        $form->addText('phone')
            ->setHtmlAttribute('placeholder', 'Telefon');

        $form->addText('street', 'Ulice a č.p.')
            ->setHtmlAttribute('placeholder', 'Ulice a č.p.');

        $form->addText('city', 'Město')
            ->setHtmlAttribute('placeholder', 'Město');

        $form->addText('zip', 'PSČ')
            ->setHtmlAttribute('placeholder', 'PSČ');

        $form->addSelect('request_status', null, [
            'wait' => 'Čeká na přijmutí',
            'active' => 'Aktivní'
        ])
            ->setPrompt('--- Status ---');

        $form->addSubmit('filter', 'Filtrovat');
        $form->addSubmit('cancel', 'Zrušit');

        return $form;
    }

    public function ChangeRequestStatus($primary, $status)
    {
        if($status == "active")
        {
            $this->accountantRepository->updateStatusById($primary);
            $this->flashMessage('Žádost byla přijata', 'success');
        }
        elseif($status == "deleted")
        {
            $this->accountantRepository->deleteAccountant($primary);
            $this->flashMessage('Klient byl odebrán', 'success');
        }

        $this->redrawControl('flashes');
        $this->redrawControl('status');
    }

    /**
     * @throws AbortException
     */
    public function actionSummary($users_id)
    {
        $accountant_id = $this->user->getId();
        $access = $this->accountantRepository->getAllByUserIdByAccountantId($users_id, $accountant_id);
        if(!$access)
        {
            $this->flashMessage('Nemáš přístup', 'warning');
            $this->redirect(':Accountant:Clients:default');
        }
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

    /**
     * @throws AbortException
     */
    public function handleAcceptClients(): void
    {
        $this->accountantRepository->AllAcceptByAccountantId($this->user->getId());
        $this->flashMessage("Žádosti byly přijaty", "success");

        if($this->isAjax())
        {
            $this['datagrid']->redrawControl('rows');
            $this->redrawControl('flashes');
        }
        else
        {
            $this->redirect('this');
        }
    }
}
