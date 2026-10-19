<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property null|string $title
 * @property array $content
 * @property string $type
 * @property null|\DateTime $closesAt
 * @property null|bool $isClosed
 */
interface Contentable
{
    public function content(): Attribute;
    public function title(): Attribute;
    public function type(): Attribute;
    public function closesAt(): Attribute;
    public function isClosed(): Attribute;
}
