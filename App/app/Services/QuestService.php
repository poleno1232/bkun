<?php

namespace App\Services;

use App\Models\Quest;

class QuestService
{
    public function getQuery(array $params)
    {
        $quests = Quest::query()->with('user');

        if (isset($params['name'])) {
            $quests->where('name', 'like', '%' . $params['name'] . '%');
        }
        if (isset($params['is_active'])) {
            $quests->where('is_active', '=', $params['is_active']);
        } else {
            $quests->where('is_active', '=', true);
        }
        if (isset($params['user_id'])) {
            $quests->where('user_id', '=', $params['user_id']);
        }

        return $quests;
    }

    public function create(array $data)
    {
        $quest = Quest::create(array_merge($data, ['user_id' => auth()->id()]));
        $quest->load(['user', 'chapters']);

        return $quest;
    }

    public function update(Quest $quest, array $data)
    {
        $quest->update($data);
        $quest->load(['user', 'chapters']);

        return $quest;
    }
}
