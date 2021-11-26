<?php

/** Author: Radek Jůzl */

namespace App\forms;

use Nette\Application\UI\Form;

class ClientsAccountantFormFactory
{
    /** @var FormFactory */
    private $formFactory;

    public function __construct(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function createConnectionForm(): Form
    {
        $form = $this->formFactory->create();

        $form->setHtmlAttribute('class', 'ajax');

        $form->addEmail('email', 'E-mail:')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'E-mail');

        $form->addSubmit('send', 'Poslat');

        return $form;
    }
}