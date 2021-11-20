<?php

/** Author: Martin Kovalski */

namespace App\BusinessModule\forms;

use Nette\Application\UI\Form;
use Nette\SmartObject;

class FormFactory
{
    use SmartObject;

    public function create(): Form
    {
        $form = new Form();
        $form->addProtection('Vaše relace vypršela. Vraťte se na domovskou stránku a zkuste to znovu.');
        $form->setHtmlAttribute('novalidate');
        return $form;
    }
}