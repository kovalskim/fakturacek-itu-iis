<?php

/** Author: Radek Jůzl */

namespace App\BusinessModule\presenters;

use App\forms\ClientsFormFactory;
use App\model\SettingInvoicesManager;
use App\repository\SettingInvoicesRepository;
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


    /** @var SettingInvoicesManager */
    private $settingInvoicesManager;

    public function __construct(ClientsFormFactory $clientsFormFactory, SettingInvoicesRepository $settingInvoicesRepository, User $user, SettingInvoicesManager $settingInvoicesManager)
    {
        parent::__construct();
        $this->clientsFormFactory = $clientsFormFactory;
        $this->settingInvoicesRepository = $settingInvoicesRepository;
        $this->user = $user;
        $this->settingInvoicesManager = $settingInvoicesManager;
    }

    public function actionDefault()
    {
        $settingData = $this->settingInvoicesRepository->selectAll($this->user->getId());
        $this->getComponent("settingInvoicesForm")->setDefaults($settingData);
        $this->template->settingDataLatte = $settingData;
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
        try
        {
            $this->settingInvoicesManager->settingInvoicesFormValidate($form, $values);
        }
        catch (Exception $e)
        {
            if($this->isAjax())
            {
                $settingData = $this->settingInvoicesRepository->selectAll($this->user->getId());
                $form->reset();
                $form->setDefaults($settingData);
                $this->redrawControl('invoicesForm');
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
    public function settingInvoicesFormSucceeded($form, $values)
    {
        try
        {
            $this->settingInvoicesManager->settingInvoicesFormSucceeded($form, $values);

            $this->flashMessage("Změna se provedla", "success");

            if($this->isAjax())
            {
                $settingData = $this->settingInvoicesRepository->selectAll($this->user->getId());
                if($values->logo_path != null)
                {
                    $this->template->settingDataLatte = $settingData;
                    $this->redrawControl('invoicesImg');
                }

                $form->reset();
                $form->setDefaults($settingData);
                $this->redrawControl('invoicesForm');
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
                $settingData = $this->settingInvoicesRepository->selectAll($this->user->getId());
                $form->reset();
                $form->setDefaults($settingData);
                $this->redrawControl('invoicesForm');
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
            $this->redrawControl('invoicesImg');
            $this->redrawControl('flashes');
        }
        else
        {
            $this->redirect('this');
        }
    }

}
