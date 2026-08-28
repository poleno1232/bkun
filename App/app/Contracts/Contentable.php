<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphTo;

interface Contentable
{
    public function content(): Attribute;
}
