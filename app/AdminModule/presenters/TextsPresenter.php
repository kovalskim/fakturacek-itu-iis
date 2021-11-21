<?php

namespace App\AdminModule\presenters;

use App\AdminModule\forms\AdministratorsFormFactory;
use App\AdminModule\model\TextsManager;
use App\PublicModule\repository\TextRepository;
use Exception;
use Nette\Application\UI\Form;

final class TextsPresenter extends BasePresenter
{
    /** @var TextRepository */
    private $textRepository;

    /** @var AdministratorsFormFactory */
    private $administratorsFormFactory;

    /** @var TextsManager */
    private $textsManager;


    public function __construct(TextRepository $textRepository, AdministratorsFormFactory $administratorsFormFactory, TextsManager $textsManager)
    {
        parent::__construct();
        $this->textRepository = $textRepository;
        $this->administratorsFormFactory = $administratorsFormFactory;
        $this->textsManager = $textsManager;
    }

    public function actionDefault()
    {

    }

    public function renderDefault()
    {
        $aboutus = $this->textRepository->getTextByType("aboutus");
        $row = ['text_aboutus' => $aboutus->text];
        $this->getComponent("textsForm")->setDefaults($row);

        $contact = $this->textRepository->getTextByType("contact");
        $row = ['text_contact' => $contact->text];
        $this->getComponent("textsForm")->setDefaults($row);

        $this->template->aboutus_img = $aboutus->img_path;
        $this->template->contact_img = $contact->img_path;
    }

    protected function createComponentTextsForm(): Form
    {
        $form = $this->administratorsFormFactory->createTextsForm();
        //$form->onValidate[] = [$this, "textsFormValidate"];
        $form->onSuccess[] = [$this, "textsFormSucceeded"];
        return $form;
    }

    public function textsFormSucceeded($form, $values)
    {
        try
        {
            $this->textsManager->textsFormSucceeded($form, $values);
        }
        catch (Exception $e)
        {
            $this->flashMessage($e->getMessage(), 'danger');
            $this->redirect(":Admin:Homepage:default");
        }

        $this->flashMessage("Změna se provedla", "success");
        $this->redirect(":Admin:Texts:default");
    }

    public function handleDeleteContact(): void
    {
        $values = ["img_path" => null];
        $this->textRepository->updateTextByType("contact", $values);
        $this->flashMessage("Obrázek se smazal", "success");
        $this->redirect(":Admin:Texts:default");
    }

    public function handleDeleteAboutus(): void
    {
        $values = ["img_path" => null];
        $this->textRepository->updateTextByType("aboutus", $values);
        $this->flashMessage("Obrázek se smazal", "success");
        $this->redirect(":Admin:Texts:default");
    }

}