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


    public function delete($primary)
    {
        $this->expensesRepository->deleteExpensesByUserId($primary);

    }
}