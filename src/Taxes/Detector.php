<?php

namespace App\Taxes;

class Detector
{
    private $seuil;

    public function __construct($seuil)
    {
        $this->seuil = $seuil;
    }

    public function detect(float $amount) : bool {
        if ($amount > $this->seuil) {
            return true;
        }

        return false;
    }
}
