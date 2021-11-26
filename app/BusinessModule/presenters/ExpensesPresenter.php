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
use App\model\ExpensesManager;
use App\model\ImageUploader;
use Nette\Utils\DateTime;

final class ExpensesPresenter extends BasePresenter
{
    /** @var ExpensesFormFactory */
    private $expensesFormFactory;

    /** @var DatagridManager */
    private $datagridManager;

    /** @var ExpensesRepository */
    private $expensesRepository;

    /** @var ExpensesManager */
    private $expensesManager;

    /** @var ImageUploader */
    private $imageUploader;

    /** @var User */
    public $user;

    private $expensesTable = 'expenses';

    public function __construct(ExpensesFormFactory $expensesFormFactory, DatagridManager  $datagridManager, User $user, ExpensesRepository $expensesRepository, ExpensesManager $expensesManager, ImageUploader $imageUploader)
    {
        parent::__construct();
        $this->expensesFormFactory = $expensesFormFactory;
        $this->datagridManager = $datagridManager;
        $this->expensesManager = $expensesManager;
        $this->user = $user;
        $this->expensesRepository = $expensesRepository;
        $this->imageUploader = $imageUploader;
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
          //TODO: datetime

            $row = ((array) $values) + ['users_id' => $user_id] + ['datetime' => NULL] + ['categories_id' => '10']; 

        try
        {
            $this->expensesRepository->insertExpensesByUserId($row);
        }
        catch (Exception $e)
        {
            $this->flashMessage($e->getMessage(), 'danger');
            $this->redirect(":Business:Expenses:default");
        }

        $this->flashMessage('Výdaj byl přidán');
        $this->redirect('this');
    }

    public function createComponentDatagrid(): Datagrid
    {
        $grid = $this->datagridManager->createDatagrid($this->expensesTable, $this->getName());

        $grid->addColumn('items', 'Položky');
        $grid->addColumn('price', 'Cena');
        
        $grid->addGlobalAction('delete', 'Vymazat', function (array $ids, Datagrid $grid) {
            foreach ($ids as $id) {
                $this->expensesManager->delete($id);
            }
            $this->flashMessage('Výdaj byl vymazán', 'success');
            $this->redrawControl('flashes');
            $grid->redrawControl('rows');

        });
        $grid->addGlobalAction('edit', 'Upravit', function (array $ids, Datagrid $grid) {
            foreach ($ids as $id) {
                $this->expencesManager->edit("juj", $id);
            }
            $this->flashMessage('Kategorie byla upravena.', 'success');
            $this->redrawControl('flashes');
            $grid->redrawControl('rows');
        });
        return $grid;
    }

}