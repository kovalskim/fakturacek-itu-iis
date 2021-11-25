<?php

namespace App\model;

use App\repository\InvoicingRepository;
use Nette\Utils\DateTime;

/** Author: Martin Kovalski */

class InvoicingManager
{
    /** @var InvoicingRepository */
    private $invoicingRepository;

    public function __construct(InvoicingRepository $invoicingRepository)
    {
        $this->invoicingRepository = $invoicingRepository;
    }

    public function getNewVariableSymbol($user_id, $pattern): int
    {
        $now = new DateTime();
        $year = $now->format('y');

        $last_vs = $this->invoicingRepository->getLastVsByUserId($user_id);

        switch ($pattern) {
            case "YYMM00":
                $month = $now->format('m');
                if($last_vs)
                {
                    $id = substr($last_vs, 4);
                    $id = (int)$id + 1;
                    $vs = $year . $month . sprintf('%02d', $id);
                }
                else
                {
                    $vs = $year . $month . "01";
                }
                break;

            case "YY000":
                if($last_vs)
                {
                    $id = substr($last_vs, 2);
                    $id = (int)$id + 1;
                    $vs = $year . sprintf('%03d', $id);
                }
                else
                {
                    $vs = $year . "001";
                }
                break;
            case "YY0000":
                if($last_vs)
                {
                    $id = substr($last_vs, 2);
                    $id = (int)$id + 1;
                    $vs = $year . sprintf('%04d', $id);
                }
                else
                {
                    $vs = $year . "0001";
                }
                break;
            default:
                $vs = 0;
        }

        return $vs;
    }

    /*public function editClientsFormValidate($form, $values = null)
    {
        if(!$values)
        {
            $values = $form->getValues();
        }

        $iterator = 0;
        foreach ($values->multiplier as $value)
        {
            if($value->name == null)
            {
                $form["multiplier"][$iterator]["name"]->addError("Chybí název položky");
            }
            if(($value->count == null) or ($value->count < 1))
            {
                $form["multiplier"][$iterator]["count"]->addError("Špatná hodnota");
            }
            if(($value->unit_price == null) or (!(is_numeric((str_replace(",",".",$value->unit_price))))) or ($value->unit_price < 0))
            {
                $form["multiplier"][$iterator]["unit_price"]->addError("Špatná hodnota");
            }
            $iterator++;
        }
    }*/

    public function saveClient()
    {

    }

    public function saveInvoicesItems($values, $invoices_id): float
    {
        $suma = 0.0;
        $array = [];

        foreach ($values->multiplier as $value)
        {
            $count = str_replace(",",".", $value->count);
            $unit_price = str_replace(",",".", $value->unit_price);
            $item_suma = $count * $unit_price;
            $suma += $item_suma;
            $stack = ["invoices_id" => $invoices_id, "name" => $value->name, "count" => $count, "unit_price"=> $unit_price, "type" => $value->type,"suma" => $item_suma];
            array_push($array, $stack);
        }

        $this->invoicingRepository->insertItemInvoice($array);
        return $suma;
    }
}