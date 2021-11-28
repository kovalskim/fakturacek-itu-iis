<?php

/** Author: Martin Kovalski */

namespace App\BusinessModule\presenters;

use App\forms\InvoicingFormFactory;
use App\model\ClientsManager;
use App\model\DatagridManager;
use App\model\InvoicingManager;
use App\model\MailSender;
use App\repository\ClientRepository;
use App\repository\InvoicingRepository;
use App\repository\SettingInvoicesRepository;
use App\repository\UserRepository;
use Exception;
use Mpdf\MpdfException;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Forms\Container;
use Nette\Utils\DateTime;
use Nextras\Datagrid\Datagrid;
use Mpdf\Mpdf;
use Nette\Application\UI\TemplateFactory;

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

    /** @var UserRepository */
    private $userRepository;

    /** @var SettingInvoicesRepository */
    private $settingInvoicesRepository;

    /** @var TemplateFactory @inject */
    public $templateFactory;

    /** @var MailSender */
    public $mailSender;

    public function __construct(DatagridManager $datagridManager, InvoicingManager $invoicingManager, InvoicingRepository $invoicingRepository, InvoicingFormFactory $invoicingFormFactory, ClientRepository $clientRepository, ClientsManager $clientsManager, UserRepository $userRepository, SettingInvoicesRepository $settingInvoicesRepository, MailSender $mailSender)
    {
        parent::__construct();
        $this->datagridManager = $datagridManager;
        $this->invoicingManager = $invoicingManager;
        $this->invoicingRepository = $invoicingRepository;
        $this->invoicingFormFactory = $invoicingFormFactory;
        $this->clientRepository = $clientRepository;
        $this->clientsManager = $clientsManager;
        $this->userRepository = $userRepository;
        $this->settingInvoicesRepository = $settingInvoicesRepository;
        $this->mailSender = $mailSender;
    }

    public function actionDefault()
    {

    }

    public function createComponentDatagrid(): Datagrid
    {
        $grid = $this->datagridManager->createDatagrid('invoices', $this->getName());

        $grid->addColumn('created', 'Datum vystavení')->enableSort(Datagrid::ORDER_DESC);
        $grid->addColumn('client_name', 'Klient')->enableSort();
        $grid->addColumn('variable_symbol', 'VS')->enableSort();
        $grid->addColumn('suma', 'Celkem')->enableSort();
        $grid->addColumn('status', 'Status')->enableSort();
        $grid->addColumn('due_date', 'Datum splatnosti')->enableSort();

        $grid->setChangeStatusCallback([$this, 'handleChangeInvoiceStatus']);

        $grid->setFilterFormFactory([$this, 'datagridFilterFormFactory']);

        $grid->setDownloadInvoiceCallback([$this, 'handleDownloadInvoice']);

        $grid->setSendReminderCallback([$this, 'sendReminder']);

        $grid->addGlobalAction('paid', 'Zaplaceno', function (array $ids, Datagrid $grid) {
            foreach ($ids as $id) {
                $this->invoicingRepository->updateInvoiceStatus($id, 'paid');
                $invoice = $this->invoicingRepository->getInvoiceDataById($id);

                //email o zaplaceni
                $subject = "Faktura č. " . $invoice->variable_symbol;
                $body = 'paidInvoiceTemplate.latte';
                $params = [
                    'subject' => $subject,
                    'name' => $invoice->user_name
                ];

                $this->mailSender->sendEmail($invoice->client_email, $subject, $body, $params);
            }
            $this->flashMessage('Faktury byly označené jako zaplacené', 'success');
            $this->redrawControl('flashes');
            $grid->redrawControl('rows');
        });

        $grid->addGlobalAction('canceled', 'Stornovat', function (array $ids, Datagrid $grid) {
            foreach ($ids as $id) {
                $this->invoicingRepository->updateInvoiceStatus($id, 'canceled');

                $invoice = $this->invoicingRepository->getInvoiceDataById($id);

                //email o zrušeni
                $subject = "Faktura č. " . $invoice->variable_symbol;
                $body = 'canceledInvoiceTemplate.latte';
                $params = [
                    'subject' => $subject,
                    'name' => $invoice->user_name
                ];

                $this->mailSender->sendEmail($invoice->client_email, $subject, $body, $params);
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
        $invoice = $this->invoicingRepository->getInvoiceDataById($primary);
        if($status == 'paid')
        {
            //email o zaplaceni
            $subject = "Faktura č. " . $invoice->variable_symbol;
            $body = 'paidInvoiceTemplate.latte';
            $params = [
                'subject' => $subject,
                'name' => $invoice->user_name
            ];

            $this->mailSender->sendEmail($invoice->client_email, $subject, $body, $params);
        }
        elseif($status == 'canceled')
        {
            //email o zrušeni
            $subject = "Faktura č. " . $invoice->variable_symbol;
            $body = 'canceledInvoiceTemplate.latte';
            $params = [
                'subject' => $subject,
                'name' => $invoice->user_name
            ];

            $this->mailSender->sendEmail($invoice->client_email, $subject, $body, $params);
        }

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

    public function actionNewInvoice()
    {
        $user_id = $this->user->getId();
        $settings = $this->settingInvoicesRepository->selectAll($user_id);
        if($settings->account_number == null)
        {
            $this->flashMessage('Není vyplněno nastavení faktury', 'warning');
            $this->redirect(':Business:SettingInvoices:default');
        }
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

        $form->onAnchor[] = [$this, 'createInvoiceFormAnchor'];
        $form->onSuccess[] = [$this, 'createInvoiceFormSucceeded'];

        $form->addSubmit('addInvoice', 'Vystavit fakturu')
            ->onClick[] = [$this, 'createInvoiceFormValidate'];

        return $form;
    }

    public function createInvoiceFormAnchor($form)
    {
        $this->redrawControl('createInvoiceForm');
    }

    public function createInvoiceFormValidate($button)
    {
        $this->clientsManager->editClientsFormValidate($button->getForm());
        //$this->invoicingManager->editClientsFormValidate($button->getForm()); //TODO: S ajaxem funguje samo kontrola
        //$this->redrawControl('createInvoiceForm'); //TODO: Nefunguje - pridani do formulare class ajax
    }

    /**
     * @throws AbortException
     * @throws Exception
     */
    public function createInvoiceFormSucceeded($form, $values)
    {
        $user_id = $this->user->getId();
        $user = $this->userRepository->getUserById($user_id);

        $created = new DateTime();
        $due_date = $created->modifyClone('+' . $values->due_days_number . ' day');

        $setting_invoices = $this->settingInvoicesRepository->selectAll($user_id);

        $variable_symbol_pattern = $setting_invoices->variable_symbol;
        $variable_symbol = $this->invoicingManager->getNewVariableSymbol($user_id, $variable_symbol_pattern);

        $invoice_values = [
            'users_id' => $user_id,
            'user_name' => $user->name,
            'user_street' => $user->street,
            'user_city' => $user->city,
            'user_zip' => $user->zip,
            'user_cin' => $user->cin,
            'user_vat' => $user->vat,
            'user_phone' => $user->phone,
            'user_email' => $user->email,
            'client_name' => $values->name,
            'client_street' => $values->street,
            'client_city' => $values->city,
            'client_zip' => $values->zip,
            'client_cin' => $values->cin,
            'client_vat' => $values->vat,
            'client_phone' => $values->phone,
            'client_email' => $values->email,
            'created' => $created,
            'due_date' => $due_date,
            'account_number' => $setting_invoices->account_number,
            'variable_symbol' => $variable_symbol,
            'logo_path' => $setting_invoices->logo_path,
            'vat_note' => $setting_invoices->vat_note,
            'footer_note' => $setting_invoices->vat_note,
            'status' => 'unpaid',
            'suma' => 0
        ];
        if($values->id)
        {
            $invoice_values += ['client_id' => $values->id];
        }
        else
        {
            $invoice_values += ['client_id' => null];
        }


        $this->invoicingRepository->insertInvoice($invoice_values);
        $id_invoices = $this->invoicingRepository->lasIdInvoice();

        $suma = $this->invoicingManager->saveInvoicesItems($values, $id_invoices);
        $invoice_values['suma'] = $suma;

        $this->invoicingRepository->updateSuma($suma, $id_invoices);

        $pdf = $this->getExportPdf($id_invoices);

        if($values->email)
        {
            //poslu fa na mail
            $content = $pdf->Output('invoice.pdf', 'S');

            $subject = "Faktura č. " . $variable_symbol;
            $body = 'newInvoiceTemplate.latte';
            $params = [
                'subject' => $subject,
                'name' => $user->name
            ];

            $this->mailSender->sendEmail($values->email, $subject, $body, $params, $content);
            $this->flashMessage("Faktura byla uložena a poslána klientovi na e-mail", "success");
        }
        else
        {
            //stahnu
            //$pdf->Output('invoice.pdf', 'D');
            $this->flashMessage("Faktura byla uložena", "success");
        }
        if($values->addClient)
        {
            if($this->invoicingManager->saveClient($values))
            {
                $this->flashMessage("Klient se uložil", "success");
            }
            else
            {
                $this->flashMessage("Klient nebyl uložen, již existuje se stejnými údaji", "warning");
            }
        }

        $this->redirect(":Business:Invoicing:default");
    }

    /**
     * @throws Exception
     */
    public function getExportPdf($invoice_id): Mpdf
    {
        $template = $this->templateFactory->createTemplate();
        $template->setFile(__DIR__ . '/../../components/exportPdf.latte');

        $template->invoice = $this->invoicingRepository->getInvoiceDataById($invoice_id);
        $template->invoice_items = $this->invoicingRepository->getInvoiceItemsById($invoice_id);

        $pdf = new mPDF();
        $pdf->ignore_invalid_utf8 = true;

        try {
            $pdf->WriteHTML($template);
        } catch (MpdfException $e) {
            throw new Exception($e);
        }

        $pdf->setHTMLFooterByName('footer');

        return $pdf;
    }

    /**
     * @secured
     * @throws Exception
     */
    public function handleDownloadInvoice($invoice_id)
    {
        $pdf = $this->getExportPdf($invoice_id);

        $pdf->Output('invoice.pdf', 'D');
        $this->redirect('this');
    }

    public function sendReminder($primary)
    {
       $invoice = $this->invoicingRepository->getInvoiceDataById($primary);
        //email varovani
        $subject = "Faktura č. " . $invoice->variable_symbol;
        $body = 'invoiceAfterDueDateTemplate.latte';
        $params = [
            'subject' => $subject,
            'name' => $invoice->user_name
        ];

        $this->mailSender->sendEmail($invoice->client_email, $subject, $body, $params);

        $this->flashMessage('E-mail byl odeslán', 'success');
        $this->redrawControl('flashes');
    }
}