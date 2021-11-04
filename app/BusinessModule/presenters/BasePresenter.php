<?php

namespace App\BusinessModule\presenters;

use Nette\Application\UI\Presenter;
use Nextras\Dbal\Connection;

abstract class BasePresenter extends Presenter
{
    /** @var Connection @inject */
    public $connection;

    public function beforeRender()
    {
        $this->setLayout('business');
    }
}