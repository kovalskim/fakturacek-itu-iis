<?php

/** Author: Radek Jůzl */

namespace App\BusinessModule\presenters;


use App\forms\ClientsAccountantFormFactory;
use App\repository\AccountantRepository;
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

    private $table = 'users';

    public function __construct(ClientsAccountantFormFactory $clientsAccountantFormFactory, AccountantRepository $accountantRepository, User $user)
    {
        parent::__construct();
        $this->clientsAccountantFormFactory = $clientsAccountantFormFactory;
        $this->accountantRepository = $accountantRepository;
        $this->user = $user;
    }

    public function actionDefault()
    {
        $this->template->isAccountantName = $this->accountantRepository->hasAccountantName($this->user->getId());
    }

    protected function createComponentClientConnectionForm(): Form
    {
        $form = $this->clientsAccountantFormFactory->createConnectionForm();
        //$form->onValidate[] = [$this, "clientConnectionFormValidate"];
        $form->onSuccess[] = [$this, "clientConnectionFormSucceeded"];
        return $form;
    }

    public function clientConnectionFormSucceeded($form, $values)
    {

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
}