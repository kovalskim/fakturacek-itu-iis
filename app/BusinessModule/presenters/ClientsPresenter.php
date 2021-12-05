<?php

/** Author: Martin Kovalski, Radek Jůzl */

namespace App\BusinessModule\presenters;

use App\forms\ClientsFormFactory;
use App\model\AresManager;
use App\model\ClientsManager;
use App\model\DatagridManager;
use App\repository\ClientRepository;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Forms\Container;
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

    /** @var ClientsManager */
    private $clientsManager;

    /** @var AresManager  */
    private $aresManager;

    private $clientTable = 'clients';

    public function __construct(ClientsFormFactory $clientsFormFactory, DatagridManager  $datagridManager, User $user, ClientRepository $clientRepository, ClientsManager $clientsManager, AresManager $aresManager)
    {
        parent::__construct();
        $this->clientsFormFactory = $clientsFormFactory;
        $this->datagridManager = $datagridManager;
        $this->user = $user;
        $this->clientRepository = $clientRepository;
        $this->clientsManager = $clientsManager;
        $this->aresManager = $aresManager;
    }

    public function actionDefault()
    {

    }

    protected function createComponentAddClientForm(): Form
    {
        $form = $this->clientsFormFactory->createClientForm();
        $form->onValidate[] = [$this, "createAddClientFormValidate"];
        $form->onSuccess[] = [$this, "createAddClientFormSucceeded"];
        return $form;
    }

    public function createAddClientFormValidate($form, $values)
    {
        if($values->cin != null)
        {
            if($this->aresManager->verificationCin($values->cin) != 0) /** Find out if the CIN exists */
            {
                $form["cin"]->addError("Toto IČ neexistuje");
            }
            $this->redrawControl('clientForm');
        }
    }

    /**
     * @throws AbortException
     */
    public function createAddClientFormSucceeded($form, $values)
    {
        $user_id = $this->user->getId();
        $row = ((array) $values) + ['users_id' => $user_id]; /** Add client to the directory with user id */
        $this->clientRepository->insertClientByUserId($row);

        $this->flashMessage('Klient byl přidán', "success");

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

    /**
     * Function draw all user clients
     */
    public function createComponentDatagrid(): Datagrid
    {
        $grid = $this->datagridManager->createDatagrid($this->clientTable, $this->getName());

        $grid->addColumn('name', 'Jméno a příjmení')->enableSort(Datagrid::ORDER_ASC);
        $grid->addColumn('cin', 'IČ');
        $grid->addColumn('vat', 'DIČ');
        $grid->addColumn('email', 'E-mail')->enableSort();
        $grid->addColumn('phone', 'Telefon');
        $grid->addColumn('street', 'Ulice a č.p.');
        $grid->addColumn('city', 'Město');
        $grid->addColumn('zip', 'PSČ');

        $grid->setFilterFormFactory([$this, 'datagridFilterFormFactory']);

        $grid->setEditFormFactory([$this, 'datagridEditFormFactory']);
        $grid->setEditFormCallback([$this, 'editFormSucceeded']);

        $grid->setDeleteCallback([$this, 'deleteClient']);

        $grid->addGlobalAction('delete', 'Odebrat', function (array $ids, Datagrid $grid) {
            foreach ($ids as $id) {
                $this->clientRepository->deleteClientById($id);
            }
            $this->flashMessage('Uživatelé byli smazáni', 'success');
            $this->redrawControl('flashes');
            $grid->redrawControl('rows');
        });

        return $grid;
    }

    /**
     * Filter form for datagrid
     */
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

        $form->addText('phone', 'Telefon')
            ->setHtmlAttribute('placeholder', 'Telefon');

        $form->addText('street', 'Ulice a č.p.')
            ->setHtmlAttribute('placeholder', 'Ulice a č.p.');

        $form->addText('city', 'Město')
            ->setHtmlAttribute('placeholder', 'Město');

        $form->addText('zip', 'PSČ')
            ->setHtmlAttribute('placeholder', 'PSČ');

        $form->addSubmit('filter', 'Filtrovat');
        $form->addSubmit('cancel', 'Zrušit');


        return $form;
    }

    /**
     * Client editing form
     */
    public function datagridEditFormFactory($row): Container
    {
        $form = new Container();
        $form->addText('name')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'Jméno a příjmení')
            ->setHtmlAttribute('class', 'form-control');

        $form->addText('cin')
            ->setHtmlAttribute('placeholder', 'IČ')
            ->setHtmlAttribute('class', 'form-control');

        $form->addText('vat')
            ->setHtmlAttribute('placeholder', 'DIČ')
            ->setHtmlAttribute('class', 'form-control');

        $form->addEmail('email')
            ->setHtmlAttribute('placeholder', 'E-mail')
            ->setHtmlAttribute('class', 'form-control');

        $form->addText('phone')
            ->setHtmlAttribute('placeholder', 'Telefon')
            ->setHtmlAttribute('class', 'form-control');

        $form->addText('street', 'Ulice a č.p.')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'Ulice a č.p.')
            ->setHtmlAttribute('class', 'form-control');

        $form->addText('city', 'Město')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'Město')
            ->setHtmlAttribute('class', 'form-control');

        $form->addText('zip', 'PSČ')
            ->setRequired()
            ->addFilter(function ($value) {
                return str_replace(' ', '', $value);
            })
            ->setHtmlAttribute("inputmode", "numeric")
            ->setHtmlAttribute('placeholder', 'PSČ')
            ->setHtmlAttribute('class', 'form-control');

        $form->addSubmit('save', 'Uložit');
        $form->addSubmit('cancel', 'Zrušit');

        if ($row) {
            $form->setDefaults($row);
        }

        $form->onValidate[] = [$this, "editFormValidate"]; /** Call validation form */

        return $form;
    }

    public function editFormValidate(Container $form)
    {
        $this->clientsManager->editClientsFormValidate($form);
    }

    public function editFormSucceeded(Container $form)
    {
        $this->clientsManager->editClientsFormSucceeded($form);

        $this->flashMessage('Uloženo', 'success');
        $this->redrawControl('flashes');
    }

    public function deleteClient($primary)
    {
        $this->clientRepository->deleteClientById($primary);

        $this->flashMessage('Klient byl smazán', 'success');
        $this->redrawControl('flashes');
    }

    public function handleLoadPersonalInfoFromAres()
    {
        if($this->isAjax())
        {
            $cin = $this->getParameter('cin');
            $form = $this->getComponent('addClientForm');
            if($cin != null)
            {
                $data = $this->aresManager->parseDataFromAres($cin);
                if($data)
                {
                    $form->setDefaults($data);
                }
                else
                {
                    $form->setDefaults(['cin' => $cin]);
                    $form['cin']->addError('Toto IČ neexistuje');
                }
            }
            else
            {
                $form['cin']->addError('IČ nebylo zadáno');
            }
            $this->redrawControl('clientForm');
        }
    }
}
