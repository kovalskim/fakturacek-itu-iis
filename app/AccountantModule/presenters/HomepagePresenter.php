<?php

namespace App\AccountantModule\presenters;

use App\repository\AccountantRepository;

final class HomepagePresenter extends BasePresenter
{
    /** @var AccountantRepository */
    private $accountantRepository;

    public function __construct(AccountantRepository $accountantRepository)
    {
        parent::__construct();
        $this->accountantRepository = $accountantRepository;
    }

    public function actionDefault()
    {

    }

    public function renderDefault()
    {
        $accountant_id = $this->user->getId();
        $this->template->requests = $this->accountantRepository->getAllClientsByAccountantID($accountant_id);
        $waiting = $this->accountantRepository->getCountClientsByAccountantID($accountant_id, 'wait');
        $active = $this->accountantRepository->getCountClientsByAccountantID($accountant_id, 'active');

        $this->template->waiting = $waiting;
        $this->template->active = $active;
        $this->template->suma = $waiting + $active;
    }
}
