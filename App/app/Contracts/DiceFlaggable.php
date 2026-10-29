<?php

namespace App\Contracts;

use App\DTO\DiceDataDTO;

interface DiceFlaggable
{
    public function hasPattern(string &$str): string;
    public function apply(DiceDataDTO $dices): void;
}
