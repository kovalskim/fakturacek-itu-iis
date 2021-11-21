<?php

/** Author: Radek Jůzl */

namespace App\BusinessModule\presenters;

use App\BusinessModule\forms\ClientsFormFactory;
use App\PublicModule\model\UploadImage;
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

    /** @var UploadImage */
    public $uploadImage;

    public function __construct(ClientsFormFactory $clientsFormFactory, SettingInvoicesRepository $settingInvoicesRepository, User $user, UploadImage $uploadImage)
    {
        parent::__construct();
        $this->clientsFormFactory = $clientsFormFactory;
        $this->settingInvoicesRepository = $settingInvoicesRepository;
        $this->user = $user;
        $this->uploadImage = $uploadImage;
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
        $form->onSuccess[] = [$this, "settingInvoicesFormSucceeded"];
        return $form;
    }

    /**
     * @throws AbortException
     */
    public function settingInvoicesFormSucceeded($form, $values)
    {
        if($values->logo_path->error == 0)
        {
            try
            {
                $this->uploadImage->uploadImgFormSucceeded($form,$values, "logo");
            }
            catch (Exception $e)
            {
                $this->flashMessage($e->getMessage(), 'danger');
                $this->redirect(":Business:SettingInvoices:default");
            }
        }
        else
        {
            $values2 = ["account_number" => $values->account_number, "variable_symbol" => $values->variable_symbol, "vat" => $values->vat];
            $this->settingInvoicesRepository->updateSetting($values2, $this->user->getId());
        }

        $this->flashMessage("Změna se provedla", "success");
        $this->redirect(":Business:SettingInvoices:default");
    }

}
