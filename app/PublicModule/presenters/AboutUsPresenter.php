<?php

namespace App\PublicModule\presenters;

use App\PublicModule\repository\TextRepository;

final class AboutUsPresenter extends BasePresenter
{
    /** @var TextRepository */
    private $textRepository;

    public function __construct(TextRepository $textRepository)
    {
        parent::__construct();
        $this->textRepository = $textRepository;
    }

    public function actionDefault()
    {

    }

    public function renderDefault()
    {
        $this->template->text = $this->textRepository->getTextByType("aboutus");
    }
}
