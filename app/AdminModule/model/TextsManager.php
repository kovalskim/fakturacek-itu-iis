<?php

namespace App\AdminModule\model;

use App\PublicModule\repository\TextRepository;
use Exception;
use Nette\Utils\Image;
use Nette\Utils\ImageException;

class TextsManager
{
    /** @var TextRepository */
    private $textRepository;

    public function __construct(TextRepository $textRepository)
    {
        $this->textRepository = $textRepository;
    }

    private function loadImg($values)
    {

    }
    /**
     * @throws Exception
     */
    public function textsFormSucceeded($form, $values)
    {
        $img_aboutus = null;
        $img_contact = null;
        if($values->img_aboutus->error == 0)
        {
            try
            {
                $img_aboutus = Image::fromFile($values->img_aboutus);
            }
            catch (ImageException $e)
            {
                throw new Exception('Fotka se nepovedla nahrát');
            }
        }

        if($values->img_contact->error == 0)
        {
            try
            {
                $img_contact = Image::fromFile($values->img_contact);
            }
            catch (ImageException $e)
            {
                throw new Exception('Fotka se nepovedla nahrát');
            }
        }

        /** Author: Martin Kovalski */
        $text = $values->text_aboutus;
        $text = str_replace("<i>","<em>", $text);
        $text = str_replace("</i>", "</em>", $text);
        $values->text_aboutus = $text;

        $text = $values->text_contact;
        $text = str_replace("<i>","<em>", $text);
        $text = str_replace("</i>", "</em>", $text);
        $values->text_contact = $text;
        /** ----- */

        if($img_aboutus != null)
        {
            $path_aboutus = "www/img/aboutus.jpeg";
            $img_aboutus->save('../'.$path_aboutus);
            $row = ['text' => $values->text_aboutus,'img_path' => $path_aboutus];
        }
        else
        {
            $row = ['text' => $values->text_aboutus];
        }
        $this->textRepository->updateTextByType("aboutus", $row);

        if($img_contact != null)
        {
            $path_contact = "www/img/contact.jpeg";
            $img_contact->save('../'.$path_contact);
            $row = ['text' => $values->text_contact,'img_path' => $path_contact];
        }
        else
        {
            $row = ['text' => $values->text_contact];
        }

        $this->textRepository->updateTextByType("contact", $row);
    }
}