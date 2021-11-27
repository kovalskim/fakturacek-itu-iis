<?php

/** Author: Martin Kovalski */

namespace App\AdminModule\presenters;

use App\repository\AdminDashboardRepository;

final class HomepagePresenter extends BasePresenter
{
    /** @var AdminDashboardRepository */
    private $adminDashboardRepository;

    public function __construct(AdminDashboardRepository $adminDashboardRepository)
    {
        parent::__construct();
        $this->adminDashboardRepository = $adminDashboardRepository;
    }

    public function actionDefault()
    {

    }

    public function renderDefault()
    {
        $this->template->adminCount = $this->adminDashboardRepository->getAdminCount();
        $this->template->accountantCount = $this->adminDashboardRepository->getAccountantCount();
        $this->template->businessCount = $this->adminDashboardRepository->getBusinessCount();
        $this->template->invoicesCount = $this->adminDashboardRepository->getInvoicesCount();
    }
}
