<?php

/** Author: Radek Jůzl */

namespace App\PublicModule\presenters;

use App\model\MailSender;
use App\repository\TextRepository;
use App\forms\ContactFormFactory;
use Exception;
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

    const INFO_EMAIL = 'info@fakturacek.cz';

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
            $this->mailSender->sendEmail(self::INFO_EMAIL, $subject, $body, $params);
            $this->flashMessage("Zpráva byla odeslána", "success");
        }

        catch (Exception $e)
        {
            $this->flashMessage($e->getMessage(), 'danger');
        }
        if($this->isAjax())
        {
            $form->reset();
            $this->redrawControl('contactAjaxForm');
            $this->redrawControl('flashes');
        }
        else
        {
            $this->redirect('this');
        }
    }

    public function renderDefault()
    {
        $this->template->text = $this->textRepository->getTextByType("contact");
    }
}
