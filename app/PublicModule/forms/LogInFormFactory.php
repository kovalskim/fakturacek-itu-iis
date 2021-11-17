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

    /** Author: Martin Kovalski */
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

    /** Author: Radek Jůzl */
    public function createRegistrationForm(): Form
    {
        $form = $this->formFactory->create();

        $form->addEmail('email', 'E-mail:')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'E-mail')
            ->setHtmlAttribute('autofocus');

        $form->addPassword('password', 'Heslo:')
            ->setRequired()
            ->addRule($form::MIN_LENGTH, 'Heslo musí mít alespoň %d znaků', 8)
            ->setHtmlAttribute('placeholder', 'Heslo');

        $form->addPassword('passwordAgain', 'Heslo znovu:')
            ->setRequired()
            ->setOmitted()
            ->addRule($form::EQUAL, 'Hesla se neshodují', $form['password'])
            ->setHtmlAttribute('placeholder', 'Heslo znovu');

        $form->addText('cin', 'IČ:')
            ->setRequired()
            ->addRule($form::LENGTH, 'IČ musí mít %d znaků', 8)
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
            ->setHtmlAttribute('placeholder', 'PSČ');

        $form->addText('phone', 'Telefon:')
            ->setHtmlAttribute('placeholder', 'Telefon');

        $form->addRadioList('role', 'Role:', ["business" => 'OSVČ', "accountant" => 'Účetní'])
            ->setDefaultValue("business")
            ->setRequired();

        $form->addSubmit('registration', 'Registrovat se');

        return $form;
    }

    /** Author: Martin Kovalski */
    public function createForgottenPasswordForm(): Form
    {
        $form = $this->formFactory->create();

        $form->addEmail('email', 'E-mail')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'E-mail')
            ->setHtmlAttribute('autofocus');

        $form->addSubmit('send', 'Odeslat');

        return $form;
    }

    /** Author: Martin Kovalski */
    public function createNewPasswordForm(): Form
    {
        $form = $this->formFactory->create();

        $form->addHidden('token');

        $form->addPassword('password', 'Nové heslo')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'Nové heslo')
            ->setHtmlAttribute('autofocus');

        $form->addPassword('password_again', 'Nové heslo znovu')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'Nové heslo znovu')
            ->addRule(Form::EQUAL, 'Hesla se neshodují', $form['password'])
            ->setOmitted();

        $form->addSubmit('change', 'Změnit heslo');

        return $form;
    }

    /** Author: Martin Kovalski */
    public function createChangePasswordForm(): Form
    {
        $form = $this->formFactory->create();

        $form->addPassword('old_password', 'Staré heslo')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'Staré heslo')
            ->setHtmlAttribute('autofocus');

        $form->addPassword('password', 'Nové heslo')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'Nové heslo');

        $form->addPassword('password_again', 'Nové heslo znovu')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'Nové heslo znovu')
            ->addRule(Form::EQUAL, 'Hesla se neshodují.', $form['password'])
            ->setOmitted();

        $form->addSubmit('change', 'Změnit heslo');

        return $form;
    }

    /** Author: Radek Jůzl */
    public function createEditProfileForm(): Form
    {
        $form = $this->formFactory->create();

        $form->addEmail('email', 'E-mail')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'E-mail')
            ->setHtmlAttribute('autofocus');

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
            ->setHtmlAttribute('placeholder', 'PSČ');

        $form->addText('phone', 'Telefon:')
            ->setHtmlAttribute('placeholder', 'Telefon');

        $form->addSubmit('send', 'Odeslat');

        return $form;
    }


}