<?php

namespace App\PublicModule\model;

/** Author: Martin Kovalski */

use Nette\Application\LinkGenerator;
use Nette\Application\UI\Template;
use Nette\Application\UI\TemplateFactory;
use Nette\Mail\Mailer;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;

class MailSender
{
    const NOREPLY_EMAIL = 'noreply@fakturacek.cz';
    const WEBPAGE_NAME = 'Fakturáček';

    /** @var LinkGenerator */
    private $linkGenerator;

    /** @var TemplateFactory */
    private $templateFactory;

    /** @var Mailer */
    private $mailer;

    public function __construct(LinkGenerator $linkGenerator, TemplateFactory $templateFactory, Mailer $mailer)
    {
        $this->linkGenerator = $linkGenerator;
        $this->templateFactory = $templateFactory;
        $this->mailer = $mailer;
    }

    private function createTemplate(): Template
    {
        $template = $this->templateFactory->createTemplate();
        $template->getLatte()->addProvider('uiControl', $this->linkGenerator);
        return $template;
    }

    private function createEmail($from, $to, $subject, $body, $params, $attachment): Message
    {
        $template = $this->createTemplate();

        $html = $template->renderToString(__DIR__ . '/../../emails/' . $body, $params);

        $mail = new Message();
        $mail->setFrom($from)
            ->addTo($to)
            ->setSubject($subject)
            ->setHtmlBody($html);

        if($attachment != NULL)
        {
            $mail->addAttachment('file.txt', $attachment);
        }

        return $mail;
    }

    public function sendEmail($to, $subject, $body, $params, $attachment = NULL, $from = self::NOREPLY_EMAIL)
    {
        $subject = '[' . self::WEBPAGE_NAME . '] ' . $subject;

        $mail = $this->createEmail($from, $to, $subject, $body, $params, $attachment);

        /** Send e-mail to Tracy on localhost */
        $this->mailer->send($mail);

        /** Send e-mail on production */
        //$mailer = new SendmailMailer();
        //$mailer->send($mail);
    }
}