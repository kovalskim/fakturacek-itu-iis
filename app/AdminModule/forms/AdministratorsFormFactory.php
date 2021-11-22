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

    public function createTextsForm(): Form
    {
        $form = $this->formFactory->create();

        $form->addTextArea('text_aboutus', 'O nás:')
            ->addRule($form::MAX_LENGTH, 'Text je příliš dlouhá', 10000)
            ->setHtmlAttribute('class', 'wysiwyg');

        $form->addUpload('img_aboutus')
            ->addRule($form::IMAGE, 'Avatar musí být JPEG, PNG, GIF or WebP.')
            ->addRule($form::MAX_FILE_SIZE, 'Maximální velikost je 5 MB.', 1024 * 1024 * 5);

        $form->addTextArea('text_contact', 'Kontakty:')
            ->addRule($form::MAX_LENGTH, 'text je příliš dlouhá', 10000)
            ->setHtmlAttribute('class', 'wysiwyg');

        $form->addUpload('img_contact')
            ->addRule($form::IMAGE, 'Avatar musí být JPEG, PNG, GIF or WebP.')
            ->addRule($form::MAX_FILE_SIZE, 'Maximální velikost je 5 MB.', 1024 * 1024 * 5);

        $form->addSubmit('send', 'Uložit změny');

        return $form;
    }

}