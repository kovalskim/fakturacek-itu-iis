<?php

/** Author: Dalibor Kyjovský */

namespace App\BusinessModule\presenters;

use App\forms\ExpensesFormFactory;
use App\model\DatagridManager;
use App\repository\ExpensesRepository;
use App\repository\CategoryRepository;
use Exception;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Security\User;
use Nette\Utils\FileSystem;
use Nextras\Datagrid\Datagrid;
use App\model\ExpensesManager;
use App\model\ImageUploader;
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

    /** @var CategoryRepository */
    private $categoryRepository;

    private $expensesTable = 'expenses';
    private $defaultCategories;

    public function __construct(ExpensesFormFactory $expensesFormFactory, DatagridManager  $datagridManager, User $user, ExpensesRepository $expensesRepository, ExpensesManager $expensesManager, ImageUploader $imageUploader, CategoryRepository $categoryRepository)
    {
        parent::__construct();
        $this->expensesFormFactory = $expensesFormFactory;
        $this->datagridManager = $datagridManager;
        $this->expensesManager = $expensesManager;
        $this->user = $user;
        $this->expensesRepository = $expensesRepository;
        $this->imageUploader = $imageUploader;
        $this->categoryRepository = $categoryRepository;
    }

    public function actionDefault()
    {
        $user_id = $this->user->getId();
        $this->defaultCategories = $this->categoryRepository->selectAllCategoryById($user_id);
        $this->getComponent("addExpensesForm")->getComponent('expenses_cat_id')->setItems($this->defaultCategories);
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
        try
        {
            $this->imageUploader->uploadImgFormSucceeded($form,$values, "expenses");
            $user_id = $this->user->getId();
            $row = ((array) $values) + ['users_id' => $user_id];
            $this->expensesRepository->insertExpensesByUserId($row);
            $this->flashMessage('Výdaj byl přidán', "success");
            if($this->isAjax())
            {
                $form->reset();
                $this->redrawControl('expensesForm');
                $this['datagrid']->redrawControl('rows');
                $this->redrawControl('flashes');
            }
            else
            {
                $this->redirect('this');
            }

        }
        catch (Exception $e)
        {
            $this->flashMessage($e->getMessage(), 'danger');
            if($this->isAjax())
            {
                $this->redrawControl('flashes');
            }
            else
            {
                $this->redirect('this');
            }
        }
    }

    public function createComponentDatagrid(): Datagrid
    {
        $user_id = $this->user->getId();

        $grid = $this->datagridManager->createDatagrid($this->expensesTable, $this->getName());

        $grid->addColumn('datetime', 'Datum zaplacení')->enableSort(Datagrid::ORDER_DESC);
        $grid->addColumn('items', 'Název položky');
        $grid->addColumn('price', 'Cena');
        $grid->addColumn('cat_id', 'Kategorie');

        $grid->setFilterFormFactory([$this, 'datagridFilterFormFactory']);
        $grid->setEditFormFactory([$this, 'datagridEditFormFactory']);
        $grid->setEditFormCallback([$this, 'editFormSucceeded']);
        $grid->setDeleteExpenseCallback([$this, 'deleteExpense']);
        
        $grid->addGlobalAction('deleteExpense', 'Vymazat', function (array $ids, Datagrid $grid) {
            foreach ($ids as $id) {
                $image_path = $this->expensesRepository->getPathById($id);
                $image_path = "../".$image_path;
                FileSystem::delete($image_path);
                $this->expensesManager->deleteExpense($id);
            }
            $this->flashMessage('Výdaje byly vymazány', 'success');
            $this->redrawControl('flashes');
            $grid->redrawControl('rows');
        });

        return $grid;
    }

    public function actionDocument($id)
    {

    }

    public function renderDocument($id)
    {
        $this->template->img_path = $this->expensesRepository->getPathById($id);
    }

    public function datagridFilterFormFactory(): Container
    {
        $form = new Container();
        $form->addText('datetime')
            ->setType('date')
            ->setHtmlAttribute('placeholder', 'Datum zaplacení');
        $form->addText('items')
            ->setHtmlAttribute('placeholder', 'Název položky');
        $form->addText('price')
            ->setHtmlAttribute('placeholder', 'Cena');
        $form->addSelect('cat_id', 'Kategorie', $this->defaultCategories)
            ->setHtmlAttribute('placeholder', 'Kategorie')
            ->setPrompt("-- Výchozí --");

        $form->addSubmit('filter', 'Filtrovat');
        $form->addSubmit('cancel', 'Zrušit');

        return $form;
    }

    public function datagridEditFormFactory($row): Container
    {
        $form = new Container();

        $form->addText('datetime', 'Datum')
            ->setType('date')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'Datum zaplacení')
            ->setHtmlAttribute('autofocus')
            ->setHtmlAttribute('class', 'form-control');

        $form->addText('items')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'Název položky')
            ->setHtmlAttribute('class', 'form-control');

        $form->addText('price')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'Cena')
            ->setHtmlAttribute('class', 'form-control');

        $form->addSelect('cat_id', 'Kategorie', $this->defaultCategories)
            ->setHtmlAttribute('placeholder', 'Kategorie')
            ->setPrompt("-- Výchozí --")
            ->setHtmlAttribute('class', 'form-control');

        $form->addSubmit('save', 'Uložit');
        $form->addSubmit('cancel', 'Zrušit');

        $form->onValidate[] = [$this, "editFormValidate"];

        if ($row) {
            $form->setDefaults($row);
            $form["datetime"]->setDefaultValue($row->datetime->format('Y-m-d'));
        }

        return $form;
    }

    public function editFormValidate(Container $form)
    {
        $this->expensesManager->expensesFormValidate($form);
    }

    public function editFormSucceeded(Container $form)
    {
        $this->expensesManager->editExpenseFormSucceeded($form);

        $this->flashMessage('Uloženo', 'success');
        $this->redrawControl('flashes');
    }

    public function deleteExpense($primary)
    {
        $this->expensesManager->deleteExpense($primary);
        $this->flashMessage('Výdaj byl vymazán.', 'success');
        $this->redrawControl('flashes');
    }
}