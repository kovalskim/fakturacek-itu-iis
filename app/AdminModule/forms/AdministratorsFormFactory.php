<?php

/** Author: Martin Kovalski */

namespace App\AdminModule\forms;

use Nette\Application\UI\Form;

class AdministratorsFormFactory
{
    /** @var FormFactory */
    private $formFactory;

    public function __construct(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function createAdministratorForm(): Form
    {
        $form = $this->formFactory->create();

        $form->addText('name', 'Jméno a příjmení:')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'Jméno a příjmení')
            ->setHtmlAttribute('autofocus');

        $form->addEmail('email', 'E-mail:')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'E-mail');

        $form->addSubmit('create', 'Vytvořit administrátora');

        return $form;
    }

}