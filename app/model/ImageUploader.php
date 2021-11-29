<?php

/** Author: Radek Jůzl */

namespace App\model;

use App\repository\SettingInvoicesRepository;
use App\repository\UserRepository;
use App\repository\ExpensesRepository;
use Exception;
use Nette\Security\User;
use Nette\Utils\Finder;
use Nette\Utils\Image;
use Nette\Utils\ImageException;
use Nette\Utils\Random;
use Nette\Utils\FileSystem;

class ImageUploader
{
    /** @var UserRepository */
    private $userRepository;

    /** @var User*/
    public $user;

    /** @var SettingInvoicesRepository */
    private $settingInvoicesRepository;


    public function __construct(UserRepository $userRepository, User $user, SettingInvoicesRepository $settingInvoicesRepository)
    {
        $this->userRepository = $userRepository;
        $this->user = $user;
        $this->settingInvoicesRepository = $settingInvoicesRepository;
    }

    /**
     * @throws Exception
     */
    private function loadImg($img): Image
    {
        try
        {
            $loadImg = Image::fromFile($img);
        }
        catch (ImageException $e)
        {
            throw new Exception($e->getMessage());
        }
        return $loadImg;
    }

    /**
     * The function crops the image of the avatar to a profile
     */
    private function editImg($img): Image
    {
        $height = $img->getHeight();
        $width = $img->getWidth();
        if($height >= $width) /** The photo is portrait */
        {
            $new_height = 100 - ((100 * ($height - $width) ) / $height);
            $new_height = $new_height . '%';

            $img->crop('0%', '50%','100%', $new_height);
            $img->resize(null, 720, Image::SHRINK_ONLY);
        }
        else /** The photo is landscape */
        {
            $new_width = 100 - ((100 * ($width - $height) ) / $width);
            $new_width = $new_width . '%';

            $img->crop('50%', '0%', $new_width, '100%');
            $img->resize(720, null, Image::SHRINK_ONLY);
        }
        return $img;
    }

    private function generateNameImg($type): string
    {
        $end = 1;
        $name = "";
        $nameFolder = "";
        if($type == "avatars")
        {
            $nameFolder = "avatars";
        }
        elseif($type == "logo")
        {
            $nameFolder = "logo";
        }
        elseif($type == "expenses" or $type == "edit")
        {
            $nameFolder = "expenses";
        }

        while($end == 1)
        {
            $end = 0;
            $name = Random::generate(10, '0-9A-Z').".jpeg"; /** Generate random name file*/
            foreach (Finder::findFiles($name)->in("../www/".$nameFolder."/") as $key => $file) /** Check that such a name is not in the folder */
            {
                $end = 1;
            }
        }
        return $name;
    }

    /**
     * Save avatar and delete old avatar
     */
    private function saveAvatar($form, $values, $name, $img)
    {
        $values->avatar_path = "www/avatars/".$name;
        $img->save('../'.$values->avatar_path);

        $old_avatar = $this->userRepository->getUserAvatar($this->user->getId());
        if($old_avatar != null)
        {
            $old_avatar = "../".$old_avatar;
            FileSystem::delete($old_avatar);
        }

        $this->userRepository->updateProfile($this->user->getId(), $values);
    }

    private function saveLogoAndSetting($form, $values, $name, $img)
    {
        $values->logo_path = "www/logo/".$name;
        $img->save('../'.$values->logo_path);

        $user_id = $this->user->getId();
        $vat = $values->vat ?? null;
        $this->userRepository->updateUserVat($user_id, $vat);
        if(isset($values->variable_symbol)) /** Save data with variable symbol */
        {
            $data = ["account_number" => $values->account_number, "variable_symbol" => $values->variable_symbol, "vat_note" => $values->vat_note, "footer_note" => $values->footer_note, "logo_path" => $values->logo_path];
        }
        else /** Save data without variable symbol */
        {
            $data = ["account_number" => $values->account_number, "vat_note" => $values->vat_note, "footer_note" => $values->footer_note, "logo_path" => $values->logo_path];
        }
        
        $this->settingInvoicesRepository->updateSetting($data, $user_id);
    }

    public function saveExpenses($form, $values, $name, $img)
    {
        $values->path = "www/expenses/".$name;
        $img->save('../'.$values->path);

        return $values;
    }

    /**
     * @throws Exception
     */
    public function uploadImgFormSucceeded($form, $values, $type)
    {
        /** Classification by type */
        if($type == "avatars")
        {
            $path = $values->avatar_path;
        }
        elseif($type == "logo")
        {
            $path = $values->logo_path;
        }
        elseif($type == "expenses")
        {
            $path = $values->path;
        }
        else
        {
            $path = null;
        }

        try
        {
            $loadImg = $this->loadImg($path); /** Load image */
        }
        catch (Exception $e)
        {
            throw new Exception('Obrázek nebyl nahrán');
        }

        if($type == "avatars")
        {
            $loadImg = $this->editImg($loadImg);
            $name = $this->generateNameImg($type);
            $this->saveAvatar($form, $values, $name, $loadImg);
        }
        elseif($type == "logo")
        {
            $name = $this->generateNameImg($type);
            $this->saveLogoAndSetting($form, $values, $name, $loadImg);
        }
        elseif($type == "expenses")
        {
            $name = $this->generateNameImg($type);
            $this->saveExpenses($form, $values, $name, $loadImg);
        }
    }

    /**
     * @throws Exception
     * The function for saving changes image in expenses
     */
    public function uploadImgEditFormSucceeded($img): string
    {
        try
        {
            $loadImg = $this->loadImg($img);
        }
        catch (Exception $e)
        {
            throw new Exception('Obrázek nebyl nahrán');
        }
        $name = $this->generateNameImg("edit");
        $path = "www/expenses/".$name;
        $loadImg->save('../'.$path);

        return $path;
    }
}