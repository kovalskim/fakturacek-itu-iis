<?php

/** Author: Martin Kovalski */

namespace App\PublicModule\presenters;

use Nette\Application\UI\Presenter;
use Nette\Security\User;
use Nextras\Dbal\Connection;

abstract class BasePresenter extends Presenter
{
    /** @var Connection @inject */
    public $connection;

    /** @var User @inject */
    public $user;

    public function beforeRender()
    {
        $this->setLayout('public');

        if($this->user->isLoggedIn())
        {
            switch ($this->user->getRoles()[0])
            {
                case 'admin':
                    $this->redirect(':Admin:Homepage:default');
                    break;

                case 'business':
                    $this->redirect(':Business:Homepage:default');
                    break;

                case 'accountant':
                    $this->redirect(':Accountant:Homepage:default');
                    break;
            }
        }
    }
}