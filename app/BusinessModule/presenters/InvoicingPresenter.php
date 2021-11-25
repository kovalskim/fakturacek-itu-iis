<?php

/** Author: Martin Kovalski */

namespace App\BusinessModule\presenters;

use App\forms\InvoicingFormFactory;
use App\model\ClientsManager;
use App\model\DatagridManager;
use App\model\InvoicingManager;
use App\repository\ClientRepository;
use App\repository\InvoicingRepository;
use Nette\Application\UI\Form;
use Nette\Forms\Container;
use Nextras\Datagrid\Datagrid;

final class InvoicingPresenter extends BasePresenter
{
    /** @var DatagridManager */
    private $datagridManager;

    /** @var InvoicingManager */
    private $invoicingManager;

    /** @var InvoicingRepository */
    private $invoicingRepository;

    /** @var InvoicingFormFactory */
    private $invoicingFormFactory;

    /** @var ClientRepository */
    private $clientRepository;

    /** @var ClientsManager */
    private $clientsManager;

    public function __construct(DatagridManager $datagridManager, InvoicingManager $invoicingManager, InvoicingRepository $invoicingRepository, InvoicingFormFactory $invoicingFormFactory, ClientRepository $clientRepository, ClientsManager $clientsManager)
    {
        parent::__construct();
        $this->datagridManager = $datagridManager;
        $this->invoicingManager = $invoicingManager;
        $this->invoicingRepository = $invoicingRepository;
        $this->invoicingFormFactory = $invoicingFormFactory;
        $this->clientRepository = $clientRepository;
        $this->clientsManager = $clientsManager;
    }

    public function actionDefault()
    {

    }

    public function createComponentDatagrid(): Datagrid
    {
        $grid = $this->datagridManager->createDatagrid('invoices', $this->getName());

        $grid->addColumn('created', 'Datum vystavení')->enableSort(Datagrid::ORDER_ASC);
        $grid->addColumn('client_name', 'Klient')->enableSort();
        $grid->addColumn('variable_symbol', 'VS')->enableSort();
        $grid->addColumn('suma', 'Celkem')->enableSort();
        $grid->addColumn('status', 'Status')->enableSort();
        $grid->addColumn('due_date', 'Datum splatnosti')->enableSort();

        $grid->setChangeStatusCallback([$this, 'handleChangeInvoiceStatus']);

        $grid->setFilterFormFactory([$this, 'datagridFilterFormFactory']);

        $grid->addGlobalAction('paid', 'Zaplaceno', function (array $ids, Datagrid $grid) {
            foreach ($ids as $id) {
                $this->invoicingRepository->updateInvoiceStatus($id, 'paid');
            }
            $this->flashMessage('Faktury byly označené jako zaplacené', 'success');
            $this->redrawControl('flashes');
            $grid->redrawControl('rows');
        });

        $grid->addGlobalAction('canceled', 'Stornovat', function (array $ids, Datagrid $grid) {
            foreach ($ids as $id) {
                $this->invoicingRepository->updateInvoiceStatus($id, 'canceled');
            }
            $this->flashMessage('Faktury byly stornovány', 'success');
            $this->redrawControl('flashes');
            $grid->redrawControl('rows');
        });

        return $grid;
    }

    /**
     * @secured
     */
    public function handleChangeInvoiceStatus($primary, $status)
    {
        $this->invoicingRepository->updateInvoiceStatus($primary, $status);

        $this->flashMessage('Status byl změněn', 'success');
        $this->redrawControl('flashes');
        $this->redrawControl('status');
        $this->redrawControl('status_links');
    }

    public function datagridFilterFormFactory(): Container
    {
        $form = new Container();

        $form->addText('client_name')
            ->setHtmlAttribute('placeholder', 'Klient');

        $form->addText('variable_symbol')
        ->setHtmlAttribute('placeholder', 'VS');

        $form->addText('suma')
            ->setHtmlAttribute('placeholder', 'Celkem');

        $form->addSelect('status', null, [
            'unpaid' => 'Nezaplacená',
            'paid' => 'Zaplacená',
            'canceled' => 'Stornovaná'
        ])
            ->setPrompt('--- Status ---');

        $form->addSubmit('filter', 'Filtrovat');
        $form->addSubmit('cancel', 'Zrušit');

        return $form;
    }

    public function actionInvoice($id)
    {
        $user_id = $this->user->getId();
        $invoice = $this->invoicingRepository->getInvoiceByIdAndUserId($id, $user_id);
        if(!$invoice)
        {
            $this->flashMessage('Požadovaná faktura nebyla nalezena', 'warning');
            $this->redirect(':Business:Invoicing:default');
        }
    }

    public function renderInvoice($id)
    {
        $user_id = $this->user->getId();
        $this->template->invoice = $this->invoicingRepository->getInvoiceByIdAndUserId($id, $user_id);
        $this->template->invoice_items = $this->invoicingRepository->getInvoiceItemsById($id);
    }

    public function handleSearch()
    {
        if($this->isAjax())
        {
            $client = $this->getParameter('client');
            if($client != null)
            {
                $this->template->results = $this->invoicingRepository->getResultsByString($client);
            }
            $this->redrawControl('results');
        }
    }

    public function handleSelect($selected_client_id)
    {
        if($this->isAjax())
        {
            $client = $this->clientRepository->getClientById($selected_client_id);
            $this->getComponent('createInvoiceForm')->setDefaults($client);
            $this->redrawControl('createInvoiceForm');
            $this->redrawControl('results');
        }
    }

    public function createComponentCreateInvoiceForm(): Form
    {
        $form = $this->invoicingFormFactory->createInvoiceForm();

        $form->onValidate[] = [$this, 'createInvoiceFormValidate'];
        $form->onSuccess[] = [$this, 'createInvoiceFormSucceeded'];

        return $form;
    }

    public function createInvoiceFormValidate($form, $values)
    {
        $this->clientsManager->editClientsFormValidate($form, $values);
    }

    public function createInvoiceFormSucceeded($form, $values)
    {
        bdump($values);

        //TODO: if not $values->id - neni ulozen klient

        $invoice_values = [

        ];
    }
}