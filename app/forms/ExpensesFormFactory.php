<?php

/** Author: Dalibor Kyjovský, Radek Jůzl */

namespace App\forms;

use Nextras\Dbal\Connection;
use Nette\Application\UI\Form;

class ExpensesFormFactory
{
    /** @var FormFactory */
    private $formFactory;

    /** @var $connection */
    public $connection;

    public function __construct(FormFactory $formFactory, Connection $connection)
    {
        $this->formFactory = $formFactory;
        $this->connection = $connection;
    }

    public function createExpensesForm(): Form
    {

        $form = $this->formFactory->create();

        $form->setHtmlAttribute('class', 'ajax');

        $form->addText('items', 'Položka:*')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'Položka')
            ->setHtmlAttribute('autofocus');

        $form->addText('price', 'Cena:*')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'Cena')
            ->addRule($form::FLOAT, 'Není peněžní hodnota')
            ->addRule($form::MIN, 'Minimální částka je %d', 0);

        $form->addSelect('expenses_cat_id', 'Kategorie:')
            ->setHtmlAttribute('placeholder', 'Kategorie')
            ->setPrompt('-- Výchozí --');

        $form->addText('datetime', 'Datum zaplacení:*')
            ->setType('date')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'Datum zaplacení');

        $form->addUpload('path', 'Doklad:*')
            ->addRule($form::IMAGE, 'Obrázek musí být JPEG, PNG, GIF or WebP.')
            ->addRule($form::MAX_FILE_SIZE, 'Maximální velikost je 5 MB.', 1024 * 1024 * 5)
            ->setRequired();

        $form->addSubmit('addExpenses', 'Přidat výdaj');

        return $form;
    }
}