<?php

namespace App\PublicModule\presenters;

use App\PublicModule\forms\LogInFormFactory;
use App\PublicModule\model\Authenticator;
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

    public function __construct(LogInFormFactory $logInFormFactory, Authenticator $authenticator, User $user)
    {
        parent::__construct();
        $this->logInFormFactory = $logInFormFactory;
        $this->authenticator = $authenticator;
        $this->user = $user;
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
        catch (AuthenticationException $e)
        {
            $this->flashMessage($e->getMessage(), 'danger');
            $this->redirect('this');
        }

        $this->flashMessage('Uživatel byl přihlášen', 'success');
        $this->redirect('this');
    }
}
