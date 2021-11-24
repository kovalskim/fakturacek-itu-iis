<?php

/** Author: Dalibor Kyjovský */

namespace App\forms;

use Nette\Application\UI\Form;

class CategoryFormFactory
{
    /** @var FormFactory */
    private $formFactory;

    public function __construct(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function createCategoryForm(): Form
    {
        $form = $this->formFactory->create();

        $form->addText('name', 'Kategorie:')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'Kategorie')
            ->setHtmlAttribute('autofocus');

        $form->addSubmit('addExpenses', 'Přidat');

        return $form;
    }

    public function deleteCategoryForm(): Form
    {
        $form = $this->formFactory->create();

        $form->addText('name', 'Kategorie:')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'Kategorie')
            ->setHtmlAttribute('autofocus');

        $form->addSubmit('addExpenses', 'Vymazat');

        return $form;
    }
 
}