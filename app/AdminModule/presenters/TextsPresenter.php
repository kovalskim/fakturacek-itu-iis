<?php

namespace App\AdminModule\presenters;

use App\AdminModule\forms\AdministratorsFormFactory;
use App\PublicModule\forms\LogInFormFactory;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;

final class TextsPresenter extends BasePresenter
{
   // /** @var UserRepository */
    //private $userRepository;

    /** @var AdministratorsFormFactory */
    private $administratorsFormFactory;


    public function __construct(/*UserRepository $userRepository,*/ AdministratorsFormFactory $administratorsFormFactory)
    {
        parent::__construct();
        //$this->userRepository = $userRepository;
        $this->administratorsFormFactory = $administratorsFormFactory;

    }

    public function actionDefault()
    {
    }

    protected function createComponentTextsForm(): Form
    {
        $form = $this->administratorsFormFactory->createTextsForm();
        return $form;
    }

}