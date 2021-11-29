<?php

/** Author: Radek Jůzl */

namespace App\forms;

use Nette\Application\UI\Form;

class SettingInvoicesFormFactory
{
    /** @var FormFactory */
    private $formFactory;

    public function __construct(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function createSettingInvoicesForm(): Form
    {
        $form = $this->formFactory->create();

        $form->setHtmlAttribute('class', 'ajax');

        $form->addText('account_number', 'Číslo účtu:*')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'Číslo účtu')
            ->setHtmlAttribute('autofocus');

        $form->addRadioList('variable_symbol', 'Variabilní symbol:*', ["YYMM00" => "YYMM00", "YY0000" => "YY0000", "YY000" => "YY000"])
            ->setDefaultValue("YYMM00")
            ->setRequired()
            ->setOption('description', 'Lze nastavit pouze jednou při počátečním nastavení');

        $form->addRadioList('vat_note', 'Plátce DPH:*', ["0" => "Nejsem plátce DPH", "1" => "Jsem plátce DPH"])
            ->setDefaultValue("0")
            ->addCondition($form::EQUAL, true)
            ->toggle("text_vat")
            ->setRequired();

        $form->addText('vat', 'DIČ:*')
            ->setOption('id', 'text_vat')
            ->addRule($form::MAX_LENGTH, 'DIČ musí mít maximálně %d znaků', 12)
            ->setHtmlAttribute('placeholder', 'DIČ');

        $form->addText('footer_note', 'Zápatí:')
            ->setHtmlAttribute('placeholder', 'Zápatí');

        $form->addUpload('logo_path', 'Logo:')
            ->addRule($form::IMAGE, 'Logo musí být JPEG, PNG, GIF or WebP')
            ->addRule($form::MAX_FILE_SIZE, 'Maximální velikost je 5 MB', 1024 * 1024 * 5);

        $form->addSubmit('saveSetting', 'Uložit');

        return $form;
    }
}