<?php

/** Author: Dalibor Kyjovský, Radek Jůzl */

namespace App\model;

use App\repository\CategoryRepository;
use Nextras\Dbal\Connection;
use Exception;

class CategoryManager
{

    /** @var CategoryRepository */
    private $categoryRepository;

    /** @var $connection */
    public $connection;

    public function __construct(CategoryRepository $categoryRepository, Connection $connection)
    {
        $this->categoryRepository = $categoryRepository;
        $this->connection = $connection;
    }


    /**
     * @throws Exception
     */
    public function deleteCategory($id)
    {
        if($this->categoryRepository->getExpensesCountByCategoryId($id) == 0) /** If this category is used */
        {
            $this->categoryRepository->deleteCategoryByUserId($id);
        }
        else
        {
           throw new Exception("Kategorie je používaná");
        }
    }

    public function categoryFormValidate($form, $values = null)
    {
        if(!$values)
        {
            $values = $form->getValues();
        }
        if($this->categoryRepository->getCategoryName($values->name)) /** Check if this category already exists */
        {
            $form["name"]->addError("Tato kategorie existuje");
        }
    }

    public function editCategoryFormSucceeded($form, $values = null)
    {
        if(!$values)
        {
            $values = $form->getValues();
        }

        $this->categoryRepository->updateCategoryById($values->cat_id, (array)$values);
    }
}

