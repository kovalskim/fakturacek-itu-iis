<?php

/** Author: Dalibor Kyjovský */

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
        $array = array();

        $categories = $this->connection->query('SELECT * FROM categories WHERE categories.id != 1')->fetchall();

        foreach ($categories as $row) {
            $array[$row->id] = $row->name;
        }

        $form = $this->formFactory->create();

        $form->addText('items', 'Položka:')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'Položka')
            ->setHtmlAttribute('autofocus');

        $form->addInteger('price', 'Cena')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'Cena')
            ->setHtmlAttribute('autofocus');   


        $form->addSelect('categories_id', 'Kategorie', $array)
            ->setHtmlAttribute('placeholder', 'Kategorie')
            ->setHtmlAttribute('autofocus')
            ->setPrompt('--- Kategorie ---');

        $form->addUpload('path', 'Doklad:')
            ->addRule($form::MIME_TYPE, 'Doklad musí být PDF, JPEG, PNG, GIF or WebP.', 'application/x-pdf,application/pdf,image/gif,image/webp,image/png,image/jpeg')
            ->addRule($form::MAX_FILE_SIZE, 'Maximální velikost je 5 MB.', 1024 * 1024 * 5)
            ->setRequired();

        $form->addSubmit('addExpenses', 'Přidat');

        return $form;
    }

    public function deleteExpensesForm(): Form
    {
        $array = array();

        $items = $this->connection->query('SELECT * FROM expenses')->fetchall();

        foreach ($items as $row) {
            $array[$row->id] = $row->items;
        }

        $form = $this->formFactory->create();

        $form->addSelect('id', 'Položka', $array)
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'Položka')
            ->setHtmlAttribute('autofocus');  

        $form->addSubmit('addExpenses', 'Vymazat');

        return $form;
    }
 
}