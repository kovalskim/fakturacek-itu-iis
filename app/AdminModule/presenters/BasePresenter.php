<?php

/** Author: Martin Kovalski */

namespace App\AdminModule\presenters;

use App\repository\UserRepository;
use Nette\Application\AbortException;
use Nette\Application\UI\Presenter;
use Nette\Http\Session;
use Nette\Security\User;
use Nextras\Application\UI\SecuredLinksPresenterTrait;
use Nextras\Dbal\Connection;

abstract class BasePresenter extends Presenter
{
    /**
     * Security trait for safe deleting
     */
    use SecuredLinksPresenterTrait;

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
     * Start up method - check if user is logged in and in correct role
     */
    public function startup()
    {
        parent::startup();
        if(!($this->user->isLoggedIn() && $this->user->getRoles()[0] == "admin"))
        {
            $this->flashMessage('Přístup odepřen', 'danger');
            $this->redirect(':Public:Homepage:default');
        }
    }

    /**
     * Set different layout for current module and load avatar path to navbar
     */
    public function beforeRender()
    {
            $this->setLayout('admin');
            $this->template->avatar = $this->userRepository->getUserAvatar($this->user->getId());
    }

    /**
     * @throws AbortException
     * Handle for log out and delete session
     */
    public function handleLogOut()
    {
        $this->session->destroy();
        $this->user->logout(true);
        $this->flashMessage('Uživatel byl úspěšně odhlášen', 'success');
        $this->redirect(':Public:Homepage:default');
    }
}