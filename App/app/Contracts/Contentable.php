<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property null|string $title
 * @property string $content
 * @property string $type
 */
interface Contentable
{
    public function content(): Attribute;
    public function title(): Attribute;
    public function type(): Attribute;
}
