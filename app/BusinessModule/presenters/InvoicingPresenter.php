<?php

/** Author: Martin Kovalski */

namespace App\BusinessModule\presenters;

use App\model\DatagridManager;
use App\model\InvoicingManager;
use App\repository\InvoicingRepository;
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

    public function __construct(DatagridManager $datagridManager, InvoicingManager $invoicingManager, InvoicingRepository $invoicingRepository)
    {
        parent::__construct();
        $this->datagridManager = $datagridManager;
        $this->invoicingManager = $invoicingManager;
        $this->invoicingRepository = $invoicingRepository;
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
}