<?php

/** Author: Martin Kovalski */

namespace App\forms;

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

        $form->setHtmlAttribute('class', 'ajax');

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

    /** Author: Radek Jůzl */
    public function createEditProfileAdminForm(): Form
    {
        $form = $this->formFactory->create();

        $form->addEmail('email', '*E-mail:')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'E-mail')
            ->setHtmlAttribute('autofocus');

        $form->addText('name', '*Jméno a příjmení:')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'Jméno a příjmení');

        $form->addText('phone', 'Telefon:')
            ->setHtmlAttribute('placeholder', 'Telefon');

        $form->addSubmit('send', 'Uložit změny');

        return $form;
    }
}