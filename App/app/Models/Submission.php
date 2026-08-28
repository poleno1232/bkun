<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'submission_update_id',
    'text',
])]
class Submission extends Model
{
    //
}
