<?php

/** Author: Radek Jůzl */

namespace App\PublicModule\model;

use App\repository\SettingInvoicesRepository;
use App\repository\UserRepository;
use Exception;
use Nette\Security\User;
use Nette\Utils\Finder;
use Nette\Utils\Image;
use Nette\Utils\ImageException;
use Nette\Utils\Random;
use Nette\Utils\FileSystem;

class UploadImage
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

    private function editImg($img): Image
    {
        $height = $img->getHeight();
        $width = $img->getWidth();
        if($height >= $width)
        {
            $new_height = 100 - ((100 * ($height - $width) ) / $height);
            $new_height = $new_height . '%';

            $img->crop('0%', '50%','100%', $new_height);
            $img->resize(null, 720, Image::SHRINK_ONLY);
        }
        else
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
        if($type == "avatars")
        {
            $nameFolder = "avatars";
        }
        else
        {
            $nameFolder = "logo";
        }

        while($end == 1)
        {
            $end = 0;
            $name = Random::generate(10, '0-9A-Z').".jpeg";
            foreach (Finder::findFiles($name)->in("../www/".$nameFolder."/") as $key => $file)
            {
                $end = 1;
            }
        }
        return $name;
    }

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

        $this->settingInvoicesRepository->updateSetting($values, $this->user->getId());
    }

    /**
     * @throws Exception
     */
    public function uploadImgFormSucceeded($form, $values, $type)
    {
        if($type == "avatars")
        {
            $path = $values->avatar_path;
        }
        else
        {
            $path = $values->logo_path;
        }

        try
        {
            $loadImg = $this->loadImg($path);
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
        else
        {
            $name = $this->generateNameImg($type);
            $this->saveLogoAndSetting($form, $values, $name, $loadImg);
        }
    }

}