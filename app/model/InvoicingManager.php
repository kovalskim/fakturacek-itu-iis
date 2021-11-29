<?php

/** Author: Martin Kovalski, Radek Jůzl */

namespace App\model;

use App\repository\ClientRepository;
use App\repository\InvoicingRepository;
use Nette\Security\User;
use Nette\Utils\DateTime;

class InvoicingManager
{
    /** @var InvoicingRepository */
    private $invoicingRepository;

    /** @var ClientRepository */
    private $clientRepository;

    /** @var User */
    public $user;

    public function __construct(InvoicingRepository $invoicingRepository, ClientRepository $clientRepository, User $user)
    {
        $this->invoicingRepository = $invoicingRepository;
        $this->clientRepository = $clientRepository;
        $this->user = $user;
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

    public function saveClient($values)
    {
        $user_id = $this->user->getId();
        $value = [
            'users_id' => $user_id,
            'name' => $values->name,
            'street' => $values->street,
            'city' => $values->city,
            'zip' => $values->zip,
            'cin' => $values->cin,
            'vat' => $values->vat,
            'phone' => $values->phone,
            'email' => $values->email
        ];

        if($this->clientRepository->isExistClient($value))
        {
            return 0;
        }
        else
        {
            $this->clientRepository->insertClientByUserId($value);
            return 1;
        }
    }

    /**
     * Function the function calculates the total price
     */
    public function saveInvoicesItems($values, $invoices_id): float
    {
        $suma = 0.0;
        $array = [];

        foreach ($values->multiplier as $value)
        {
            $count = str_replace(",",".", $value->count); /** Replacing the line with a dot - decimal numbers are with a dot */
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