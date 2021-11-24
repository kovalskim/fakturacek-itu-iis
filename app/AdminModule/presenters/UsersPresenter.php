<?php

namespace App\AdminModule\presenters;

use App\model\AdministratorsManager;
use App\model\DatagridManager;
use Nette\Forms\Container;
use Nette\Utils\Html;
use Nextras\Datagrid\Datagrid;

/** Author: Radek Jůzl */

final class UsersPresenter extends BasePresenter
{
    /** @var DatagridManager */
    private $datagridManager;

    /** @var AdministratorsManager */
    private $administratorsManager;

    private $userTable = 'users';

    public function __construct(DatagridManager $datagridManager, AdministratorsManager $administratorsManager)
    {
        parent::__construct();
        $this->datagridManager = $datagridManager;
        $this->administratorsManager = $administratorsManager;
    }

    public function createComponentDatagrid(): Datagrid
    {
        $grid = $this->datagridManager->createDatagrid($this->userTable, $this->getName());

        /** Columns from table */
        $grid->addColumn('avatar_path', 'Avatar');
        $grid->addColumn('name', 'Jméno a příjmení')->enableSort(Datagrid::ORDER_DESC);
        $grid->addColumn('email', 'E-mail')->enableSort();
        $grid->addColumn('phone', 'Telefon');
        $grid->addColumn('address', 'Adresa');
        $grid->addColumn('role', 'Role')->enableSort();
        $grid->addColumn('status', 'Status')->enableSort();
        $grid->addColumn('dates', Html::el()->setHtml('Poslední přihlášení<br>Poslední změna hesla'));

        $grid->setFilterFormFactory([$this, 'datagridFilterFormFactory']);

        $grid->setBanCallback([$this, 'ban']);
        $grid->setAllowCallback([$this, 'allow']);

        $grid->addGlobalAction('ban', 'Zablokovat', function (array $ids, Datagrid $grid) {
            foreach ($ids as $id) {
                $this->administratorsManager->ban($id);
            }
            $this->flashMessage('Uživatele byli zablokováni', 'success');
            $this->redrawControl('flashes');
            $grid->redrawControl('rows');
        });

        $grid->addGlobalAction('allow', 'Odblokovat', function (array $ids, Datagrid $grid) {
            foreach ($ids as $id) {
                $this->administratorsManager->allow($id);
            }
            $this->flashMessage('Uživatele byli odblokování', 'success');
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

        $form->addText('email') //must be text!
        ->setHtmlAttribute('placeholder', 'E-mail');

        $form->addText('phone', 'Telefon')
            ->setHtmlAttribute('placeholder', 'Telefon');

        $form->addSelect('role', null , [
            'business' => 'OSVČ',
            'accountant' => 'Účetní'
        ])
            ->setPrompt('--- Role ---');

        $form->addSelect('status', null, [
            'new' => 'Nový',
            'active' => 'Aktivní',
            'banned' => 'Zablokovaný'
        ])
            ->setPrompt('--- Status ---');

        $form->addSubmit('filter', 'Filtrovat')->getControlPrototype()->class = 'btn btn-primary';
        $form->addSubmit('cancel', 'Zrušit')->getControlPrototype()->class = 'btn';

        return $form;
    }

    public function ban($primary)
    {
        $this->administratorsManager->ban($primary);
        $this->flashMessage('Účet byl zablokován', 'success');
        $this->redrawControl('flashes');
    }

    public function allow($primary)
    {
        $this->administratorsManager->allow($primary);
        $this->flashMessage('Účet byl odblokován', 'success');
        $this->redrawControl('flashes');
    }
}