<?php

/** Author: Radek Jůzl */

namespace App\AdminModule\presenters;

use App\forms\TextsFormFactory;
use App\model\TextsManager;
use App\repository\TextRepository;
use Exception;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;

final class TextsPresenter extends BasePresenter
{
    /** @var TextRepository */
    private $textRepository;

    /** @var TextsFormFactory */
    private $textsFormFactory;

    /** @var TextsManager */
    private $textsManager;

    public function __construct(TextRepository $textRepository, TextsFormFactory $textsFormFactory, TextsManager $textsManager)
    {
        parent::__construct();
        $this->textRepository = $textRepository;
        $this->textsFormFactory = $textsFormFactory;
        $this->textsManager = $textsManager;
    }

    /**
     * Load texts into the text editor
     */
    public function actionDefault()
    {
        $aboutus = $this->textRepository->getTextByType("aboutus");
        $row = ['text_aboutus' => $aboutus->text];
        $this->getComponent("textsForm")->setDefaults($row);

        $contact = $this->textRepository->getTextByType("contact");
        $row = ['text_contact' => $contact->text];
        $this->getComponent("textsForm")->setDefaults($row);
    }

    public function renderDefault()
    {
        $aboutus = $this->textRepository->getTextByType("aboutus");
        $contact = $this->textRepository->getTextByType("contact");
        $this->template->aboutus_img = $aboutus->img_path;
        $this->template->contact_img = $contact->img_path;
    }

    protected function createComponentTextsForm(): Form
    {
        $form = $this->textsFormFactory->createTextsForm();
        $form->onSuccess[] = [$this, "textsFormSucceeded"];
        return $form;
    }

    /**
     * @throws AbortException
     */
    public function textsFormSucceeded($form, $values)
    {
        try
        {
            $this->textsManager->textsFormSucceeded($form, $values);
            $this->flashMessage("Změna se provedla", "success");

        }
        catch (Exception $e)
        {
            $this->flashMessage($e->getMessage(), 'danger');
        }

        $this->redirect('this');
    }

    /**
     * Ajax deletion
     */
    /**
     * @throws AbortException
     */
    public function handleDeleteContact(): void
    {
        $values = ["img_path" => null];
        $this->textRepository->updateTextByType("contact", $values);
        $this->flashMessage("Obrázek se smazal", "success");

        if($this->isAjax())
        {
            $contact = $this->textRepository->getTextByType("contact");
            $this->template->contact_img = $contact->img_path;
            $this->redrawControl('contactImg');
            $this->redrawControl('flashes');
        }
        else
        {
            $this->redirect('this');
        }
    }

    /**
     * Ajax deletion
     */
    /**
     * @throws AbortException
     */
    public function handleDeleteAboutus(): void
    {
        $values = ["img_path" => null];
        $this->textRepository->updateTextByType("aboutus", $values);
        $this->flashMessage("Obrázek se smazal", "success");

        if($this->isAjax())
        {
            $aboutus = $this->textRepository->getTextByType("aboutus");
            $this->template->aboutus_img = $aboutus->img_path;
            $this->redrawControl('aboutusImg');
            $this->redrawControl('flashes');
        }
        else
        {
            $this->redirect('this');
        }
    }

}