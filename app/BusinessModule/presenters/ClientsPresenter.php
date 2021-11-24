<?php

/** Author: Radek Jůzl */

namespace App\BusinessModule\presenters;

use App\forms\ClientsFormFactory;
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
        $grid->addColumn('street', 'Ulice a č.p.');
        $grid->addColumn('city', 'Město');
        $grid->addColumn('zip', 'PSČ');

        $grid->setFilterFormFactory([$this, 'datagridFilterFormFactory']);

        $grid->setEditFormFactory([$this, 'datagridEditFormFactory']);
        $grid->setEditFormCallback([$this, 'editFormSucceeded']);

        return $grid;
    }

    public function datagridFilterFormFactory(): Container
    {
        $form = new Container();
        $form->addText('name')
            ->setHtmlAttribute('placeholder', 'Jméno a příjmení');

        $form->addText('cin')
            ->setHtmlAttribute('placeholder', 'IČ');

        $form->addText('email') //must be text!
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

    public function datagridEditFormFactory($row): Container
    {
        $form = new Container();
        $form->addText('name')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'Jméno a příjmení');

        $form->addText('cin')
            ->setHtmlAttribute('placeholder', 'IČ');

        $form->addEmail('email')
            ->setHtmlAttribute('placeholder', 'E-mail');

        $form->addText('phone')
            ->setHtmlAttribute('placeholder', 'Telefon');

        $form->addText('street', 'Ulice a č.p.')
            ->setHtmlAttribute('placeholder', 'Ulice a č.p.');

        $form->addText('city', 'Město')
            ->setHtmlAttribute('placeholder', 'Město');

        $form->addText('zip', 'PSČ')
            ->setHtmlAttribute('placeholder', 'PSČ');

        $form->addSubmit('save', 'Uložit');
        $form->addSubmit('cancel', 'Zrušit');

        if ($row) {
            $form->setDefaults($row);
        }
        return $form;
    }

    public function editFormSucceeded(Container $form)
    {
        //TODO: proces edit form

        $this->flashMessage('Uloženo', 'success');
        $this->redrawControl('flashes');
    }
}
