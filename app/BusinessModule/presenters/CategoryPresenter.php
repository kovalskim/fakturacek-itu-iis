<?php

/** Author: Dalibor Kyjovský */

namespace App\BusinessModule\presenters;

use App\forms\CategoryFormFactory;
use App\model\DatagridManager;
use App\model\CategoryManager;
use App\repository\CategoryRepository;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Security\User;
use Nextras\Datagrid\Datagrid;
use Nette\Forms\Container;

final class CategoryPresenter extends BasePresenter
{
    /** @var categoryFormFactory */
    private $categoryFormFactory;

    /** @var DatagridManager */
    private $datagridManager;

    /** @var CategoryManager */
    private $categoryManager;

    /** @var categoryRepository */
    private $categoryRepository;

    /** @var User */
    public $user;

    private $categoryTable = 'categories';

    public function __construct(CategoryFormFactory $categoryFormFactory, DatagridManager  $datagridManager, CategoryManager $categoryManager, User $user, CategoryRepository $categoryRepository)
    {
        parent::__construct();
        $this->categoryFormFactory = $categoryFormFactory;
        $this->datagridManager = $datagridManager;
        $this->categoryManager = $categoryManager;
        $this->user = $user;
        $this->categoryRepository = $categoryRepository;
    }

    public function actionDefault()
    {

    }

    public function createComponentAddCategoryForm(): Form
    {
        $form = $this->categoryFormFactory->createCategoryForm();
        $form->onSuccess[] = [$this, "createAddCategoryFormSucceeded"];
        return $form;
    }

    /**
     * @throws AbortException
     */
    public function createAddCategoryFormSucceeded($form, $values)
    {
        $user_id = $this->user->getId();
        $row = ((array) $values) + ['users_id' => $user_id ];

        $this->categoryRepository->insertCategoryByUserId($row);

        $this->flashMessage('Kategorie byla přidán');
        $this->redirect('this');
    }

    public function createComponentDatagrid(): Datagrid
    {
        $grid = $this->datagridManager->createDatagrid($this->categoryTable, $this->getName());
    

        $grid->addColumn('name', 'Kategorie');
        $grid->setFilterFormFactory([$this, 'datagridFilterFormFactory']);


        $grid->setEditFormFactory([$this, 'datagridEditFormFactory']);
        $grid->setEditFormCallback([$this, 'editFormSucceeded']);

        $grid->setDeleteCategoryCallback([$this, 'deleteCategory']);



        $grid->addGlobalAction('deleteCategory', 'Vymazat', function (array $ids, Datagrid $grid) {
            foreach ($ids as $id) {
                if($this->categoryManager->deleteCategory($id)){
                    $this->flashMessage('Kategorie je používána a tutíž nebyla vymazána.', 'success');
                }
                else {
                    $this->flashMessage('Kategorie byla vymazána.', 'success');
                }
            }

            $this->redrawControl('flashes');
            $grid->redrawControl('rows');

        });

        return $grid;
    }

    public function datagridFilterFormFactory(): Container
    {
        $form = new Container();
        $form->addText('name')
            ->setHtmlAttribute('placeholder', 'Název kategorie');

        $form->addSubmit('filter', 'Filtrovat');
        $form->addSubmit('cancel', 'Zrušit');

        return $form;
    }

    public function datagridEditFormFactory($row): Container
    {
        $form = new Container();
        $form->addText('name')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'Název kategorie');


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
        $this->categoryManager->editCategoryFormValidate($form);
    }

    public function editFormSucceeded(Container $form)
    {
        $this->categoryManager->editCategoryFormSucceeded($form);

        $this->flashMessage('Uloženo', 'success');
        $this->redrawControl('flashes');
    }

    public function deleteCategory($primary)
    {
        if($this->categoryManager->deleteCategory($primary)){
            $this->flashMessage('Kategorie je používána a tutíž nebyla vymazána.', 'success');
        }
        else {
            $this->flashMessage('Kategorie byla vymazána.', 'success');
        }
        $this->redrawControl('flashes');
    }

}
