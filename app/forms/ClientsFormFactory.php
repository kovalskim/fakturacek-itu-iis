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

    public function createSettingInvoicesForm(): Form
    {
        $form = $this->formFactory->create();

        $form->setHtmlAttribute('class', 'ajax');

        $form->addText('account_number', '*Číslo účtu:')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'Číslo účtu')
            ->setHtmlAttribute('autofocus');

        $form->addRadioList('variable_symbol', '*Variabilní symbol:', ["yymmxx" => "YYMM00", "yyxxxx" => "YY0000", "yyxxx" => "YY000"])
            ->setDefaultValue("yymmxx")
            ->setRequired();

        $form->addTextArea('vat', '*DPH:')
            ->setHtmlAttribute('placeholder', 'DPH')
            ->setRequired();

        $form->addUpload('logo_path', 'Logo:')
            ->addRule($form::IMAGE, 'Logo musí být JPEG, PNG, GIF or WebP.')
            ->addRule($form::MAX_FILE_SIZE, 'Maximální velikost je 5 MB.', 1024 * 1024 * 5);

        $form->addSubmit('saveSetting', 'Uložit');

        return $form;
    }
}