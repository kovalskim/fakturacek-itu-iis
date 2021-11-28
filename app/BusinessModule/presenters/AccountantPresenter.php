<?php

/** Author: Radek Jůzl */

namespace App\BusinessModule\presenters;

use App\forms\ClientsAccountantFormFactory;
use App\model\UserManager;
use App\repository\AccountantRepository;
use Exception;
use Nette\Application\AbortException;
use Nette\Security\User;
use Nette\Application\UI\Form;

final class AccountantPresenter extends BasePresenter
{
    /** @var ClientsAccountantFormFactory */
    private $clientsAccountantFormFactory;

    /** @var AccountantRepository */
    private $accountantRepository;

    /** @var User */
    public $user;

    /** @var UserManager */
    private $userManager;

    public function __construct(ClientsAccountantFormFactory $clientsAccountantFormFactory, AccountantRepository $accountantRepository, User $user, UserManager $userManager)
    {
        parent::__construct();
        $this->clientsAccountantFormFactory = $clientsAccountantFormFactory;
        $this->accountantRepository = $accountantRepository;
        $this->user = $user;
        $this->userManager = $userManager;
    }

    public function actionDefault()
    {

    }

    public function renderDefault()
    {
        $this->template->isAccountantName = $this->accountantRepository->hasAccountantName($this->user->getId());
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
            $this->userManager->addClientAccountant($values->email, $this->user->getId(), "business"); /** User send request to accountant */
            $this->flashMessage("Žádost o přidaní byla odeslána", 'success');
            if($this->isAjax())
            {
                $form->reset();
                $this->redrawControl('clientConnectionForm');
                $this->template->isAccountantName = $this->accountantRepository->hasAccountantName($this->user->getId());
                $this->redrawControl('accountClientConnection');
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

    /**
     * @throws AbortException
     */
    public function handleDeleteAccountant(): void
    {
        $this->accountantRepository->deleteAccountant($this->user->getId());
        $this->flashMessage("Účetní byla odebrána", "success");

        if($this->isAjax())
        {
            $this->template->isAccountantName = $this->accountantRepository->hasAccountantName($this->user->getId());
            $this->redrawControl('accountClientConnection');
            $this->redrawControl('flashes');
        }
        else
        {
            $this->redirect('this');
        }
    }

    /**
     * @throws AbortException
     */
    public function handleAddAccountant(): void
    {
        $this->accountantRepository->updateStatusById($this->user->getId()); /** User accepted request */
        $this->flashMessage("Účetní byl udělen přístup", "success");

        if($this->isAjax())
        {
            $this->template->isAccountantName = $this->accountantRepository->hasAccountantName($this->user->getId());
            $this->redrawControl('accountClientConnection');
            $this->redrawControl('flashes');
        }
        else
        {
            $this->redirect('this');
        }
    }

    /**
     * @throws AbortException
     */
    public function actionAddConnection($token)
    {
        try
        {
            $this->userManager->checkToken($token, "accountant_permission"); /** check hash */
        }
        catch (Exception $e)
        {
            $this->flashMessage($e->getMessage(), 'danger');
            $this->redirect(':Business:Accountant:default');
        }

        $this->accountantRepository->updateStatus($token);
        $this->flashMessage("Účetní byl udělen přístup", 'success');
        $this->redirect(':Business:Accountant:default');
    }
}