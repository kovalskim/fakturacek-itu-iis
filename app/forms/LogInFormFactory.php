<?php

/** Author: Martin Kovalski, Radek Jůzl */

namespace App\forms;

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

        $form->setHtmlAttribute('class', 'ajax');

        $form->addEmail('email', 'E-mail:*')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'E-mail')
            ->setHtmlAttribute('autofocus');

        $form->addPassword('password', 'Heslo:*')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'Heslo');

        $form->addSubmit('login', 'Přihlásit se');

        return $form;
    }

    public function createRegistrationForm(): Form
    {
        $form = $this->formFactory->create();

        $form->setHtmlAttribute('class', 'ajax');

        $form->addEmail('email', 'E-mail:*')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'E-mail')
            ->setHtmlAttribute('autofocus');

        $form->addPassword('password', 'Heslo:*')
            ->setRequired()
            ->addRule($form::MIN_LENGTH, 'Heslo musí mít alespoň %d znaků', 8)
            ->setHtmlAttribute("inputmode", "numeric")
            ->setHtmlAttribute('placeholder', 'Heslo');

        $form->addPassword('passwordAgain', 'Heslo znovu:*')
            ->setRequired()
            ->setOmitted()
            ->addRule($form::EQUAL, 'Hesla se neshodují', $form['password'])
            ->setHtmlAttribute('placeholder', 'Heslo znovu');

        $form->addText('cin', 'IČ:*')
            ->setRequired()
            ->addRule($form::LENGTH, 'IČ musí mít %d znaků', 8)
            ->setHtmlAttribute('placeholder', 'IČ');

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
            ->addFilter(function ($value) {
            return str_replace(' ', '', $value); // odstraníme mezery z PSČ
            })
            ->addRule($form::PATTERN, 'PSČ není ve tvaru pěti číslic', '\d{5}')
            ->setHtmlAttribute("inputmode", "numeric")
            ->setHtmlAttribute('placeholder', 'PSČ');

        $form->addText('phone', 'Telefon:')
            ->setHtmlAttribute('placeholder', 'Telefon');

        $form->addRadioList('role', 'Role:*', ["business" => 'OSVČ', "accountant" => 'Účetní'])
            ->setDefaultValue("business")
            ->setRequired();

        $form->addSubmit('registration', 'Registrovat se');

        return $form;
    }

    public function createForgottenPasswordForm(): Form
    {
        $form = $this->formFactory->create();

        $form->setHtmlAttribute('class', 'ajax');

        $form->addEmail('email', 'E-mail:')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'E-mail')
            ->setHtmlAttribute('autofocus');

        $form->addSubmit('send', 'Odeslat');

        return $form;
    }

    public function createNewPasswordForm(): Form
    {
        $form = $this->formFactory->create();

        $form->addHidden('token');

        $form->addPassword('password', 'Nové heslo:')
            ->setRequired()
            ->addRule($form::MIN_LENGTH, 'Heslo musí mít alespoň %d znaků', 8)
            ->setHtmlAttribute('placeholder', 'Nové heslo')
            ->setHtmlAttribute('autofocus');

        $form->addPassword('password_again', 'Nové heslo znovu:')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'Nové heslo znovu')
            ->addRule($form::EQUAL, 'Hesla se neshodují', $form['password'])
            ->setOmitted();

        $form->addSubmit('change', 'Změnit heslo');

        return $form;
    }

    public function createChangePasswordForm(): Form
    {
        $form = $this->formFactory->create();

        $form->addPassword('old_password', 'Staré heslo:')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'Staré heslo')
            ->setHtmlAttribute('autofocus');

        $form->addPassword('password', 'Nové heslo:')
            ->setRequired()
            ->addRule($form::MIN_LENGTH, 'Heslo musí mít alespoň %d znaků', 8)
            ->setHtmlAttribute('placeholder', 'Nové heslo');

        $form->addPassword('password_again', 'Nové heslo znovu')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'Nové heslo znovu')
            ->addRule($form::EQUAL, 'Hesla se neshodují.', $form['password'])
            ->setOmitted();

        $form->addSubmit('change', 'Změnit heslo');

        return $form;
    }

    public function createEditProfileForm(): Form
    {
        $form = $this->formFactory->create();

        $form->setHtmlAttribute('class', 'ajax');

        $form->addEmail('email', 'E-mail:*')
            ->setRequired()
            ->setHtmlAttribute('placeholder', 'E-mail')
            ->setHtmlAttribute('autofocus');

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
            ->addFilter(function ($value) {
                    return str_replace(' ', '', $value); // odstraníme mezery z PSČ
                })
            ->addRule($form::PATTERN, 'PSČ není ve tvaru pěti číslic', '\d{5}')
            ->setHtmlAttribute("inputmode", "numeric")
            ->setHtmlAttribute('placeholder', 'PSČ');

        $form->addText('phone', 'Telefon:')
            ->setHtmlAttribute('placeholder', 'Telefon');

        $form->addSubmit('send', 'Uložit změny');

        return $form;
    }

    public function createUploadAvatarForm(): Form
    {
        $form = $this->formFactory->create();

        $form->setHtmlAttribute('class', 'ajax');

        $form->addUpload('avatar_path')
            ->addRule($form::IMAGE, 'Avatar musí být JPEG, PNG, GIF or WebP.')
            ->addRule($form::MAX_FILE_SIZE, 'Maximální velikost je 5 MB.', 1024 * 1024 * 5)
            ->setRequired();

        $form->addSubmit('send', 'Nahrát fotku');

        return $form;
    }
}