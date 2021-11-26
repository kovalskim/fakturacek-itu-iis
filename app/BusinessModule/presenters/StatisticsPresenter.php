<?php

/** Author: Dalibor Kyjovský */

namespace App\BusinessModule\presenters;

use Nette\Security\User;
use App\repository\StatisticsRepository;



final class StatisticsPresenter extends BasePresenter
{

    /** @var User*/
    public $user;

    /** @var StatisticsRepository */
    private $statisticsRepository;

    private $expensesTable = 'expenses';

    public function __construct(User $user, StatisticsRepository $statisticsRepository)
    {
        parent::__construct();
        $this->user = $user;
        $this->statisticsRepository = $statisticsRepository;
    }

    public function actionDefault()
    {
        //echo($this->statisticsRepository->getSumExpenses($this->user->getId()));
    }

    public function renderDefault()
    {
       // $this->template->statistics = $this->statisticsRepository->getUserProfile($this->user->getId());

        $this->template->statistics = $this->statisticsRepository->getSumExpenses($this->user->getId());
    }

}