<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AmountExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('amount', [$this, 'amount'])
        ];
    }

    public function amount($value, $symbol = '€', $decSep = ',', $thousandsSep = ' ')
    {
        // 19169 => 191,69 €
        $finalValue = $value / 100;
        // 191.69
        $finalValue = number_format($finalValue, 2, $decSep, $thousandsSep);
        // 191,69
        return $finalValue . ' ' . $symbol;
    }
}