<?php

/** Author: Dalibor Kyjovský, Radek Jůzl */

namespace App\model;

use App\repository\ExpensesRepository;
use Exception;
use Nette\Utils\FileSystem;

class ExpensesManager
{

    /** @var ExpensesRepository */
    private $expensesRepository;

    /** @var ImageUploader */
    private $imageUploader;

    public function __construct(ExpensesRepository $expensesRepository, ImageUploader $imageUploader)
    {
        $this->expensesRepository = $expensesRepository;
        $this->imageUploader = $imageUploader;
    }


    public function deleteExpense($primary)
    {
        $image_path = $this->expensesRepository->getPathById($primary);
        $image_path = "../".$image_path;
        FileSystem::delete($image_path);
        $this->expensesRepository->deleteExpensesByUserId($primary);
    }

    public function expensesFormValidate($form, $values = null)
    {
        if(!$values)
        {
            $values = $form->getValues();
        }
        $price = str_replace(",",".", $values->price);
        if(!(is_numeric($price)))
        {
            $form["price"]->addError("Není peněžní hodnota");
        }
        elseif($price < 0)
        {
            $form["price"]->addError("Minimální částka je 0");
        }
        if($values->img->error == 0)
        {
            $type = $values->img->getContentType();
            if($type != "image/png" and $type != "image/jpeg" and $type != "image/gif" and $type != "image/webp")
            {
                $form["img"]->addError("Obrázek musí být JPEG, PNG, GIF or WebP.");
            }
            elseif($values->img->size > (1024*1024*5))
            {
                $form["img"]->addError("Maximální velikost je 5 MB.");
            }
        }
    }

    /**
     * @throws Exception
     */
    public function editExpenseFormSucceeded($form, $values = null)
    {
        if(!$values)
        {
            $values = $form->getValues();
        }
        $values->price = str_replace(",",".", $values->price);

        $image_path = $this->expensesRepository->getPathById($values->id);
        $image_path = "../".$image_path;
        FileSystem::delete($image_path);

        if($values->img->error == 0)
        {
            try
            {
                $path = $this->imageUploader->uploadImgEditFormSucceeded($values->img);
            }
            catch(Exception $e)
            {
                throw new Exception('Nepovedlo se nahrát fotku');
            }
            $values_edit = [
                "datetime" => $values->datetime,
                "items" => $values->items,
                "price" => $values->price,
                "expenses_cat_id" => $values->cat_id,
                "id" => $values->id,
                "path" => $path
            ];
        }
        else
        {
            $values_edit = [
                "datetime" => $values->datetime,
                "items" => $values->items,
                "price" => $values->price,
                "expenses_cat_id" => $values->cat_id,
                "id" => $values->id
            ];

        }
        $this->expensesRepository->updateExpenseById($values->id, $values_edit);
    }

}
