<?php

namespace App\PublicModule\forms;

use Nette\Application\UI\Form;

class LogInFormFactory
{
    /** @var FormFactory */
    private $formFactory;

    public function __construct(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function createLogInForm(): Form
    {
        $form = $this->formFactory->create();

        $form->addEmail('email', 'E-mail:')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'E-mail')
            ->setHtmlAttribute('autofocus');

        $form->addPassword('password', 'Heslo:')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'Heslo');

        $form->addSubmit('login', 'Přihlásit se');

        return $form;
    }

    public function createRegistrationForm(): Form
    {
        $form = $this->formFactory->create();

        $form->addEmail('email', 'E-mail:')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'E-mail')
            ->setHtmlAttribute('autofocus');

        $form->addPassword('password', 'Heslo:')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'Heslo');

        $form->addPassword('passwordAgain', 'Heslo znovu:')
            ->setRequired()
            ->setOmitted()
            ->addRule($form::EQUAL, 'Hesla se neshodují', $form['password'])
            ->setHtmlAttribute('placeholder', 'Heslo znovu');

        $form->addText('cin', 'IČ:')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'IČ');

        $form->addText('name', 'Jméno a příjmení:')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'Jméno a příjmení');

        $form->addText('street', 'Ulice a č.p.:')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'Ulice a č.p.:');

        $form->addText('city', 'Město')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'Město');

        $form->addText('zip', 'PSČ')
            ->setRequired()
            ->setHtmlAttribute("inputmode", "numeric")
            //TODO PSC add rule
            ->setHtmlAttribute('placeholder', 'PSČ');

        $form->addText('phone', 'Telefon:')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'Telefon');

        $form->addRadioList('role', 'Role:', ["bussines" => 'OSVČ', "accountant" => 'Účetní'])
            ->setDefaultValue("bussines")
            ->setRequired();

        $form->addSubmit('registration', 'Registrovat se');

        return $form;
    }
}