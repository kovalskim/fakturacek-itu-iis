<?php

namespace App\model;


use Exception;

class AresManager
{
    public function verificationCin($cin): int
    {
        if(is_numeric($cin))
        {
            $xml = file_get_contents("http://wwwinfo.mfcr.cz/cgi-bin/ares/darv_std.cgi?ico=".$cin);

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
}
