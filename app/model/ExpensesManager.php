<?php

/** Author: Dalibor Kyjovský, Radek Jůzl */

namespace App\model;

use App\repository\ExpensesRepository;
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

    public function expensesFormValidate($form, $values = null)
    {
        if(!$values)
        {
            $values = $form->getValues();
        }
        $price = str_replace(",",".", $values->price);
        if(!(is_numeric($price)))
        {
            $form["price"]->addError("Není peněžní hodnota");
        }
        elseif($price < 0)
        {
            $form["price"]->addError("Minimální částka je 0");
        }
    }

    public function editExpenseFormSucceeded($form, $values = null)
    {
        if(!$values)
        {
            $values = $form->getValues();
        }
        $values->price = str_replace(",",".", $values->price);
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
