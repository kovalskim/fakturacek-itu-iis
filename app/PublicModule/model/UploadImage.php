<?php

/** Author: Radek Jůzl */

namespace App\PublicModule\model;

use App\PublicModule\repository\UserRepository;
use Exception;
use Nette\Security\User;
use Nette\Utils\Finder;
use Nette\Utils\Image;
use Nette\Utils\ImageException;
use Nette\Utils\Random;
use Nette\Utils\UnknownImageFileException;
use Nette\Utils\FileSystem;

class UploadImage
{
    /** @var UserRepository */
    private $userRepository;

    /** @var User*/
    public $user;

    public function __construct(UserRepository $userRepository, User $user)
    {
        $this->userRepository = $userRepository;
        $this->user = $user;
    }

    /**
     * @throws ImageException
     */
    public function uploadAvatarFormSucceeded($form, $values)
    {
        try
        {
            $avatar = Image::fromFile($values->avatar_path);
        }
        catch (ImageException $e)
        {
            throw new Exception('Avatar nebyl nahrán');
        }

        $height = $avatar->getHeight();
        $width = $avatar->getWidth();
        if($height >= $width)
        {
            $new_height = 100 - ((100 * ($height - $width) ) / $height);
            $new_height = $new_height . '%';

            $avatar->crop('0%', '50%','100%', $new_height);
            $avatar->resize(null, 720, Image::SHRINK_ONLY);
        }
        else
        {
            $new_width = 100 - ((100 * ($width - $height) ) / $width);
            $new_width = $new_width . '%';

            $avatar->crop('50%', '0%', $new_width, '100%');
            $avatar->resize(720, null, Image::SHRINK_ONLY);
        }
        $end = 1;
        while($end == 1)
        {
            $end = 0;
            $name = Random::generate(10, '0-9A-Z').".jpeg";
            foreach (Finder::findFiles($name)->in("../www/avatars/") as $key => $file)
            {
               $end = 1;
            }
        }

        $values->avatar_path = "www/avatars/".$name;
        $avatar->save('../'.$values->avatar_path);

        $old_avatar = $this->userRepository->getUserAvatar($this->user->getId());
        if($old_avatar != null)
        {
            $old_avatar = "../".$old_avatar;
            FileSystem::delete($old_avatar);
        }

        $this->userRepository->updateProfile($this->user->getId(), $values);
    }
}