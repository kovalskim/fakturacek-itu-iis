<?php

/** Author: Radek Jůzl */

namespace App\PublicModule\presenters;

use App\forms\LogInFormFactory;
use App\model\AresManager;
use App\model\UserManager;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;

final class RegistrationPresenter extends BasePresenter
{
    /** @var LogInFormFactory */
    private $logInFormFactory;

    /** @var UserManager */
    private $userManager;

    /** @var AresManager */
    private $aresManager;

    public function __construct(LogInFormFactory $logInFormFactory, UserManager $userManager, AresManager $aresManager)
    {
        parent::__construct();
        $this->logInFormFactory = $logInFormFactory;
        $this->userManager = $userManager;
        $this->aresManager = $aresManager;
    }

    public function actionDefault()
    {

    }

    protected function createComponentRegistrationForm(): Form
    {
        $form = $this->logInFormFactory->createRegistrationForm();
        $form->onValidate[] = [$this, "registrationFormValidate"];
        $form->onSuccess[] = [$this, "registrationFormSucceeded"];
        return $form;
    }

    /**
     * The function calls the data validation in the form
     */
    public function registrationFormValidate($form, $values)
    {
        $this->userManager->registrationFormValidate($form, $values);
        if($this->isAjax())
        {
            $this->redrawControl('registrationForm');
        }
    }

    /**
     * @throws AbortException
     */
    public function registrationFormSucceeded($form, $values)
    {
        $this->userManager->registrationFormSucceeded($form, $values);
        $this->flashMessage("Registrace byla úspěšná", "success");
        $this->redirect(":Public:Homepage:default");
    }

    public function handleLoadPersonalInfoFromAres()
    {
        if($this->isAjax())
        {
            $cin = $this->getParameter('cin');
            $form = $this->getComponent('registrationForm');
            if($cin != null)
            {
                $data = $this->aresManager->parseDataFromAres($cin);
                if($data)
                {
                    $form->setDefaults($data);
                }
                else
                {
                    $form->setDefaults(['cin' => $cin]);
                    $form['cin']->addError('Toto IČ neexistuje');
                }
            }
            else
            {
                $form['cin']->addError('IČ nebylo zadáno');
            }
            $this->redrawControl('registrationForm');
        }
    }
}
