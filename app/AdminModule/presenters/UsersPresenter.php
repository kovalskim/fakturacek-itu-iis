<?php

namespace App\AdminModule\presenters;

use App\AdminModule\model\UserManager;
use App\PublicModule\model\DatagridManager;
use Nextras\Datagrid\Datagrid;


/** Author: Radek Jůzl */

final class UsersPresenter extends BasePresenter
{
    /** @var DatagridManager */
    private $datagridManager;

        private $userTable = 'users';

    public function __construct(DatagridManager $datagridManager)
    {
        parent::__construct();
        $this->datagridManager = $datagridManager;
    }

    public function createComponentDatagrid(): Datagrid
    {
        $grid = $this->datagridManager->createDatagrid($this->userTable, $this->getName());

        /** Columns from table */
        $grid->addColumn('avatar_path', 'Avatar');
        $grid->addColumn('name', 'Jméno a příjmení')->enableSort();
        $grid->addColumn('email', 'E-mail')->enableSort();
        $grid->addColumn('phone', 'Telefon');
        $grid->addColumn('address', 'Adresa:');
        $grid->addColumn('role', 'Role')->enableSort();
        $grid->addColumn('status', 'Status');
        $grid->addColumn('password_timestamp', 'Poslední změna hesla');
        $grid->addColumn('login_timestamp', 'Poslední přihlášení');

        return $grid;
    }
}