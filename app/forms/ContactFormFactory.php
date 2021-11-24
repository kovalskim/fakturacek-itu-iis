<?php

/** Author: Radek Jůzl */

namespace App\forms;

use Nette\Application\UI\Form;

class ContactFormFactory
{
    /** @var FormFactory */
    private $formFactory;

    public function __construct(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function createContactForm(): Form
    {
        $form = $this->formFactory->create();

        $form->addText('name', 'Jméno:')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'Jméno')
            ->setHtmlAttribute('autofocus');

        $form->addEmail('email', 'E-mail:')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'E-mail');

        $form->addTextarea('message', 'Zpráva:')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'Zpráva');

        $form->addSubmit('send', 'Odeslat');

        return $form;
    }

}