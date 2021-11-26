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
use Nette\Forms\Container;

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
        if($values->categories_id == NULL)
        {
            $values->categories_id = "1";
        }
        if($values->path == NULL)
        {
            $values->path = "";
        }
            $row = ((array) $values) + ['users_id' => $user_id]; 

        try
        {
            $this->imageUploader->uploadImgFormSucceeded($form,$values, "expenses");
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
        $user_id = $this->user->getId();

        $grid = $this->datagridManager->createDatagrid($this->expensesTable, $this->getName());

       // $grid->addColumn('id', id);
        $grid->addColumn('items', 'Položky')->enableSort(Datagrid::ORDER_ASC);
        $grid->addColumn('price', 'Cena')->enableSort(Datagrid::ORDER_ASC);
        $grid->addColumn('categories_id', 'Kategorie');
        $grid->addColumn('path', 'Doklad');
        $grid->setFilterFormFactory([$this, 'datagridFilterFormFactory']);

        $grid->setEditFormFactory([$this, 'datagridEditFormFactory']);
        $grid->setEditFormCallback([$this, 'editFormSucceeded']);

        $grid->setDeleteExpenseCallback([$this, 'deleteExpense']);

        
        $grid->addGlobalAction('deleteExpense', 'Vymazat', function (array $ids, Datagrid $grid) {
            foreach ($ids as $id) {
                $this->expensesManager->deleteExpense($id);
            }
            $this->flashMessage('Výdaj byl vymazán', 'success');
            $this->redrawControl('flashes');
            $grid->redrawControl('rows');

        });

        return $grid;
    }

    public function datagridFilterFormFactory(): Container
    {
        $form = new Container();
        $form->addText('items')
            ->setHtmlAttribute('placeholder', 'Položka');
        $form->addText('price')
            ->setHtmlAttribute('placeholder', 'Cena');
        $form->addText('categories_id')
            ->setHtmlAttribute('placeholder', 'Kategorie');

        $form->addSubmit('filter', 'Filtrovat');
        $form->addSubmit('cancel', 'Zrušit');

        return $form;
    }

    public function datagridEditFormFactory($row): Container
    {
        $form = new Container();
        $form->addText('items')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'Název');

        $form->addText('price')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'Cena');

        $form->addText('categories_id')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'Kategorie');


        $form->addSubmit('save', 'Uložit');
        $form->addSubmit('cancel', 'Zrušit');

        if ($row) {
            $form->setDefaults($row);
        }

        $form->onValidate[] = [$this, "editFormValidate"];

        return $form;
    }

    public function editFormValidate(Container $form)
    {
        $this->expensesManager->editExpenseFormValidate($form);
    }

    public function editFormSucceeded(Container $form)
    {
        $this->expensesManager->editExpenseFormSucceeded($form);

        $this->flashMessage('Uloženo', 'success');
        $this->redrawControl('flashes');
    }

    public function deleteExpense($primary)
    {
        if($this->expensesManager->deleteExpense($primary)){ //TODO
            $this->flashMessage('Kategorie je používána a tutíž nebyla vymazána.', 'success');
        }
        else {
            $this->flashMessage('Kategorie byla vymazána.', 'success');
        }
        $this->redrawControl('flashes');
    }


}