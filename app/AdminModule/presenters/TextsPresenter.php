<?php

namespace App\AdminModule\presenters;

use App\forms\AdministratorsFormFactory;
use App\AdminModule\model\TextsManager;
use App\repository\TextRepository;
use Exception;
use Nette\Application\AbortException;
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
            bdump($values);
            $this->textsManager->textsFormSucceeded($form, $values);
            $this->flashMessage("Změna se provedla", "success");

            if($this->isAjax())
            {
                $aboutus = $this->textRepository->getTextByType("aboutus");
                $row_aboutus = ['text_aboutus' => $aboutus->text];
                $contact = $this->textRepository->getTextByType("contact");
                $row_contact = ['text_contact' => $contact->text];

                if($contact->img_path != null)
                {
                    $this->template->contact_img = $contact->img_path;
                    $this->redrawControl('contactImg');
                }
                if($aboutus->img_path != null)
                {
                    $this->template->aboutus_img = $aboutus->img_path;
                    $this->redrawControl('aboutusImg');
                }

                $form->reset();
                $form->setDefaults($row_aboutus);
                $form->setDefaults($row_contact);

                $form["text_aboutus"]->setHtmlAttribute('class', 'wysiwyg');
                $form["text_contact"]->setHtmlAttribute('class', 'wysiwyg');

                $this->redrawControl('textImgForm');
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
                $aboutus = $this->textRepository->getTextByType("aboutus");
                $row_aboutus = ['text_aboutus' => $aboutus->text];
                $contact = $this->textRepository->getTextByType("contact");
                $row_contact = ['text_contact' => $contact->text];

                $form->reset();
                $form->setDefaults($row_aboutus);
                $form->setDefaults($row_contact);
                $this->redrawControl('textImgForm');
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