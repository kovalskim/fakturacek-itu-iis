<?php

/** Author: Radek Jůzl */

namespace App\BusinessModule\presenters;


use App\PublicModule\forms\LogInFormFactory;
use App\PublicModule\model\EditProfile;
use App\PublicModule\repository\UserRepository;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Security\User;

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

    public function __construct(User $user, UserRepository $userRepository, LogInFormFactory $logInFormFactory, EditProfile $editProfile)
    {
        parent::__construct();
        $this->user = $user;
        $this->userRepository = $userRepository;
        $this->logInFormFactory = $logInFormFactory;
        $this->editProfile = $editProfile;
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
        $this->editProfile->editProfileFormSucceeded($form, $values);

        $this->flashMessage("Změna se provedla", "success");
        $this->redirect(":Business:Profile:default");
    }


}
