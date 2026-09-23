<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $submission_update_id
 * @property int $user_id
 * @property string $text
 */
#[Fillable([
    'submission_update_id',
    'text',
])]
class Submission extends Model
{
    //
}
