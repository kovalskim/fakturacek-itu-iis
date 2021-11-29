<?php

/** Author: Martin Kovalski */

namespace App\AdminModule\presenters;

use App\forms\AdministratorsFormFactory;
use App\model\AdministratorsManager;
use App\model\DatagridManager;
use Exception;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Forms\Container;
use Nextras\Datagrid\Datagrid;

final class AdministratorsPresenter extends BasePresenter
{
    /** @var AdministratorsFormFactory */
    private $administratorsFormFactory;

    /** @var AdministratorsManager */
    private $administratorsManager;

    /** @var DatagridManager */
    private $datagridManager;

    private $userTable = 'users';

    public function __construct(AdministratorsFormFactory $administratorsFormFactory, AdministratorsManager $administratorsManager, DatagridManager $datagridManager)
    {
        parent::__construct();
        $this->administratorsFormFactory = $administratorsFormFactory;
        $this->administratorsManager = $administratorsManager;
        $this->datagridManager = $datagridManager;
    }

    public function actionDefault()
    {

    }

    public function createComponentCreateAdministratorForm(): Form
    {
        $form = $this->administratorsFormFactory->createAdministratorForm();

        $form->onSuccess[] = [$this, 'createAdministratorFormSucceeded'];
        return $form;
    }

    /**
     * @throws AbortException
     * Add new administrator and view flash message and redraw it
     */
    public function createAdministratorFormSucceeded($form, $values)
    {
        try
        {
            $this->administratorsManager->createAdministratorFormSucceeded($form, $values);

            $this->flashMessage('Administrátor byl vytvořen', 'success');

            if($this->isAjax())
            {
                $form->reset();
                $this->redrawControl('administratorForm');
                $this['datagrid']->redrawControl('rows');
                $this->redrawControl('flashes');
            }
            else
            {
                $this->redirect('this');
            }
        }
        catch(Exception $e)
        {
            $this->flashMessage($e->getMessage(), 'danger');
            if($this->isAjax())
            {
                $form->reset();
                $this->redrawControl('administratorForm');
                $this->redrawControl('flashes');
            }
            else
            {
                $this->redirect('this');
            }
        }

    }

    /**
     * List of administrators in one table
     */
    public function createComponentDatagrid(): Datagrid
    {
        $grid = $this->datagridManager->createDatagrid($this->userTable, $this->getName());

        /** Columns from table */
        $grid->addColumn('avatar', 'Avatar');
        $grid->addColumn('name', 'Jméno a příjmení')->enableSort(Datagrid::ORDER_ASC);
        $grid->addColumn('email', 'E-mail')->enableSort();
        $grid->addColumn('phone', 'Telefon');
        $grid->addColumn('status', 'Status')->enableSort();

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

    /**
     * Form for filtering in datagrid
     */
    public function datagridFilterFormFactory(): Container
    {
        $form = new Container();
        $form->addText('name')
            ->setHtmlAttribute('placeholder', 'Jméno a příjmení');

        $form->addText('email') //must be text!
            ->setHtmlAttribute('placeholder', 'E-mail');

        $form->addText('phone', 'Telefon')
            ->setHtmlAttribute('placeholder', 'Telefon');

        $form->addSelect('status', null, [
            'new' => 'Nový',
            'active' => 'Aktivní',
            'banned' => 'Zablokovaný'
        ])
            ->setPrompt('--- Status ---');

        $form->addSubmit('filter', 'Filtrovat');
        $form->addSubmit('cancel', 'Zrušit');

        return $form;
    }

    /**
     * Handle for banning administrator
     */
    public function ban($primary)
    {
        $this->administratorsManager->ban($primary);
        $this->flashMessage('Účet byl zablokován', 'success');
        $this->redrawControl('flashes');
    }

    /**
     * Handle for allowing access for administrator
     */
    public function allow($primary)
    {
        $this->administratorsManager->allow($primary);
        $this->flashMessage('Účet byl odblokován', 'success');
        $this->redrawControl('flashes');
    }
}
