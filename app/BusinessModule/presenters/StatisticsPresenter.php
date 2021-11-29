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
        $this->template->sumRevenuesLast30 = $this->statisticsRepository->getSumRevenuesLast30day($this->user->getId());
        $this->template->sumExpensesLast30 = $this->statisticsRepository->getSumExpensesLast30day($this->user->getId());
    }
}