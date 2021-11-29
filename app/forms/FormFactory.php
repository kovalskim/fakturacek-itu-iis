<?php

/** Author: Martin Kovalski */

namespace App\forms;

use Nette\Application\UI\Form;
use Nette\SmartObject;
use Nextras\FormsRendering\Renderers\Bs4FormRenderer;
use Nextras\FormsRendering\Renderers\FormLayout;

class FormFactory
{
    use SmartObject;

    public function create(): Form
    {
        $form = new Form();
        $form->addProtection('Vaše relace vypršela, vraťte se na domovskou stránku a zkuste to znovu');
        $form->setHtmlAttribute('novalidate');

        $form->setRenderer(new Bs4FormRenderer(FormLayout::INLINE));

        return $form;
    }
}