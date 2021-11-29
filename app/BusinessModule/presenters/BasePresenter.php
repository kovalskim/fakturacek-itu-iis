<?php

/** Author: Martin Kovalski */

namespace App\BusinessModule\presenters;

use App\repository\UserRepository;
use Nette\Application\AbortException;
use Nette\Application\UI\Presenter;
use Nette\Http\Session;
use Nette\Security\User;
use Nextras\Application\UI\SecuredLinksPresenterTrait;
use Nextras\Dbal\Connection;

abstract class BasePresenter extends Presenter
{
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
     */
    public function startup()
    {
        parent::startup();
        if(!($this->user->isLoggedIn() && $this->user->getRoles()[0] == "business"))
        {
            $this->flashMessage('Přístup odepřen', 'danger');
            $this->redirect(':Public:Homepage:default');
        }
    }

    public function beforeRender()
    {
        $this->setLayout('business');
        $this->template->avatar = $this->userRepository->getUserAvatar($this->user->getId());
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