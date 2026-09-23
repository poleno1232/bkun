<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property null|string $title
 * @property array $content
 * @property string $type
 * @property null|\DateTime $closes_at
 * @property null|bool $is_closed
 */
interface Contentable
{
    public function content(): Attribute;
    public function title(): Attribute;
    public function type(): Attribute;
    public function closes_at(): Attribute;
    public function is_closed(): Attribute;
}
