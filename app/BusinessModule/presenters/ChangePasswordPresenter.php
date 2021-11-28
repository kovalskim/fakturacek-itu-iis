<?php

/** Author: Radek Jůzl */

namespace App\BusinessModule\presenters;

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
        try
        {
            $this->userManager->changePasswordFormSucceeded($form, $values);
            $this->flashMessage('Heslo bylo úspěšně změněno', 'success');
        }
        catch (Exception $e)
        {
            $this->flashMessage($e->getMessage(), 'danger');
        }
        $this->redirect('this');
    }


}
