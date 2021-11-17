<?php

namespace App\AdminModule\presenters;

/** Author: Martin Kovalski */

use App\AdminModule\forms\AdministratorsFormFactory;
use App\AdminModule\model\AdministratorsManager;
use Exception;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;

final class AdministratorsPresenter extends BasePresenter
{
    /** @var AdministratorsFormFactory */
    private $administratorsFormFactory;

    /** @var AdministratorsManager */
    private $administratorsManager;

    public function __construct(AdministratorsFormFactory $administratorsFormFactory, AdministratorsManager $administratorsManager)
    {
        parent::__construct();
        $this->administratorsFormFactory = $administratorsFormFactory;
        $this->administratorsManager = $administratorsManager;
    }

    public function actionDefault()
    {

    }

    public function createComponentCreateAdministratorForm(): Form
    {
        $form = $this->administratorsFormFactory->createAdministratorForm();

        $form->onSuccess[] = [$this, 'createAdministratorFormSucceeded'];
        return $form;
    }

    /**
     * @throws AbortException
     */
    public function createAdministratorFormSucceeded($form, $values)
    {
        try
        {
            $this->administratorsManager->createAdministratorFormSucceeded($form, $values);
        }
        catch(Exception $e)
        {
            $this->flashMessage($e->getMessage(), 'danger');
            $this->redirect('this');
        }
        $this->flashMessage('Administrátor byl vytvořen');
        $this->redirect('this');
    }
}
