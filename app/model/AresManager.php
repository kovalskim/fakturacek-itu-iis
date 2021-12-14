<?php

/** Author: Radek Jůzl */

namespace App\model;

class AresManager
{
    public function verificationCin($cin): int
    {
        if(is_numeric($cin))
        {
            $xml = file_get_contents("https://wwwinfo.mfcr.cz/cgi-bin/ares/darv_std.cgi?ico=".$cin);

            $pos = strpos($xml, "Pocet_zaznamu");
            if(is_numeric($xml[$pos+14]))
            {
                if(((int) ($xml[$pos+14])) > 0)
                {
                    return 0;
                }
            }
        }
        return 1;
    }

    private function loadData($position, $xml): string
    {
        $data = "";
        while($xml[$position] != '<')
        {
            $data .= $xml[$position];
            $position++;
        }
        return $data;
    }

    /**
     * @param $cin
     * @return array|null
     *
     */
    public function parseDataFromAres($cin): ?array
    {
        if(is_numeric($cin))
        {
            $xml = file_get_contents("https://wwwinfo.mfcr.cz/cgi-bin/ares/darv_std.cgi?ico=".$cin);

            $position = strpos($xml, "Obchodni_firma");
            if(!($position === false))
            {
                $jmeno = $this->loadData($position+15, $xml);
            }
            else
            {
                return null;
            }

            $position = strpos($xml, "Nazev_obce");
            if(!($position === false))
            {
                $mesto = $this->loadData($position+11, $xml);
            }
            else
            {
                return null;
            }

            $position = strpos($xml, "Nazev_ulice");
            if(!($position === false))
            {
                $ulice = $this->loadData($position+12, $xml);
            }
            else
            {
                $ulice = $mesto;
            }

            $position = strpos($xml, "Cislo_domovni");
            if(!($position === false))
            {
                $domovni = $this->loadData($position+14, $xml);
            }
            else
            {
                return null;
            }

            $orientacni = "";
            $position = strpos($xml, "Cislo_orientacni");
            if(!($position === false))
            {
                $orientacni = "/" . $this->loadData($position+17, $xml);
            }

            $position = strpos($xml, "PSC");
            if(!($position === false))
            {
                $psc = $this->loadData($position+4, $xml);
            }
            else
            {
                return null;
            }

            return [
                "cin" => $cin,
                "name" => $jmeno,
                "city" => $mesto,
                "street" => $ulice . " " . $domovni . $orientacni,
                "zip" => $psc
            ];
        }
        return null;
    }
}
