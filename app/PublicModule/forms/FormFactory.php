<?php

namespace App\PublicModule\forms;

use Nette\Application\UI\Form;
use Nette\SmartObject;

class FormFactory
{
    use SmartObject;

    public function create(): Form
    {
        $form = new Form();
        $form->addProtection();
        $form->setHtmlAttribute('novalidate');
        return $form;
    }
}