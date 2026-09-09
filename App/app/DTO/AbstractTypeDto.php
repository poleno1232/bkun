<?php

namespace App\DTO;

abstract class AbstractTypeDto implements \JsonSerializable
{
    public abstract static function make(...$arguments);
}
