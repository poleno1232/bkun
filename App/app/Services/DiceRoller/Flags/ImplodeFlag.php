<?php

namespace App\Services\DiceRoller\Flags;

use App\Contracts\DiceFlaggable;
use App\DTO\DiceDataDTO;

class ImplodeFlag implements DiceFlaggable
{
    public function hasPattern(string &$str): string
    {
        if (str_contains($str, 'i')) {
            $str = str_replace('i', '', $str);

            return true;
        }

        return false;
    }

    public function apply(DiceDataDTO $dices): void
    {
        $dices->setIndividual(true);
    }
}
