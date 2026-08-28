<?php

namespace App\Observers;

use App\Models\Chapter;

class ChapterObserver
{
    public function creating(Chapter $chapter)
    {
        // Ensure correct order
        $chapter->order = Chapter::where('quest_id', $chapter->quest_id)->max('order') ?? 0;
    }
}
