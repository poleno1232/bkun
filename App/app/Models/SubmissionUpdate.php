<?php

namespace App\Models;

use App\Contracts\Contentable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'quest_id',
    'chapter_id',
    'posted_at',
])]
class SubmissionUpdate extends Model implements Contentable
{

    public function content(): Attribute
    {
        return Attribute::make();
    }
}
