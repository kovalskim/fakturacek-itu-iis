<?php

/** Author: Radek Jůzl */

namespace App\AccountantModule\presenters;

use App\forms\ClientsAccountantFormFactory;
use App\model\DatagridManager;
use App\model\UserManager;
use App\repository\AccountantRepository;
use App\repository\InvoicingRepository;
use App\repository\UserRepository;
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

    /** @var UserRepository */
    private $userRepository;

    /** @var InvoicingRepository */
    private $invoicingRepository;

    private $table = 'accountant_permission';
    private $client_id;

    public function __construct(ClientsAccountantFormFactory $clientsAccountantFormFactory, DatagridManager $datagridManager, UserManager $userManager, User $user, AccountantRepository $accountantRepository, UserRepository $userRepository, InvoicingRepository $invoicingRepository)
    {
        parent::__construct();
        $this->clientsAccountantFormFactory = $clientsAccountantFormFactory;
        $this->datagridManager = $datagridManager;
        $this->userManager = $userManager;
        $this->user = $user;
        $this->accountantRepository = $accountantRepository;
        $this->userRepository = $userRepository;
        $this->invoicingRepository = $invoicingRepository;
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
        $this->client_id = $users_id;
    }

    public function renderSummary($users_id)
    {
        $this->template->userData = $this->userRepository->getUserById($users_id);
    }

    public function createComponentDatagridInvoices(): Datagrid
    {
        $grid = $this->datagridManager->createDatagrid('invoices', $this->getName(), $this->client_id, "invoices");

        $grid->addColumn('created', 'Datum vystavení')->enableSort(Datagrid::ORDER_ASC);
        $grid->addColumn('client_name', 'Klient')->enableSort();
        $grid->addColumn('variable_symbol', 'VS')->enableSort();
        $grid->addColumn('suma', 'Celkem')->enableSort();
        $grid->addColumn('status', 'Status')->enableSort();
        $grid->addColumn('due_date', 'Datum splatnosti')->enableSort();

        $grid->setFilterFormFactory([$this, 'datagridFilterInvoicesFormFactory']);

        return $grid;
    }

    public function datagridFilterInvoicesFormFactory(): Container
    {
        $form = new Container();

        $form->addText('created')
            ->setHtmlAttribute('placeholder', 'Datum vystavení');

        $form->addText('client_name')
            ->setHtmlAttribute('placeholder', 'Klient');

        $form->addText('variable_symbol')
            ->setHtmlAttribute('placeholder', 'VS');

        $form->addText('suma')
            ->setHtmlAttribute('placeholder', 'Celkem');

        $form->addSelect('status', null, [
            'unpaid' => 'Nezaplacená',
            'paid' => 'Zaplacená',
            'canceled' => 'Stornována'
        ]);

        $form->addText('due_date', 'Datum splatnosti')
            ->setHtmlAttribute('placeholder', 'Datum splatnosti');


        $form->addSubmit('filter', 'Filtrovat');
        $form->addSubmit('cancel', 'Zrušit');

        return $form;
    }

    public function createComponentDatagridExpenses(): Datagrid
    {
        $grid = $this->datagridManager->createDatagrid('expenses', $this->getName(), $this->client_id, "expenses");

        $grid->addColumn('datetime', 'Datum zaplacení')->enableSort(Datagrid::ORDER_ASC);
        $grid->addColumn('items', 'Název položky')->enableSort();
        $grid->addColumn('price', 'Cena')->enableSort();
        $grid->addColumn('name', 'Kategorie')->enableSort();
        $grid->addColumn('path', 'Účtenka');

        $grid->setFilterFormFactory([$this, 'datagridFilterExpensesFormFactory']);

        return $grid;
    }

    public function datagridFilterExpensesFormFactory(): Container
    {
        $form = new Container();

        $form->addText('datetime')
            ->setHtmlAttribute('placeholder', 'Datum zaplacení');

        $form->addText('items')
            ->setHtmlAttribute('placeholder', 'Název položky');

        $form->addText('price')
            ->setHtmlAttribute('placeholder', 'Cena');

        $form->addText('name')
            ->setHtmlAttribute('placeholder', 'Kategorie');

        $form->addSubmit('filter', 'Filtrovat');
        $form->addSubmit('cancel', 'Zrušit');

        return $form;
    }

    /**
     * @throws AbortException
     */
    public function actionInvoice($id, $users_id)
    {
        $accountant_id = $this->user->getId();
        $access = $this->accountantRepository->getAllByUserIdByAccountantId($users_id, $accountant_id);

        if(!$access)
        {
            $this->flashMessage('Nemáš přístup', 'warning');
            $this->redirect(':Accountant:Clients:default');
        }

        $invoice = $this->invoicingRepository->getInvoiceByIdAndUserId($id, $users_id);
        if(!$invoice)
        {
            $this->flashMessage('Požadovaná faktura nebyla nalezena', 'warning');
            $this->redirect(':Business:Invoicing:default');
        }
    }

    public function renderInvoice($id, $users_id)
    {
        $this->template->invoice = $this->invoicingRepository->getInvoiceByIdAndUserId($id, $users_id);
        $this->template->invoice_items = $this->invoicingRepository->getInvoiceItemsById($id);
    }
}
