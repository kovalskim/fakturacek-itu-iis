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
        $path_aboutus = "www/img/aboutus.jpeg";
        $path_contact = "www/img/contact.jpeg";
        if($img_aboutus != null)
        {
            $img_aboutus->save('../'.$path_aboutus);
        }
        if($img_contact != null)
        {
            $img_contact->save('../'.$path_contact);
        }

        $row = ['text' => $values->text_aboutus,'img_path' => $path_aboutus];
        $this->textRepository->updateTextByType("aboutus", $row);
        $row = ['text' => $values->text_contact,'img_path' => $path_contact];
        $this->textRepository->updateTextByType("contact", $row);
    }
}