<?php

namespace App\BusinessModule\presenters;


use App\PublicModule\repository\UserRepository;
use Nette\Security\User;

final class ProfilePresenter extends BasePresenter
{
    /** @var User*/
    public $user;

    /** @var UserRepository */
    private $userRepository;

    public function __construct(User $user, UserRepository $userRepository)
    {
        parent::__construct();
        $this->user = $user;
        $this->userRepository = $userRepository;
    }

    public function actionDefault()
    {

    }

    public function renderDefault()
    {
        $this->template->data = $this->userRepository->getUserProfile($this->user->getId());
        if($this->template->data->avatar_path == null)
        {
            $this->template->data->avatar_path = "img\avatar_default.svg";
        }
        if($this->template->data->phone == null)
        {
            $this->template->data->phone = "nevyplněno";
        }
        if($this->template->data->account_number == null)
        {
            $this->template->data->account_number = "nevyplněno";
        }
    }

}
