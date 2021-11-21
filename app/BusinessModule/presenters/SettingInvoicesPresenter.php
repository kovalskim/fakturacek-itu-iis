<?php

/** Author: Radek Jůzl */

namespace App\BusinessModule\presenters;

use App\BusinessModule\forms\ClientsFormFactory;
use App\BusinessModule\model\SettingInvoices;
use App\PublicModule\repository\SettingInvoicesRepository;
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


    /** @var SettingInvoices */
    private $settingInvoices;

    public function __construct(ClientsFormFactory $clientsFormFactory, SettingInvoicesRepository $settingInvoicesRepository, User $user, SettingInvoices $settingInvoices)
    {
        parent::__construct();
        $this->clientsFormFactory = $clientsFormFactory;
        $this->settingInvoicesRepository = $settingInvoicesRepository;
        $this->user = $user;
        $this->settingInvoices = $settingInvoices;
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
            $this->settingInvoices->settingInvoicesFormValidate($form, $values);
        }
        catch (Exception $e)
        {
            $this->flashMessage($e->getMessage(), 'danger');
            $this->redirect(":Business:SettingInvoices:default");
        }
    }

    /**
     * @throws AbortException
     */
    public function settingInvoicesFormSucceeded($form, $values)
    {
        try
        {
            $this->settingInvoices->settingInvoicesFormSucceeded($form, $values);
        }
        catch (Exception $e)
        {
            $this->flashMessage($e->getMessage(), 'danger');
            $this->redirect(":Business:SettingInvoices:default");
        }

        $this->flashMessage("Změna se provedla", "success");
        $this->redirect(":Business:SettingInvoices:default");
    }

    /**
     * @throws AbortException
     */
    public function handleDeleteLogo(): void
    {
        $values = ["logo_path" => null];
        $this->settingInvoicesRepository->updateSetting($values, $this->user->getId());
        $this->flashMessage("Obrázek se smazal", "success");
        $this->redirect(":Business:SettingInvoices:default");
    }

}
