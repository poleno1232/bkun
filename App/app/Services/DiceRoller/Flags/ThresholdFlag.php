<?php

namespace App\Services\DiceRoller\Flags;

use App\Contracts\DiceFlaggable;
use App\DTO\DiceDataDTO;

class ThresholdFlag implements DiceFlaggable
{
    private ?int $target = null;

    public function hasPattern(string &$str): string
    {
        if (preg_match('/t(\d+)/', $str, $matches)) {
            $this->target = (int) $matches[1];

            $str = str_replace($matches[0], '', $str);
            return true;
        }

        return false;
    }

    public function apply(DiceDataDTO $dices): void
    {
        $dices->setSuccess();
        foreach ($dices->getResult() as $result) {
            $result < $this->target ?: $dices->appendSuccess();
        }
    }
}
