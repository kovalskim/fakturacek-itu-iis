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
        FileSystem::delete($image_path); /** Delete image from server */
        $this->expensesRepository->deleteExpensesByUserId($primary); /** Delete image from database */
    }

    public function expensesFormValidate($form, $values = null)
    {
        if(!$values)
        {
            $values = $form->getValues();
        }
        $price = str_replace(",",".", $values->price);
        if(!(is_numeric($price))) /** check if it is the number */
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
            if($type != "image/png" and $type != "image/jpeg" and $type != "image/gif" and $type != "image/webp") /** Check if it is the correct type  */
            {
                $form["img"]->addError("Obrázek musí být JPEG, PNG, GIF or WebP.");
            }
            elseif($values->img->size > (1024*1024*5)) /** Max. 5 MB */
            {
                $form["img"]->addError("Maximální velikost je 5 MB.");
            }
        }
    }

    /**
     * @throws Exception
     * Function
     */
    public function editExpenseFormSucceeded($form, $values = null)
    {
        if(!$values)
        {
            $values = $form->getValues();
        }
        $values->price = str_replace(",",".", $values->price);

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
            $image_path = $this->expensesRepository->getPathById($values->id);
            $image_path = "../".$image_path;
            FileSystem::delete($image_path);
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
