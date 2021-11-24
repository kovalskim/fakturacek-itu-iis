<?php

namespace App\model;

use App\model\ImageUploader;
use App\repository\SettingInvoicesRepository;
use Exception;
use Nette\Security\User;

class SettingInvoicesManager
{
    /** @var SettingInvoicesRepository */
    private $settingInvoicesRepository;

    /** @var User */
    public $user;

    /** @var ImageUploader */
    private $imageUploader;

    public function __construct(SettingInvoicesRepository $settingInvoicesRepository, User $user, ImageUploader $imageUploader)
    {
        $this->settingInvoicesRepository = $settingInvoicesRepository;
        $this->user = $user;
        $this->imageUploader = $imageUploader;
    }

    public function settingInvoicesFormValidate($form, $values)
    {
        $account_number = $values->account_number;
        $error = 0;
        $hodnota = 0;
        $nula = 0;
        $check = 0;
        $posun = 0;
        $vahy = array("1","2","4","8","5","10","9","7","3","6");

        $lenght = strlen($account_number) - 1;
        for($i=$lenght; $i >= 0; $i--)
        {
            if($account_number[$i] != "/")
            {
                if ($account_number[$i] != "-")
                {
                    if(!($account_number[$i] >= 1 and $account_number[$i] <= 9))
                    {
                        if($account_number[$i] != 0)
                        {
                            $error = 1;
                            break;
                        }
                    }
                    else
                    {
                        $nula++;
                    }
                }

            }

            if($check == 0)
            {
                if($i == $lenght-4)
                {
                    if ($account_number[$i] != "/")
                    {
                        $error = 1;
                        break;
                    }
                    $account_number[$i] = 0;
                    $nula = 0;
                    $check = 1;
                }
            }
            elseif($check == 1)
            {
                if($account_number[$i] == "-")
                {
                    if($nula < 2)
                    {
                        dump($nula);
                        $error = 1;
                        break;
                    }
                    $check = 2;
                    $posun = 0;
                    $error = 1;
                }
                else
                {
                    if($posun > 9)
                    {
                        $error = 1;
                        break;
                    }
                    $hodnota += $account_number[$i] * $vahy[$posun];
                    $posun++;
                }

            }
            elseif($check == 2)
            {
                $error = 0;
                if($posun > 5)
                {
                    $error = 1;
                    break;
                }
                $hodnota += $account_number[$i] * $vahy[$posun];
                $posun++;
            }
        }
        if($error or (($hodnota % 11) != 0))
        {
            $form["account_number"]->addError('Špatný formát čísla bankovního účtu.');
        }
    }

    /**
     * @throws Exception
     */
    public function settingInvoicesFormSucceeded($form, $values)
    {
        if($values->logo_path->error == 0)
        {
            try
            {
                $this->imageUploader->uploadImgFormSucceeded($form,$values, "logo");
            }
            catch (Exception $e)
            {
                throw new Exception('Změna se nepovedla');
            }
        }
        else
        {
            $values2 = ["account_number" => $values->account_number, "variable_symbol" => $values->variable_symbol, "vat" => $values->vat];
            $this->settingInvoicesRepository->updateSetting($values2, $this->user->getId());
        }
    }
}
