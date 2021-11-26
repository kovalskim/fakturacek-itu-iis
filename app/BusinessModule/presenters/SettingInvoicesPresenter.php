<?php

/** Author: Radek Jůzl */

namespace App\BusinessModule\presenters;

use App\forms\SettingInvoicesFormFactory;
use App\model\SettingInvoicesManager;
use App\repository\SettingInvoicesRepository;
use App\repository\UserRepository;
use Exception;
use Nette\Application\AbortException;
use Nette\Security\User;
use Nette\Application\UI\Form;

class SettingInvoicesPresenter extends BasePresenter
{

    /** @var SettingInvoicesFormFactory */
    private $settingInvoicesFormFactory;

    /** @var SettingInvoicesRepository */
    private $settingInvoicesRepository;

    /** @var User */
    public $user;

    /** @var UserRepository */
    private $userRepository;

    /** @var SettingInvoicesManager */
    private $settingInvoicesManager;

    public function __construct(SettingInvoicesFormFactory $settingInvoicesFormFactory, SettingInvoicesRepository $settingInvoicesRepository, User $user, SettingInvoicesManager $settingInvoicesManager, UserRepository $userRepository)
    {
        parent::__construct();
        $this->settingInvoicesFormFactory = $settingInvoicesFormFactory;
        $this->settingInvoicesRepository = $settingInvoicesRepository;
        $this->user = $user;
        $this->settingInvoicesManager = $settingInvoicesManager;
        $this->userRepository = $userRepository;
    }

    public function actionDefault()
    {
        $settingData = $this->settingInvoicesRepository->selectAll($this->user->getId());
        $settingData->vat = $this->userRepository->getUserById($this->user->getId())->vat;

        if ($settingData->variable_symbol == null) {
            $settingData->variable_symbol = "YYMM00";
        } else {
            $this->getComponent('settingInvoicesForm')->getComponent('variable_symbol')->setDisabled();
        }

        $this->getComponent("settingInvoicesForm")->setDefaults($settingData);
    }

    public function renderDefault()
    {
        $settingData = $this->settingInvoicesRepository->selectAll($this->user->getId());
        $settingData->vat = $this->userRepository->getUserById($this->user->getId())->vat;
        $this->template->settingData = $settingData;
    }

    protected function createComponentSettingInvoicesForm(): Form
    {
        $form = $this->settingInvoicesFormFactory->createSettingInvoicesForm();
        $form->onValidate[] = [$this, "settingInvoicesFormValidate"];
        $form->onSuccess[] = [$this, "settingInvoicesFormSucceeded"];
        return $form;
    }

    /**
     * @throws AbortException
     */
    public function settingInvoicesFormValidate($form, $values)
    {
        $this->settingInvoicesManager->settingInvoicesFormValidate($form, $values);
        $this->redrawControl("invoicesForm");
    }

    /**
     * @throws AbortException
     */
    public function settingInvoicesFormSucceeded($form, $values)
    {
        try
        {
            $this->settingInvoicesManager->settingInvoicesFormSucceeded($form, $values);

            $this->flashMessage("Změna se provedla", "success");

            if($this->isAjax())
            {
                $settingData = $this->settingInvoicesRepository->selectAll($this->user->getId());
                $settingData->vat = $this->userRepository->getUserById($this->user->getId())->vat;
                $this->template->settingData = $settingData;

                $form->reset();
                $form['variable_symbol']->setDisabled();
                $form->setDefaults($settingData);

                $this->redrawControl('invoicesForm');
                $this->redrawControl('invoicesTable');
                $this->redrawControl('flashes');
            }
            else
            {
                $this->redirect('this');
            }
        }
        catch (Exception $e)
        {
            $this->flashMessage($e->getMessage(), 'danger');
            if($this->isAjax())
            {
                $this->redrawControl('flashes');
            }
            else
            {
                $this->redirect('this');
            }
        }
    }

    /**
     * @throws AbortException
     */
    public function handleDeleteLogo(): void
    {
        $values = ["logo_path" => null];
        $this->settingInvoicesRepository->updateSetting($values, $this->user->getId());
        $this->flashMessage("Obrázek se smazal", "success");

        if($this->isAjax())
        {
            $settingData = $this->settingInvoicesRepository->selectAll($this->user->getId());
            $settingData->vat = $this->userRepository->getUserById($this->user->getId())->vat;
            $this->template->settingData = $settingData;
            $this->redrawControl('invoicesTable');
            $this->redrawControl('flashes');
        }
        else
        {
            $this->redirect('this');
        }
    }

}
