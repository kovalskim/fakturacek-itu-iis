<?php

/** Author: Martin Kovalski */

namespace App\AccountantModule\presenters;

use App\repository\UserRepository;
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

    /** @var UserRepository */
    private $userRepository;

    public function injectSession(Session $session)
    {
        $this->session = $session;
    }

    public function injectUserRepository(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @throws AbortException
     */
    public function beforeRender()
    {
        if($this->user->isLoggedIn() && $this->user->getRoles()[0] == "accountant")
        {
            $this->setLayout('accountant');
            $this->template->avatar = $this->userRepository->getUserAvatar($this->user->getId());
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