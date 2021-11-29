<?php

/** Author: Martin Kovalski, Radek Jůzl */

namespace App\forms;

use Nette\Application\UI\Form;
use Nette\Forms\Container;

class InvoicingFormFactory
{
    /** @var FormFactory */
    private $formFactory;

    public function __construct(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function createInvoiceForm(): Form
    {
        $form = $this->formFactory->create();

        $form->setHtmlAttribute('class', 'ajax');

        $form->addHidden('id');

        $form->addText('name', 'Jméno a příjmení:*')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'Jméno a příjmení');

        $form->addText('street', 'Ulice a č.p.:*')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'Ulice a č.p.:');

        $form->addText('city', 'Město:*')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'Město');

        $form->addText('zip', 'PSČ:*')
            ->setRequired()
            ->addRule($form::PATTERN, 'PSČ není ve tvaru pěti číslic', '\d{5}')
            ->addFilter(function ($value) {
                return str_replace(' ', '', $value); // odstraníme mezery z PSČ
            })
            ->setHtmlAttribute("inputmode", "numeric")
            ->setHtmlAttribute('placeholder', 'PSČ');

        $form->addText('cin', 'IČ:')
            ->addRule($form::LENGTH, 'IČ musí mít %d znaků', 8)
            ->setHtmlAttribute("inputmode", "numeric")
            ->setHtmlAttribute('placeholder', 'IČ');

        $form->addText('vat', 'DIČ:')
            ->addRule($form::MAX_LENGTH, 'DIČ musí mít maximálně %d znaků', 12)
            ->setHtmlAttribute('placeholder', 'DIČ');

        $form->addText('phone', 'Telefon:')
            ->setHtmlAttribute('placeholder', 'Telefon');

        $form->addEmail('email', 'E-mail:')
            ->setHtmlAttribute('placeholder', 'E-mail');

        $form->addCheckbox('addClient', 'Uložit jako nového klienta do adresáře');

        $form->addInteger('due_days_number', 'Dní do splatnosti:*')
            ->setRequired()
            ->setDefaultValue(14)
            ->addRule($form::MIN, 'Minimální počet dnů do splatnosti je %d', 1)
            ->setHtmlAttribute('inputmode', 'numeric');

        $copies = 1;
        $maxCopies = 20;

        $multiplier = $form->addMultiplier('multiplier', function (Container $container, Form $form) {
            $container->addText('name', 'Název položky:*')
                ->setRequired()
                ->setHtmlAttribute('placeholder', 'Název položky');
            $container->addText('count', 'Počet:*')
                ->setRequired()
                ->addRule($form::MIN, 'Minimální počet je %d', 0.5)
                ->setHtmlAttribute('inputmode', 'numeric')
                ->setHtmlAttribute('placeholder', 'Počet');
            $container->addRadioList('type', 'Jednotka:*', ['hours' => 'hod', 'pieces' => 'ks'])
                ->setDefaultValue('hours');
            $container->addText('unit_price', 'Cena za hod/ks:*')
                ->setRequired()
                ->addRule($form::FLOAT, 'Není peněžní hodnota')
                ->addRule($form::MIN, 'Minimální částka je %d', 0)
                ->setHtmlAttribute('placeholder', 'Cena za hod/ks');
        }, $copies, $maxCopies);

        $multiplier->addCreateButton('Přidat položku')
            ->addClass('ajax');

        $multiplier->addRemoveButton('Odebrat položku')
            ->addClass('ajax');

        return $form;
    }
}
