<?php

namespace App\model;

/** Author: Dalibor Kyjovský, Radek Jůzl */

namespace App\model;

use App\repository\ExpensesRepository;
use App\model\UserManager;
use Exception;
use Nette\Utils\FileSystem;

class ExpensesManager
{

    /** @var ExpensesRepository */
    private $expensesRepository;

    public function __construct(ExpensesRepository $expensesRepository)
    {
        $this->expensesRepository = $expensesRepository;
    }


    public function deleteExpense($primary)
    {
        $image_path = $this->expensesRepository->getPathById($primary);
        $image_path = "../".$image_path;
        FileSystem::delete($image_path);
        $this->expensesRepository->deleteExpensesByUserId($primary);
    }

    public function editExpenseFormSucceeded($form, $values = null)
    {
        if(!$values)
        {
            $values = $form->getValues();
        }
        $values_edit = [
            "datetime" => $values->datetime,
            "items" => $values->items,
            "price" => $values->price,
            "expenses_cat_id" => $values->cat_id,
            "id" => $values->id
        ];
        $this->expensesRepository->updateExpenseById($values->id, $values_edit);
    }
}
