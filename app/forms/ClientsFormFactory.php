<?php

/** Author: Radek Jůzl */

namespace App\forms;

use Nette\Application\UI\Form;

class ClientsFormFactory
{
    /** @var FormFactory */
    private $formFactory;

    public function __construct(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function createClientForm(): Form
    {
        $form = $this->formFactory->create();

        $form->setHtmlAttribute('class', 'ajax');

        $form->addText('name', '*Jméno a příjmení:')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'Jméno a příjmení')
            ->setHtmlAttribute('autofocus');

        $form->addText('cin', 'IČ:')
            ->addRule($form::LENGTH, 'IČ musí mít %d znaků', 8)
            ->setHtmlAttribute("inputmode", "numeric")
            ->setHtmlAttribute('placeholder', 'IČ');

        $form->addEmail('email', 'E-mail:')
            ->setHtmlAttribute('placeholder', 'E-mail');

        $form->addText('street', '*Ulice a č.p.:')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'Ulice a č.p.:');

        $form->addText('city', '*Město:')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'Město');

        $form->addText('zip', '*PSČ:')
            ->setRequired()
            ->addRule($form::PATTERN, 'PSČ není ve tvaru pěti číslic', '\d{5}')
            ->addFilter(function ($value) {
                return str_replace(' ', '', $value); // odstraníme mezery z PSČ
            })
            ->setHtmlAttribute("inputmode", "numeric")
            ->setHtmlAttribute('placeholder', 'PSČ');

        $form->addText('phone', 'Telefon:')
            ->setHtmlAttribute('placeholder', 'Telefon');

        $form->addSubmit('addClient', 'Přidat');

        return $form;
    }

}