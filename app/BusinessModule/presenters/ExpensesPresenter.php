<?php

/** Author: Dalibor Kyjovský */

namespace App\BusinessModule\presenters;

use App\forms\ExpensesFormFactory;
use App\forms\CategoryFormFactory;
use App\model\DatagridManager;
use App\repository\ExpensesRepository;
use App\repository\CategoryRepository;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Security\User;
use Nextras\Datagrid\Datagrid;

final class ExpensesPresenter extends BasePresenter
{
    /** @var ExpensesFormFactory */
    private $expensesFormFactory;

    /** @var DatagridManager */
    private $datagridManager;

    /** @var ExpensesRepository */
    private $expensesRepository;

    /** @var User */
    public $user;

    private $expensesTable = 'expenses';

    public function __construct(ExpensesFormFactory $expensesFormFactory, DatagridManager  $datagridManager, User $user, ExpensesRepository $expensesRepository)
    {
        parent::__construct();
        $this->expensesFormFactory = $expensesFormFactory;
        $this->datagridManager = $datagridManager;
        $this->user = $user;
        $this->expensesRepository = $expensesRepository;
    }

    public function actionDefault()
    {

    }

    public function createComponentAddExpensesForm(): Form
    {
        $form = $this->expensesFormFactory->createExpensesForm();
        $form->onSuccess[] = [$this, "createAddExpensesFormSucceeded"];
        return $form;
    }

    /**
     * @throws AbortException
     */
    public function createAddExpensesFormSucceeded($form, $values)
    {
        $user_id = $this->user->getId();
        $row = ((array) $values) + ['users_id' => $user_id] + ['datetime' => '2021-11-22 10:23:04'] + ['path' => ''];   //TODO: datetime

        $this->expensesRepository->insertExpensesByUserId($row);

        $this->flashMessage('Výdaj byl přidán');
        $this->redirect('this');
    }

    public function createComponentDatagrid(): Datagrid
    {
        $grid = $this->datagridManager->createDatagrid($this->expensesTable, $this->getName());

        $grid->addColumn('items', 'Položky');
        $grid->addColumn('price', 'Cena');
        
        $grid->addColumn('delete', 'delete');
        return $grid;
    }

    public function createComponentDeleteExpensesForm(): Form
    {
        $form = $this->expensesFormFactory->deleteExpensesForm();
        $form->onSuccess[] = [$this, "createDeleteExpensesFormSucceeded"];
        return $form;
    }

    /**
     * @throws AbortException
     */
    public function createDeleteExpensesFormSucceeded($form, $values)
    {
      //var_dump($values);
        $user_id = $this->user->getId();
        $row = ((array) $values);   //TODO: datetime

        $this->expensesRepository->deleteExpensesByUserId($row);

        $this->flashMessage('Výdaj byl vymazán');
        $this->redirect('this');
    }
}