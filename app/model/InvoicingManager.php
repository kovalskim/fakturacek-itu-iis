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
}