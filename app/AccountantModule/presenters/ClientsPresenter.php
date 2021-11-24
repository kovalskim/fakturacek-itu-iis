<?php

namespace App\AccountantModule\presenters;

use App\model\DatagridManager;
use Nextras\Datagrid\Datagrid;

final class ClientsPresenter extends BasePresenter
{
    /** @var DatagridManager */
    private $datagridManager;

    private $clientTable = 'users';

    public function __construct(DatagridManager  $datagridManager)
    {
        parent::__construct();
        $this->datagridManager = $datagridManager;
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
