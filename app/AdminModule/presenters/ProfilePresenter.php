<?php

/** Author: Radek Jůzl */

namespace App\AdminModule\presenters;

use App\forms\AdministratorsFormFactory;
use App\forms\LogInFormFactory;
use App\model\ProfileManager;
use App\model\ImageUploader;
use App\repository\UserRepository;
use Exception;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Security\User;
use Nette\Utils\FileSystem;

final class ProfilePresenter extends BasePresenter
{
    /** @var User*/
    public $user;

    /** @var UserRepository */
    private $userRepository;

    /** @var AdministratorsFormFactory */
    private $administratorsFormFactory;

    /** @var ProfileManager */
    private $profileManager;

    /** @var ImageUploader */
    private $imageUploader;

    /** @var LogInFormFactory */
    private $logInFormFactory;

    public function __construct(User $user, UserRepository $userRepository, AdministratorsFormFactory $administratorsFormFactory, ProfileManager $profileManager, ImageUploader $imageUploader, LogInFormFactory $logInFormFactory)
    {
        parent::__construct();
        $this->user = $user;
        $this->userRepository = $userRepository;
        $this->administratorsFormFactory = $administratorsFormFactory;
        $this->profileManager = $profileManager;
        $this->imageUploader = $imageUploader;
        $this->logInFormFactory = $logInFormFactory;
    }

    public function actionDefault()
    {

    }

    public function renderDefault()
    {
        $this->template->profile = $this->userRepository->getUserProfile($this->user->getId());
    }

    public function actionEdit()
    {
        $profile = $this->userRepository->getUserProfile($this->user->getId());
        $this->getComponent("editProfileForm")->setDefaults($profile);
    }

    protected function createComponentEditProfileForm(): Form
    {
        $form = $this->administratorsFormFactory->createEditProfileAdminForm();
        $form->onValidate[] = [$this, "editProfileFormValidate"];
        $form->onSuccess[] = [$this, "editProfileFormSucceeded"];
        return $form;
    }

    public function editProfileFormValidate($form, $values)
    {
        $this->profileManager->editProfileFormValidate($form, $values);
    }

    /**
     * @throws AbortException
     */

    public function editProfileFormSucceeded($form, $values)
    {
        $new_email = $this->profileManager->editProfileFormSucceeded($form, $values);
        if($new_email)
        {
            $this->session->destroy();
            $this->user->logout();
            $this->flashMessage('Ověř si nový e-mail a přihlaš se s ním!', 'info');
            $this->redirect(':Public:Homepage:default');
        }

        $this->flashMessage("Změna se provedla", "success");
        $this->redirect(":Admin:Profile:default");
    }

    public function actionUpload()
    {
        $this->template->profile = $this->userRepository->getUserProfile($this->user->getId());
    }

    protected function createComponentUploadAvatarForm(): Form
    {
        $form = $this->logInFormFactory->createUploadAvatarForm();
        $form->onSuccess[] = [$this, "uploadAvatarFormSucceeded"];
        return $form;
    }

    /**
     * @throws AbortException
     */
    public function uploadAvatarFormSucceeded($form, $values)
    {
        try
        {
            $this->imageUploader->uploadImgFormSucceeded($form,$values, "avatars");
        }
        catch (Exception $e)
        {
            $this->flashMessage($e->getMessage(), 'danger');
            $this->redirect(":Admin:Profile:default");
        }

        $this->flashMessage("Avatar se nahrál", "success");
        $this->redirect(":Admin:Profile:default");
    }

    /**
     * @throws AbortException
     */
    public function handleDeleteAvatar(): void
    {
        $values = ["avatar_path" => null];
        $old_avatar = $this->userRepository->getUserAvatar($this->user->getId());
        if($old_avatar != null)
        {
            $old_avatar = "../".$old_avatar;
            FileSystem::delete($old_avatar);
        }
        $this->userRepository->updateProfile($this->user->getId(), $values);
        $this->flashMessage("Obrázek se smazal", "success");
        $this->redirect(":Admin:Profile:default");
    }

}
