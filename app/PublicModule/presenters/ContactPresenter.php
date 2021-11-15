<?php

/** Author: Radek Jůzl */

namespace App\PublicModule\presenters;

use App\PublicModule\model\MailSender;
use App\PublicModule\repository\TextRepository;
use App\PublicModule\forms\ContactFormFactory;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;

final class ContactPresenter extends BasePresenter
{
    /** @var TextRepository */
    private $textRepository;

    /** @var ContactFormFactory */
    private $contactFormFactory;

    /** @var MailSender */
    private $mailSender;


    public function __construct(TextRepository $textRepository, ContactFormFactory $contactFormFactory, MailSender $mailSender)
    {
        parent::__construct();
        $this->textRepository = $textRepository;
        $this->contactFormFactory = $contactFormFactory;
        $this->mailSender = $mailSender;
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
        $subject = "Kontaktní formulář";
        $body = 'contactTemplate.latte';
        $params = [
            'subject' => $subject,
            'email' => $values->email,
            'name' => $values->name,
            'message' => $values->message
        ];

        try
        {
            $this->mailSender->sendEmail("radekjuzl@seznam.cz", $subject, $body, $params); //TODO: Komu poslat?
            $this->flashMessage("Zpráva byla odeslána", "success");
        }

        catch (Exception $e)
        {
            $this->flashMessage($e->getMessage(), 'danger');
        }

        $this->redirect('this');
    }

    public function renderDefault()
    {
        $this->template->text = $this->textRepository->getTextByType("contact");
    }
}
