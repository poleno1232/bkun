<?php

namespace App\Services\DiceRoller\Flags;

use App\Contracts\DiceFlaggable;
use App\DTO\DiceDataDTO;

class ModifierFlag implements DiceFlaggable
{
    private int $totalModifier = 0;

    public function hasPattern(string &$str): string
    {
        $found = false;

        while (preg_match('/^([+-])(\d+)/', $str, $matches)) {
            $found = true;
            $sign = $matches[1];
            $value = (int)$matches[2];

            $this->totalModifier += ($sign === '-') ? -$value : $value;

            $str = substr($str, strlen($matches[0]));
        }

        return $found;
    }

    public function apply(DiceDataDTO $dices): void
    {
        $dices->modifyResultBy($this->totalModifier);
    }
}
