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
        $row = ((array) $values);

        $this->categoryRepository->insertCategoryByUserId($row);

        $this->flashMessage('Kategorie byla přidán');
        $this->redirect('this');
    }

    public function createComponentDatagrid(): Datagrid
    {
        $grid = $this->datagridManager->createDatagrid($this->categoryTable, $this->getName());

        $grid->addColumn('name', 'Kategorie');
        $grid->addGlobalAction('delete', 'Vymazat', function (array $ids, Datagrid $grid) {
            foreach ($ids as $id) {
                $this->categoryManager->delete($id);
            }
            $this->flashMessage('Kategorie byla vymazána', 'success');
            $this->redrawControl('flashes');
            $grid->redrawControl('rows');
            

        });

        return $grid;
    }



}
