<?php

/** Author: Martin Kovalski */

namespace App\PublicModule\presenters;

use App\PublicModule\forms\LogInFormFactory;
use App\PublicModule\model\Authenticator;
use App\PublicModule\model\UserManager;
use Exception;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;
use Nette\Security\User;

final class HomepagePresenter extends BasePresenter
{
    /** @var LogInFormFactory */
    private $logInFormFactory;

    /** @var Authenticator */
    private $authenticator;

    /** @var User */
    public $user;

    /** @var UserManager */
    private $userManager;

    public function __construct(LogInFormFactory $logInFormFactory, Authenticator $authenticator, User $user, UserManager $userManager)
    {
        parent::__construct();
        $this->logInFormFactory = $logInFormFactory;
        $this->authenticator = $authenticator;
        $this->user = $user;
        $this->userManager = $userManager;
    }

    public function actionDefault()
    {

    }

    protected function createComponentLogInForm(): Form
    {
        $form = $this->logInFormFactory->createLogInForm();

        $form->onSuccess[] = [$this, 'logInFormSucceeded'];

        return $form;
    }

    /**
     * @throws AbortException
     */
    public function logInFormSucceeded($form, $values)
    {
        $this->user->setAuthenticator($this->authenticator);

        try
        {
            $this->user->login($values->email, $values->password);
        }
        catch (Exception $e)
        {
            $this->flashMessage($e->getMessage(), 'danger');
            $this->redirect('this');
        }

        $this->flashMessage('Uživatel byl přihlášen', 'success');
        $this->redirect('this');
    }

    public function actionForgottenPassword()
    {

    }

    protected function createComponentForgottenPasswordForm(): Form
    {
        $form = $this->logInFormFactory->createForgottenPasswordForm();

        $form->onSuccess[] = [$this, 'forgottenPasswordFormSucceeded'];

        return $form;
    }

    /**
     * @throws AbortException
     */
    public function forgottenPasswordFormSucceeded($form, $values)
    {
        try
        {
            $this->userManager->forgottenPasswordFormSucceeded($form, $values);
            $this->flashMessage('E-mail byl odeslán', 'success');
        }
        catch (Exception $e)
        {
            $this->flashMessage($e->getMessage(), 'danger');
        }
        $this->redirect('this');
    }

    /**
     * @throws AbortException
     */
    public function actionNewPassword($token)
    {
        try
        {
            $this->userManager->checkToken($token);
        }
        catch (Exception $e)
        {
            $this->flashMessage($e->getMessage(), 'danger');
            $this->redirect('this');
        }

        /** Insert token as default value in newPassword Form */
        $form = $this->getComponent('newPasswordForm');

        /** Before form is submitted */
        if (!$form->isSubmitted()) {
            $default = [
                'token' => $token
            ];
            $form->setDefaults($default);
        }
    }

    protected function createComponentNewPasswordForm(): Form
    {
        $form = $this->logInFormFactory->createNewPasswordForm();

        $form->onSuccess[] = [$this, 'newPasswordFormSucceeded'];

        return $form;
    }

    /**
     * @throws AbortException
     */
    public function newPasswordFormSucceeded($form, $values)
    {
        try
        {
            $this->userManager->newPasswordFormSucceeded($form, $values);
            $this->flashMessage('Heslo bylo změněno', 'success');
        }
        catch (Exception $e)
        {
            $this->flashMessage($e->getMessage(), 'danger');
        }
        $this->redirect(':Public:Homepage:default');
    }
}
