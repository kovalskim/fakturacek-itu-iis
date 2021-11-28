<?php

/** Author: Martin Kovalski, Radek Jůzl */

namespace App\AccountantModule\presenters;

use App\forms\ClientsAccountantFormFactory;
use App\model\UserManager;
use App\repository\AccountantRepository;
use App\repository\UserRepository;
use Exception;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Security\User;

final class HomepagePresenter extends BasePresenter
{
    /** @var AccountantRepository */
    private $accountantRepository;

    /** @var UserManager */
    private $userManager;

    /** @var ClientsAccountantFormFactory */
    private $clientsAccountantFormFactory;

    public function __construct(AccountantRepository $accountantRepository, UserManager $userManager, ClientsAccountantFormFactory $clientsAccountantFormFactory)
    {
        parent::__construct();
        $this->accountantRepository = $accountantRepository;
        $this->userManager = $userManager;
        $this->clientsAccountantFormFactory = $clientsAccountantFormFactory;
    }

    public function actionDefault()
    {

    }

    public function renderDefault()
    {
        $accountant_id = $this->user->getId();
        $this->template->requests = $this->accountantRepository->getAllClientsByAccountantID($accountant_id);

        $this->template->suma = $active = $this->accountantRepository->getCountClientsByAccountantID($accountant_id, 'active');
    }

    protected function createComponentClientConnectionForm(): Form
    {
        $form = $this->clientsAccountantFormFactory->createConnectionForm();
        $form->onSuccess[] = [$this, "clientConnectionFormSucceeded"];
        return $form;
    }

    /**
     * @throws AbortException
     */
    public function clientConnectionFormSucceeded($form, $values)
    {
        try
        {
            $this->userManager->addClientAccountant($values->email, $this->user->getId(), "accountant");
            $this->flashMessage("Žádost o přidaní byla odeslána", 'success');
            if($this->isAjax())
            {
                $form->reset();
                $this->redrawControl('clientConnectionForm');
                $this->redrawControl('flashes');
            }
            else
            {
                $this->redirect('this');
            }
        }
        catch (Exception $e)
        {
            $this->flashMessage($e->getMessage(), 'danger');
            if($this->isAjax())
            {
                $this->redrawControl('clientConnectionForm');
                $this->redrawControl('flashes');
            }
            else
            {
                $this->redirect('this');
            }
        }

    }
}
