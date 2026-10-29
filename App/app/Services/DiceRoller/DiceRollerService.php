<?php

namespace App\Services\DiceRoller;

use App\DTO\DiceDataDTO;
use App\Services\DiceRoller\Flags\ImplodeFlag;
use App\Services\DiceRoller\Flags\ModifierFlag;
use App\Services\DiceRoller\Flags\ThresholdFlag;
use App\Contracts\DiceFlaggable;

class DiceRollerService
{
    private const array FLAGS = [
        ModifierFlag::class,
        ImplodeFlag::class,
        ThresholdFlag::class,
    ];

    /** @var array<DiceFlaggable> $flagPipeline */
    private array $flagPipeline = [];

    public function parse(string $command): DiceDataDTO
    {
        $argumentsString = str_replace('/dice ', '', $command);

        preg_match('/^(\d+)d(\d+)/', $argumentsString, $baseMatches);

        if (count($baseMatches) === 0) {
            throw new \RuntimeException("Dice values mismatch");
        }

        unset($this->flagPipeline);
        foreach (self::FLAGS as $flagClass) {
            $this->flagPipeline[] = new $flagClass();
        }

        $diceData = DiceDataDTO::make((int)$baseMatches[1], (int)$baseMatches[2]);
        $argumentsString = substr($argumentsString, strlen($baseMatches[0]));

        foreach ($this->flagPipeline as $flag) {
            if ($flag->hasPattern($argumentsString)) {
                $flag->apply($diceData);
            }
        }

        return $diceData;
    }
}
