<?php

/** Author: Martin Kovalski */

namespace App\BusinessModule\presenters;

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

    public function beforeRender()
    {
        if($this->user->isLoggedIn() && $this->user->getRoles()[0] == "business")
        {
            $this->setLayout('business');
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