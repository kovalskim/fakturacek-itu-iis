<?php

/** Author: Radek Jůzl */

namespace App\AccountantModule\presenters;


use App\PublicModule\forms\LogInFormFactory;
use App\PublicModule\model\EditProfile;
use App\PublicModule\model\UploadImage;
use App\PublicModule\repository\UserRepository;
use Exception;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Http\Session;
use Nette\Security\User;
use Nette\Utils\ImageException;
use Nette\Utils\UnknownImageFileException;

final class ProfilePresenter extends BasePresenter
{
    /** @var User*/
    public $user;

    /** @var UserRepository */
    private $userRepository;

    /** @var LogInFormFactory */
    private $logInFormFactory;

    /** @var EditProfile */
    private $editProfile;

    /** @var UploadImage */
    private $uploadImage;

    public function __construct(User $user, UserRepository $userRepository, LogInFormFactory $logInFormFactory, EditProfile $editProfile, UploadImage $uploadImage)
    {
        parent::__construct();
        $this->user = $user;
        $this->userRepository = $userRepository;
        $this->logInFormFactory = $logInFormFactory;
        $this->editProfile = $editProfile;
        $this->uploadImage = $uploadImage;
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
        $form = $this->logInFormFactory->createEditProfileForm();
        $form->onValidate[] = [$this, "editProfileFormValidate"];
        $form->onSuccess[] = [$this, "editProfileFormSucceeded"];
        return $form;
    }

    public function editProfileFormValidate($form, $values)
    {
        $this->editProfile->editProfileFormValidate($form, $values);
    }

    /**
     * @throws AbortException
     */

    public function editProfileFormSucceeded($form, $values)
    {
        $new_email = $this->editProfile->editProfileFormSucceeded($form, $values);
        if($new_email)
        {
            $this->session->destroy();
            $this->user->logout();
            $this->flashMessage('Ověř si nový e-mail a přihlaš se s ním!', 'info');
            $this->redirect(':Public:Homepage:default');
        }

        $this->flashMessage("Změna se provedla", "success");
        $this->redirect(":Accountant:Profile:default");
    }

    public function actionUpload()
    {

    }

    protected function createComponentUploadAvatarForm(): Form
    {
        $form = $this->logInFormFactory->createUploadAvatarForm();
        $form->onSuccess[] = [$this, "uploadAvatarFormSucceeded"];
        return $form;
    }

    public function uploadAvatarFormSucceeded($form, $values)
    {
        try
        {
            $this->uploadImage->uploadAvatarFormSucceeded($form,$values);
        }
        catch (Exception $e)
        {
            $this->flashMessage($e->getMessage(), 'danger');
            $this->redirect(":Accountant:Profile:default");
        }

        $this->flashMessage("Avatar se nahrál", "success");
        $this->redirect(":Accountant:Profile:default");
    }

}
