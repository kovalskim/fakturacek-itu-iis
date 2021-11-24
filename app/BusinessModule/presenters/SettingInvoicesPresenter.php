<?php

/** Author: Radek Jůzl */

namespace App\BusinessModule\presenters;

use App\forms\ClientsFormFactory;
use App\model\SettingInvoicesManager;
use App\repository\SettingInvoicesRepository;
use App\repository\UserRepository;
use Exception;
use Nette\Application\AbortException;
use Nette\Security\User;
use Nette\Application\UI\Form;

class SettingInvoicesPresenter extends BasePresenter
{

    /** @var ClientsFormFactory */
    private $clientsFormFactory;

    /** @var SettingInvoicesRepository */
    private $settingInvoicesRepository;

    /** @var User */
    public $user;

    /** @var UserRepository */
    private $userRepository;

    /** @var SettingInvoicesManager */
    private $settingInvoicesManager;

    public function __construct(ClientsFormFactory $clientsFormFactory, SettingInvoicesRepository $settingInvoicesRepository, User $user, SettingInvoicesManager $settingInvoicesManager, UserRepository $userRepository)
    {
        parent::__construct();
        $this->clientsFormFactory = $clientsFormFactory;
        $this->settingInvoicesRepository = $settingInvoicesRepository;
        $this->user = $user;
        $this->settingInvoicesManager = $settingInvoicesManager;
        $this->userRepository = $userRepository;
    }

    public function actionDefault()
    {
        $settingData = $this->settingInvoicesRepository->selectAll($this->user->getId());
        $vat = $this->userRepository->getUserById($this->user->getId());
        $this->template->settingDataLatte = $settingData;
        $this->template->vatLatte = $vat;
        if($settingData->variable_symbol == null)
        {
            $settingData->variable_symbol = "YYMM00";
        }
        $this->getComponent("settingInvoicesForm")->setDefaults($settingData);
    }

    protected function createComponentSettingInvoicesForm(): Form
    {
        $form = $this->clientsFormFactory->createSettingInvoicesForm();
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
                $this->template->settingDataLatte = $settingData;
                $vat = $this->userRepository->getUserById($this->user->getId());
                $this->template->vatLatte = $vat;

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
            $this->template->settingDataLatte = $this->settingInvoicesRepository->selectAll($this->user->getId());
            $this->redrawControl('invoicesTable');
            $this->redrawControl('flashes');
        }
        else
        {
            $this->redirect('this');
        }
    }

}
