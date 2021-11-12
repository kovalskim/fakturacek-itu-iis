<?php

/** Author: Radek Jůzl */

namespace App\PublicModule\presenters;

use App\PublicModule\repository\TextRepository;
use App\PublicModule\forms\ContactFormFactory;
use App\PublicModule\model\ContactManager;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;

final class ContactPresenter extends BasePresenter
{
    /** @var TextRepository */
    private $textRepository;

    /** @var ContactFormFactory */
    private $contactFormFactory;

    /** @var ContactManager */
    private $contactManager;

    public function __construct(TextRepository $textRepository, ContactFormFactory $contactFormFactory, ContactManager $contactManager)
    {
        parent::__construct();
        $this->textRepository = $textRepository;
        $this->contactFormFactory = $contactFormFactory;
        $this->contactManager = $contactManager;
    }

    public function actionDefault()
    {

    }

    protected function createComponentContactForm(): Form
    {
        $form = $this->contactFormFactory->createContactForm();
        $form->onSuccess[] = [$this, "contactFormSucceeded"];
        return $form;
    }

    /**
     * @throws AbortException
     */
    public function contactFormSucceeded($form, $values)
    {
        $this->contactManager->contactFormSucceeded($form, $values);

        $this->flashMessage("zpráva byla odeslána", "success");
        $this->redirect(":Public:Contact:default");
    }

    public function renderDefault()
    {
        $this->template->text = $this->textRepository->getTextByType("contact");
    }
}
