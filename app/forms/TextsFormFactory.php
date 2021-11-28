<?php

/** Author: Radek Jůzl */

namespace App\forms;

use Nette\Application\UI\Form;

class TextsFormFactory
{
    /** @var FormFactory */
    private $formFactory;

    public function __construct(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
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
