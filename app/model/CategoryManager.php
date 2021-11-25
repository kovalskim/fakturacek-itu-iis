<?php

namespace App\model;

/** Author: Dalibor Kyjovský */

use App\repository\CategoryRepository;
use App\model\UserManager;
use Exception;

class CategoryManager
{

    /** @var CategoryRepository */
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }


    public function delete($id)
    {
        $this->categoryRepository->deleteCategoryByUserId($id);

    }
}