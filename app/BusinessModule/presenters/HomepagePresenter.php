<?php

namespace App\BusinessModule\presenters;

use App\repository\AccountantRepository;
use App\repository\InvoicingRepository;

final class HomepagePresenter extends BasePresenter
{
    /** @var InvoicingRepository*/
    private $invoicingRepository;

    /** @var AccountantRepository */
    private $accountantRepository;

    public function __construct(InvoicingRepository $invoicingRepository, AccountantRepository $accountantRepository)
    {
        parent::__construct();
        $this->invoicingRepository = $invoicingRepository;
        $this->accountantRepository = $accountantRepository;
    }

    public function actionDefault()
    {

    }

    public function renderDefault()
    {
        $user_id = $this->user->getId();
        $this->template->unpaidInvoices = $this->invoicingRepository->getUnpaidInvoicesByUserId($user_id);
        $this->template->afterDueDateInvoices = $this->invoicingRepository->getAfterDueDateInvoicesByUserId($user_id);
        $this->template->isAccountantName = $this->accountantRepository->hasAccountantName($this->user->getId());
    }
}

