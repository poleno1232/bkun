<?php

namespace App\Observers;

use App\Models\Quest;

class QuestObserver
{
    public function created(Quest $quest)
    {
        // Ensure first chapter created
        $quest->chapters()->create(['title' => 'Home', 'quest_id' => $quest->id]);
    }
}
