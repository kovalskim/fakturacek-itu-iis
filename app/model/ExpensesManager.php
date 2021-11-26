<?php

namespace App\model;

/** Author: Dalibor Kyjovský */

use App\repository\ExpensesRepository;
use App\model\UserManager;
use Exception;

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
        $this->expensesRepository->deleteExpensesByUserId($primary);

    }

    public function editExpenseFormValidate($form, $values = null)
    {
        if(!$values)
        {
            $values = $form->getValues();
        }

    }

    public function editExpenseFormSucceeded($form, $values = null)
    {
        if(!$values)
        {
            $values = $form->getValues();
        }

        $id = $values->id;

        $this->expensesRepository->updateExpenseById($id, (array)$values);
    }
}