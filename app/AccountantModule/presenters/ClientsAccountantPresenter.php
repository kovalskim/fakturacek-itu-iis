<?php

/** Author: Radek Jůzl */

namespace App\AccountantModule\presenters;

use App\forms\ClientsAccountantFormFactory;
use App\model\DatagridManager;
use Nette\Application\UI\Form;
use Nextras\Datagrid\Datagrid;

final class ClientsAccountantPresenter extends BasePresenter
{
    /** @var ClientsAccountantFormFactory */
    private $clientsAccountantFormFactory;

    /** @var DatagridManager */
    private $datagridManager;

    private $table = 'users';

    public function __construct(ClientsAccountantFormFactory $clientsAccountantFormFactory, DatagridManager $datagridManager)
    {
        parent::__construct();
        $this->clientsAccountantFormFactory = $clientsAccountantFormFactory;
        $this->datagridManager = $datagridManager;

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

    public function clientConnectionFormSucceeded($form, $values)
    {

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
}
