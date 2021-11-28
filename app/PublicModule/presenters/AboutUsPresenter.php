<?php

/** Author: Radek Jůzl */

namespace App\PublicModule\presenters;

use App\repository\TextRepository;

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
        $this->template->text = $this->textRepository->getTextByType("aboutus"); /** Load text and picture from database */
    }
}
