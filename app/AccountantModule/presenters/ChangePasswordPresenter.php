<?php

/** Author: Radek Jůzl */

namespace App\AccountantModule\presenters;

use App\forms\LogInFormFactory;
use App\model\UserManager;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Exception;

final class ChangePasswordPresenter extends BasePresenter
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

    protected function createComponentChangePasswordForm(): Form
    {
        $form = $this->logInFormFactory->createChangePasswordForm();
        $form->onSuccess[] = [$this, "changePasswordFormSucceeded"];
        return $form;
    }

    /**
     * @throws AbortException
     */
    public function changePasswordFormSucceeded($form, $values)
    {
        $error = 0;
        try
        {
            $this->userManager->changePasswordFormSucceeded($form, $values);
            $this->flashMessage('Heslo bylo úspěšně změněno', 'success');
        }
        catch (Exception $e)
        {
            $error = 1;
            $this->flashMessage($e->getMessage(), 'danger');
            if($this->isAjax())
            {
                $this->redrawControl('flashes');
                $form->reset();
                $this->redrawControl('changePasswordForm');
            }
            else
            {
                $this->redirect('this');
            }
        }

        if(!$error)
        {
            $this->redirect(':Accountant:Profile:default');
        }
    }
}
