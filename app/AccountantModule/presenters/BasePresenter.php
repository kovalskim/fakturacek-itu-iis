<?php

namespace App\AccountantModule\presenters;

use Nette\Application\AbortException;
use Nette\Application\UI\Presenter;
use Nette\Http\Session;
use Nette\Security\User;
use Nextras\Dbal\Connection;

abstract class BasePresenter extends Presenter
{
    /** @var Connection @inject */
    public $connection;

    /** @var User @inject */
    public $user;

    /** @var Session */
    private $session;

    public function injectSession(Session $session)
    {
        $this->session = $session;
    }

    /**
     * @throws AbortException
     */
    public function beforeRender()
    {
        if($this->user->isLoggedIn() && $this->user->getRoles()[0] == "accountant")
        {
            $this->setLayout('accountant');
        }
        else
        {
            $this->flashMessage('Přístup odepřen', 'danger');
            $this->redirect(':Public:Homepage:default');
        }
    }

    /**
     * @throws AbortException
     */
    public function handleLogOut()
    {
        $this->session->destroy();
        $this->user->logout(true);
        $this->flashMessage('Uživatel byl úspěšně odhlášen', 'success');
        $this->redirect(':Public:Homepage:default');
    }
}