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

    public function __construct(User $user, StatisticsRepository $statisticsRepository)
    {
        parent::__construct();
        $this->user = $user;
        $this->statisticsRepository = $statisticsRepository;
    }

    public function actionDefault()
    {

    }

    public function renderDefault()
    {
        $this->template->expenses = $this->statisticsRepository->getSumExpenses($this->user->getId());
        $this->template->revenues = $this->statisticsRepository->getSumRevenues($this->user->getId());

        $this->template->getExpensesPerYear = $this->statisticsRepository->getExpensesPerYear($this->user->getId());
        $this->template->getRevenuesPerYear = $this->statisticsRepository->getRevenuesPerYear($this->user->getId());
        $this->template->getExpensesPerMonth = $this->statisticsRepository->getExpensesPerMonth($this->user->getId());
        $this->template->getRevenuesPerMonth = $this->statisticsRepository->getRevenuesPerMonth($this->user->getId());
        $this->template->getExpensesPerTMonths = $this->statisticsRepository->getExpensesPerTMonths($this->user->getId());
        $this->template->getRevenuesPerTMonths = $this->statisticsRepository->getRevenuesPerTMonths($this->user->getId());
    }
}