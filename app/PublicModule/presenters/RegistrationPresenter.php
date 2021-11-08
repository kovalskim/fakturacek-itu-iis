<?php

namespace App\PublicModule\presenters;

use App\PublicModule\forms\LogInFormFactory;
use App\PublicModule\model\UserManager;
use Exception;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;

final class RegistrationPresenter extends BasePresenter
{
    /** @var LogInFormFactory */
    private $logInFormFactory;

    /** @var UserManager */
    private $userManager;

    public function __construct(LogInFormFactory $logInFormFactory, UserManager $userManager)
    {
        parent::__construct();
        $this->logInFormFactory = $logInFormFactory;
        $this->userManager = $userManager;
    }

    public function actionDefault()
    {

    }

    protected function createComponentRegistrationForm(): Form
    {
        $form = $this->logInFormFactory->createRegistrationForm();
        $form->onValidate = [$this, "registrationFormValidate"];
        $form->onSuccess = [$this, "registrationFormSucceeded"];
        return $form;
    }

    /**
     * @throws AbortException
     */
    public function registrationFormSucceeded($form, $values)
    {
        try
        {
            $this->userManager->registrationFormSucceeded($form, $values);
        }
        catch (Exception $e)
        {
            $this->flashMessage($e, "danger");
            $this->redirect("this");
        }
        $this->flashMessage("Povedlo se", "success");
        $this->redirect(":Public:Homepage:default");

    }

    public function registrationFormValidate($form, $values)
    {
        $this->userManager->registrationFormValidate($form, $values);
    }


}
