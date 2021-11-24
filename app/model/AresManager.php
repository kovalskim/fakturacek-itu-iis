<?php

namespace App\model;

use Atrox\Matcher;

class AresManager
{
    public function verificationCin($cin): int
    {
        /*$xml = file_get_contents("http://wwwinfo.mfcr.cz/cgi-bin/ares/darv_std.cgi?ico=27074358");

        $parse = Matcher::multi('//Ares_odpovedi', [
            'exist'    => 'Pocet_zaznamu',
            'test' => 'odpoved_pocet'
        ])->fromXml();

        $xml_parse = $parse($xml);
        bdump($xml_parse);*/

        return 0;
    }

}
